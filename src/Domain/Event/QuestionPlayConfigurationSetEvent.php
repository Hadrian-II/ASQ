<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use ILIAS\Data\UUID\Uuid;
use ilDateTime;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use Fluxlabs\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;

/**
 * Class QuestionPlayConfigurationSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionPlayConfigurationSetEvent extends AbstractDomainEvent
{
    protected ?QuestionPlayConfiguration $play_configuration;

    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occurred_on,
        QuestionPlayConfiguration $play_configuration = null
    ) {
        parent::__construct($aggregate_id, $occurred_on);

        $this->play_configuration = $play_configuration;
    }

    public function getPlayConfiguration() : QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }

    public function getEventBody() : string
    {
        return json_encode($this->play_configuration);
    }

    public function restoreEventBody(string $json_data) : void
    {
        $this->play_configuration = AbstractValueObject::deserialize($json_data);
    }

    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
