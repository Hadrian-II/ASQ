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

    /**
     *
     * @var Uuid
     */
    private $id;

    /**
     *
     * @var QuestionType
     */
    private $type;

    /**
     *
     * @var ?RevisionId
     */
    private $revision_id;

    /**
     *
     * @var ?QuestionData
     */
    private $data;

    /**
     *
     * @var ?QuestionPlayConfiguration
     */
    private $play_configuration;

    /**
     *
     * @var ?AnswerOption[]
     */
    private $answer_options;

    /**
     *
     * @var ?Feedback
     */
    private $feedback;

    /**
     *
     * @var ?QuestionHints
     */
    private $question_hints;

    /**
     *
     * @param Question $question
     *
     * @return QuestionDto
     */
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

        return $dto;
    }

    /**
     *
     * @return Uuid
     */
    public function getId() : Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid $id
     */
    public function setId(Uuid $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getType() : QuestionType
    {
        return $this->type;
    }

    /**
     * @param QuestionType $type
     */
    public function setType(QuestionType $type) : void
    {
        $this->type = $type;
    }

    /**
     *
     * @param bool $complete
     */
    public function setComplete(bool $complete) : void
    {
        $this->complete = $complete;
    }

    /**
     *
     * @return bool
     */
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

    /**
     *
     * @return string
     */
    public function getRevisionId() : ?RevisionId
    {
        return $this->revision_id;
    }

    /**
     *
     * @return QuestionData
     */
    public function getData() : ?QuestionData
    {
        return $this->data;
    }

    /**
     *
     * @param QuestionData $data
     */
    public function setData(?QuestionData $data) : void
    {
        $this->data = $data;
    }

    /**
     *
     * @return QuestionPlayConfiguration
     */
    public function getPlayConfiguration() : ?QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }

    /**
     *
     * @param QuestionPlayConfiguration $play_configuration
     */
    public function setPlayConfiguration(?QuestionPlayConfiguration $play_configuration) : void
    {
        $this->play_configuration = $play_configuration;
    }

    public function hasAnswerOptions() : bool
    {
        return !is_null($this->answer_options) && count($this->answer_options) > 0;
    }

    /**
     *
     * @return AnswerOption[]
     */
    public function getAnswerOptions() : ?array
    {
        return $this->answer_options;
    }

    /**
     *
     * @param ?AnswerOption[] $answer_options
     */
    public function setAnswerOptions(?array $answer_options) : void
    {
        $this->answer_options = $answer_options;
    }

    /**
     * @return bool
     */
    public function hasFeedback() : bool
    {
        return !is_null($this->feedback);
    }

    /**
     *
     * @return Feedback
     */
    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }

    /**
     *
     * @param Feedback $feedback
     */
    public function setFeedback(?Feedback $feedback) : void
    {
        $this->feedback = $feedback;
    }

    /**
     * @return bool
     */
    public function hasHints() : bool
    {
        return !is_null($this->question_hints) && count($this->question_hints->getHints()) > 0;
    }

    /**
     *
     * @return QuestionHints
     */
    public function getQuestionHints() : ?QuestionHints
    {
        return $this->question_hints;
    }

    /**
     *
     * @param QuestionHints $question_hints
     */
    public function setQuestionHints(?QuestionHints $question_hints) : void
    {
        $this->question_hints = $question_hints;
    }

    /**
     * {@inheritDoc}
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        $vars['type'] = $this->getType()->serialize();
        $vars['id'] = $this->getId()->toString();
        return $vars;
    }

    /**
     * @param string $json_data
     * @return QuestionDto
     */
    public static function deserialize(string $json_data) : QuestionDto
    {
        $data = json_decode($json_data, true);
        $facory = new Factory();

        $object = new QuestionDto();
        $object->id = $facory->fromString($data['id']);
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
