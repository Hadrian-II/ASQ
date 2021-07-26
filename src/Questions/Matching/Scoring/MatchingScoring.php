<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Scoring;

use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Matching\MatchingAnswer;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Application\Exception\AsqException;

/**
 * Class MultipleChoiceScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingScoring extends AbstractScoring
{
    public function score(AbstractValueObject $answer) : float
    {
        $matches = [];
        $wrong_deduction = $this->question->getPlayConfiguration()->getScoringConfiguration()->getWrongDeduction();

        foreach ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMatches() as $match) {
            $key = $match->getDefinitionId() . '-' . $match->getTermId();
            $matches[$key] = $match->getPoints();
        };

        $score = 0;

        $given_matches = [];
        foreach ($answer->getMatches() as $given_match) {
            if (in_array($given_match, $given_matches)) {
                throw new AsqException('One Matching was found multiple Times');
            }
            else {
                $given_matches[] = $given_match;
            }

            if (array_key_exists($given_match, $matches)) {
                $score += $matches[$given_match];
            } else {
                $score -= $wrong_deduction;
            }
        }

        return $score;
    }

    public function getBestAnswer() : AbstractValueObject
    {
        $matches = [];

        foreach ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMatches() as $match) {
            $matches[] = $match->getDefinitionId() . '-' . $match->getTermId();
        };

        return new MatchingAnswer($matches);
    }

    protected function calculateMaxScore() : float
    {
        $max_score = 0;

        foreach ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMatches() as $match) {
            $max_score += intval($match->getPoints());
        };

        return $max_score;
    }

    public function isComplete() : bool
    {
        return true;
    }
}
