<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class ImageMapStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageMapStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return ImageMapConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return ImageMapAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupImageMap::class;
    }
}