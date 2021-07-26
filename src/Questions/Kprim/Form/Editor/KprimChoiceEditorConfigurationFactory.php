<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form\Editor;

use ILIAS\UI\Component\Input\Field\SwitchableGroup;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Kprim\Editor\Data\KprimChoiceEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class KprimChoiceEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_SHUFFLE_ANSWERS = 'kce_shuffle';
    const VAR_SINGLE_LINE = 'kce_single_line';
    const VAR_THUMBNAIL_SIZE = 'kce_thumbnail';
    const VAR_LABEL_TYPE = 'kcd_label';
    const VAR_LABEL_TRUE = 'kce_label_true';
    const VAR_LABEL_FALSE = 'kce_label_false';

    const STR_TRUE = "True";
    const STR_FALSE = "False";

    const LABEL_RIGHT_WRONG = "label_rw";
    const LABEL_PLUS_MINUS = "label_pm";
    const LABEL_APPLICABLE = "label_app";
    const LABEL_ADEQUATE = "label_aed";
    const LABEL_CUSTOM = "label_custom";

    const STR_RIGHT = 'right';
    const STR_WRONG = 'wrong';
    const STR_PLUS = '+';
    const STR_MINUS = '-';
    const STR_APPLICABLE = 'applicable';
    const STR_NOT_APPLICABLE = 'not applicable';
    const STR_ADEQUATE = 'adequate';
    const STR_NOT_ADEQUATE = 'not adequate';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $shuffle = $this->factory->input()->field()->checkbox($this->language->txt('asq_label_shuffle'));

        $thumb_size = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_thumb_size'),
            $this->language->txt('asq_description_thumb_size')
        );

        $optionLabel = $this->GenerateOptionLabelField($value);


        if ($value !== null) {
            $shuffle = $shuffle->withValue($value->isShuffleAnswers() ?? false);
            $thumb_size = $thumb_size->withValue($value->getThumbnailSize());
        }

        $fields[self::VAR_SHUFFLE_ANSWERS] = $shuffle;
        $fields[self::VAR_THUMBNAIL_SIZE] = $thumb_size;
        $fields[self::VAR_LABEL_TYPE] = $optionLabel;

        return $fields;
    }

    private function GenerateOptionLabelField(?KprimChoiceEditorConfiguration $config) : SwitchableGroup
    {
        $selected_value = self::LABEL_RIGHT_WRONG;

        if ($config !== null) {
            if ($config->getLabelTrue() === self::STR_RIGHT && $config->getLabelFalse() === self::STR_WRONG) {
                $selected_value = self::LABEL_RIGHT_WRONG;
            } elseif ($config->getLabelTrue() === self::STR_PLUS && $config->getLabelFalse() === self::STR_MINUS) {
                $selected_value = self::LABEL_PLUS_MINUS;
            } elseif ($config->getLabelTrue() === self::STR_APPLICABLE && $config->getLabelFalse() === self::STR_NOT_APPLICABLE) {
                $selected_value = self::LABEL_APPLICABLE;
            } elseif ($config->getLabelTrue() === self::STR_ADEQUATE && $config->getLabelFalse() === self::STR_NOT_ADEQUATE) {
                $selected_value = self::LABEL_ADEQUATE;
            } elseif (empty($config->getLabelTrue())) {
                $selected_value = self::LABEL_RIGHT_WRONG;
            } else {
                $selected_value = self::LABEL_CUSTOM;
            }
        }

        $customLabelTrue =
            $this->factory->input()->field()->text(
                $this->language->txt('asq_label_user_true')
            )->withMaxLength(32);

            $customLabelFalse = $this->factory->input()->field()->text(
                $this->language->txt('asq_label_user_false')
            )->withMaxLength(32);

        if ($selected_value == self::LABEL_CUSTOM) {
            $customLabelTrue = $customLabelTrue->withValue($config->getLabelTrue());
            $customLabelFalse = $customLabelFalse->withValue($config->getLabelFalse());
        }

        $optionLabel = $this->factory->input()->field()->switchableGroup(
            [
                self::LABEL_RIGHT_WRONG =>
                    $this->factory->input()->field()->group(
                        [],
                        $this->language->txt('asq_label_right_wrong')
                    ),
                self::LABEL_PLUS_MINUS =>
                    $this->factory->input()->field()->group(
                        [],
                        $this->language->txt('asq_label_plus_minus')
                    ),
                self::LABEL_APPLICABLE =>
                    $this->factory->input()->field()->group(
                        [],
                        $this->language->txt('asq_label_applicable')
                    ),
                self::LABEL_ADEQUATE =>
                    $this->factory->input()->field()->group(
                        [],
                        $this->language->txt('asq_label_adequate')
                    ),
                self::LABEL_CUSTOM =>
                    $this->factory->input()->field()->group(
                        [
                            self::VAR_LABEL_TRUE => $customLabelTrue,
                            self::VAR_LABEL_FALSE => $customLabelFalse
                        ],
                        $this->language->txt('asq_label_userdefined')
                    )
            ],
            $this->language->txt('asq_label_obtion_labels'),
            $this->language->txt('asq_description_options')
        )->withValue($selected_value);

        return $optionLabel;
    }

    /**
     * @param $postdata array
     * @return KprimChoiceEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        switch ($postdata[self::VAR_LABEL_TYPE][0]) {
            case self::LABEL_RIGHT_WRONG:
                $label_true = self::STR_RIGHT;
                $label_false = self::STR_WRONG;
                break;
            case self::LABEL_PLUS_MINUS:
                $label_true = self::STR_PLUS;
                $label_false = self::STR_MINUS;
                break;
            case self::LABEL_APPLICABLE:
                $label_true = self::STR_APPLICABLE;
                $label_false = self::STR_NOT_APPLICABLE;
                break;
            case self::LABEL_ADEQUATE:
                $label_true = self::STR_ADEQUATE;
                $label_false = self::STR_NOT_ADEQUATE;
                break;
            case self::LABEL_CUSTOM:
                $label_true = $postdata[self::VAR_LABEL_TYPE][1][self::VAR_LABEL_TRUE];
                $label_false = $postdata[self::VAR_LABEL_TYPE][1][self::VAR_LABEL_FALSE];
                break;
        }

        return new KprimChoiceEditorConfiguration(
            $postdata[self::VAR_SHUFFLE_ANSWERS],
            $postdata[self::VAR_THUMBNAIL_SIZE],
            $label_true,
            $label_false
        );
    }

    /**
     * @return KprimChoiceEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new KprimChoiceEditorConfiguration(null, null, self::STR_RIGHT, self::STR_WRONG);
    }
}
