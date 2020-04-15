<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\UserInterface\Web\Form\Config\AnswerOptionForm;

/**
 * Class OrderingScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingScoringConfiguration extends AbstractConfiguration {
    /**
     * @var ?float
     */
    protected $points;
    
    
    static function create(?float $points = null) : OrderingScoringConfiguration
    {
        $object = new OrderingScoringConfiguration();
        $object->points = $points;
        return $object;
    }
    
    /**
     * @return int
     */
    public function getPoints(): ?float
    {
        return $this->points;
    }
}