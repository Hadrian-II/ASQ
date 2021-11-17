<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use ILIAS\Data\UUID\Uuid;
use DateTimeImmutable;
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
        DateTimeImmutable $occurred_on,
        ?Feedback $feedback = null
    ) {
        parent::__construct($aggregate_id, $occurred_on);

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
