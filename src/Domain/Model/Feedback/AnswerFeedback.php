<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Feedback;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Abstract Class FeedbackDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AnswerFeedback extends AbstractValueObject
{
    const VAR_ANSWER_FEEDBACK_CORRECT = 'answer_feedback_correct';
    const VAR_ANSWER_FEEDBACK_WRONG = 'answer_feedback_wrong';
    const VAR_ANSWER_FEEDBACK_CORRECT_PAGE_ID = 1;
    const VAR_ANSWER_FEEDBACK_WRONG_PAGE_ID = 2;
    const VAR_FEEDBACK_TYPE_INT_ID = 'feedback_type_int_id';
    /**
     * @var string
     */
    protected $answer_feedback;


    public function __construct(?string $answer_feedback = "")
    {
        $this->answer_feedback = $answer_feedback;
    }


    public function getAnswerFeedback() : string
    {
        return $this->answer_feedback;
    }
}
