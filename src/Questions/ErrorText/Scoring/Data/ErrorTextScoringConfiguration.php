<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;


use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class ErrorTextScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextScoringConfiguration extends AbstractConfiguration {
    /**
     * @var ?float
     */
    protected $points_wrong;    
    
    static function create(?float $points_wrong = null) : ErrorTextScoringConfiguration
    {
        $object = new ErrorTextScoringConfiguration();
        $object->points_wrong = $points_wrong;
        return $object;
    }
    
    /**
     * @return int
     */
    public function getPointsWrong() : ?float
    {
        return $this->points_wrong;
    }
}