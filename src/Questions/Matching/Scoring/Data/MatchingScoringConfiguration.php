<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Scoring\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MatchingScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingScoringConfiguration extends AbstractValueObject
{
    protected ?float $wrong_deduction;

    public function __construct(?float $wrong_deduction = null)
    {
        $this->wrong_deduction = $wrong_deduction;
    }

    public function getWrongDeduction() : float
    {
        return $this->wrong_deduction ?? 0.0;
    }
}
