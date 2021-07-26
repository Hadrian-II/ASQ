<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Definitions\IAsqQuestionEditor;

/**
 * Abstract Class AbstractEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractEditor implements IAsqQuestionEditor
{
    protected QuestionDto $question;

    protected ?AbstractValueObject $answer;

    protected bool $render_feedback;


    public function __construct(QuestionDto $question)
    {
        $this->question = $question;
    }

    public function setRenderFeedback(bool $render_feedback) : void
    {
        $this->render_feedback = $render_feedback;
    }

    public function setAnswer(?AbstractValueObject $answer) : void
    {
        $this->answer = $answer;
    }

    public function additionalJSFile() : ?string
    {
        return null;
    }
}
