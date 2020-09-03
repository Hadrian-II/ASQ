<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ILIAS\UI\Component\Component;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;

/**
 * Class AnswerFeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AnswerFeedbackComponent implements Component
{
    use PathHelper;

    /**
     * @var QuestionDto
     */
    private $question;
    /**
     * @var AbstractValueObject
     */
    private $answer;

    /**
     * @param QuestionDto $question
     * @param AbstractValueObject $answer
     */
    public function __construct(QuestionDto $question, AbstractValueObject $answer)
    {
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * @return QuestionDto
     */
    public function getQuestion() : QuestionDto
    {
        return $this->question;
    }

    /**
     * @return AbstractValueObject
     */
    public function getAnswer() : AbstractValueObject
    {
        return $this->answer;
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Component\Component::getCanonicalName()
     */
    public function getCanonicalName()
    {
        return AnswerFeedbackComponent::class;
    }
}
