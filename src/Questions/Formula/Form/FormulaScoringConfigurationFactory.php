<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\Formula\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\FormulaScoringVariable;
use ilTextInputGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTextAreaInputGUI;

/**
 * Class FormulaScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_FORMULA = 'fs_formula';
    const VAR_UNITS = 'fs_units';
    const VAR_PRECISION = 'fs_precision';
    const VAR_TOLERANCE = 'fs_tolerance';
    const VAR_RESULT_TYPE = 'fs_type';
    const VAR_VARIABLES = 'fs_variables';

    /**
     * @var AsqTableInput
     */
    private $variables_table;

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $formula = new ilTextAreaInputGUI($this->language->txt('asq_label_formula'), self::VAR_FORMULA);
        $formula->setInfo('<br /><input type="button" value="' . $this->language->txt('asq_parse_question') . '" class="js_parse_question btn btn-default" />');
        $formula->setRequired(true);
        $fields[] = $formula;

        $units = new ilTextInputGUI($this->language->txt('asq_label_units'), self::VAR_UNITS);
        $units->setInfo($this->language->txt('asq_description_units'));
        $fields[self::VAR_UNITS] = $units;

        $precision = new ilNumberInputGUI($this->language->txt('asq_label_precision'), self::VAR_PRECISION);
        $precision->setInfo($this->language->txt('asq_description_precision'));
        $precision->setRequired(true);
        $fields[self::VAR_PRECISION] = $precision;

        $tolerance = new ilNumberInputGUI($this->language->txt('asq_label_tolerance'), self::VAR_TOLERANCE);
        $tolerance->setInfo($this->language->txt('asq_description_tolerance'));
        $tolerance->setSuffix('%');
        $fields[self::VAR_TOLERANCE] = $tolerance;

        $result_type = new ilRadioGroupInputGUI($this->language->txt('asq_label_result_type'), self::VAR_RESULT_TYPE);
        $result_type->setRequired(true);
        $result_type->addOption(new ilRadioOption(
            $this->language->txt('asq_label_result_all'),
            FormulaScoringConfiguration::TYPE_ALL,
            $this->language->txt('asq_description_result_all')
        ));
        $result_type->addOption(new ilRadioOption(
            $this->language->txt('asq_label_result_decimal'),
            FormulaScoringConfiguration::TYPE_DECIMAL,
            $this->language->txt('asq_description_result_decimal')
        ));
        $result_type->addOption(new ilRadioOption(
            $this->language->txt('asq_label_result_fraction'),
            FormulaScoringConfiguration::TYPE_FRACTION,
            $this->language->txt('asq_description_result_fraction')
        ));
        $result_type->addOption(new ilRadioOption(
            $this->language->txt('asq_label_result_coprime_fraction'),
            FormulaScoringConfiguration::TYPE_COPRIME_FRACTION,
            $this->language->txt('asq_description_result_coprime_fraction')
        ));
        $fields[self::VAR_RESULT_TYPE] = $result_type;

        $fields[self::VAR_VARIABLES] = $this->getVariablesTable($value);

        if ($value !== null) {
            $formula->setValue($value->getFormula());
            $units->setValue($value->getUnitString());
            $precision->setValue($value->getPrecision());
            $tolerance->setValue($value->getTolerance());
            $result_type->setValue($value->getResultType());
        } else {
            $tolerance->setValue(0);
            $result_type->setValue(FormulaScoringConfiguration::TYPE_ALL);
        }

        return $fields;
    }

    /**
     * @return FormulaScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        $variables = [];
        $raw_variables = $this->getVariablesTable($this->getDefaultValue())->readValues();

        foreach ($raw_variables as $raw_variable) {
            $variables[] = FormulaScoringVariable::create(
                floatval($raw_variable[FormulaScoringVariable::VAR_MIN]),
                floatval($raw_variable[FormulaScoringVariable::VAR_MAX]),
                $raw_variable[FormulaScoringVariable::VAR_UNIT],
                empty($raw_variable[FormulaScoringVariable::VAR_MULTIPLE_OF]) ?
                    null:
                    floatval($raw_variable[FormulaScoringVariable::VAR_MULTIPLE_OF])
            );
        }

        return FormulaScoringConfiguration::create(
            $this->readString(self::VAR_FORMULA),
            $this->readString(self::VAR_UNITS),
            $this->readInt(self::VAR_PRECISION),
            $this->readFloat(self::VAR_TOLERANCE),
            $this->readInt(self::VAR_RESULT_TYPE),
            $variables
        );
    }

    /**
     * @param FormulaScoringConfiguration $value
     * @return AsqTableInput
     */
    private function getVariablesTable(FormulaScoringConfiguration $value) : AsqTableInput
    {
        if (is_null($this->variables_table)) {
            $this->variables_table = new AsqTableInput(
                $this->language->txt('asq_label_variables'),
                self::VAR_VARIABLES,
                $value->getVariablesArray(),
                [
                    new AsqTableInputFieldDefinition(
                        $this->language->txt('asq_header_min'),
                        AsqTableInputFieldDefinition::TYPE_TEXT,
                        FormulaScoringVariable::VAR_MIN
                    ),
                    new AsqTableInputFieldDefinition(
                        $this->language->txt('asq_header_max'),
                        AsqTableInputFieldDefinition::TYPE_TEXT,
                        FormulaScoringVariable::VAR_MAX
                    ),
                    new AsqTableInputFieldDefinition(
                        $this->language->txt('asq_header_unit'),
                        AsqTableInputFieldDefinition::TYPE_TEXT,
                        FormulaScoringVariable::VAR_UNIT
                    ),
                    new AsqTableInputFieldDefinition(
                        $this->language->txt('asq_header_multiple_of'),
                        AsqTableInputFieldDefinition::TYPE_TEXT,
                        FormulaScoringVariable::VAR_MULTIPLE_OF
                    )
                ],
                [
                    AsqTableInput::OPTION_HIDE_ADD_REMOVE => true
                ]
            );
            $this->variables_table->setRequired(true);
        }

        return $this->variables_table;
    }

    /**
     * @return FormulaScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return FormulaScoringConfiguration::create();
    }
}
