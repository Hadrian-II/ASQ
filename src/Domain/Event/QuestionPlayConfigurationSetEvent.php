<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use ILIAS\Data\UUID\Uuid;
use ilDateTime;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;

/**
 * Class QuestionPlayConfigurationSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionPlayConfigurationSetEvent extends AbstractDomainEvent
{
    protected QuestionPlayConfiguration $play_configuration;

    public function __construct(
        Uuid $aggregate_id,
        ilDateTime $occured_on,
        int $initiating_user_id,
        QuestionPlayConfiguration $play_configuration = null
    ) {
        parent::__construct($aggregate_id, $occured_on, $initiating_user_id);

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
