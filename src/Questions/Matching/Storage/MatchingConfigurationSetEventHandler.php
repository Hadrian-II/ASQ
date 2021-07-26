<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Matching\Editor\Data\MatchingEditorConfiguration;
use srag\asq\Questions\Matching\Scoring\Data\MatchingScoringConfiguration;
use srag\asq\Questions\Matching\Editor\Data\MatchingItem;
use srag\asq\Questions\Matching\Editor\Data\MatchingMapping;

/**
 * Class MatchingConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    const TYPE_DEFINITION = 0;
    const TYPE_TERM = 1;

    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config MatchingEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config MatchingScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupMatching::TABLENAME_MATCHING_CONFIGURATION));
        $this->db->insert(SetupMatching::TABLENAME_MATCHING_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'shuffle' => ['integer', $editor_config->getShuffle()],
            'thumbnail_size' => ['integer', $editor_config->getThumbnailSize()],
            'matching_mode' => ['integer', $editor_config->getMatchingMode()],
            'wrong_deduction' => ['float', $scoring_config->getWrongDeduction()]
        ]);

        $this->storeItems($editor_config->getDefinitions(), $id,self::TYPE_DEFINITION);
        $this->storeItems($editor_config->getTerms(), $id, self::TYPE_TERM);
        $this->storeMappings($editor_config->getMatches(), $id);
    }

    /**
     * @param MatchingItem[] $items
     * @param int $config_id
     * @param int $type
     */
    private function storeItems(array $items, int $config_id, int $type) : void
    {
        foreach ($items as $item) {
            $id = intval($this->db->nextId(SetupMatching::TABLENAME_MATCHING_ITEM));
            $this->db->insert(SetupMatching::TABLENAME_MATCHING_ITEM, [
                'item_id' => ['integer', $id],
                'config_id' => ['integer', $config_id],
                'type' => ['integer', $type],
                'id' => ['text', $item->getId()],
                'text' => ['text', $item->getText()],
                'image' => ['text', $item->getImage()]
            ]);
        }
    }

    /**
     * @param MatchingMapping[] $mappings
     * @param int $config_id
     */
    private function storeMappings(array $mappings, int $config_id) : void
    {
        foreach ($mappings as $mapping) {
            $this->db->insert(SetupMatching::TABLENAME_MATCHING_MAPPING, [
                'config_id' => ['integer', $config_id],
                'definition_id' => ['text', $mapping->getDefinitionId()],
                'term_id' => ['text', $mapping->getTermId()],
                'points' => ['float', $mapping->getPoints()]
            ]);
        }
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupMatching::TABLENAME_MATCHING_CONFIGURATION .' c
                left join ' . SetupMatching::TABLENAME_MATCHING_ITEM . ' i on c.config_id = i.config_id
                where c.event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $terms = [];
        $definitions = [];
        foreach ($rows as $entry) {
            $item = new MatchingItem(
                $entry['id'],
                $entry['text'],
                $entry['image']
            );

            switch (intval($entry['type'])) {
                case self::TYPE_DEFINITION:
                    $definitions[] = $item;
                    break;
                case self::TYPE_TERM:
                    $terms[] = $item;
                    break;
            }
        }

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new MatchingEditorConfiguration(
                    $this->readInt($rows[0]['shuffle']),
                    $this->readInt($rows[0]['thumbnail_size']),
                    $this->readInt($rows[0]['matching_mode']),
                    $definitions,
                    $terms,
                    $this->getMappings($rows[0]['config_id'])
                    ),
                new MatchingScoringConfiguration(
                    $this->readFloat($rows[0]['wrong_deduction'])
                    )
                )
            );
    }

    /**
     * @param string $config_id
     * @return MatchingMapping[]
     */
    private function getMappings(string $config_id) : array
    {
        $res = $this->db->query(
            sprintf(
                'select * from ' . SetupMatching::TABLENAME_MATCHING_MAPPING .' c
                 where c.config_id = %s',
                $this->db->quote($config_id, 'int')
                )
            );

        return array_map(function($entry) {
            return new MatchingMapping(
                $entry['definition_id'],
                $entry['term_id'],
                $this->readFloat($entry['points'])
            );
        }, $this->db->fetchAll($res));
    }

}