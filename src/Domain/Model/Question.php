<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use ILIAS\Data\UUID\Uuid;
use ilDateTime;
use srag\CQRS\Aggregate\AbstractAggregateRoot;
use srag\CQRS\Aggregate\IsRevisable;
use srag\CQRS\Aggregate\RevisionId;
use srag\CQRS\Event\DomainEvent;
use srag\CQRS\Event\Standard\AggregateCreatedEvent;
use srag\CQRS\Event\Standard\AggregateRevisionCreatedEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Event\QuestionDataSetEvent;
use srag\asq\Domain\Event\QuestionFeedbackSetEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Feedback\Feedback;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Question extends AbstractAggregateRoot implements IsRevisable
{
    const VAR_TYPE = 'question_type';

    private ?string $question_type;

    private ?RevisionId $revision_id;

    private ?int $creator_id;

    private ?QuestionData $data;

    private ?QuestionPlayConfiguration $play_configuration;
    /**
     * @var ?AnswerOption[]
     */
    private ?array $answer_options;

    private ?QuestionHints $hints;

    private ?Feedback $feedback;

    private ?bool $has_unrevised_changes;

    public static function createNewQuestion(
        Uuid $question_uuid,
        int $initiating_user_id,
        QuestionType $question_type
    ) : Question {
        $question = new Question();
        $question->ExecuteEvent(
            new AggregateCreatedEvent(
                $question_uuid,
                new ilDateTime(time(), IL_CAL_UNIX),
                $initiating_user_id,
                [self::VAR_TYPE => $question_type->getTitleKey()]
            )
        );

        return $question;
    }

    protected function applyEvent(DomainEvent $event) : void
    {
        $this->has_unrevised_changes = get_class($event) !== AggregateRevisionCreatedEvent::class;

        parent::applyEvent($event);
    }

    protected function applyAggregateCreatedEvent(DomainEvent $event) : void
    {
        parent::applyAggregateCreatedEvent($event);
        $this->creator_id = $event->getInitiatingUserId();
        $this->question_type = $event->getAdditionalData()[self::VAR_TYPE];
    }

    protected function applyQuestionDataSetEvent(QuestionDataSetEvent $event) : void
    {
        $this->data = $event->getData();
    }

    protected function applyQuestionPlayConfigurationSetEvent(QuestionPlayConfigurationSetEvent $event) : void
    {
        $this->play_configuration = $event->getPlayConfiguration();
    }

    protected function applyAggregateRevisionCreatedEvent(AggregateRevisionCreatedEvent $event) : void
    {
        $this->revision_id = $event->getRevisionId();
    }

    protected function applyQuestionAnswerOptionsSetEvent(QuestionAnswerOptionsSetEvent $event) : void
    {
        $this->answer_options = $event->getAnswerOptions();
    }

    protected function applyQuestionHintsSetEvent(QuestionHintsSetEvent $event) : void
    {
        $this->hints = $event->getHints();
    }

    protected function applyQuestionFeedbackSetEvent(QuestionFeedbackSetEvent $event) : void
    {
        $feedback = $event->getFeedback();
        $this->feedback = $feedback;
    }

    public function getType() : ?string
    {
        return $this->question_type;
    }

    public function getData() : ?QuestionData
    {
        return $this->data;
    }

    public function setData(?QuestionData $data, int $creator_id) : void
    {
        if (!QuestionData::isNullableEqual($data, $this->getData())) {
            $this->ExecuteEvent(new QuestionDataSetEvent($this->getAggregateId(), new ilDateTime(time(), IL_CAL_UNIX), $creator_id, $data));
        }
    }

    public function getPlayConfiguration() : ?QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }

    public function setPlayConfiguration(
        ?QuestionPlayConfiguration $play_configuration,
        int $creator_id
    ) : void {
        if (!QuestionPlayConfiguration::isNullableEqual($play_configuration, $this->getPlayConfiguration())) {
            $this->ExecuteEvent(new QuestionPlayConfigurationSetEvent(
                $this->getAggregateId(),
                new ilDateTime(time(), IL_CAL_UNIX),
                $creator_id,
                $play_configuration
            ));
        }
    }

    /**
     * @return ?AnswerOption[]
     */
    public function getAnswerOptions() : ?array
    {
        return $this->answer_options;
    }

    /**
     * @param AnswerOption[] $options
     * @param int $creator_id
     * @throws \ilDateTimeException
     */
    public function setAnswerOptions(?array $options, int $creator_id)
    {
        if (AbstractValueObject::isNullableArrayEqual($options, $this->answer_options)) {
            return;
        }

        $this->ExecuteEvent(new QuestionAnswerOptionsSetEvent(
            $this->getAggregateId(),
            new ilDateTime(time(), IL_CAL_UNIX),
            $creator_id,
            $options
        ));
    }

    public function getHints() : ?QuestionHints
    {
        return $this->hints;
    }

    public function setHints(?QuestionHints $hints, int $creator_id) : void
    {
        if (!QuestionHints::isNullableEqual($hints, $this->getHints())) {
            $this->ExecuteEvent(new QuestionHintsSetEvent(
                $this->getAggregateId(),
                new ilDateTime(time(), IL_CAL_UNIX),
                $creator_id,
                $hints
            ));
        }
    }

    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(
        ?Feedback $feedback,
        int $creator_id
    ) : void {
        if (!Feedback::isNullableEqual($feedback, $this->getFeedback())) {
            $this->ExecuteEvent(new QuestionFeedbackSetEvent(
                $this->getAggregateId(),
                new ilDateTime(time(), IL_CAL_UNIX),
                $creator_id,
                $feedback
            ));
        }
    }

    public function getCreatorId() : int
    {
        return $this->creator_id;
    }

    public function setCreatorId(int $creator_id) : void
    {
        $this->creator_id = $creator_id;
    }

    public function hasUnrevisedChanges() : bool
    {
        return $this->has_unrevised_changes;
    }

    public function getRevisionId() : ?RevisionId
    {
        return $this->revision_id;
    }

    public function setRevisionId(RevisionId $id, int $user_id)
    {
        $this->ExecuteEvent(new AggregateRevisionCreatedEvent(
            $this->getAggregateId(),
            new ilDateTime(time(), IL_CAL_UNIX),
            $user_id,
            $id
        ));
    }
}
