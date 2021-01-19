<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringVariable;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

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
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $formula = $this->factory->input()->field()->text(
            $this->language->txt('asq_label_formula'),
            '<br /><input type="button" value="' . $this->language->txt('asq_parse_question') . '" class="js_parse_question btn btn-default" />'
        );

        $units = $this->factory->input()->field()->text(
            $this->language->txt('asq_label_units'),
            $this->language->txt('asq_description_units')
        );

        $precision = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_precision'),
            $this->language->txt('asq_description_precision')
        );

        $tolerance = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_tolerance'),
            $this->language->txt('asq_description_tolerance')
        );

        $result_type = $this->factory->input()->field()->radio($this->language->txt('asq_label_result_type'))
            ->withOption(
                strval(FormulaScoringConfiguration::TYPE_ALL),
                $this->language->txt('asq_label_result_all'),
                $this->language->txt('asq_description_result_all')
            )
            ->withOption(
                strval(FormulaScoringConfiguration::TYPE_DECIMAL),
                $this->language->txt('asq_label_result_decimal'),
                $this->language->txt('asq_description_result_decimal')
            )
            ->withOption(
                strval(FormulaScoringConfiguration::TYPE_FRACTION),
                $this->language->txt('asq_label_result_fraction'),
                $this->language->txt('asq_description_result_fraction')
            )
            ->withOption(
                strval(FormulaScoringConfiguration::TYPE_COPRIME_FRACTION),
                $this->language->txt('asq_label_result_coprime_fraction'),
                $this->language->txt('asq_description_result_coprime_fraction')
            );

        $variables_table = $this->asq_ui->getAsqTableInput(
            $this->language->txt('asq_label_variables'),
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
            ]
        )->withOptions(
            [
                AsqTableInput::OPTION_HIDE_ADD_REMOVE => true
            ]
        );



        if ($value !== null) {
            $formula = $formula->withValue(strval($value->getFormula()));
            $units = $units->withValue(strval($value->getUnitString()));
            $precision = $precision->withValue($value->getPrecision());
            $tolerance = $tolerance->withValue($value->getTolerance());
            $result_type = $result_type->withValue(
                strval($value->getResultType() ?? strval(FormulaScoringConfiguration::TYPE_ALL))
            );
            $variables_table = $variables_table->withValue($value->getVariablesArray());
        } else {
            $tolerance = $tolerance->withValue("0");
            $result_type = $result_type->withValue(strval(FormulaScoringConfiguration::TYPE_ALL));
        }

        $fields[self::VAR_FORMULA] = $formula;
        $fields[self::VAR_UNITS] = $units;
        $fields[self::VAR_PRECISION] = $precision;
        $fields[self::VAR_TOLERANCE] = $tolerance;
        $fields[self::VAR_RESULT_TYPE] = $result_type;
        $fields[self::VAR_VARIABLES] = $variables_table;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return FormulaScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        $variables = [];

        foreach ($postdata[self::VAR_VARIABLES] as $raw_variable) {
            $variables[] = new FormulaScoringVariable(
                $this->readFloat($raw_variable[FormulaScoringVariable::VAR_MIN]),
                $this->readFloat($raw_variable[FormulaScoringVariable::VAR_MAX]),
                $raw_variable[FormulaScoringVariable::VAR_UNIT],
                empty($raw_variable[FormulaScoringVariable::VAR_MULTIPLE_OF]) ?
                    null:
                    $this->readFloat($raw_variable[FormulaScoringVariable::VAR_MULTIPLE_OF])
            );
        }

        return new FormulaScoringConfiguration(
            $postdata[self::VAR_FORMULA],
            $postdata[self::VAR_UNITS],
            $this->readInt($postdata[self::VAR_PRECISION]),
            $this->readFloat($postdata[self::VAR_TOLERANCE]),
            $this->readInt($postdata[self::VAR_RESULT_TYPE]),
            $variables
        );
    }

    /**
     * @return FormulaScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new FormulaScoringConfiguration();
    }
}
