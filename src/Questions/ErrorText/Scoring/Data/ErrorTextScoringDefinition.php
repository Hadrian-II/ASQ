<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ErrorTextScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextScoringDefinition extends AbstractValueObject
{
    /**
     * @var ?int
     */
    protected $wrong_word_index;
    /**
     * @var ?int
     */
    protected $wrong_word_length;
    /**
     * @var ?string
     */
    protected $correct_text;
    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param int $wrong_word_index
     * @param int $wrong_word_length
     * @param string $correct_text
     * @param float $points
     * @return ErrorTextScoringDefinition
     */
    public static function create(
        ?int $wrong_word_index,
        ?int $wrong_word_length,
        ?string $correct_text,
        ?float $points
    ) : ErrorTextScoringDefinition {
        $object = new ErrorTextScoringDefinition();
        $object->wrong_word_index = $wrong_word_index;
        $object->wrong_word_length = $wrong_word_length;
        $object->correct_text = $correct_text;
        $object->points = $points;
        return $object;
    }

    /**
     * @return int
     */
    public function getWrongWordIndex() : ?int
    {
        return $this->wrong_word_index;
    }

    /**
     * @return int
     */
    public function getWrongWordLength() : ?int
    {
        return $this->wrong_word_length;
    }

    /**
     * @return string
     */
    public function getCorrectText() : ?string
    {
        return $this->correct_text;
    }

    /**
     * @return number
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }
}
