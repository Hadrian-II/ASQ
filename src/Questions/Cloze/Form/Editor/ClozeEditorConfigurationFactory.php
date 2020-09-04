<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Form\Editor;

use ILIAS\DI\UIServices;
use ILIAS\UI\Renderer;
use ILIAS\UI\Component\Input\Field\Input;
use ILIAS\UI\Component\Input\Field\Section;
use ilLanguage;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\AsqGateway;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

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
    use InputHandlingTrait;

    const VAR_CLOZE_TEXT = 'cze_text';
    const VAR_GAP_SIZE = 'cze_gap_size';
    const VAR_TEXT_METHOD = 'cze_text_method';
    const VAR_GAP = 'cze_gap';
    const VAR_GAP_TYPE = 'cze_gap_type';
    const VAR_GAP_DEFINITION = 'cze_gap_definition';
    const VAR_GAP_ITEMS = 'cze_gap_items';
    const VAR_GAP_VALUE = 'cze_gap_value';
    const VAR_GAP_UPPER = 'cze_gap_upper';
    const VAR_GAP_LOWER = 'cze_gap_lower';
    const VAR_GAP_POINTS = 'cze_gap_points';

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @param ilLanguage $language
     * @param UIServices $ui
     */
    public function __construct(ilLanguage $language, UIServices $ui)
    {
        $this->renderer = $ui->renderer();

        parent::__construct($language, $ui);
    }

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $cloze_text = $this->factory->input()->field()->text(
            $this->language->txt('asq_label_cloze_text'),
            $this->language->txt('asq_description_cloze_text') .
            '<br /><input type="button"
               value="' . $this->language->txt('asq_parse_question') . '"
               class="js_parse_cloze_question btn btn-default" />' .
               self::createTemplates());

        if ($value !== null) {
            $cloze_text = $cloze_text->withValue($value->getClozeText());
        }

        $fields[self::VAR_CLOZE_TEXT] = $cloze_text;

        $gaps = $_SERVER['REQUEST_METHOD'] !== 'POST' ? $value->getGaps() : $this->readGapConfigs($_POST);

        for ($i = 1; $i <= count($gaps); $i += 1) {
            $fields[$i . self::VAR_GAP] = $this->createGapFields($this->getGapType($gaps[$i - 1]), $gaps[$i - 1]);
        }

        return $fields;
    }

    /**
     * @param ClozeGapConfiguration $config
     * @return string
     */
    private function getGapType(ClozeGapConfiguration $config) : string
    {
        switch (get_class($config)) {
            case TextGapConfiguration::class:
                return ClozeGapConfiguration::TYPE_TEXT;
            case SelectGapConfiguration::class:
                return ClozeGapConfiguration::TYPE_DROPDOWN;
            case NumericGapConfiguration::class:
                return ClozeGapConfiguration::TYPE_NUMBER;
        }
    }

    /**
     * @param string $type
     * @param ClozeGapConfiguration $gap
     * @return Section
     */
    private function createGapFields(string $type, ?ClozeGapConfiguration $gap = null) : Section
    {
        $gap_type = $this->factory->input()->field()->select(
            $this->language->txt('asq_label_gap_type'),
            [
                ClozeGapConfiguration::TYPE_DROPDOWN => $this->language->txt('asq_label_gap_type_dropdown'),
                ClozeGapConfiguration::TYPE_TEXT => $this->language->txt('asq_label_gap_type_text'),
                ClozeGapConfiguration::TYPE_NUMBER => $this->language->txt('asq_label_gap_type_number')
            ],
            sprintf('<a class="btn btn-default btn-sm js_delete_button">%s</a>', $this->language->txt('asq_label_btn_delete_gap'))
        )->withValue($type);

        $fields[self::VAR_GAP_TYPE] =$gap_type;

        switch($type) {
            case ClozeGapConfiguration::TYPE_DROPDOWN:
                $fields += $this->createTextGapFields($gap);
                break;
            case ClozeGapConfiguration::TYPE_TEXT:
                $fields += $this->createSelectGapFields($gap);
                break;
            case ClozeGapConfiguration::TYPE_NUMBER:
                $fields += $this->createNumberGapFields($gap);
                break;
        }

        $section = $this->factory->input()->field()->section(
            $fields,
            $this->language->txt('asq_header_feedback_answers'));

        return $section;
    }

    /**
     * @param ?TextGapConfiguration $gap
     * @return Input[]
     */
    private function createTextGapFields(?TextGapConfiguration $gap = null) : array
    {
        $fields = [];

        $gap_items = AsqGateway::get()->ui()->getAsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $this->getClozeGapItemFieldDefinitions());

        // needs value to render in template
        $field_size = $this->factory->input()->field()->text($this->language->txt('asq_textfield_size'))->withValue('');

        $text_scoring = new TextScoring($this->language);
        $text_method = $text_scoring->getScoringTypeSelectionField($this->factory)->withValue('');

        if (! is_null($gap)) {
            $gap_items = $gap_items->withValue($gap->getItemsArray());
            $field_size = $field_size->withValue($gap->getFieldLength());
            $text_method = $text_method->withValue($gap->getMatchingMethod());
        }

        $fields[self::VAR_GAP_ITEMS] = $gap_items;
        $fields[self::VAR_GAP_SIZE] = $field_size;
        $fields[self::VAR_TEXT_METHOD] = $text_method;

        return $fields;
    }

    /**
     * @param ?SelectGapConfiguration $gap
     * @return Input[]
     */
    private function createSelectGapFields(?SelectGapConfiguration $gap = null) : array
    {
        $fields = [];

        $gap_items = AsqGateway::get()->ui()->getAsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $this->getClozeGapItemFieldDefinitions());

        if (! is_null($gap)) {
            $gap_items = $gap_items->withValue($gap->getItemsArray());
        }

        $fields[self::VAR_GAP_ITEMS] = $gap_items;

        return $fields;
    }

    /**
     * @param NumericGapConfiguration $gap
     * @return Input[]
     */
    private function createNumberGapFields(?NumericGapConfiguration $gap = null) : array
    {
        $fields = [];

        // needs value to render in template
        $value = $this->factory->input()->field()->text($this->language->txt('asq_correct_value'))->withValue('');
        $upper = $this->factory->input()->field()->text($this->language->txt('asq_label_upper_bound'))->withValue('');
        $lower = $this->factory->input()->field()->text($this->language->txt('asq_label_lower_bound'))->withValue('');
        $points = $this->factory->input()->field()->text($this->language->txt('asq_header_points'))->withValue('');
        $field_size = $this->factory->input()->field()->text($this->language->txt('asq_textfield_size'))->withValue('');

        if (! is_null($gap)) {
            $value = $value->withValue($gap->getValue());
            $upper = $upper->withValue($gap->getUpper());
            $lower = $lower->withValue($gap->getLower());
            $points = $points->withValue($gap->getPoints());
            $field_size = $field_size->withValue($gap->getFieldLength());
        }

        $fields[self::VAR_GAP_VALUE] = $value;
        $fields[self::VAR_GAP_UPPER] = $upper;
        $fields[self::VAR_GAP_LOWER] = $lower;
        $fields[self::VAR_GAP_POINTS] = $points;
        $fields[self::VAR_GAP_SIZE] = $field_size;

        return $fields;
    }

    /**
     * @return string
     */
    private function createTemplates() : string
    {
        return sprintf(
            '<div class="cloze_template" style="display: none;">
                            <div class="text">%s</div>
                            <div class="number">%s</div>
                            <div class="select">%s</div>
                        </div>',
            $this->renderer->render($this->createGapFields(ClozeGapConfiguration::TYPE_TEXT)),
            $this->renderer->render($this->createGapFields(ClozeGapConfiguration::TYPE_NUMBER)),
            $this->renderer->render($this->createGapFields(ClozeGapConfiguration::TYPE_DROPDOWN))
        );
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
                ClozeGapItem::VAR_TEXT
            ),
            new AsqTableInputFieldDefinition(
                $this->language->txt('asq_header_points'),
                AsqTableInputFieldDefinition::TYPE_TEXT,
                ClozeGapItem::VAR_POINTS
            )
        ];
    }

    /**
     * @param array $postdata
     * @return ClozeEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return ClozeEditorConfiguration::create(
            $this->readString($postdata[self::VAR_CLOZE_TEXT]),
            []//$this->readGapConfigs($postdata)
        );
    }

    /**
     * @param array $postdata
     * @return ClozeGapConfiguration[]
     */
    public function readGapConfigs(array $postdata) : array
    {
        $i = 1;
        $gap_configs = [];

        while ($this->isPostVarSet($i . self::VAR_GAP_TYPE)) {
            $istr = strval($i);

            if ($this->readString($istr . self::VAR_GAP_TYPE) === ClozeGapConfiguration::TYPE_TEXT) {
                $gap_configs[] = self::readTextGapConfiguration($istr);
            } elseif ($this->readString($istr . self::VAR_GAP_TYPE) === ClozeGapConfiguration::TYPE_DROPDOWN) {
                $gap_configs[] = self::readSelectGapConfiguration($istr);
            } elseif ($this->readString($istr . self::VAR_GAP_TYPE) === ClozeGapConfiguration::TYPE_NUMBER) {
                $gap_configs[] = self::readNumericGapConfiguration($istr);
            }

            $i += 1;
        }

        return $gap_configs;
    }

    /**
     * @param string $i
     * @param array $postdata
     * @return NumericGapConfiguration
     */
    private function readNumericGapConfiguration(string $i, array $postdata) : NumericGapConfiguration
    {
        return NumericGapConfiguration::Create(
            $this->readFloat($i . self::VAR_GAP_VALUE),
            $this->readFloat($i . self::VAR_GAP_UPPER),
            $this->readFloat($i . self::VAR_GAP_LOWER),
            $this->readFloat($i . self::VAR_GAP_POINTS),
            $this->readInt($i . self::VAR_GAP_SIZE)
        );
    }

    /**
     * @param string $i
     * @param array $postdata
     * @return SelectGapConfiguration
     */
    private function readSelectGapConfiguration(string $i, array $postdata) : SelectGapConfiguration
    {
        $gap_items = new AsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $i . self::VAR_GAP_ITEMS,
            [],
            $this->getClozeGapItemFieldDefinitions()
        );

        return SelectGapConfiguration::Create(
            array_map(
                function ($raw_item) {
                    return ClozeGapItem::create(
                        $raw_item[ClozeGapItem::VAR_TEXT],
                        floatval($raw_item[ClozeGapItem::VAR_POINTS])
                    );
                },
                $gap_items->readValues()
            )
        );
    }

    /**
     * @param string $i
     * @param array $postdata
     * @return TextGapConfiguration
     */
    private function readTextGapConfiguration(string $i, array $postdata) : TextGapConfiguration
    {
        $gap_items = new AsqTableInput(
            $this->language->txt('asq_label_gap_items'),
            $i . self::VAR_GAP_ITEMS,
            [],
            $this->getClozeGapItemFieldDefinitions()
        );

        return TextGapConfiguration::Create(
            array_map(
                function ($raw_item) {
                    return ClozeGapItem::create(
                        $raw_item[ClozeGapItem::VAR_TEXT],
                        floatval($raw_item[ClozeGapItem::VAR_POINTS])
                    );
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
