<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaEditorConfiguration extends AbstractValueObject
{
    public static function create() : FormulaEditorConfiguration
    {
        return new FormulaEditorConfiguration();
    }
}
