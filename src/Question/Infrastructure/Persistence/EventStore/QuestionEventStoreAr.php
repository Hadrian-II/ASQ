<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\EventStore;

use srag\CQRS\Event\AbstractStoredEvent;

/**
 * Class questionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionEventStoreAr extends AbstractStoredEvent {

	const STORAGE_NAME = "asq_qst_event_store";

	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::STORAGE_NAME;
	}
}
