<?php
declare(strict_types=1);

namespace srag\asq\Domain;

use srag\CQRS\Aggregate\AbstractAggregateRepository;
use srag\CQRS\Aggregate\AbstractAggregateRoot;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\EventStore;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStore;

/**
 * Class QuestionRepository
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionRepository extends AbstractAggregateRepository
{

    /**
     * @var QuestionEventStore
     */
    private $event_store;

    /**
     * QuestionRepository constructor.
     */
    protected function __construct()
    {
        parent::__construct();
        $this->event_store = new QuestionEventStore();
    }

    /**
     * @return EventStore
     */
    protected function getEventStore() : EventStore
    {
        return $this->event_store;
    }

    /**
     * @param DomainEvents $event_history
     *
     * @return AbstractAggregateRoot
     */
    protected function reconstituteAggregate(DomainEvents $event_history) : AbstractAggregateRoot
    {
        return Question::reconstitute($event_history);
    }

    /**
     * @return int
     */
    public function getNextId() : int
    {
        return $this->event_store->getNextId();
    }

    public function getAggregateByIliasId(int $id) : Question
    {
        $aggregate_id = $this->event_store->getAggregateIdOfIliasId($id);

        return $this->getAggregateRootById($aggregate_id);
    }
}
