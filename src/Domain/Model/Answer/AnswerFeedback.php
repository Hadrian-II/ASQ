<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer;

use JsonSerializable;
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
abstract class AnswerFeedback extends AbstractValueObject implements JsonSerializable
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


    public function equals(AbstractValueObject $other) : bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }

        if ($this->getAnswerFeedback() !== $other->getAnswerFeedback()) {
            return false;
        }

        return true;
    }


    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
