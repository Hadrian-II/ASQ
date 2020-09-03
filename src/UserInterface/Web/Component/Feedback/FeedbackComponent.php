<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ILIAS\UI\Component\Component;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;

/**
 * Class FeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class FeedbackComponent implements Component
{
    /**
     * @var QuestionDto
     */
    private $question;

    /**
     * @var AbstractValueObject
     */
    private $answer;

    /**
     * @param QuestionDto $question_dto
     * @param AbstractValueObject $answer
     */
    public function __construct(QuestionDto $question_dto, AbstractValueObject $answer)
    {
        $this->question = $question_dto;
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
        return FeedbackComponent::class;
    }
}
