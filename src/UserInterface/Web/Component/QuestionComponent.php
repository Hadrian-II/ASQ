<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component;

use ILIAS\UI\Component\Component;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class QuestionComponent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionComponent implements Component
{
    use PathHelper;

    private QuestionDto $question_dto;

    private ?AbstractValueObject $answer = null;

    private bool $show_feedback = false;

    public function __construct(QuestionDto $question_dto)
    {
        $this->question_dto = $question_dto;
    }

    public function withAnswer(AbstractValueObject $answer) : QuestionComponent
    {
        $clone = clone $this;
        $clone->answer = $answer;

        return $clone;
    }

    public function withShowFeedback(bool $show_feedback) : QuestionComponent
    {
        $clone = clone $this;
        $clone->show_feedback = $show_feedback;

        return $clone;
    }

    public function getQuestion() : QuestionDto
    {
        return $this->question_dto;
    }

    public function getAnswer() : ?AbstractValueObject
    {
        return $this->answer;
    }

    public function doesShowFeedback() : bool
    {
        return $this->show_feedback;
    }

    public function withAnswerFromPost() : QuestionComponent
    {
        $editor_class = $this->question_dto->getType()->getEditorClass();
        $editor = new $editor_class($this->question_dto);

        $clone = clone $this;
        $clone->answer = $editor->readAnswer();

        return $clone;
    }

    public function getCanonicalName() : string
    {
        return QuestionComponent::class;
    }
}
