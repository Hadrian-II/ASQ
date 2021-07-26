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
    private QuestionDto $question;

    private AbstractValueObject $answer;

    public function __construct(QuestionDto $question_dto, AbstractValueObject $answer)
    {
        $this->question = $question_dto;
        $this->answer = $answer;
    }

    public function getQuestion() : QuestionDto
    {
        return $this->question;
    }

    public function getAnswer() : AbstractValueObject
    {
        return $this->answer;
    }

    public function getCanonicalName() : string
    {
        return FeedbackComponent::class;
    }
}
