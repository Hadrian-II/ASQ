<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use srag\CQRS\Aggregate\AbstractEventSourcedAggregateRoot;
use srag\CQRS\Aggregate\AggregateRoot;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Aggregate\IsRevisable;
use srag\CQRS\Aggregate\RevisionId;
use srag\CQRS\Event\DomainEvents;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Event\QuestionCreatedEvent;
use srag\asq\Domain\Event\QuestionDataSetEvent;
use srag\asq\Domain\Event\QuestionFeedbackSetEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Event\QuestionLegacyDataSetEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Event\QuestionRevisionCreatedEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Hint\QuestionHints;

/**
 * Class Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Question extends AbstractEventSourcedAggregateRoot implements IsRevisable
{
    const SYSTEM_USER_ID = 3;
    /**
     * @var RevisionId
     */
    private $revision_id;
    /**
     * @var string
     */
    private $revision_name;
    /**
     * @var int
     */
    private $creator_id;
    /**
     * @var int
     */
    private $container_obj_id;
    /**
     * @var int
     */
    private $question_int_id;
    /**
     * @var QuestionData
     */
    private $data;
    /**
     * @var QuestionPlayConfiguration
     */
    private $play_configuration;
    /**
     * @var AnswerOptions
     */
    private $answer_options;
    /**
     * @var QuestionHints
     */
    private $hints;
    /**
     * @var QuestionLegacyData
     */
    private $legacy_data;
    /**
     * @var ContentEditingMode
     */
    private $contentEditingMode;
    /**
     * @var Feedback
     */
    protected $feedback;


    /**
     * Question constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->answers = [];
        $this->answer_options = new AnswerOptions();
        $this->hints = new QuestionHints([]);

        /**
         * TODO: I guess this is not the right place.
         * It just helps to develop for the moment.
         */
        $this->contentEditingMode = new ContentEditingMode(
            ContentEditingMode::PAGE_OBJECT
        );
    }


    /**
     * @param DomainObjectId $question_uuid
     * @param int            $initiating_user_id
     *
     * @return Question
     */
    public static function createNewQuestion(
        DomainObjectId $question_uuid,
        int $container_obj_id,
        int $initiating_user_id,
        int $question_int_id
    ) : Question {
        $question = new Question();
        $question->ExecuteEvent(
            new QuestionCreatedEvent(
                $question_uuid,
                $container_obj_id,
                $initiating_user_id,
                $question_int_id
            ));

        return $question;
    }


    /**
     * @param QuestionCreatedEvent $event
     */
    protected function applyQuestionCreatedEvent(QuestionCreatedEvent $event)
    {
        $this->aggregate_id = $event->getAggregateId();
        $this->creator_id = $event->getInitiatingUserId();
        $this->container_obj_id = $event->getContainerId();
        $this->question_int_id = $event->getItemId();
    }


    /**
     * @param QuestionDataSetEvent $event
     */
    protected function applyQuestionDataSetEvent(QuestionDataSetEvent $event)
    {
        $this->data = $event->getData();
    }


    /**
     * @param QuestionPlayConfigurationSetEvent $event
     */
    protected function applyQuestionPlayConfigurationSetEvent(QuestionPlayConfigurationSetEvent $event)
    {
        $this->play_configuration = $event->getPlayConfiguration();
    }


    /**
     * @param QuestionRevisionCreatedEvent $event
     */
    protected function applyQuestionRevisionCreatedEvent(QuestionRevisionCreatedEvent $event)
    {
        $this->revision_id = new RevisionId($event->getRevisionKey());
    }


    /**
     * @param QuestionAnswerOptionsSetEvent $event
     */
    protected function applyQuestionAnswerOptionsSetEvent(QuestionAnswerOptionsSetEvent $event)
    {
        $this->answer_options = $event->getAnswerOptions();
    }


    /**
     * @param QuestionHintsSetEvent $event
     */
    protected function applyQuestionHintsSetEvent(QuestionHintsSetEvent $event)
    {
        $this->hints = $event->getHints();
    }

    /**
     * @param QuestionFeedbackSetEvent $event
     */
    protected function applyQuestionFeedbackSetEvent(QuestionFeedbackSetEvent $event)
    {
        $feedback = $event->getFeedback();
        $this->feedback = $feedback;
    }


    /**
     * @param QuestionLegacyDataSetEvent $event
     */
    protected function applyQuestionLegacyDataSetEvent(QuestionLegacyDataSetEvent $event)
    {
        $this->legacy_data = $event->getLegacyData();
    }


    /**
     * @return QuestionData
     */
    public function getData() : ?QuestionData
    {
        return $this->data;
    }


    /**
     * @param QuestionData $data
     * @param int          $container_obj_id
     * @param int          $creator_id
     */
    public function setData(QuestionData $data, int $creator_id = self::SYSTEM_USER_ID): void
    {
        $this->ExecuteEvent(new QuestionDataSetEvent($this->getAggregateId(), $this->getContainerObjId(), $creator_id, $this->getQuestionIntId(), $data));
    }

    /**
     *
     * @return QuestionPlayConfiguration
     */
    public function getPlayConfiguration(): ?QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }


    /**
     * @param QuestionPlayConfiguration $play_configuration
     * @param int                       $creator_id
     */
    public function setPlayConfiguration(
        QuestionPlayConfiguration $play_configuration,
        int $creator_id = self::SYSTEM_USER_ID
    ) : void {
        $this->ExecuteEvent(new QuestionPlayConfigurationSetEvent(
            $this->getAggregateId(),
            $this->getContainerObjId(),
            $creator_id,
            $this->getQuestionIntId(),
            $play_configuration));
    }


    /**
     * @return QuestionLegacyData
     */
    public function getLegacyData() : ?QuestionLegacyData
    {
        return $this->legacy_data;
    }


    /**
     * @param QuestionLegacyData $legacy_data
     * @param int                $creator_id
     */
    public function setLegacyData(
        QuestionLegacyData $legacy_data,
        int $creator_id = self::SYSTEM_USER_ID
    ) : void {
        $this->ExecuteEvent(new QuestionLegacyDataSetEvent(
            $this->getAggregateId(),
            $this->getContainerObjId(),
            $creator_id,
            $this->getQuestionIntId(),
            $legacy_data));
    }


    /**
     * @return AnswerOptions
     */
    public function getAnswerOptions() : AnswerOptions
    {
        return $this->answer_options;
    }

    /**
     *
     * @param AnswerOptions $options
     * @param int $creator_id
     */
    public function setAnswerOptions(AnswerOptions $options, int $creator_id = self::SYSTEM_USER_ID)
    {
        $this->ExecuteEvent(new QuestionAnswerOptionsSetEvent($this->getAggregateId(), $this->getContainerObjId(), $creator_id, $this->getQuestionIntId(), $options));
    }


    /**
     * @return QuestionHints
     */
    public function getHints() : ?QuestionHints
    {
        return $this->hints;
    }


    /**
     * @param QuestionHints $hints
     * @param int           $creator_id
     */
    public function setHints(QuestionHints $hints, int $creator_id = self::SYSTEM_USER_ID)
    {
        $this->ExecuteEvent(new QuestionHintsSetEvent(
            $this->getAggregateId(),
            $this->getContainerObjId(),
            $creator_id,
            $this->getQuestionIntId(),
            $hints));
    }

    /**
     * @return Feedback
     */
    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }


    /**
     * @param Feedback $feedback
     * @param int $creator_id
     */
    public function setFeedback(
        Feedback $feedback,
        int $creator_id = self::SYSTEM_USER_ID
    ) : void {
        $this->ExecuteEvent(new QuestionFeedbackSetEvent(
            $this->getAggregateId(),
            $this->getContainerObjId(),
            $creator_id,
            $this->getQuestionIntId(),
            $feedback));
    }


    /**
     * @return ContentEditingMode
     */
    public function getContentEditingMode() : ContentEditingMode
    {
        return $this->contentEditingMode;
    }


    /**
     * @param ContentEditingMode $contentEditingMode
     */
    public function setContentEditingMode(ContentEditingMode $contentEditingMode) : void
    {
        $this->contentEditingMode = $contentEditingMode;
    }


    /**
     * @return int
     */
    public function getCreatorId() : int
    {
        return $this->creator_id;
    }


    /**
     * @return int
     */
    public function getContainerObjId() : int
    {
        return $this->container_obj_id;
    }


    /**
     * @return int
     */
    public function getQuestionIntId() : int
    {
        return $this->question_int_id;
    }


    /**
     * @param int $creator_id
     */
    public function setCreatorId(int $creator_id) : void
    {
        $this->creator_id = $creator_id;
    }


    /**
     * @return RevisionId revision id of object
     */
    public function getRevisionId() : ?RevisionId
    {
        return $this->revision_id;
    }


    /**
     * @param RevisionId $id
     *
     * @return mixed|void
     */
    public function setRevisionId(RevisionId $id)
    {
        $this->ExecuteEvent(new QuestionRevisionCreatedEvent(
            $this->getAggregateId(),
            $this->getContainerObjId(),
            $this->getCreatorId(),
            $this->getQuestionIntId(),
            $id->GetKey()));
    }


    /**
     * @return string
     *
     * Name of the revision used by the RevisionFactory when generating a revision
     * Using of Creation Date and or an increasing Number are encouraged
     *
     */
    public function getRevisionName() : ?string
    {
        return time();
    }


    /**
     * @return array
     *
     * Data used for signing the revision, so this method needs to to collect all
     * Domain specific data of an object and return it as an array
     */
    public function getRevisionData() : array
    {
        $data = [];
        $data[] = $this->getAggregateId()->getId();
        $data[] = $this->getData();
        $data[] = $this->getAnswerOptions();

        return $data;
    }


    /**
     * @param DomainEvents $event_history
     * @return AggregateRoot
     */
    public static function reconstitute(DomainEvents $event_history) : AggregateRoot
    {
        $question = new Question();
        foreach ($event_history->getEvents() as $event) {
            $question->applyEvent($event);
        }

        return $question;
    }
    
    public function isQuestionComplete() : bool {
        //TODO as soon as presenter gets meat, check for presence of presenter
        if (is_null($this->data) ||
            is_null($this->play_configuration) ||
            is_null($this->play_configuration->getEditorConfiguration()) ||
            is_null($this->play_configuration->getScoringConfiguration())) 
        {
            return false;        
        }

        return $this->data->isComplete() &&
               QuestionPlayConfiguration::isComplete($this);
    }
}