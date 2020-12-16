<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use ILIAS\Data\UUID\Factory;
use ILIAS\Data\UUID\Uuid;
use ilDBInterface;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\IEventStore;
use srag\CQRS\Event\Standard\AggregateCreatedEvent;
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
use srag\asq\Infrastructure\Setup\sql\SetupDatabase;
use srag\asq\Questions\Cloze\Storage\ClozeAnswerOptionsSetEventHandler;
use srag\asq\Questions\Cloze\Storage\ClozeConfigurationSetEventHandler;
use srag\asq\Questions\Choice\Storage\MultipleChoice\MultipleChoiceAnswerOptionsSetEventHandler;
use srag\asq\Questions\Choice\Storage\MultipleChoice\MultipleChoiceConfigurationSetEventHandler;
use srag\asq\Questions\Choice\Storage\ImageMap\ImageMapAnswerOptionsSetEventHandler;
use srag\asq\Questions\Choice\Storage\ImageMap\ImageMapConfigurationSetEventHandler;
use srag\asq\Questions\ErrorText\Storage\ErrorTextAnswerOptionsSetEventHandler;
use srag\asq\Questions\ErrorText\Storage\ErrorTextConfigurationSetEventHandler;
use srag\asq\Questions\Essay\Storage\EssayAnswerOptionsSetEventHandler;
use srag\asq\Questions\Essay\Storage\EssayConfigurationSetEventHandler;
use srag\asq\Questions\FileUpload\Storage\FileUploadConfigurationSetEventHandler;
use srag\asq\Questions\Formula\Storage\FormulaAnswerOptionsSetEventHandler;
use srag\asq\Questions\Formula\Storage\FormulaConfigurationSetEventHandler;

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
    const TABLE_NAME_QUESTION_HINT = 'rqes_question_hint';
    const TABLE_NAME_QUESTION_FEEDBACK = 'rqes_question_feedback';
    const TABLE_NAME_QUESTION_ANSWER_FEEDBACK = 'rqes_afeedback';

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
        AggregateCreatedEvent::class => AggregateCreatedEventHandler::class,
        QuestionDataSetEvent::class => QuestionDataSetEventHandler::class,
        QuestionFeedbackSetEvent::class => QuestionFeedbackSetEventHandler::class,
        QuestionHintsSetEvent::class => QuestionHintsSetEventHandler::class
    ];

    /**
     * @var array
     */
    const TYPE_HANDLERS = [
        SetupDatabase::CLOZE => [
            QuestionAnswerOptionsSetEvent::class => ClozeAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => ClozeConfigurationSetEventHandler::class
        ],
        SetupDatabase::SINGLE_CHOICE => [
            QuestionAnswerOptionsSetEvent::class => MultipleChoiceAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => MultipleChoiceConfigurationSetEventHandler::class
        ],
        SetupDatabase::MULTIPLE_CHOICE => [
            QuestionAnswerOptionsSetEvent::class => MultipleChoiceAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => MultipleChoiceConfigurationSetEventHandler::class
        ],
        SetupDatabase::IMAGE_MAP => [
            QuestionAnswerOptionsSetEvent::class => ImageMapAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => ImageMapConfigurationSetEventHandler::class
        ],
        SetupDatabase::ERROR_TEXT => [
            QuestionAnswerOptionsSetEvent::class => ErrorTextAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => ErrorTextConfigurationSetEventHandler::class
        ],
        SetupDatabase::ESSAY => [
            QuestionAnswerOptionsSetEvent::class => EssayAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => EssayConfigurationSetEventHandler::class
        ],
        SetupDatabase::FILE_UPLOAD => [
            QuestionPlayConfigurationSetEvent::class => FileUploadConfigurationSetEventHandler::class
        ],
        SetupDatabase::FORMULA => [
            QuestionAnswerOptionsSetEvent::class => FormulaAnswerOptionsSetEventHandler::class,
            QuestionPlayConfigurationSetEvent::class => FormulaConfigurationSetEventHandler::class
        ],
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
            $classname = self::TYPE_HANDLERS[$type][$event_type];
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
        $type = $this->getQuestionType($id);

        $res = $this->db->query(
            sprintf(
                'select * from ' . self::TABLE_NAME . '
                 where question_id = %s',
                $this->db->quote($id->toString(), 'string')
                )
            );

        $events = new DomainEvents();
        while ($row = $this->db->fetchAssoc($res))
        {
            $event_type = $row['event_name'];

            if ($event_type === AggregateCreatedEvent::class) {
                $row[Question::VAR_TYPE] = $type;
            }

            if ($this->isGenericEvent($event_type))
            {
                $generic_handler = $this->getGenericHandler($event_type);
                $events->addEvent($generic_handler->loadEvent($row));
            }
            else
            {
                $type_specific_handler = $this->getTypeSpecificHandler($type, $event_type);
                $events->addEvent($type_specific_handler->loadEvent($row));
            }
        }

        return $events;
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