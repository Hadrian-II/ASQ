<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MatchingScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingScoringConfiguration extends AbstractValueObject
{
    /**
     * @var ?float
     */
    protected $wrong_deduction;

    /**
     * @param float $wrong_deduction
     * @return MatchingScoringConfiguration
     */
    public static function create(?float $wrong_deduction = null) : MatchingScoringConfiguration
    {
        $object = new MatchingScoringConfiguration();
        $object->wrong_deduction = $wrong_deduction;
        return $object;
    }

    /**
     * @return float
     */
    public function getWrongDeduction() : float
    {
        return $this->wrong_deduction ?? 0.0;
    }
}
