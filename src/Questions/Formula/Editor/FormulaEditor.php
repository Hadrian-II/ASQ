<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Questions\Formula\FormulaAnswer;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringVariable;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class FormulaEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaEditor extends AbstractEditor
{
    use InputHandlingTrait;

    const VAR_UNIT = 'fe_unit';

    /**
     * @var FormulaScoringConfiguration
     */
    private $configuration;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();

        parent::__construct($question);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::readAnswer()
     */
    public function readAnswer() : AbstractValueObject
    {
        $answers = [];
        $index = 1;
        $continue = true;
        while ($continue) {
            $continue = false;

            $continue |= $this->processVar('$v' . $index, $answers);
            $continue |= $this->processVar('$r' . $index, $answers);
            $index += 1;
        }

        return FormulaAnswer::create($answers);
    }

    /**
     * @param string $name
     * @param array $answers
     * @return bool
     */
    private function processVar(string $name, array &$answers) : bool
    {
        $value = $this->readString($this->getPostVariable($name));

        if (!empty($value)) {
            $answers[$name] = $value;

            $unit = $this->readString($this->getUnitPostVariable($name));

            if (!empty($unit)) {
                $answers[$name . self::VAR_UNIT] = $unit;
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::generateHtml()
     */
    public function generateHtml() : string
    {
        $output = $this->configuration->getFormula();

        foreach (range(1, count($this->question->getAnswerOptions()->getOptions())) as $resindex) {
            $output = $this->createResult($resindex, $output, $this->question->getPlayConfiguration()->getScoringConfiguration()->getUnits());
        }

        $varindex = 1;
        foreach ($this->configuration->getVariables() as $variable) {
            $output = $this->createVariable($varindex, $output, $variable);
            $varindex += 1;
        }

        return $output;
    }

    /**
     * @param int $index
     * @param string $output
     * @param string $units
     * @return string
     */
    private function createResult(int $index, string $output, ?array $units) : string
    {
        $name = '$r' . $index;

        $html = sprintf('<input type="text" length="20" name="%s" value="%s" />%s', $this->getPostVariable($name), !is_null($this->answer) ? $this->answer->getValues()[$name] : '', !empty($units) ? $this->createUnitSelection($units, $name) : '');

        return str_replace($name, $html, $output);
    }

    /**
     * @param string $units
     * @param string $name
     * @return string
     */
    private function createUnitSelection(array $units, string $name) : string
    {
        return sprintf(
            '<select name="%s">%s</select>',
            $this->getUnitPostVariable($name),
            implode(array_map(function ($unit) use ($name) {
                return sprintf(
                               '<option value="%1$s" %2$s>%1$s</option>',
                               $unit,
                               !is_null($this->answer) && $this->answer->getValues()[$name . self::VAR_UNIT] === $unit ? 'selected="selected"' : ''
                           );
            }, $units))
        );
    }

    /**
     * @param int $index
     * @param string $output
     * @param FormulaScoringVariable $def
     * @return string
     */
    private function createVariable(int $index, string $output, FormulaScoringVariable $def) : string
    {
        $name = '$v' . $index;

        $html = sprintf(
            '<input type="hidden" name="%1$s" value="%2$s" />%2$s %3$s',
            $this->getPostVariable($name),
            !is_null($this->answer) ?
                $this->answer->getValues()[$name] :
                $this->question->getPlayConfiguration()->getScoringConfiguration()->generateVariableValue($def),
            $def->getUnit()
        );

        return str_replace($name, $html, $output);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getPostVariable(string $name) : string
    {
        return $name . $this->question->getId();
    }

    /**
     * @param string $name
     * @return string
     */
    private function getUnitPostVariable(string $name) : string
    {
        return $name . $this->question->getId() . self::VAR_UNIT;
    }

    /**
     * @return string
     */
    public static function getDisplayDefinitionClass() : string
    {
        return EmptyDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        return true;
    }
}
