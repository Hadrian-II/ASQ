<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\EventStore;

use Fluxlabs\CQRS\Event\EventStore;

/**
 * Class QuestionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionEventStore extends EventStore
{
    protected function getEventArClass() : string
    {
        return QuestionEventStoreAr::class;
    }
}
