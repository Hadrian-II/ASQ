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
    protected ?int $wrong_word_index;

    protected ?int $wrong_word_length;

    protected ?string $correct_text;

    protected ?float $points;

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

    public function getWrongWordIndex() : ?int
    {
        return $this->wrong_word_index;
    }

    public function getWrongWordLength() : ?int
    {
        return $this->wrong_word_length;
    }

    public function getCorrectText() : ?string
    {
        return $this->correct_text;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }
}
