<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Editor;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Questions\Formula\FormulaAnswer;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringVariable;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class FormulaEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaEditor extends AbstractEditor
{
    use PostAccess;

    const VAR_UNIT = 'fe_unit';

    private FormulaScoringConfiguration $configuration;

    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();

        parent::__construct($question, $is_disabled);
    }

    public function readAnswer() : ?AbstractValueObject
    {
        $results = [];
        $variables = [];
        $index = 1;
        $continue = true;
        while ($continue) {
            $continue = false;

            $continue |= $this->processVar('$v' . $index, $variables);
            $continue |= $this->processVar('$r' . $index, $results);
            $index += 1;
        }

        return new FormulaAnswer($variables, $results);
    }

    private function processVar(string $name, array &$answers) : bool
    {
        $postvar = $this->getPostVariableName($name);

        if ($this->isPostVarSet($postvar)) {
            $answers[$name] = $this->getPostValue($postvar);

            $unitpostvar = $this->getUnitPostVariableName($name);

            if ($this->isPostVarSet($unitpostvar)) {
                $answers[$name . self::VAR_UNIT] = $this->getPostValue($unitpostvar);
            }

            return true;
        }

        return false;
    }

    public function generateHtml() : string
    {
        $output = $this->configuration->getFormula();

        foreach (range(1, count($this->question->getAnswerOptions())) as $resindex) {
            $output = $this->createResult($resindex, $output, $this->question->getPlayConfiguration()->getScoringConfiguration()->getUnits());
        }

        $varindex = 1;
        foreach ($this->configuration->getVariables() as $variable) {
            $output = $this->createVariable($varindex, $output, $variable);
            $varindex += 1;
        }

        return $output;
    }

    private function createResult(int $index, string $output, ?array $units) : string
    {
        $name = '$r' . $index;

        $html = sprintf(
            '<input type="text" length="20" name="%s" value="%s" %s/>%s',
            $this->getPostVariableName($name),
            $this->getResultValue($name) ?? '',
            $this->is_disabled ? 'disabled="disabled"' : '',
            !empty($units) ? $this->createUnitSelection($units, $name) : '');

        return str_replace($name, $html, $output);
    }

    private function getResultValue(string $name) : ?string
    {
        if (is_null($this->answer) ||
            is_null($this->answer->getResults()) ||
            !array_key_exists($name, $this->answer->getResults()))
        {
            return null;
        }

        return $this->answer->getResults()[$name];
    }

    private function getVariableValue(string $name) : ?string
    {
        if (is_null($this->answer) ||
            is_null($this->answer->getVariables()) ||
            !array_key_exists($name, $this->answer->getVariables()))
        {
            return null;
        }

        return $this->answer->getVariables()[$name];
    }

    private function createUnitSelection(array $units, string $name) : string
    {
        return sprintf(
            '<select name="%s">%s</select>',
            $this->getUnitPostVariableName($name),
            implode(array_map(function ($unit) use ($name) {
                return sprintf(
                    '<option value="%1$s" %2$s>%1$s</option>',
                    $unit,
                    $this->getResultValue($name . self::VAR_UNIT) === $unit ? 'selected="selected"' : ''
                );
            }, $units))
        );
    }

    private function createVariable(int $index, string $output, FormulaScoringVariable $def) : string
    {
        $name = '$v' . $index;

        $html = sprintf(
            '<input type="hidden" name="%1$s" value="%2$s" />%2$s %3$s',
            $this->getPostVariableName($name),
            $this->getVariableValue($name) ?? $this->question->getPlayConfiguration()->getScoringConfiguration()->generateVariableValue($def),
            $def->getUnit()
        );

        return str_replace($name, $html, $output);
    }

    private function getPostVariableName(string $name) : string
    {
        return $name . $this->question->getId()->toString();
    }

    private function getUnitPostVariableName(string $name) : string
    {
        return $name . $this->question->getId()->toString() . self::VAR_UNIT;
    }

    public function isComplete() : bool
    {
        return true;
    }
}
