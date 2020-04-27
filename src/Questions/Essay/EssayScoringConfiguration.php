<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;

use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Scoring\TextScoring;

/**
 * Class EssayScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringConfiguration extends AbstractConfiguration {
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
     * @return EssayScoringConfiguration
     */
    public static function create(?int $matching_mode = TextScoring::TM_CASE_INSENSITIVE,
                                  ?int $scoring_mode = EssayScoring::SCORING_MANUAL,
                                  ?float $points = null) : EssayScoringConfiguration
    {

        $object = new EssayScoringConfiguration();

        $object->matching_mode = $matching_mode;
        $object->scoring_mode = $scoring_mode;
        $object->points = $points;

        return $object;
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