<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\QuestionData;
use ILIAS\Data\UUID\Uuid;
use ilDateTime;

/**
 * Class QuestionDataSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDataSetEvent extends AbstractDomainEvent
{
    /**
     * @var QuestionData
     */
    protected $data;


    /**
     * @param Uuid $aggregate_id
     * @param ilDateTime $occured_on
     * @param int $initiating_user_id
     * @param int $question_int_id
     * @param QuestionData $data
     */
    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occured_on,
        int $initiating_user_id,
        QuestionData $data = null
    ) {
        parent::__construct($aggregate_id, $occured_on, $initiating_user_id);

        $this->data = $data;
    }

    /**
     * @return QuestionData
     */
    public function getData() : QuestionData
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getEventBody() : string
    {
        return json_encode($this->data);
    }

    /**
     * @param string $json_data
     */
    public function restoreEventBody(string $json_data) : void
    {
        $this->data = AbstractValueObject::deserialize($json_data);
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
