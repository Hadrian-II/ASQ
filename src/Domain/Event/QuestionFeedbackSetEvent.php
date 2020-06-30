<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use ilDateTime;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Feedback;

/**
 * Class QuestionFeedbackSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFeedbackSetEvent extends AbstractDomainEvent
{
    /**
     * @var ?Feedback
     */
    protected $feedback;


    /**
     * @param string $aggregate_id
     * @param int $container_obj_id
     * @param int $initiating_user_id
     * @param int $question_int_id
     * @param Feedback $feedback
     */
    public function __construct(
        string $aggregate_id,
        ilDateTime $occured_on,
        int $initiating_user_id,
        ?Feedback $feedback = null
    ) {
        parent::__construct($aggregate_id, $occured_on, $initiating_user_id);

        $this->feedback = $feedback;
    }

    /**
     * @return Feedback
     */
    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }

    /**
     * @return string
     */
    public function getEventBody() : string
    {
        return json_encode($this->feedback);
    }

    /**
     * @param string $json_data
     */
    public function restoreEventBody(string $json_data) : void
    {
        $this->feedback = Feedback::deserialize($json_data);
    }

    /**
     * @return int
     */
    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
