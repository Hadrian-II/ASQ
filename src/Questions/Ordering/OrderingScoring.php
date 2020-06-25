<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class OrderingScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingScoring extends AbstractScoring
{
    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    function score(Answer $answer) : float
    {
        $reached_points = 0.0;

        /** @var OrderingScoringConfiguration $scoring_conf */
        $scoring_conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        $answers = $answer->getSelectedOrder();

        $reached_points = $scoring_conf->getPoints();

        /* To be valid answers need to be in the same order as in the question definition
         * what means that the correct answer will just be an increasing amount of numbers
         * so if the number should get smaller it is an error.
         */
        for ($i = 0; $i < count($answers) - 1; $i++) {
            if ($answers[$i] > $answers[$i + 1]) {
                $reached_points = 0.0;
            }
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
        $answers = [];

        for ($i = 1; $i <= count($this->question->getAnswerOptions()->getOptions()); $i++) {
            $answers[] = $i;
        }

        return OrderingAnswer::create($answers);
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
        /** @var OrderingScoringConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getScoringConfiguration();

        if (empty($config->getPoints())) {
            return false;
        }

        return true;
    }
}