<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class KprimStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return KprimConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return KprimAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupKprim::class;
    }
}