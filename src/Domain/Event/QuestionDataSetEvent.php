<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractIlContainerItemDomainEvent;
use srag\asq\Domain\Model\QuestionData;

/**
 * Class QuestionDataSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDataSetEvent extends AbstractIlContainerItemDomainEvent {

	public const NAME = 'QuestionDataSetEvent';
	/**
	 * @var QuestionData
	 */
	protected $data;


    /**
     * QuestionDataSetEvent constructor.
     *
     * @param DomainObjectId    $id
     * @param int               $creator_id
     * @param QuestionData|null $data
     *
     * @throws \ilDateTimeException
     */
	public function __construct(DomainObjectId $aggregate_id, 
	                            int $container_obj_id, 
	                            int $initiating_user_id, 
	                            int $question_int_id,
	                            QuestionData $data = null)
	{
	    parent::__construct($aggregate_id, $question_int_id, $container_obj_id, $initiating_user_id);
	    
		$this->data = $data;
	}

	/**
	 * @return string
	 *
	 * Add a Constant EVENT_NAME to your class: Name it: Classname
	 * e.g. 'QuestionCreatedEvent'
	 */
	public function getEventName(): string {
		return self::NAME;
	}

	/**
	 * @return QuestionData
	 */
	public function getData(): QuestionData {
		return $this->data;
	}

    /**
     * @return string
     */
	public function getEventBody(): string {
		return json_encode($this->data);
	}

	/**
	 * @param string $json_data
	 */
	public function restoreEventBody(string $json_data) : void {
		$this->data = AbstractValueObject::deserialize($json_data);
	}
}