<?php
declare(strict_types = 1);

namespace srag\asq\Domain;

use JsonSerializable;
use srag\CQRS\Aggregate\RevisionId;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Feedback\Feedback;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Infrastructure\Persistence\QuestionType;
use ILIAS\Data\UUID\Uuid;
use srag\CQRS\Aggregate\AbstractValueObject;
use ILIAS\Data\UUID\Factory;

/**
 * Class QuestionDto
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDto implements JsonSerializable
{
    const IL_COMPONENT_ID = 'asq';

    private Uuid $id;

    private QuestionType $type;

    private ?RevisionId $revision_id;

    private ?QuestionData $data;

    private ?QuestionPlayConfiguration $play_configuration;

    /**
     *
     * @var ?AnswerOption[]
     */
    private ?array $answer_options;

    private ?Feedback $feedback;

    private ?QuestionHints $question_hints;

    private bool $has_unrevisioned_changes;

    public static function CreateFromQuestion(Question $question, QuestionType $type) : QuestionDto
    {
        $dto = new QuestionDto();

        $dto->id = $question->getAggregateId();
        $dto->type = $type;

        $dto->revision_id = $question->getRevisionId();
        $dto->data = $question->getData();
        $dto->play_configuration = $question->getPlayConfiguration();
        $dto->answer_options = $question->getAnswerOptions();

        $dto->feedback = $question->getFeedback();
        $dto->question_hints = $question->getHints();
        $dto->has_unrevisioned_changes = $question->hasUnrevisedChanges();

        return $dto;
    }

    public function getId() : Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id) : void
    {
        $this->id = $id;
    }

    public function getType() : QuestionType
    {
        return $this->type;
    }

    public function setType(QuestionType $type) : void
    {
        $this->type = $type;
    }

    public function isComplete() : bool
    {
        if (is_null($this->data) ||
            is_null($this->play_configuration) ||
            is_null($this->play_configuration->getEditorConfiguration()) ||
            is_null($this->play_configuration->getScoringConfiguration())) {
            return false;
        }

        $editor_class = $this->getType()->getEditorClass();
        $editor = new $editor_class($this);

        $scoring_class = $this->getType()->getScoringClass();
        $scoring = new $scoring_class($this);

        return $this->data->isComplete() &&
               $editor->isComplete() &&
               $scoring->isComplete();
    }

    public function getRevisionId() : ?RevisionId
    {
        return $this->revision_id;
    }

    public function getData() : ?QuestionData
    {
        return $this->data;
    }

    public function setData(?QuestionData $data) : void
    {
        $this->data = $data;
    }

    public function getPlayConfiguration() : ?QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }

    public function setPlayConfiguration(?QuestionPlayConfiguration $play_configuration) : void
    {
        $this->play_configuration = $play_configuration;
    }

    public function hasAnswerOptions() : bool
    {
        return !is_null($this->answer_options) && count($this->answer_options) > 0;
    }

    public function getAnswerOptions() : ?array
    {
        return $this->answer_options;
    }

    public function setAnswerOptions(?array $answer_options) : void
    {
        $this->answer_options = $answer_options;
    }

    public function hasFeedback() : bool
    {
        return !is_null($this->feedback);
    }

    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback) : void
    {
        $this->feedback = $feedback;
    }

    public function hasHints() : bool
    {
        return !is_null($this->question_hints) && count($this->question_hints->getHints()) > 0;
    }

    public function getQuestionHints() : ?QuestionHints
    {
        return $this->question_hints;
    }

    public function setQuestionHints(?QuestionHints $question_hints) : void
    {
        $this->question_hints = $question_hints;
    }

    public function hasUnrevisedChanges() : bool
    {
        return $this->has_unrevisioned_changes;
    }

    public function jsonSerialize() : array
    {
        $vars = get_object_vars($this);
        $vars['type'] = $this->getType()->serialize();
        $vars['id'] = $this->getId()->toString();
        return $vars;
    }

    public static function deserialize(string $json_data) : QuestionDto
    {
        $data = json_decode($json_data, true);
        $factory = new Factory();

        $object = new QuestionDto();
        $object->id = $factory->fromString($data['id']);
        $object->type = QuestionType::deserialize($data['type']);
        $object->answer_options = AbstractValueObject::createFromArray($data['answer_options']);
        $object->data = QuestionData::createFromArray($data['data']);
        $object->feedback = Feedback::createFromArray($data['feedback']);
        $object->play_configuration = QuestionPlayConfiguration::createFromArray($data['play_configuration']);
        $object->question_hints = QuestionHints::createFromArray($data['question_hints']);
        $object->revision_id = RevisionId::createFromArray($data['revision_id']);

        return $object;
    }
}
