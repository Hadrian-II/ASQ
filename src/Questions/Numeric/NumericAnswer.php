<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Numeric;

use srag\asq\Domain\Model\Answer\Answer;

/**
 * Class NumericAnswer
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericAnswer extends Answer
{

    /**
     * @var ?float
     */
    protected $value;

    /**
     * @param float $value
     * @return NumericAnswer
     */
    public static function create(?float $value = null) : NumericAnswer
    {
        $object = new NumericAnswer();
        $object->value = $value;
        return $object;
    }

    /**
     * @return float|NULL
     */
    public function getValue() : ?float
    {
        return $this->value;
    }
}
