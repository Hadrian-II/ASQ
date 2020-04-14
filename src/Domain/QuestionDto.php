<?php
declare(strict_types = 1);
namespace srag\asq\Domain;

use JsonSerializable;
use srag\CQRS\Aggregate\RevisionId;
use srag\asq\Domain\Model\Feedback;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Domain\Model\QuestionTypeDefinition;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

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
     * @var string
     */
    private $id;

    /**
     *
     * @var QuestionTypeDefinition
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
     * @var ?AnswerOptions
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
    public static function CreateFromQuestion(Question $question): QuestionDto
    {
        $dto = new QuestionDto();

        $dto->id = $question->getAggregateId()->getId();
        $dto->type = $question->getType();
        $dto->complete = $question->isQuestionComplete();
        
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType(): QuestionTypeDefinition
    {
        return $this->type;
    }
    
    /**
     *
     * @param bool $complete
     */
    public function setComplete(bool $complete)
    {
        $this->complete = $complete;
    }

    /**
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     *
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getRevisionId(): ?RevisionId
    {
        return $this->revision_id;
    }

    /**
     *
     * @return QuestionData
     */
    public function getData(): ?QuestionData
    {
        return $this->data;
    }

    /**
     *
     * @param QuestionData $data
     */
    public function setData(?QuestionData $data): void
    {
        $this->data = $data;
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
     *
     * @param QuestionPlayConfiguration $play_configuration
     */
    public function setPlayConfiguration(?QuestionPlayConfiguration $play_configuration): void
    {
        $this->play_configuration = $play_configuration;
    }

    public function hasAnswerOptions() : bool {
        return !is_null($this->answer_options) && count($this->answer_options->getOptions()) > 0;
    }
    
    /**
     *
     * @return AnswerOptions
     */
    public function getAnswerOptions(): ?AnswerOptions
    {
        return $this->answer_options;
    }

    /**
     *
     * @param AnswerOptions $answer_options
     */
    public function setAnswerOptions(?AnswerOptions $answer_options): void
    {
        $this->answer_options = $answer_options;
    }

    /**
     * @return bool
     */
    public function hasFeedback() : bool {
        return !is_null($this->feedback);
    }
    
    /**
     *
     * @param Feedback $feedback
     */
    public function setFeedback(?Feedback $feedback): void
    {
        $this->feedback = $feedback;
    }

    /**
     *
     * @return Feedback
     */
    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
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
    public function getQuestionHints(): ?QuestionHints
    {
        return $this->question_hints;
    }

    /**
     *
     * @param QuestionHints $question_hints
     */
    public function setQuestionHints(?QuestionHints $question_hints): void
    {
        $this->question_hints = $question_hints;
    }
    
    /**
     * {@inheritDoc}
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
    
    /**
     * @param string $json_data
     * @return QuestionDto
     */
    public static function deserialize(string $json_data) 
    {
        $data = json_decode($json_data, true);
        
        $object = new QuestionDto();
        $object->id = $data['id'];
        $object->type = QuestionTypeDefinition::createFromArray($data['type']);
        $object->answer_options = AnswerOptions::createFromArray($data['answer_options']);
        $object->data = QuestionData::createFromArray($data['data']);
        $object->feedback = Feedback::createFromArray($data['feedback']);
        $object->play_configuration = QuestionPlayConfiguration::createFromArray($data['play_configuration']);
        $object->question_hints = QuestionHints::createFromArray($data['question_hints']);
        $object->revision_id = RevisionId::createFromArray($data['revision_id']);
        
        return $object;
    }
}