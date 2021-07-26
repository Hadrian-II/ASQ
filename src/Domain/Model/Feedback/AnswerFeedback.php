<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Feedback;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Abstract Class FeedbackDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class AnswerFeedback extends AbstractValueObject
{
    const VAR_FEEDBACK_TYPE_INT_ID = 'feedback_type_int_id';

    protected ?string $answer_feedback;


    public function __construct(?string $answer_feedback = '')
    {
        $this->answer_feedback = $answer_feedback;
    }


    public function getAnswerFeedback() : string
    {
        return $this->answer_feedback;
    }
}
