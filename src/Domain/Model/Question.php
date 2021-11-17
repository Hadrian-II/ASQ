<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use ILIAS\Data\UUID\Uuid;
use DateTimeImmutable;
use srag\asq\Domain\Event\QuestionMetadataSetEvent;
use Fluxlabs\CQRS\Aggregate\AbstractAggregateRoot;
use Fluxlabs\CQRS\Aggregate\IsRevisable;
use Fluxlabs\CQRS\Aggregate\RevisionId;
use Fluxlabs\CQRS\Event\DomainEvent;
use Fluxlabs\CQRS\Event\Standard\AggregateCreatedEvent;
use Fluxlabs\CQRS\Event\Standard\AggregateRevisionCreatedEvent;
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
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class Question
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Question extends AbstractAggregateRoot implements IsRevisable
{
    const VAR_TYPE = 'question_type';

    private ?string $question_type = null;

    private ?RevisionId $revision_id = null;

    private ?int $creator_id = null;

    private ?QuestionData $data = null;

    private ?QuestionPlayConfiguration $play_configuration = null;
    /**
     * @var ?AnswerOption[]
     */
    private ?array $answer_options = null;

    private ?QuestionHints $hints = null;

    private ?Feedback $feedback = null;

    private ?bool $has_unrevised_changes = null;

    /**
     * @var ?AbstractValueObject
     */
    private ?array $metadata = [];

    public static function createNewQuestion(
        Uuid $question_uuid,
        QuestionType $question_type
    ) : Question {
        $question = new Question();
        $question->ExecuteEvent(
            new AggregateCreatedEvent(
                $question_uuid,
                new DateTimeImmutable(),
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

    public function setData(?QuestionData $data) : void
    {
        if (!QuestionData::isNullableEqual($data, $this->getData())) {
            $this->ExecuteEvent(new QuestionDataSetEvent($this->getAggregateId(), new DateTimeImmutable(), $data));
        }
    }

    public function getPlayConfiguration() : ?QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }

    public function setPlayConfiguration(?QuestionPlayConfiguration $play_configuration) : void {
        if (!QuestionPlayConfiguration::isNullableEqual($play_configuration, $this->getPlayConfiguration())) {
            $this->ExecuteEvent(new QuestionPlayConfigurationSetEvent(
                $this->getAggregateId(),
                new DateTimeImmutable(),
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
     */
    public function setAnswerOptions(?array $options)
    {
        if (AbstractValueObject::isNullableArrayEqual($options, $this->answer_options)) {
            return;
        }

        $this->ExecuteEvent(new QuestionAnswerOptionsSetEvent(
            $this->getAggregateId(),
            new DateTimeImmutable(),
            $options
        ));
    }

    public function getHints() : ?QuestionHints
    {
        return $this->hints;
    }

    public function setHints(?QuestionHints $hints) : void
    {
        if (!QuestionHints::isNullableEqual($hints, $this->getHints())) {
            $this->ExecuteEvent(new QuestionHintsSetEvent(
                $this->getAggregateId(),
                new DateTimeImmutable(),
                $hints
            ));
        }
    }

    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback) : void {
        if (!Feedback::isNullableEqual($feedback, $this->getFeedback())) {
            $this->ExecuteEvent(new QuestionFeedbackSetEvent(
                $this->getAggregateId(),
                new DateTimeImmutable(),
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

    public function setRevisionId(RevisionId $id) : void
    {
        $this->ExecuteEvent(new AggregateRevisionCreatedEvent(
            $this->getAggregateId(),
            new DateTimeImmutable(),
            $id
        ));
    }

    public function setMetadata(array $metadata) : void
    {
        foreach ($metadata as $meta_for => $meta) {
            if (!array_key_exists($meta_for, $this->metadata) ||
                !AbstractValueObject::isNullableEqual($meta, $this->metadata[$meta_for])) {
                $this->ExecuteEvent(new QuestionMetadataSetEvent(
                    $this->getAggregateId(),
                    new DateTimeImmutable(),
                    $meta,
                    $meta_for
                ));
            }
        }
    }

    protected function applyQuestionMetadataSetEvent(QuestionMetadataSetEvent $event) : void
    {
        if ($event->getMeta() === null) {
            unset($this->metadata[$event->getMetaFor()]);
        }
        else {
            $this->metadata[$event->getMetaFor()] = $event->getMeta();
        }
    }

    public function getMetadata() : ?array
    {
        return $this->metadata;
    }
}
