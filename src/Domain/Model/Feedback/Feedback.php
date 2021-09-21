<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Feedback;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class Feedback
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Feedback extends AbstractValueObject
{
    const OPT_ANSWER_OPTION_FEEDBACK_MODE_NONE = 0;
    const OPT_ANSWER_OPTION_FEEDBACK_MODE_ALL = 1;
    const OPT_ANSWER_OPTION_FEEDBACK_MODE_CHECKED = 2;
    const OPT_ANSWER_OPTION_FEEDBACK_MODE_CORRECT = 3;

    protected ?string $answer_correct_feedback;

    protected ?string $answer_wrong_feedback;

    protected ?int $answer_option_feedback_mode;

    /**
     * @var string[]
     */
    protected array $answer_option_feedbacks;

    public function __construct(
        ?string $answer_correct_feedback = null,
        ?string $answer_wrong_feedback = null,
        ?int $answer_option_feedback_mode = null,
        array $answer_option_feedbacks = []
    ) {
        $this->answer_correct_feedback = $answer_correct_feedback;
        $this->answer_wrong_feedback = $answer_wrong_feedback;
        $this->answer_option_feedback_mode = $answer_option_feedback_mode;
        $this->answer_option_feedbacks = $answer_option_feedbacks;
    }

    public function getAnswerCorrectFeedback() : ?string
    {
        return $this->answer_correct_feedback;
    }

    public function getAnswerWrongFeedback() : ?string
    {
        return $this->answer_wrong_feedback;
    }

    public function getAnswerOptionFeedbackMode() : ?int
    {
        return $this->answer_option_feedback_mode;
    }

    public function getAnswerOptionFeedbacks() : array
    {
        return $this->answer_option_feedbacks;
    }

    public function hasAnswerOptionFeedback(string $option_id) : bool
    {
        return array_key_exists($option_id, $this->answer_option_feedbacks);
    }

    public function getFeedbackForAnswerOption(string $option_id) : ?string
    {
        return $this->answer_option_feedbacks[$option_id];
    }
}
