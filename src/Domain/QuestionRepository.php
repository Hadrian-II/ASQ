<?php
declare(strict_types=1);

namespace srag\asq\Domain;

use srag\CQRS\Aggregate\AbstractAggregateRepository;
use srag\CQRS\Aggregate\AbstractAggregateRoot;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\EventStore;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStore;
use srag\CQRS\Event\IEventStore;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

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
     * @var IEventStore
     */
    private $event_store;

    /**
     * QuestionRepository constructor.
     */
    protected function __construct()
    {
        parent::__construct();
        //$this->event_store = new QuestionEventStore();
        global $DIC;
        $this->event_store = new RelationalQuestionEventStore($DIC->database());
    }

    /**
     * @return EventStore
     */
    protected function getEventStore() : IEventStore
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
}
