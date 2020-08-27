<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaAnswer extends AbstractValueObject
{
    /**
     * @var ?array
     */
    protected $values;

    /**
     * @param array $values
     * @return FormulaAnswer
     */
    public static function create(?array $values = null) : FormulaAnswer
    {
        $object = new FormulaAnswer();
        $object->values = $values;
        return $object;
    }

    /**
     * @return ?array
     */
    public function getValues() : ?array
    {
        return $this->values;
    }
}
