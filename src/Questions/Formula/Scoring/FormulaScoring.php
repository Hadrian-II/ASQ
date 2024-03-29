<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Scoring;

use EvalMath;
use Exception;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Formula\FormulaAnswer;
use srag\asq\Questions\Formula\Editor\FormulaEditor;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringDefinition;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringVariable;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class FormulaScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaScoring extends AbstractScoring
{
    use InputHandlingTrait;

    protected FormulaScoringConfiguration $configuration;

    public function __construct($question)
    {
        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();
    }

    public function score(?AbstractValueObject $answer) : float
    {
        if ($answer === null) {
            return 0;
        }

        $reached_points = 0.0;

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var $result FormulaScoringDefinition */
            $result = $option->getScoringDefinition();

            $variables = $answer->getVariables();

            if (is_null($variables)) {
                return 0.0;
            }

            $formula = $result->getFormula();

            foreach ($variables as $key => $value) {
                $formula = str_replace($key, $value, $formula);
            }

            $math = new EvalMath();

            $result_expected = $math->evaluate($formula);

            $answers = $answer->getResults();
            $result_given = null;
            $raw_result = $answers['$r' . $option->getOptionId()];
            $unit_given = $answers['$r' . $option->getOptionId() . FormulaEditor::VAR_UNIT] ?? '';

            //get decimal value of answer if allowed
            if (($this->configuration->getResultType() === FormulaScoringConfiguration::TYPE_ALL ||
                $this->configuration->getResultType() === FormulaScoringConfiguration::TYPE_DECIMAL) &&
                is_numeric($this->readFloat($raw_result)))
            {
                $result_given = $this->readFloat($raw_result);
            }

            //get compound result if no value yet and it is allowed
            if (is_null($result_given) &&
                $this->configuration->getResultType() !== FormulaScoringConfiguration::TYPE_DECIMAL &&
                strpos($raw_result, '/'))
            {
                $nr = "/\d+/";
                $numbers= [];
                $found = preg_match_all($nr, $raw_result, $numbers);

                $whole = $found === 3 ? floatval($numbers[0][0]) : 0;
                $denominator = floatval(end($numbers[0]));
                $numerator = floatval(prev($numbers[0]));

                $result_given = $whole + $numerator / $denominator;

                // invalidate result if not coprime and option is set
                if ($this->configuration->getResultType() === FormulaScoringConfiguration::TYPE_COPRIME_FRACTION &&
                    $this->greatest_common_divisor($numerator, $denominator) !== 1.0) {
                    $result_given = null;
                }
            }

            if (!is_null($result_given)) {
                $difference = abs($result_expected - $result_given);
                $max_allowed_difference = $result_expected / 100 * max($this->configuration->getTolerance(), PHP_FLOAT_EPSILON);

                if ($difference <= $max_allowed_difference &&
                    $unit_given === $result->getUnit()) {
                    $reached_points += $result->getPoints();
                }
            }
        }

        return $reached_points;
    }

    /**
     * Euclids gcd algorithm
     *
     * @param float $a
     * @param float $b
     * @return float
     */
    private function greatest_common_divisor(float $a, float $b) : float
    {
        return ($a % $b) ? $this->greatest_common_divisor($b, $a % $b) : $b;
    }

    public function getBestAnswer() : AbstractValueObject
    {
        $values = [];

        $ix = 1;
        foreach ($this->configuration->getVariables() as $var) {
            $values['$v' . strval($ix)] = $this->configuration->generateVariableValue($var);
            $ix += 1;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            $this->generateResult($values, $option->getScoringDefinition(), $option->getOptionId());
        }

        return new FormulaAnswer($values);
    }

    private function generateResult(array &$values, FormulaScoringDefinition $def, string $ix) : void
    {
        $values['$r' . $ix . FormulaEditor::VAR_UNIT] = $def->getUnit();

        $formula = $def->getFormula();

        foreach ($values as $key => $value) {
            $formula = str_replace($key, $value, $formula);
        }

        $math = new EvalMath();

        $result = floatval($math->evaluate($formula));

        if ($this->configuration->getResultType() == FormulaScoringConfiguration::TYPE_ALL ||
            $this->configuration->getResultType() == FormulaScoringConfiguration::TYPE_DECIMAL)
        {
            $values['$r' . $ix] = strval($result);
        }
        else {
            $mod = fmod($result, 1);

            if ($mod === 0.0) {
                $divisor = 1;
            }
            else {
                $divisor = 1 / $mod;
            }

            $values['$r' . $ix] = sprintf('%d / %d', $result * $divisor, $divisor);
        }
    }

    protected function calculateMaxScore() : float
    {
        $max_score = 0.0;

        foreach ($this->question->getAnswerOptions() as $option) {
            $max_score += $option->getScoringDefinition()->getPoints();
        }

        return $max_score;
    }

    public function isComplete() : bool
    {
        if (is_null($this->configuration->getResultType())) {
            return false;
        }

        foreach ($this->configuration->getVariables() as $var) {
            if (!$var->isComplete()) {
                return false;
            }

            if (!$this->isVarValid($var)) {
                return false;
            }
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var FormulaScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (!$option_config->isComplete($this->configuration)) {
                return false;
            }

            if (!$this->isResultValid($option_config)) {
                return false;
            }
        }

        return true;
    }

    private function isVarValid(FormulaScoringVariable $var) : bool
    {
        if (!$this->inPrecision($var->getMax(), $this->configuration->getPrecision()) ||
            !$this->inPrecision($var->getMin(), $this->configuration->getPrecision()) ||
            !$this->inPrecision($var->getMultipleOf(), $this->configuration->getPrecision())) {
            return false;
        }

        if (!empty($var->getUnit()) &&
            (is_null($this->configuration->getUnits()) ||
            !in_array($var->getUnit(), $this->configuration->getUnits()))) {
            return false;
        }

        return true;
    }

    private function inPrecision(float $number, ?int $precision) : bool
    {
        $mult = $number * (10 ** $precision ?? 0);

        return ceil($mult) === floor($mult);
    }

    private function isResultValid(FormulaScoringDefinition $result) : bool
    {
        if (!empty($result->getUnit()) &&
            (is_null($this->configuration->getUnits()) ||
            !in_array($result->getUnit(), $this->configuration->getUnits()))) {
            return false;
        }

        $variables = [];

        $i = 0;
        foreach ($this->configuration->getVariables() as $var) {
            $i += 1;
            $variables['$v' . $i] = $this->configuration->generateVariableValue($var);
        }

        $formula = $result->getFormula();

        foreach ($variables as $key => $value) {
            $formula = str_replace($key, $value, $formula);
        }

        $math = new EvalMath();

        try {
            $math->evaluate($formula);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
