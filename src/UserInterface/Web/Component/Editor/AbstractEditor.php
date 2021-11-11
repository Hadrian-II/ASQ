<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Editor;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Definitions\IAsqQuestionEditor;

/**
 * Abstract Class AbstractEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class AbstractEditor implements IAsqQuestionEditor
{
    protected QuestionDto $question;

    protected ?AbstractValueObject $answer = null;

    protected bool $render_feedback = false;

    protected bool $is_disabled;

    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->question = $question;
        $this->is_disabled = $is_disabled;
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
