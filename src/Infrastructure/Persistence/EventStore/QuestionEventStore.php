<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\EventStore;

use srag\CQRS\Event\EventStore;

/**
 * Class QuestionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionEventStore extends EventStore
{
    protected function getEventArClass() : string
    {
        return QuestionEventStoreAr::class;
    }
}
