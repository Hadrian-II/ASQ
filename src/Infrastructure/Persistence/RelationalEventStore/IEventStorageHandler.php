<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use Fluxlabs\CQRS\Event\DomainEvent;

/**
 * Interface IEventStorageHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void;

    public function loadEvents(array $data) : array;
}