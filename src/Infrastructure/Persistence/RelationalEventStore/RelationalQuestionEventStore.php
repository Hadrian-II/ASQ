<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use ILIAS\Data\UUID\Factory;
use ILIAS\Data\UUID\Uuid;
use ilDBInterface;
use ilDateTime;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\IEventStore;
use srag\CQRS\Event\Standard\AggregateCreatedEvent;
use srag\asq\Application\Service\AsqServices;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Event\QuestionDataSetEvent;
use srag\asq\Domain\Event\QuestionFeedbackSetEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers\AggregateCreatedEventHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers\QuestionDataSetEventHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers\QuestionFeedbackSetEventHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers\QuestionHintsSetEventHandler;

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
    const TABLE_NAME_QUESTION_CREATED = 'rqes_question_created';
    const TABLE_NAME_QUESTION_DATA = 'rqes_question_data';
    const TABLE_NAME_QUESTION_HINT = 'rqes_question_hint';
    const TABLE_NAME_QUESTION_FEEDBACK = 'rqes_question_feedback';
    const TABLE_NAME_QUESTION_ANSWER_FEEDBACK = 'rqes_afeedback';

    /**
     * @var ilDBInterface
     */
    private $db;

    /**
     * @var AsqServices
     */
    private $asq;

    /**
     * @var Factory
     */
    private $uuid_factory;

    /**
     * @var array
     */
    const GENERIC_HANDLERS = [
        AggregateCreatedEvent::class => AggregateCreatedEventHandler::class,
        QuestionDataSetEvent::class => QuestionDataSetEventHandler::class,
        QuestionFeedbackSetEvent::class => QuestionFeedbackSetEventHandler::class,
        QuestionHintsSetEvent::class => QuestionHintsSetEventHandler::class
    ];

    /**
     * @var array
     */
    private $handlers = [];

    /**
     * @var array
     */
    private $type_handlers;

    /**
     * @param ilDBInterface $db
     */
    public function __construct(ilDBInterface $db, AsqServices $asq)
    {
        $this->db = $db;
        $this->asq = $asq;
        $this->uuid_factory = new Factory();
    }

    /**
     * {@inheritDoc}
     * @see \srag\CQRS\Event\IEventStore::commit()
     */
    public function commit(DomainEvents $events): void
    {
        /** @var $event \srag\CQRS\Event\AbstractDomainEvent */
        foreach ($events->getEvents() as $event)
        {
            if ($event instanceof AggregateCreatedEvent) {
                $type = $event->getAdditionalData()[Question::VAR_TYPE];
                $this->storeQuestionType($event->getAggregateId(), $event->getAdditionalData()[Question::VAR_TYPE]);
            }
            else {
                $type = $this->getQuestionType($event->getAggregateId());
            }

            $event_id = $this->uuid_factory->uuid4();

            $id = $this->db->nextId(self::TABLE_NAME);
            $this->db->insert(self::TABLE_NAME, [
                'id' => ['integer', $id],
                'event_id' => ['text', $event_id->toString()],
                'event_version' => ['integer', $event->getEventVersion()],
                'question_id' => ['text', $event->getAggregateId()->toString()],
                'event_name' => ['text', $event->getEventName()],
                'occurred_on' => ['integer', time()],
                'initiating_user_id' => ['integer', $event->getInitiatingUserId()]
            ]);

            if ($this->isGenericEvent($event->getEventName()))
            {
                $generic_handler = $this->getGenericHandler($event->getEventName());
                $generic_handler->handleEvent($event, intval($id));
            }
            else
            {
                $type_specific_handler = $this->getTypeSpecificHandler($type, $event->getEventName());
                $type_specific_handler->handleEvent($event, intval($id));
            }
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

        return $this->db->fetchAssoc($res)['question_type'];
    }

    /**
     * @param Uuid $id
     * @param string $type
     */
    private function storeQuestionType(Uuid $id, string $type) : void
    {
        $this->db->insert(self::TABLE_NAME_QUESTION_INDEX, [
            'question_id' => ['text', $id->toString()],
            'question_type' => ['text', $type]
        ]);
    }

    private function isGenericEvent(string $event) : bool
    {
        return ($event === AggregateCreatedEvent::class ||
                $event === QuestionDataSetEvent::class ||
                $event === QuestionFeedbackSetEvent::class ||
                $event === QuestionHintsSetEvent::class);
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
            $classname = $this->getTypeHandlers()[$type][$event_type];
            $this->handlers[$type][$event_type] = new $classname($this->db);
        }

        return $this->handlers[$type][$event_type];
    }

    /**
     * @return array
     */
    private function getTypeHandlers() : array
    {
        if ($this->type_handlers === null) {
            foreach ($this->asq->question()->getAvailableQuestionTypes() as $type) {
                $storage_class =  $type->getStorageClass();
                $storage = new $storage_class();

                $this->type_handlers[$type->getTitleKey()] = [
                    QuestionAnswerOptionsSetEvent::class => $storage->getAnswerOptionHandler(),
                    QuestionPlayConfigurationSetEvent::class => $storage->getPlayConfigurationHandler()
                ];
            }
        }


        return $this->type_handlers;
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
        $type = $this->getQuestionType($id);

        $res = $this->db->query(
            sprintf(
                'select * from ' . self::TABLE_NAME . '
                 where question_id = %s',
                $this->db->quote($id->toString(), 'string')
                )
            );

        $events = [];

        $data = $this->db->fetchAll($res);

        $event_names = array_unique(
            array_map(
                function($row) {
                    return $row['event_name'];
                },
                $data
            )
        );

        foreach ($event_names as $event_name)
        {
            $event_data = array_filter($data, function($row) use($event_name) {
                return $row['event_name'] === $event_name;
            });

            if ($this->isGenericEvent($event_name))
            {
                $generic_handler = $this->getGenericHandler($event_name);

                foreach ($generic_handler->loadEvents($event_data) as $event) {
                    $events[] = $event;
                }

            }
            else
            {
                $type_specific_handler = $this->getTypeSpecificHandler($type, $event_name);

                foreach ($type_specific_handler->loadEvents($event_data) as $event) {
                    $events[] = $event;
                }
            }
        }

        usort(
            $events,
            function($a, $b) {
                $atime = $a->getOccurredOn();
                $btime = $b->getOccurredOn();

                if (ilDateTime::_equals($atime, $btime)) {
                    return 0;
                }

                return ilDateTime::_before($atime, $btime) ? -1 : 1;
            }
        );

        $domain_events = new DomainEvents();
        foreach ($events as $event) {
            $domain_events->addEvent($event);
        }
        return $domain_events;
    }

    /**
     * {@inheritDoc}
     * @see \srag\CQRS\Event\IEventStore::aggregateExists()
     */
    public function aggregateExists(Uuid $id): bool
    {
        global $DIC;

        $sql = sprintf(
            'SELECT Count(*) as count FROM %s where aggregate_id = %s',
            self::TABLE_NAME,
            $DIC->database()->quote($id->toString(), 'string')
            );

        $res = $DIC->database()->query($sql);

        return $DIC->database()->fetchAssoc($res)['count'] > 0;
    }

    /**
     * {@inheritDoc}
     * @see \srag\CQRS\Event\IEventStore::getEventStream()
     */
    public function getEventStream(?string $from_id = null): DomainEvents
    {

    }
}