<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Cloze\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\ClozeGapConfiguration;
use srag\asq\Questions\Cloze\ClozeGapItem;
use srag\asq\Questions\Cloze\NumericGapConfiguration;
use srag\asq\Questions\Cloze\SelectGapConfiguration;
use srag\asq\Questions\Cloze\TextGapConfiguration;
use ilFormSectionHeaderGUI;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilNumberInputGUI;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use ilPropertyFormGUI;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class ClozeEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeEditorConfigurationFactory extends AbstractObjectFactory
{
    use PostAccess;

    const VAR_CLOZE_TEXT = 'cze_text';
    const VAR_GAP_SIZE = 'cze_gap_size';
    const VAR_TEXT_METHOD = 'cze_text_method';
    const VAR_GAP_TYPE = 'cze_gap_type';
    const VAR_GAP_ITEMS = 'cze_gap_items';
    const VAR_GAP_VALUE = 'cze_gap_value';
    const VAR_GAP_UPPER = 'cze_gap_upper';
    const VAR_GAP_LOWER = 'cze_gap_lower';
    const VAR_GAP_POINTS = 'cze_gap_points';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $cloze_text = new ilTextAreaInputGUI($this->language->txt('asq_label_cloze_text'), self::VAR_CLOZE_TEXT);
        $cloze_text->setRequired(true);
        //TODO? template addidtion is rather hacky
        $cloze_text->setInfo($this->language->txt('asq_description_cloze_text') .
            '<br /><input type="button"
                                           value="' . $this->language->txt('asq_parse_question') . '"
                                           class="js_parse_cloze_question btn btn-default" />' .
            self::createTemplates());
        $fields[self::VAR_CLOZE_TEXT] = $cloze_text;

        $gaps = $_SERVER['REQUEST_METHOD'] !== 'POST' ? $value->getGaps() : $this->readGapConfigs();

        for ($i = 1; $i <= count($gaps); $i += 1) {
            $fields = array_merge($fields, $this->createGapFields($i, $gaps[$i - 1]));
        }

        if ($value !== null) {
            $cloze_text->setValue($value->getClozeText());
        }

        return $fields;
    }

    /**
     * @param int $index
     * @param ClozeGapConfiguration $gap
     * @return array
     */
    private function createGapFields(int $index, ClozeGapConfiguration $gap = null) : array
    {
        $fields = [];

        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle('');
        $fields[] = $spacer;

        $gap_type = new ilSelectInputGUI($this->language->txt('asq_label_gap_type'), $index . self::VAR_GAP_TYPE);
        $gap_type->setOptions([
            ClozeGapConfiguration::TYPE_DROPDOWN => $this->language->txt('asq_label_gap_type_dropdown'),
            ClozeGapConfiguration::TYPE_TEXT => $this->language->txt('asq_label_gap_type_text'),
            ClozeGapConfiguration::TYPE_NUMBER => $this->language->txt('asq_label_gap_type_number')
        ]);
        $gap_type->setInfo(sprintf('<a class="btn btn-default btn-sm js_delete_button">%s</a>', $this->language->txt('asq_label_btn_delete_gap')));
        $fields[$index . self::VAR_GAP_TYPE] = $gap_type;

        if (!is_null($gap)) {
            if (get_class($gap) === TextGapConfiguration::class) {
                $fields = array_merge($fields, self::createTextGapFields($gap, $index));
                $gap_type->setValue(ClozeGapConfiguration::TYPE_TEXT);
            }
            else if (get_class($gap) === SelectGapConfiguration::class) {
                $fields = array_merge($fields, self::createSelectGapFields($gap, $index));
                $gap_type->setValue(ClozeGapConfiguration::TYPE_DROPDOWN);
            }
            else if (get_class($gap) === NumericGapConfiguration::class) {
                $fields = array_merge($fields, self::createNumberGapFields($gap, $index));
                $gap_type->setValue(ClozeGapConfiguration::TYPE_NUMBER);
            }
        }

        return $fields;
    }

    /**
     * @param TextGapConfiguration $gap
     * @param int $index
     * @return array
     */
    private function createTextGapFields(TextGapConfiguration $gap, int $index) : array
    {
        $fields = [];

        $items = is_null($gap) ? [] : $gap->getItemsArray();

        $gap_items = new AsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $index . self::VAR_GAP_ITEMS,
            $items,
            $this->getClozeGapItemFieldDefinitions());
        $gap_items->setRequired(true);

        $fields[$index .self::VAR_GAP_ITEMS] = $gap_items;

        $field_size = new ilNumberInputGUI(
            $this->language->txt('asq_textfield_size'),
            $index . self::VAR_GAP_SIZE);
        $field_size->setValue($gap->getFieldLength());
        $fields[$index . self::VAR_GAP_SIZE] = $field_size;

        $text_scoring = new TextScoring($this->language);
        $text_method = $text_scoring->getScoringTypeSelectionField($index . self::VAR_TEXT_METHOD);
        $text_method->setValue($gap->getMatchingMethod());
        $fields[$index . self::VAR_TEXT_METHOD] = $text_method;

        return $fields;
    }

    /**
     * @param SelectGapConfiguration $gap
     * @param int $index
     * @return \srag\asq\UserInterface\Web\Fields\AsqTableInput[]
     */
    private function createSelectGapFields(SelectGapConfiguration $gap, int $index) : array
    {
        $fields = [];

        $items = is_null($gap) ? [] : $gap->getItemsArray();

        $gap_items = new AsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $index . self::VAR_GAP_ITEMS,
            $items,
            $this->getClozeGapItemFieldDefinitions());
        $gap_items->setRequired(true);
        $fields[$index .self::VAR_GAP_ITEMS] = $gap_items;

        return $fields;
    }

    /**
     * @param NumericGapConfiguration $gap
     * @param int $index
     * @return array
     */
    private function createNumberGapFields(NumericGapConfiguration $gap, int $index) : array
    {
        $fields = [];

        $value = new ilNumberInputGUI($this->language->txt('asq_correct_value'), $index . self::VAR_GAP_VALUE);
        $value->setRequired(true);
        $value->allowDecimals(true);
        $value->setValue($gap->getValue());
        $fields[$index . self::VAR_GAP_VALUE] = $value;

        $upper = new ilNumberInputGUI($this->language->txt('asq_label_upper_bound'), $index . self::VAR_GAP_UPPER);
        $upper->setRequired(true);
        $upper->allowDecimals(true);
        $upper->setValue($gap->getUpper());
        $fields[$index . self::VAR_GAP_UPPER]= $upper;

        $lower = new ilNumberInputGUI($this->language->txt('asq_label_lower_bound'), $index . self::VAR_GAP_LOWER);
        $lower->setRequired(true);
        $lower->allowDecimals(true);
        $lower->setValue($gap->getLower());
        $fields[$index . self::VAR_GAP_LOWER] = $lower;

        $points = new ilNumberInputGUI($this->language->txt('asq_header_points'), $index . self::VAR_GAP_POINTS);
        $points->setRequired(true);
        $points->setValue($gap->getPoints());
        $fields[$index . self::VAR_GAP_POINTS] = $points;

        $field_size = new ilNumberInputGUI(
            $this->language->txt('asq_textfield_size'),
            $index . self::VAR_GAP_SIZE);
        $field_size->setValue($gap->getFieldLength());
        $fields[$index . self::VAR_GAP_SIZE] = $field_size;

        return $fields;
    }

    /**
     * @return string
     */
    private function createTemplates() : string
    {
        return sprintf('<div class="cloze_template" style="display: none;">
                            <div class="text">%s</div>
                            <div class="number">%s</div>
                            <div class="select">%s</div>
                        </div>',
            $this->createTemplate(TextGapConfiguration::Create()),
            $this->createTemplate(NumericGapConfiguration::Create()),
            $this->createTemplate(SelectGapConfiguration::Create()));
    }

    /**
     * @param ClozeGapConfiguration $config
     * @return string
     */
    private function createTemplate(ClozeGapConfiguration $config) : string
    {
        $fields = self::createGapFields(0, $config);

        $template_form = new ilPropertyFormGUI();

        foreach ($fields as $field) {
            $template_form->addItem($field);
        }

        return '<div class="ilFormHeader"></div>' . $template_form->getHTML();
    }

    /**
     * @return array
     */
    private function getClozeGapItemFieldDefinitions() : array
    {
        return [
            new AsqTableInputFieldDefinition(
                $this->language->txt('asq_header_value'),
                AsqTableInputFieldDefinition::TYPE_TEXT,
                ClozeGapItem::VAR_TEXT),
            new AsqTableInputFieldDefinition(
                $this->language->txt('asq_header_points'),
                AsqTableInputFieldDefinition::TYPE_TEXT,
                ClozeGapItem::VAR_POINTS)
        ];
    }

    /**
     * @return ClozeEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return ClozeEditorConfiguration::create(
            $this->readString(self::VAR_CLOZE_TEXT),
            $this->readGapConfigs());
    }

    /**
     * @return ClozeGapConfiguration[]
     */
    public function readGapConfigs() : array
    {
        $i = 1;
        $gap_configs = [];

        while ($this->isPostVarSet($i . self::VAR_GAP_TYPE)) {
            $istr = strval($i);

            if ($this->readString($istr . self::VAR_GAP_TYPE) === ClozeGapConfiguration::TYPE_TEXT) {
                $gap_configs[] = self::readTextGapConfiguration($istr);
            }
            else if ($this->readString($istr . self::VAR_GAP_TYPE) === ClozeGapConfiguration::TYPE_DROPDOWN) {
                $gap_configs[] = self::readSelectGapConfiguration($istr);
            }
            else if ($this->readString($istr . self::VAR_GAP_TYPE) === ClozeGapConfiguration::TYPE_NUMBER) {
                $gap_configs[] = self::readNumericGapConfiguration($istr);
            }

            $i += 1;
        }

        return $gap_configs;
    }

    /**
     * @param string $i
     * @return NumericGapConfiguration
     */
    private function readNumericGapConfiguration(string $i) : NumericGapConfiguration
    {
        return NumericGapConfiguration::Create(
            $this->readFloat($i . self::VAR_GAP_VALUE),
            $this->readFloat($i . self::VAR_GAP_UPPER),
            $this->readFloat($i . self::VAR_GAP_LOWER),
            $this->readFloat($i . self::VAR_GAP_POINTS),
            $this->readInt($i . self::VAR_GAP_SIZE));
    }

    /**
     * @param string $i
     * @return SelectGapConfiguration
     */
    private function readSelectGapConfiguration(string $i) : SelectGapConfiguration
    {
        $gap_items = new AsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $i . self::VAR_GAP_ITEMS,
            [],
            $this->getClozeGapItemFieldDefinitions());

        return SelectGapConfiguration::Create(
            array_map(
                function ($raw_item)
                {
                    return ClozeGapItem::create(
                        $raw_item[ClozeGapItem::VAR_TEXT],
                        floatval($raw_item[ClozeGapItem::VAR_POINTS]));
                },
                $gap_items->readValues()
            )
        );
    }

    /**
     * @param string $i
     * @return TextGapConfiguration
     */
    private function readTextGapConfiguration(string $i) : TextGapConfiguration
    {
        $gap_items = new AsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $i . self::VAR_GAP_ITEMS,
            [],
            $this->getClozeGapItemFieldDefinitions());

        return TextGapConfiguration::Create(
            array_map(
                function ($raw_item)
                {
                    return ClozeGapItem::create(
                        $raw_item[ClozeGapItem::VAR_TEXT],
                        floatval($raw_item[ClozeGapItem::VAR_POINTS]));
                },
                $gap_items->readValues()
            ),
            $this->readInt($i . self::VAR_GAP_SIZE),
            $this->readInt($i . self::VAR_TEXT_METHOD)
        );
    }

    /**
     * @return ClozeEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return ClozeEditorConfiguration::create('', []);
    }
}