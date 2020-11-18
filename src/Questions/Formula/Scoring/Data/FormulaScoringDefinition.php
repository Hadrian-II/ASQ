<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringDefinition extends AbstractValueObject
{
    /**
     * @var ?string
     */
    protected $formula;

    /**
     * @var ?string
     */
    protected $unit;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param ?string $formula
     * @param ?string $unit
     * @param ?float $points
     */
    public function __construct(
        ?string $formula = null,
        ?string $unit = null,
        ?float $points = null
    ) {
        $this->formula = $formula;
        $this->unit = $unit;
        $this->points = $points;
    }

    /**
     * @return ?string
     */
    public function getFormula() : ?string
    {
        return $this->formula;
    }

    /**
     * @return ?string
     */
    public function getUnit() : ?string
    {
        return $this->unit ?? '';
    }

    /**
     * @return ?float
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @param array $units
     * @return bool
     */
    public function isComplete(FormulaScoringConfiguration $config) : bool
    {
        if (is_null($this->getPoints())) {
            return false;
        }

        if (!empty($this->getUnit()) &&
            !in_array($this->getUnit(), $config->getUnits())) {
            return false;
        }

        return true;
    }
}
