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
     */
    public function __construct(
        ?int $wrong_word_index = null,
        ?int $wrong_word_length = null,
        ?string $correct_text = null,
        ?float $points = null
    ) {
        $this->wrong_word_index = $wrong_word_index;
        $this->wrong_word_length = $wrong_word_length;
        $this->correct_text = $correct_text;
        $this->points = $points;
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
