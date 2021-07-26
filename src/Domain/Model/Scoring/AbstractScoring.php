<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Scoring;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Definitions\IAsqQuestionScoring;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Abstract Class AbstractScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractScoring implements IAsqQuestionScoring
{
    const BEST_ANSWER_CREATION_IMPOSSIBLE_ERROR = 'Best Answer generation impossible for this Question';

    protected QuestionDto $question;

    protected float $max_score;

    protected float $min_score;

    public function __construct(QuestionDto $question)
    {
        $this->question = $question;
    }

    public function getMaxScore() : float
    {
        if (is_null($this->max_score)) {
            $this->max_score = $this->calculateMaxScore();
        }

        return $this->max_score;
    }

    abstract protected function calculateMaxScore() : float;

    public function getMinScore() : float
    {
        if (is_null($this->max_score)) {
            $this->max_score = $this->calculateMinScore();
        }

        return $this->max_score;
    }

    protected function calculateMinScore() : float
    {
        return $this->calculateMaxHintDeduction();
    }

    protected function calculateMaxHintDeduction() : float
    {
        if ($this->question->hasHints()) {
            return array_reduce($this->question->getQuestionHints()->getHints(), function ($sum, $hint) {
                return $sum += $hint->getPointDeduction();
            }, 0.0);
        } else {
            return 0.0;
        }
    }

    public function getAnswerFeedbackType(AbstractValueObject $answer) : int
    {
        if ($this->getMaxScore() < PHP_FLOAT_EPSILON) {
            return self::ANSWER_CORRECTNESS_NOT_DETERMINABLE;
        } elseif (abs($this->score($answer) - $this->getMaxScore()) < PHP_FLOAT_EPSILON) {
            return self::ANSWER_CORRECT;
        } else {
            return self::ANSWER_INCORRECT;
        }
    }
}
