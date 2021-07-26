<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class OrderingStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return OrderingConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return OrderingAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupOrdering::class;
    }
}