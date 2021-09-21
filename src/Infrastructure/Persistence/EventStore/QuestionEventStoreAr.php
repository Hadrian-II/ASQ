<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\EventStore;

use Fluxlabs\CQRS\Event\AbstractStoredEvent;

/**
 * Class questionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionEventStoreAr extends AbstractStoredEvent
{
    const STORAGE_NAME = "asq_qst_event_store";

    public static function returnDbTableName() : string
    {
        return self::STORAGE_NAME;
    }
}
