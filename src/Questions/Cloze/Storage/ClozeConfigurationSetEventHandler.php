<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;
use srag\asq\Questions\Cloze\Scoring\Data\ClozeScoringConfiguration;
use function srag\asq\Questions\Cloze\Storage\ClozePlayConfigurationSetEventHandler\storeGapItems;
use function srag\asq\Questions\Cloze\Storage\ClozePlayConfigurationSetEventHandler\storeNumericGap;
use function srag\asq\Questions\Cloze\Storage\ClozePlayConfigurationSetEventHandler\storeSelectGap;
use function srag\asq\Questions\Cloze\Storage\ClozePlayConfigurationSetEventHandler\storeTextGap;

/**
 * Class ClozeConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    const DEFAULT_GAP = -1;

    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $cloze_config ClozeEditorConfiguration */
        $cloze_config = $event->getPlayConfiguration()->getEditorConfiguration();

        $cloze_id = intval($this->db->nextId(SetupCloze::TABLENAME_CLOZE_CONFIGURATION));
        $this->db->insert(SetupCloze::TABLENAME_CLOZE_CONFIGURATION, [
            'cloze_id' => ['integer', $cloze_id],
            'event_id' => ['integer', $event_id],
            'text' => ['clob', $cloze_config->getClozeText()]
        ]);

        foreach ($cloze_config->getGaps() as $gap) {
            $this->storeGap($gap, $cloze_id);
        }
    }

    private function storeGap(ClozeGapConfiguration $gap, int $cloze_id) : void
    {
        switch (get_class($gap)) {
            case TextGapConfiguration::class:
                $this->storeTextGap($gap, $cloze_id);
                break;
            case SelectGapConfiguration::class:
                $this->storeSelectGap($gap, $cloze_id);
                break;
            case NumericGapConfiguration::class:
                $this->storeNumericGap($gap, $cloze_id);
                break;
        }
    }

    private function storeTextGap(TextGapConfiguration $gap, int $cloze_id) : void
    {
        $gap_id = intval($this->db->nextId(SetupCloze::TABLENAME_CLOZE_GAP));
        $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP, [
            'gap_id' => ['integer', $gap_id],
            'cloze_id' => ['integer', $cloze_id],
            'gap_type' => ['text', ClozeGapConfiguration::TYPE_TEXT],
            'field_length' => ['integer', $gap->getFieldLength()],
            'text_match_method' => ['integer', $gap->getMatchingMethod()]
        ]);

        $this->storeGapItems($gap->getItems(), $gap_id);
    }

    private function storeSelectGap(SelectGapConfiguration $gap, int $cloze_id) : void
    {
        $gap_id = intval($this->db->nextId(SetupCloze::TABLENAME_CLOZE_GAP));
        $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP, [
            'gap_id' => ['integer', $gap_id],
            'cloze_id' => ['integer', $cloze_id],
            'gap_type' => ['text', ClozeGapConfiguration::TYPE_DROPDOWN]
        ]);

        $this->storeGapItems($gap->getItems(), $gap_id);
    }

    /**
     * @param ClozeGapItem[] $items
     * @param int $gap_id
     */
    private function storeGapItems(array $items, int $gap_id) : void
    {
        foreach ($items as $item) {
            $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP_ITEM, [
                'gap_id' => ['integer', $gap_id],
                'item_text' => ['text', $item->getText()],
                'item_points' => ['float', $item->getPoints()]
            ]);
        }
    }

    private function storeNumericGap(NumericGapConfiguration $gap, int $cloze_id) : void
    {
        $gap_id = intval($this->db->nextId(SetupCloze::TABLENAME_CLOZE_GAP));
        $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP, [
            'gap_id' => ['integer', $gap_id],
            'cloze_id' => ['integer', $cloze_id],
            'gap_type' => ['text', ClozeGapConfiguration::TYPE_NUMBER],
            'field_length' => ['integer', $gap->getFieldLength()],
            'value' => ['float', $gap->getValue()],
            'upper' => ['float', $gap->getUpper()],
            'lower' => ['float', $gap->getLower()],
            'gap_points' => ['float', $gap->getPoints()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupCloze::TABLENAME_CLOZE_CONFIGURATION .' c
                 left join ' . SetupCloze::TABLENAME_CLOZE_GAP .' g on c.cloze_id = g.cloze_id
                 left join ' . SetupCloze::TABLENAME_CLOZE_GAP_ITEM . ' i on g.gap_id = i.gap_id
                 where c.event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $items = [];
        foreach ($rows as $row)
        {
            $gap_id = $row['gap_id'] ?? self::DEFAULT_GAP;

            if (!array_key_exists($gap_id, $items)) {
                $items[$gap_id] = [];
            }

            $items[$gap_id][] = $row;
        }

        $gaps = [];

        foreach ($items as $gap) {
            switch (current($gap)['gap_type']) {
                case ClozeGapConfiguration::TYPE_DROPDOWN:
                    $gaps[] = $this->createSelectGap($gap);
                    break;
                case ClozeGapConfiguration::TYPE_NUMBER:
                    $gaps[] = $this->createNumericGap($gap);
                    break;
                case ClozeGapConfiguration::TYPE_TEXT:
                    $gaps[] = $this->createTextGap($gap);
                    break;
            }
        }

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new ClozeEditorConfiguration(
                    $rows[0]['text'],
                    $gaps
                ),
                new ClozeScoringConfiguration()
            )
        );
    }

    private function createSelectGap(array $gap) : SelectGapConfiguration
    {
        return new SelectGapConfiguration($this->createGapItems($gap));
    }

    private function createTextGap(array $gap) : TextGapConfiguration
    {
        $data = reset($gap);

        return new TextGapConfiguration(
            $this->createGapItems($gap),
            $this->readInt($data['field_length']),
            $this->readInt($data['text_match_method']));
    }

    private function createGapItems(array $gap_items) : array
    {
        return array_map(function($item) {
            return new ClozeGapItem($item['item_text'], $this->readFloat($item['item_points']));
        }, $gap_items);
    }

    private function createNumericGap(array $gap) : NumericGapConfiguration
    {
        $data = reset($gap);

        return new NumericGapConfiguration(
            $this->readFloat($data['value']),
            $this->readFloat($data['upper']),
            $this->readFloat($data['lower']),
            $this->readFloat($data['gap_points']),
            $this->readInt($data['field_length']));
    }
}