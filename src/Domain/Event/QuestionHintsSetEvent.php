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
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionHintsSetEvent extends AbstractDomainEvent
{
    protected ?QuestionHints $hints;

    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occurred_on,
        int $initiating_user_id,
        ?QuestionHints $hints = null
    ) {
        parent::__construct($aggregate_id, $occurred_on, $initiating_user_id);

        $this->hints = $hints;
    }

    public function getHints() : ?QuestionHints
    {
        return $this->hints;
    }

    public function getEventBody() : string
    {
        return json_encode($this->hints);
    }

    public function restoreEventBody(string $json_data) : void
    {
        $this->hints = QuestionHints::deserialize($json_data);
    }

    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
