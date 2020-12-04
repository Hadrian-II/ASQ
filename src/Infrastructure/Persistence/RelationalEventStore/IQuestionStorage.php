<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\IQuestionDBSetup;

/**
 * Interface IQuestionStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IQuestionStorage
{
    /**
     * @return IEventStorageHandler
     */
    public function getPlayConfigurationHandler() : string;
    /**
     * @return IEventStorageHandler
     */
    public function getAnswerOptionHandler() : string;

    /**
     * @return IQuestionDBSetup
     */
    public function getSetup() : string;
}