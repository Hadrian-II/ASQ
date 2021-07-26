<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use srag\CQRS\Event\DomainEvent;

/**
 * Interface IEventStorageHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void;

    public function loadEvents(array $data) : array;
}