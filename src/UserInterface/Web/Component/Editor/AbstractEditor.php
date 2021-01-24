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
    /**
     * @var QuestionDto
     */
    protected $question;
    /**
     * @var ?AbstractValueObject
     */
    protected $answer;
    /**
     * @var bool
     */
    protected $render_feedback;

    /**
     * AbstractEditor constructor.
     *
     * @param QuestionDto   $question
     * @param array|null $configuration
     */
    public function __construct(QuestionDto $question)
    {
        $this->question = $question;
    }

    public function setRenderFeedback(bool $render_feedback)
    {
        $this->render_feedback = $render_feedback;
    }

    /**
     * @param AbstractValueObject $answer
     */
    public function setAnswer(?AbstractValueObject $answer) : void
    {
        $this->answer = $answer;
    }
}
