<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class NumericStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return NumericConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        // not used
        return '';
    }

    public function getSetup(): string
    {
        return SetupNumeric::class;
    }
}