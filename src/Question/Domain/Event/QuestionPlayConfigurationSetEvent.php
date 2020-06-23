<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use ilDateTime;

/**
 * Class QuestionPlayConfigurationSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionPlayConfigurationSetEvent extends AbstractDomainEvent {
	/**
	 * @var QuestionPlayConfiguration
	 */
	protected $play_configuration;

    /**
     *
     * @param string $aggregate_id
     * @param ilDateTime $occured_on
     * @param int $initiating_user_id
     * @param QuestionPlayConfiguration $play_configuration
     */
	public function __construct(
	    string $aggregate_id,
	    ilDateTime $occured_on,
	    int $initiating_user_id,
	    QuestionPlayConfiguration $play_configuration = null)
	{
	    parent::__construct($aggregate_id, $occured_on, $initiating_user_id);

		$this->play_configuration = $play_configuration;
	}

	/**
	 * @return QuestionPlayConfiguration
	 */
	public function getPlayConfiguration() : QuestionPlayConfiguration {
		return $this->play_configuration;
	}

    /**
     * @return string
     */
	public function getEventBody() : string {
		return json_encode($this->play_configuration);
	}

	/**
	 * @param string $json_data
	 */
	public function restoreEventBody(string $json_data) : void {
		$this->play_configuration = AbstractValueObject::deserialize($json_data);
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