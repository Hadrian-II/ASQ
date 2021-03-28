<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\CQRS\Event\Standard\AggregateCreatedEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;
use srag\asq\Domain\Model\Question;

/**
 * Class AggregateCreatedEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AggregateCreatedEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        $id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_CREATED);
        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_CREATED, [
            'id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'type' => ['text', $event->getAdditionalData()[Question::VAR_TYPE]]
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString() : string
    {
        return 'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_CREATED .' where event_id in(%s)';
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::createEvent()
     */
    public function createEvent(array $data, array $rows) : DomainEvent
    {
        return new AggregateCreatedEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            [Question::VAR_TYPE => $rows[0]['type']]);
    }
}