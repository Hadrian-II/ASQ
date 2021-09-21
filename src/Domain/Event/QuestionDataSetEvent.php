<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use Fluxlabs\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\QuestionData;
use ILIAS\Data\UUID\Uuid;
use ilDateTime;

/**
 * Class QuestionDataSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionDataSetEvent extends AbstractDomainEvent
{
    protected ?QuestionData $data;

    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occurred_on,
        int $initiating_user_id,
        ?QuestionData $data = null
    ) {
        parent::__construct($aggregate_id, $occurred_on, $initiating_user_id);

        $this->data = $data;
    }

    public function getData() : ?QuestionData
    {
        return $this->data;
    }

    public function getEventBody() : string
    {
        return json_encode($this->data);
    }

    public function restoreEventBody(string $json_data) : void
    {
        $this->data = AbstractValueObject::deserialize($json_data);
    }

    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
