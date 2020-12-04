<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class ClozeAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return ClozePlayConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return ClozeAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupCloze::class;
    }
}