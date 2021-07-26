<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class NumericStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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