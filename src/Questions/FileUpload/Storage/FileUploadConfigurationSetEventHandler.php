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
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString(): string
    {
        return 'select * from ' . SetupFileUpload::TABLENAME_FILEUPLOAD_CONFIGURATION .' where event_id in(%s)';
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::createEvent()
     */
    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new FileUploadEditorConfiguration(
                    intval($rows[0]['max_size']),
                    $rows[0]['allowed_extensions']
                ),
                new FileUploadScoringConfiguration(
                    floatval($rows[0]['points']),
                    boolval($rows[0]['completed_by_sub'])
                )
            )
        );
    }
}