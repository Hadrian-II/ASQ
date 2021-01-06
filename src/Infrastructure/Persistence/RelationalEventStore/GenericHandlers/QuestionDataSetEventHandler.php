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

        $id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA);
        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA, [
            'id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'title' => ['text', $question_data->getTitle()],
            'text' => ['clob', $question_data->getQuestionText()],
            'author' => ['text', $question_data->getAuthor()],
            'description' => ['clob', $question_data->getDescription()],
            'working_time' => ['integer', $question_data->getWorkingTime()],
            'lifecycle' => ['integer', $question_data->getLifecycle()]
        ]);
    }
    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString(): string
    {
        return 'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_DATA .' where event_id in (%s)';
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::createEvent()
     */
    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionDataSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionData(
                $rows[0]['title'] ?? '',
                $rows[0]['text'] ?? '',
                $rows[0]['author'] ?? '',
                $rows[0]['description'] ?? '',
                intval($rows[0]['working_time']),
                intval($rows[0]['lifecycle'])
            )
        );
    }
}