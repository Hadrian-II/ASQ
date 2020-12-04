<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup;

/**
 * Interface IQuestionDBSetup
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IQuestionDBSetup
{
    public function setup() : void;

    public function drop() : void;
}