<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringConfiguration extends AbstractValueObject
{
    protected ?string $formula;

    protected ?string $units;

    protected ?int $precision;

    protected ?float $tolerance;

    protected ?int $result_type;

    /**
     * @var FormulaScoringVariable[]
     */
    protected ?array $variables = [];

    const TYPE_ALL = 1;
    const TYPE_DECIMAL = 2;
    const TYPE_FRACTION = 3;
    const TYPE_COPRIME_FRACTION = 4;

    public function __construct(
        ?string $formula = null,
        ?string $units = null,
        ?int $precision = null,
        ?float $tolerance = null,
        ?int $result_type = null,
        ?array $variables = []
    ) {
        $this->formula = $formula;
        $this->units = $units;
        $this->precision = $precision;
        $this->tolerance = $tolerance;
        $this->result_type = $result_type;
        $this->variables = $variables;
    }

    public function getFormula() : ?string
    {
        return $this->formula;
    }

    public function getUnits() : ?array
    {
        if (is_null($this->units) || empty($this->units)) {
            return null;
        }

        return array_map(function ($unit) {
            return trim($unit);
        }, explode(',', $this->units));
    }

    public function getUnitString() : ?string
    {
        return $this->units;
    }

    public function getPrecision() : ?int
    {
        return $this->precision;
    }

    public function getTolerance() : ?float
    {
        return $this->tolerance;
    }

    public function getResultType() : ?int
    {
        return $this->result_type;
    }

    /**
     * @return ?FormulaScoringVariable[]
     */
    public function getVariables() : ?array
    {
        return $this->variables;
    }

    public function getVariablesArray() : array
    {
        $var_array = [];

        foreach ($this->variables as $variable) {
            $var_array[] = $variable->getAsArray();
        }

        return $var_array;
    }

    public function generateVariableValue(FormulaScoringVariable $def) : string
    {
        $exp = 10 ** $this->getPrecision();

        $min = intval($def->getMin() * $exp);
        $max = intval($def->getMax() * $exp);
        $number = mt_rand($min, $max);

        if (!is_null($def->getMultipleOf())) {
            $mult_of = $def->getMultipleOf() * $exp;

            $number -= $number % $mult_of;

            if ($number < $min) {
                $number += $mult_of;
            }
        }

        $number /= $exp;

        if ($this->getPrecision() === null ||
            $this->getPrecision() === 0) {
            return strval($number);
        }
        else {
            return sprintf('%.' . $this->getPrecision() . 'F', $number);
        }
    }
}
