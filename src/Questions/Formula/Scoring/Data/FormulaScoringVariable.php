<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaScoringVariable
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaScoringVariable extends AbstractValueObject
{
    const VAR_MIN = 'fsv_min';
    const VAR_MAX = 'fsv_max';
    const VAR_UNIT = 'fsv_unit';
    const VAR_MULTIPLE_OF = 'fsv_multiple_of';

    protected ?float $min;

    protected ?float $max;

    protected ?string $unit;

    protected ?float $multiple_of;

    public function __construct(
        ?float $min,
        ?float $max,
        ?string $unit,
        ?float $multiple_of
    ) {
        $this->min = $min;
        $this->max = $max;
        $this->unit = $unit;
        $this->multiple_of = $multiple_of;
    }

    public function getMin() : ?float
    {
        return $this->min;
    }

    public function getMax() : ?float
    {
        return $this->max;
    }

    public function getUnit() : ?string
    {
        return $this->unit;
    }

    public function getMultipleOf() : ?float
    {
        return $this->multiple_of;
    }

    public function getAsArray() : array
    {
        return [
            self::VAR_MIN => $this->min,
            self::VAR_MAX => $this->max,
            self::VAR_UNIT => $this->unit,
            self::VAR_MULTIPLE_OF => $this->multiple_of
        ];
    }

    public function isComplete() : bool
    {
        return !is_null($this->getMax()) &&
               !is_null($this->getMin()) &&
               !is_null($this->getMultipleOf());
    }
}
