<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Essay\Scoring\EssayScoring;

/**
 * Class EssayScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringConfiguration extends AbstractValueObject
{
    /**
     * @var ?int
     */
    protected $matching_mode;

    /**
     * @var ?int
     */
    protected $scoring_mode;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param int $matching_mode
     * @param int $scoring_mode
     * @param float $points
     */
    public function __construct(
        ?int $matching_mode = TextScoring::TM_CASE_INSENSITIVE,
        ?int $scoring_mode = EssayScoring::SCORING_MANUAL,
        ?float $points = null
    ) {
        $this->matching_mode = $matching_mode;
        $this->scoring_mode = $scoring_mode;
        $this->points = $points;
    }

    /**
     * @return ?int
     */
    public function getMatchingMode() : ?int
    {
        return $this->matching_mode;
    }

    /**
     * @return ?int
     */
    public function getScoringMode() : ?int
    {
        return $this->scoring_mode;
    }

    /**
     * @return ?float
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }
}
