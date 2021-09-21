<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Scoring;

use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Ordering\OrderingAnswer;
use srag\asq\Questions\Ordering\Scoring\Data\OrderingScoringConfiguration;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingScoring extends AbstractScoring
{
    public function score(AbstractValueObject $answer) : float
    {
        $reached_points = 0.0;

        /** @var OrderingScoringConfiguration $scoring_conf */
        $scoring_conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        $answers = $answer->getSelectedOrder();

        $reached_points = $scoring_conf->getPoints();

        // prevent empty answers being counted as correct
        if (count($answers) !== count($this->question->getAnswerOptions()))
        {
            return 0;
        }

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

    protected function calculateMaxScore() : float
    {
        return $this->question->getPlayConfiguration()->getScoringConfiguration()->getPoints();
    }

    public function getBestAnswer() : AbstractValueObject
    {
        $answers = [];

        for ($i = 1; $i <= count($this->question->getAnswerOptions()); $i++) {
            $answers[] = $i;
        }

        return new OrderingAnswer($answers);
    }

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
