<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\QuestionHandlers;

use srag\CQRS\Event\DomainEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\IEventStorageHandler;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\SetupCloze;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use ilDateTime;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Cloze\Scoring\Data\ClozeScoringConfiguration;

/**
 * Class ClozePlayConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozePlayConfigurationSetEventHandler implements IEventStorageHandler
{
    const DEFAULT_GAP = -1;

    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $cloze_config ClozeEditorConfiguration */
        $cloze_config = $event->getPlayConfiguration()->getEditorConfiguration();

        $cloze_id = $this->db->insert(SetupCloze::TABLENAME_CLOZE_CONFIGURATION, [
            'event_id' => $event_id,
            'text' => $cloze_config->getClozeText()
        ]);

        foreach ($cloze_config->getGaps() as $gap) {
            storeGap($gap, $cloze_id);
        }
    }

    /**
     * @param ClozeGapConfiguration $gap
     * @param int $cloze_id
     */
    private function storeGap(ClozeGapConfiguration $gap, int $cloze_id) : void
    {
        switch (get_class($gap)) {
            case TextGapConfiguration::class:
                storeTextGap($gap, $cloze_id);
                break;
            case SelectGapConfiguration::class:
                storeSelectGap($gap, $cloze_id);
                break;
            case NumericGapConfiguration::class:
                storeNumericGap($gap, $cloze_id);
                break;
        }
    }

    /**
     * @param TextGapConfiguration $gap
     * @param int $cloze_id
     */
    private function storeTextGap(TextGapConfiguration $gap, int $cloze_id) : void
    {
        $gap_id = $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP, [
            'cloze_id' => $cloze_id,
            'gap_type' => ClozeGapConfiguration::TYPE_TEXT,
            'field_length' => $gap->getFieldLength(),
            'text_match_method' => $gap->getMatchingMethod()
        ]);

        storeGapItems($gap->getItems(), $gap_id);
    }

    /**
     * @param SelectGapConfiguration $gap
     * @param int $cloze_id
     */
    private function storeSelectGap(SelectGapConfiguration $gap, int $cloze_id) : void
    {
        $gap_id = $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP, [
            'cloze_id' => $cloze_id,
            'gap_type' => ClozeGapConfiguration::TYPE_DROPDOWN
        ]);

        storeGapItems($gap->getItems(), $gap_id);
    }

    /**
     * @param ClozeGapItem[] $items
     * @param int $gap_id
     */
    private function storeGapItems(array $items, int $gap_id) : void
    {
        foreach ($items as $item) {
            $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP_ITEM, [
                'gap_id' => $gap_id,
                'text' => $item->getText(),
                'points' => $item->getPoints()
            ]);
        }
    }

    /**
     * @param NumericGapConfiguration $gap
     * @param int $cloze_id
     */
    private function storeNumericGap(NumericGapConfiguration $gap, int $cloze_id) : void
    {
        $this->db->insert(SetupCloze::TABLENAME_CLOZE_GAP, [
            'cloze_id' => $cloze_id,
            'gap_type' => ClozeGapConfiguration::TYPE_NUMBER,
            'field_length' => $gap->getFieldLength(),
            'value' => $gap->getValue(),
            'upper' => $gap->getUpper(),
            'lower' => $gap->getLower(),
            'points' => $gap->getPoints()
        ]);
    }

    /**
     * @param array $data
     * @return DomainEvent
     */
    public function loadEvent(array $data) : DomainEvent
    {
        $res = $this->db->query(
            sprintf(
                'select * from ' . SetupCloze::TABLENAME_CLOZE_CONFIGURATION .' as c
                 left outer join' . SetupCloze::TABLENAME_CLOZE_GAP .' as g on c.id = g.cloze_id
                 left outer join' . SetupCloze::TABLENAME_CLOZE_GAP_ITEM . ' as i on g.id = i.gap_id
                 where c.event_id = %s',
                $this->db->quote($data['event_id'], 'int')
            )
        );

        $items = [];
        $cloze_text = '';
        while ($row = $this->db->fetchAssoc($res))
        {
            $cloze_id = $row['c.id'];
            $gap_id = $row['g.id'] ?? self::DEFAULT_GAP;

            if (!array_key_exists($cloze_id, $items)) {
                $cloze_text = $row['c.text'];
                $items[$cloze_id] = [];
            }
            if (!array_key_exists($gap_id, $items[$cloze_id])) {
                $items[$cloze_id][$gap_id] = [];
            }

            $items[$cloze_id][$gap_id] = $row;
        }

        $gaps = [];

        foreach ($items as $gap) {
            switch (current($gap)['g.gap_type']) {
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
            $data['initiating_user_id'],
            QuestionPlayConfiguration::create(
                ClozeEditorConfiguration::create($cloze_text, $gaps),
                ClozeScoringConfiguration::create()
            )
        );
    }

    /**
     * @param array $gap
     * @return SelectGapConfiguration
     */
    private function createSelectGap(array $gap) : SelectGapConfiguration
    {
        return SelectGapConfiguration::Create($this->createGapItems($gap));
    }

    /**
     * @param array $gap
     * @return TextGapConfiguration
     */
    private function createTextGap(array $gap) : TextGapConfiguration
    {
        $data = reset($gap);

        return TextGapConfiguration::Create(
            $this->createGapItems($gap),
            $data['g.field_length'],
            $data['g.text_match_method']);
    }

    /**
     * @param array $gap_items
     * @return array
     */
    private function createGapItems(array $gap_items) : array
    {
        return array_map(function($item) {
            return ClozeGapItem::create($item['i.text'], $item['i.points']);
        }, $gap_items);
    }

    /**
     * @param array $gap
     * @return NumericGapConfiguration
     */
    private function createNumericGap(array $gap) : NumericGapConfiguration
    {
        $data = reset($gap);

        return NumericGapConfiguration::Create(
            $data['g.value'],
            $data['g.upper'],
            $data['g.lower'],
            $data['g.points'],
            $data['g.field_length']);
    }
}