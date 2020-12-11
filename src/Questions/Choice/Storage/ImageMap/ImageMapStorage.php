<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class ImageMapStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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