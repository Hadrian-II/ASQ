<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use ILIAS\Data\UUID\Uuid;
use srag\CQRS\Event\DomainEvent;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\IEventStore;
use ilDBInterface;
use Exception;
use ILIAS\Data\UUID\Factory;
use srag\CQRS\Event\Standard\AggregateCreatedEvent;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Event\QuestionDataSetEvent;
use srag\asq\Domain\Event\QuestionFeedbackSetEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;

/**
 * Class RelationalQuestionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class RelationalQuestionEventStore implements IEventStore
{
    const TABLE_NAME = 'rqes_events';
    const TABLE_NAME_QUESTION_INDEX = 'rqes_question_index';
    const TABLE_NAME_QUESTION_DATA = 'rqes_question_data';

    /**
     * @var ilDBInterface
     */
    private $db;

    /**
     * @var Factory
     */
    private $uuid_factory;

    /**
     * @var array
     */
    const GENERIC_HANDLERS = [

    ];

    /**
     * @var array
     */
    const TYPE_HANDLERS = [

    ];

    /**
     * @var array
     */
    private $handlers = [];

    /**
     * @param ilDBInterface $db
     */
    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
        $this->uuid_factory = new Factory();
    }

    /**
     * {@inheritDoc}
     * @see \srag\CQRS\Event\IEventStore::commit()
     */
    public function commit(DomainEvents $events): void
    {
        $this->db->beginTransaction();

        try
        {
            /** @var $event \srag\CQRS\Event\AbstractDomainEvent */
            foreach ($events as $event)
            {
                if ($event instanceof AggregateCreatedEvent) {
                    $type = $event->getAdditionalData()[Question::VAR_TYPE];
                    $this->storeQuestionType($event->getAggregateId(), $event->getAdditionalData()[Question::VAR_TYPE]);
                }
                else {
                    $type = $this->getQuestionType($event->getAggregateId());
                }

                $event_id = $this->uuid_factory->uuid4();

                $id = $this->db->insert(self::TABLE_NAME, [
                    'event_id' => $event_id->toString(),
                    'event_version' => $event->getEventVersion(),
                    'question_id' => $event->getAggregateId()->toString(),
                    'event_name' => $event->getEventName(),
                    'occurred_on' => time(),
                    'initiating_user_id' => $event->getInitiatingUserId()
                ]);

                if ($this->isGenericEvent($event))
                {
                    $generic_handler = $this->getGenericHandler(get_class($event));
                    $generic_handler->storeEvent($event, $id);
                }
                else
                {
                    $type_specific_handler = $this->getTypeSpecificHandler($type);
                    $type_specific_handler->storeEvent($event, $id);
                }
            }
            $this->db->commit();
        }
        catch (Exception $e)
        {
            $this->db->rollback();
        }
    }

    /**
     * @param Uuid $id
     * @return string
     */
    private function getQuestionType(Uuid $id) : string
    {
        $res = $this->db->query(
            sprintf(
                'select question_type from rqes_question_index where question_id = %s',
                $this->db->quote($id->toString(), 'string')
            )
        );

        return $res->fetch()['question_type'];
    }

    /**
     * @param Uuid $id
     * @param string $type
     */
    private function storeQuestionType(Uuid $id, string $type) : void
    {
        $this->db->insert(self::TABLE_NAME_QUESTION_INDEX, [
            'question_id' => $id->toString(),
            'question_type' => $type
        ]);
    }

    private function isGenericEvent(DomainEvent $event) : bool
    {
        return ($event instanceof AggregateCreatedEvent ||
                $event instanceof QuestionDataSetEvent ||
                $event instanceof QuestionFeedbackSetEvent ||
                $event instanceof QuestionHintsSetEvent);
    }

    /**
     * @param string $type
     * @param string $event_type
     * @return IEventStorageHandler
     */
    private function getTypeSpecificHandler(string $type, string $event_type) : IEventStorageHandler
    {
        if (! array_key_exists($type, $this->handlers)) {
            $this->handlers[$type] = [];
        }

        if (! array_key_exists($event_type, $this->handlers[$type])) {
            $classname = self::TYPE_HANDLERS[$type];
            $this->handlers[$type][$event_type] = new $classname($this->db);
        }

        return $this->handlers[$type][$event_type];
    }

    /**
     * @param string $type
     * @return IEventStorageHandler
     */
    private function getGenericHandler(string $type) : IEventStorageHandler
    {
        if (! array_key_exists($type, $this->handlers)) {
            $classname = self::GENERIC_HANDLERS[$type];
            $this->handlers[$type] = new $classname($this->db);
        }

        return $this->handlers[$type];
    }

    /**
     * {@inheritDoc}
     * @see \srag\CQRS\Event\IEventStore::getAggregateHistoryFor()
     */
    public function getAggregateHistoryFor(Uuid $id): DomainEvents
    {

    }

    /**
     * {@inheritDoc}
     * @see \srag\CQRS\Event\IEventStore::getEventStream()
     */
    public function getEventStream(?string $from_id = null): DomainEvents
    {

    }
}