<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class EssayStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return EssayConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return EssayAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupEssay::class;
    }
}