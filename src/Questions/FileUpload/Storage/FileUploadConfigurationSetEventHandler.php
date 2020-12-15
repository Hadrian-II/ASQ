<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\FileUpload\Editor\Data\FileUploadEditorConfiguration;
use srag\asq\Questions\FileUpload\Scoring\Data\FileUploadScoringConfiguration;

/**
 * Class FileUploadConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config FileUploadEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config FileUploadScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupFileUpload::TABLENAME_FILEUPLOAD_CONFIGURATION));
        $this->db->insert(SetupFileUpload::TABLENAME_FILEUPLOAD_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'max_size' => ['integer', $editor_config->getMaximumSize()],
            'allowed_extensions' => ['text', $editor_config->getAllowedExtensions()],
            'points' => ['float', $scoring_config->getPoints()],
            'completed_by_sub' => ['integer', $scoring_config->isCompletedBySubmition()]
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
                'select * from ' . SetupFileUpload::TABLENAME_FILEUPLOAD_CONFIGURATION .' c
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
                new FileUploadEditorConfiguration(
                        intval($row['max_size']),
                        $row['allowed_extensions']
                    ),
                new FileUploadScoringConfiguration(
                        floatval($row['points']),
                        boolval($row['completed_by_sub'])
                    )
                )
            );
    }
}