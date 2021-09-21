<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ILIAS\UI\Component\Component;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;

/**
 * Class FeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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
