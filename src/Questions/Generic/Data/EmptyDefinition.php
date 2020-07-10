<?php
declare(strict_types=1);

namespace srag\asq\Questions\Generic\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EmptyDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EmptyDefinition extends AbstractValueObject
{
    public static function create() : EmptyDefinition
    {
        return new EmptyDefinition();
    }
}
