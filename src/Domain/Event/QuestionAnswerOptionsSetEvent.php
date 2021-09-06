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
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionAnswerOptionsSetEvent extends AbstractDomainEvent
{
    /**
     * @var ?AnswerOption[]
     */
    protected ?array $answer_options;

    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occurred_on,
        int $initiating_user_id,
        ?array $options = null
    ) {
        parent::__construct($aggregate_id, $occurred_on, $initiating_user_id);

        $this->answer_options = $options;
    }

    /**
     * @return ?AnswerOption[]
     */
    public function getAnswerOptions() : ?array
    {
        return $this->answer_options;
    }

    public function getEventBody() : string
    {
        return json_encode($this->answer_options);
    }

    public function restoreEventBody(string $json_data) : void
    {
        $this->answer_options = AbstractValueObject::deserialize($json_data);
    }

    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
