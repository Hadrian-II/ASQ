<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Hint\QuestionHints;
use ILIAS\Data\UUID\Uuid;
use ilDateTime;

/**
 * Class QuestionHintsSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHintsSetEvent extends AbstractDomainEvent
{
    /**
     * @var ?QuestionHints
     */
    protected $hints;


    /**
     * @param Uuid $aggregate_id
     * @param ilDateTime $occured_on
     * @param int $initiating_user_id
     * @param QuestionHints $hints
     */
    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occured_on,
        int $initiating_user_id,
        ?QuestionHints $hints = null
    ) {
        parent::__construct($aggregate_id, $occured_on, $initiating_user_id);

        $this->hints = $hints;
    }

    /**
     * @return QuestionHints
     */
    public function getHints() : ?QuestionHints
    {
        return $this->hints;
    }

    public function getEventBody() : string
    {
        return json_encode($this->hints);
    }

    /**
     * @param string $json_data
     */
    public function restoreEventBody(string $json_data) : void
    {
        $this->hints = QuestionHints::deserialize($json_data);
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
