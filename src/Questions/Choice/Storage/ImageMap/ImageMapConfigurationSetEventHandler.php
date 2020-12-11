<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringConfiguration;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorConfiguration;

/**
 * Class ImageMapConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $image_config ImageMapEditorConfiguration */
        $image_config = $event->getPlayConfiguration()->getEditorConfiguration();

        $image_id = intval($this->db->nextId(SetupImageMap::TABLENAME_IMAGEMAP_CONFIGURATION));
        $this->db->insert(SetupImageMap::TABLENAME_IMAGEMAP_CONFIGURATION, [
            'image_id' => ['integer', $image_id],
            'event_id' => ['integer', $event_id],
            'image' => ['text', $image_config->getImage()],
            'is_multi' => ['integer', $image_config->isMultipleChoice()],
            'max_answers' => ['integer', $image_config->getMaxAnswers()]
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
                'select * from ' . SetupImageMap::TABLENAME_IMAGEMAP_CONFIGURATION .' c
                 where c.event_id = %s',
                $this->db->quote($data['id'], 'int')
                )
            );

        $row = $this->db->fetchAssoc($res);

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new ImageMapEditorConfiguration(
                    $row['image'],
                    boolval($row['is_multi']),
                    intval($row['max_answers'])
                ),
                new MultipleChoiceScoringConfiguration()
            )
        );
    }
}