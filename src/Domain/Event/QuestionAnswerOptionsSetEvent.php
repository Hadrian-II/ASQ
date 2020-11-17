<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use ilDateTime;
use ILIAS\Data\UUID\Uuid;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionAnswerOptionsSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionAnswerOptionsSetEvent extends AbstractDomainEvent
{
    /**
     * @var AnswerOption[]
     */
    protected $answer_options;

    /**
     * @param Uuid $aggregate_id
     * @param ilDateTime $occured_on
     * @param int $initiating_user_id
     * @param int $question_int_id
     * @param AnswerOption[] $options
     */
    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occured_on,
        int $initiating_user_id,
        array $options = null
    ) {
        parent::__construct($aggregate_id, $occured_on, $initiating_user_id);

        $this->answer_options = $options;
    }

    /**
     * @return AnswerOption[]
     */
    public function getAnswerOptions() : array
    {
        return $this->answer_options;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventBody() : string
    {
        return json_encode($this->answer_options);
    }

    /**
     * @param string $json_data
     */
    public function restoreEventBody(string $json_data) : void
    {
        $this->answer_options = AbstractValueObject::deserialize($json_data);
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
