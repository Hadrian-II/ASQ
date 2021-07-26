<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use ilDateTime;
use srag\CQRS\Aggregate\RevisionId;
use srag\CQRS\Event\DomainEvent;
use srag\CQRS\Event\Standard\AggregateRevisionCreatedEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

/**
 * Class AggregateRevisionCreatedEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AggregateRevisionCreatedEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $revision_id RevisionId */
        $revision_id = $event->getRevisionId();

        $id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_REVISION);
        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_REVISION, [
            'id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'key' => ['text', $revision_id->GetKey()],
            'name' => ['text', $revision_id->getName()],
            'algorithm' => ['text', $revision_id->getAlgorithm()]
        ]);
    }

    public function getQueryString() : string
    {
        return 'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_REVISION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows) : DomainEvent
    {
        return new AggregateRevisionCreatedEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            RevisionId::create(
                $rows[0]['key'],
                $rows[0]['algorithm'],
                $rows[0]['name'])
            );
    }
}