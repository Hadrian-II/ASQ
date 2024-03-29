<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Scoring;

use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Numeric\NumericAnswer;
use srag\asq\Questions\Numeric\Scoring\Data\NumericScoringConfiguration;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class NumericScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class NumericScoring extends AbstractScoring
{
    public function score(?AbstractValueObject $answer) : float
    {
        if ($answer === null) {
            return 0;
        }

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

    protected function calculateMaxScore() : float
    {
        return $this->question->getPlayConfiguration()->getScoringConfiguration()->getPoints();
    }

    public function getBestAnswer() : AbstractValueObject
    {
        /** @var NumericScoringConfiguration $conf */
        $conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        return new NumericAnswer(($conf->getUpperBound() + $conf->getLowerBound()) / 2);
    }

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
