<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use ILIAS\Data\UUID\Uuid;
use ilDateTime;
use Fluxlabs\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Feedback\Feedback;

/**
 * Class QuestionFeedbackSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionFeedbackSetEvent extends AbstractDomainEvent
{
    protected ?Feedback $feedback;

    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occurred_on,
        int $initiating_user_id,
        ?Feedback $feedback = null
    ) {
        parent::__construct($aggregate_id, $occurred_on, $initiating_user_id);

        $this->feedback = $feedback;
    }

    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }

    public function getEventBody() : string
    {
        return json_encode($this->feedback);
    }

    public function restoreEventBody(string $json_data) : void
    {
        $this->feedback = Feedback::deserialize($json_data);
    }

    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
