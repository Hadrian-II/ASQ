<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

/**
 * Class NumericGapConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericGapConfiguration extends ClozeGapConfiguration
{
    /**
     * @var ?float
     */
    protected $value;

    /**
     * @var ?float
     */
    protected $upper;

    /**
     * @var ?float
     */
    protected $lower;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var ?int
     */
    protected $field_length;

    /**
     * @param float $value
     * @param float $upper
     * @param float $lower
     * @param float $points
     * @param int $field_length
     */
    public function __construct(
        ?float $value = null,
        ?float $upper = null,
        ?float $lower = null,
        ?float $points = null,
        ?int $field_length = null
    ) {
        $this->value = $value;
        $this->upper = $upper;
        $this->lower = $lower;
        $this->points = $points;
        $this->field_length = $field_length;
    }

    /**
     * @return ?float
     */
    public function getValue() : ?float
    {
        return $this->value;
    }

    /**
     * @return ?float
     */
    public function getUpper() : ?float
    {
        return $this->upper;
    }

    /**
     * @return ?float
     */
    public function getLower() : ?float
    {
        return $this->lower;
    }

    /**
     * @return ?int
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return ?int
     */
    public function getFieldLength() : int
    {
        return $this->field_length ?? self::DEFAULT_FIELD_LENGTH;
    }

    /**
     * @return ?float
     */
    public function getMaxPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        return !is_null($this->getPoints()) &&
               !is_null($this->getLower()) &&
               !is_null($this->getUpper()) &&
               !is_null($this->getValue());
    }
}
