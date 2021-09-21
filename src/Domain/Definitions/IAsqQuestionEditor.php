<?php
namespace srag\asq\Domain\Definitions;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Interface IAsqQuestionEditor
 *
 * Defines an asq Quuestion Editor element, that is part of the QuestionControl
 * and used to display an Editor (The Question UI where the user can input his answers).
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IAsqQuestionEditor
{
    /**
     * Flag used to indicate if the Question Feedback should be displayed
     *
     * @param bool $render_feedback
     */
    public function setRenderFeedback(bool $render_feedback);

    /**
     * Generate Html for the current Question with an optional given Answer
     *
     * @return string
     */
    public function generateHtml() : string;

    /**
     * Reads the given answer from POST
     * Returns null if no Answer given
     *
     * @return ?AbstractValueObject
     */
    public function readAnswer() : ?AbstractValueObject;

    /**
     * Sets an answer to display to the Control
     *
     * @param ?AbstractValueObject $answer
     */
    public function setAnswer(?AbstractValueObject $answer) : void;

    /**
     * Checks if the given Question is complete, as in able to
     * display a meaningful and complete Question
     *
     * @return bool
     */
    public function isComplete() : bool;

    /**
     * Here the editor can specify an optional editor JS file
     *
     * @return ?string
     */
    public function additionalJSFile() : ?string;
}
