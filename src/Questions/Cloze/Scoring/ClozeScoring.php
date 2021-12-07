<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Cloze\ClozeAnswer;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;

/**
 * Class ClozeScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeScoring extends AbstractScoring
{
    protected ClozeEditorConfiguration $configuration;

    private TextScoring $text_scoring;

    private float $reached_points;

    public function __construct($question)
    {
        global $DIC;

        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->text_scoring = new TextScoring($DIC->language());
    }

    public function score(?AbstractValueObject $answer) : float
    {
        if ($answer === null) {
            return 0;
        }

        $given_answer = $answer->getAnswers();

        $this->reached_points = 0.0;

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $gap_configuration = $this->configuration->getGaps()[$i - 1];

            if (! array_key_exists($i, $given_answer)) {
                continue;
            }

            if (get_class($gap_configuration) === SelectGapConfiguration::class) {
                $this->scoreSelectGap($given_answer[$i], $gap_configuration);
            } elseif (get_class($gap_configuration) === TextGapConfiguration::class) {
                $this->scoreTextGap($given_answer[$i], $gap_configuration);
            } elseif (get_class($gap_configuration) === NumericGapConfiguration::class) {
                $this->scoreNumericGap(floatval($given_answer[$i]), $gap_configuration);
            }
        }

        return $this->reached_points;
    }

    private function scoreSelectGap(string $answer, SelectGapConfiguration $gap_configuration) : void
    {
        /** @var $gap ClozeGapItem */
        foreach ($gap_configuration->getItems() as $gap_item) {
            if ($answer === $gap_item->getText()) {
                $this->reached_points += $gap_item->getPoints();
                return;
            }
        }
    }

    private function scoreTextGap(string $answer, TextGapConfiguration $gap_configuration) : void
    {
        /** @var $gap ClozeGapItem */
        foreach ($gap_configuration->getItems() as $gap_item) {
            if ($this->text_scoring->isMatch($answer, $gap_item->getText(), $gap_configuration->getMatchingMethod())) {
                $this->reached_points += $gap_item->getPoints();
                return;
            }
        }
    }

    private function scoreNumericGap(float $answer, NumericGapConfiguration $gap_configuration) : void
    {
        if ($gap_configuration->getUpper() >= $answer &&
            $gap_configuration->getLower() <= $answer) {
            $this->reached_points += $gap_configuration->getPoints();
        }
    }

    protected function calculateMaxScore() : float
    {
        $max_score = 0.0;

        foreach ($this->configuration->getGaps() as $gap_configuration) {
            $max_score += $gap_configuration->getMaxPoints();
        }

        return $max_score;
    }

    public function getBestAnswer() : AbstractValueObject
    {
        $answers = [];
        $i = 1;

        foreach ($this->configuration->getGaps() as $gap) {
            if (get_class($gap) === SelectGapConfiguration::class) {
                $answers[strval($i)] = $this->getBestClozeItemAnswer($gap->getItems());
            } elseif (get_class($gap) === TextGapConfiguration::class) {
                $answers[strval($i)] = $this->getBestClozeItemAnswer($gap->getItems());
            } elseif (get_class($gap) === NumericGapConfiguration::class) {
                $answers[strval($i)] = strval($gap->getValue());
            }
            $i += 1;
        }

        return new ClozeAnswer($answers);
    }

    private function getBestClozeItemAnswer(array $items) : string
    {
        $best_points = 0;
        $best_text = '';

        foreach ($items as $item) {
            if ($item->getPoints() > $best_points) {
                $best_points = $item->getPoints();
                $best_text = $item->getText();
            }
        }

        return $best_text;
    }

    public function isComplete() : bool
    {
        return true;
    }
}
