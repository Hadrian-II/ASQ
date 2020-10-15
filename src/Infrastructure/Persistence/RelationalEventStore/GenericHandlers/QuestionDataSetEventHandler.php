<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionDataSetEvent;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

/**
 * Class QuestionDataSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDataSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var QuestionData $question_data */
        $question_data = $event->getData();

        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA, [
            'event_id' => $event_id,
            'title' => $question_data->getTitle(),
            'text' => $question_data->getQuestionText(),
            'author' => $question_data->getAuthor(),
            'description' => $question_data->getDescription(),
            'working_type' => $question_data->getWorkingTime(),
            'lifecycle' => $question_data->getLifecycle()
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
                'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA .' where event_id = %s',
                $this->db->quote($data['event_id'], 'int')
            )
        );

        $row = $this->db->fetchAssoc($res);

        return new QuestionDataSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $data['initiating_user_id'],
            QuestionData::create(
                $row['title'],
                $row['text'],
                $row['author'],
                $row['description'],
                $row['working_type'],
                $row['lifecycle']
            )
        );
    }
}