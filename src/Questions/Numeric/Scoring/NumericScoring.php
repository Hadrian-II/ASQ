<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Scoring;

use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Numeric\NumericAnswer;
use srag\asq\Questions\Numeric\Scoring\Data\NumericScoringConfiguration;

/**
 * Class NumericScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericScoring extends AbstractScoring
{
    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    public function score(Answer $answer) : float
    {
        $reached_points = 0;

        /** @var NumericScoringConfiguration $scoring_conf */
        $scoring_conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        $float_answer = $answer->getValue();

        if ($float_answer !== null &&
            $scoring_conf->getLowerBound() <= $float_answer &&
            $scoring_conf->getUpperBound() >= $float_answer) {
            $reached_points = $scoring_conf->getPoints();
        }

        return $reached_points;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        return $this->question->getPlayConfiguration()->getScoringConfiguration()->getPoints();
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::getBestAnswer()
     */
    public function getBestAnswer() : Answer
    {
        /** @var NumericScoringConfiguration $conf */
        $conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        return NumericAnswer::create(($conf->getUpperBound() + $conf->getLowerBound()) / 2);
    }

    /**
     * @return string
     */
    public static function getScoringDefinitionClass() : string
    {
        return EmptyDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        /** @var NumericScoringConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getScoringConfiguration();

        if (empty($config->getPoints()) ||
            empty($config->getLowerBound() ||
            empty($config->getUpperBound()))) {
            return false;
        }

        return true;
    }
}
