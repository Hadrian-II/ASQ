<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class FormulaEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaEditorConfiguration extends AbstractConfiguration
{
    public static function create() : FormulaEditorConfiguration
    {
        return new FormulaEditorConfiguration();
    }
}
