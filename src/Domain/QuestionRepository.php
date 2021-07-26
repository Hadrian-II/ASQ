<?php
declare(strict_types=1);

namespace srag\asq\Domain;

use srag\CQRS\Aggregate\AbstractAggregateRepository;
use srag\CQRS\Aggregate\AbstractAggregateRoot;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\EventStore;
use srag\CQRS\Event\IEventStore;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

/**
 * Class QuestionRepository
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionRepository extends AbstractAggregateRepository
{
    private IEventStore $event_store;

    public function __construct()
    {
        global $DIC;
        parent::__construct();
        $this->event_store = new RelationalQuestionEventStore($DIC->database());
    }

    protected function getEventStore() : IEventStore
    {
        return $this->event_store;
    }

    protected function reconstituteAggregate(DomainEvents $event_history) : AbstractAggregateRoot
    {
        return Question::reconstitute($event_history);
    }
}
