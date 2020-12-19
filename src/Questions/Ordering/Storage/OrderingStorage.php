<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class OrderingStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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