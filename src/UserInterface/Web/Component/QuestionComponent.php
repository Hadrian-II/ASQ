<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component;

use ILIAS\UI\Component\Component;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class QuestionComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionComponent implements Component
{
    use PathHelper;

    /**
     * @var QuestionDto
     */
    private $question_dto;

    /**
     * @var AbstractValueObject
     */
    private $answer;

    /**
     * @var bool
     */
    private $show_feedback = false;

    /**
     * @param QuestionDto $question_dto
     */
    public function __construct(QuestionDto $question_dto)
    {
        $this->question_dto = $question_dto;
    }

    /**
     * @param AbstractValueObject $answer
     * @return QuestionComponent
     */
    public function withAnswer(AbstractValueObject $answer) : QuestionComponent
    {
        $clone = clone $this;
        $clone->answer = $answer;

        return $clone;
    }

    /**
     * @param bool $show_feedback
     * @return QuestionComponent
     */
    public function withShowFeedback(bool $show_feedback) : QuestionComponent
    {
        $clone = clone $this;
        $clone->show_feedback = $show_feedback;

        return $clone;
    }

    /**
     * @return QuestionDto
     */
    public function getQuestion()
    {
        return $this->question_dto;
    }

    /**
     * @return AbstractValueObject
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @return boolean
     */
    public function doesShowFeedback()
    {
        return $this->show_feedback;
    }

    /**
     * @return QuestionComponent
     */
    public function withAnswerFromPost() : QuestionComponent
    {
        $editor_class = $this->question_dto->getType()->getEditorClass();
        $editor = new $editor_class($this->question_dto);

        $clone = clone $this;
        $clone->answer = $editor->readAnswer();

        return $clone;
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Component\Component::getCanonicalName()
     */
    public function getCanonicalName()
    {
        return QuestionComponent::class;
    }
}
