<?php
declare(strict_types=1);

namespace srag\asq\Domain;

use Fluxlabs\CQRS\Aggregate\AbstractAggregateRepository;
use Fluxlabs\CQRS\Aggregate\AbstractAggregateRoot;
use Fluxlabs\CQRS\Event\DomainEvents;
use Fluxlabs\CQRS\Event\EventStore;
use Fluxlabs\CQRS\Event\IEventStore;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStore;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;
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
        parent::__construct();
        $this->event_store = new QuestionEventStore();
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
