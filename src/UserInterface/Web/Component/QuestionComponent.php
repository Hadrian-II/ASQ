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
    const SHOW_HEADER_WITH_POINTS = 1;
    const SHOW_HEADER = 2;
    const SHOW_NOTHING = 3;

    use PathHelper;

    private QuestionDto $question_dto;

    private ?AbstractValueObject $answer = null;

    private bool $show_feedback = false;

    private bool $is_disabled = false;

    private int $title_display = self::SHOW_HEADER;

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

    public function withDisabled(bool $is_disabled) : QuestionComponent
    {
        $clone = clone $this;
        $clone->is_disabled = $is_disabled;

        return $clone;
    }

    public function withTitleDisplay(int $title_display) : QuestionComponent
    {
        $modes = [self::SHOW_HEADER_WITH_POINTS, self::SHOW_HEADER, self::SHOW_NOTHING];

        if (!in_array($title_display, $modes)) {
            return $this;
        }

        $clone = clone $this;
        $clone->title_display = $title_display;

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

    public function isDisabled() : bool
    {
        return $this->is_disabled;
    }

    public function getTitleDisplay() : int
    {
        return $this->title_display;
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
