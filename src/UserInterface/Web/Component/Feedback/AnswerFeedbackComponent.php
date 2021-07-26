<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ILIAS\UI\Component\Component;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class AnswerFeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class AnswerFeedbackComponent implements Component
{
    use PathHelper;

    private QuestionDto $question;

    private AbstractValueObject $answer;

    public function __construct(QuestionDto $question, AbstractValueObject $answer)
    {
        $this->question = $question;
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
        return AnswerFeedbackComponent::class;
    }
}
