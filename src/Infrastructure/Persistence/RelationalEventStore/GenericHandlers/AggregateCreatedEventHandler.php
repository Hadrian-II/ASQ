<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use Fluxlabs\CQRS\Event\Standard\AggregateCreatedEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;
use srag\asq\Domain\Model\Question;

/**
 * Class AggregateCreatedEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class AggregateCreatedEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        $id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_CREATED);
        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_CREATED, [
            'id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'type' => ['text', $event->getAdditionalData()[Question::VAR_TYPE]]
        ]);
    }

    public function getQueryString() : string
    {
        return 'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_CREATED .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows) : DomainEvent
    {
        $date = new DateTimeImmutable();

        return new AggregateCreatedEvent(
            $this->factory->fromString($data['question_id']),
            $date->setTimestamp($data['occurred_on']),
            [Question::VAR_TYPE => $rows[0]['type']]);
    }
}