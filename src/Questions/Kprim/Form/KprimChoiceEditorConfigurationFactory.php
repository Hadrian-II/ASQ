<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Questions\Kprim\KprimChoiceEditorConfiguration;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use ilCheckboxInputGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTextInputGUI;

/**
 * Class KprimChoiceEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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

    /**
     * @var $value KprimChoiceEditorConfiguration
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $shuffle = new ilCheckboxInputGUI($this->language->txt('asq_label_shuffle'), self::VAR_SHUFFLE_ANSWERS);
        $shuffle->setValue(self::STR_TRUE);
        $fields[self::VAR_SHUFFLE_ANSWERS] = $shuffle;

        $thumb_size = new ilNumberInputGUI(
            $this->language->txt('asq_label_thumb_size'),
            self::VAR_THUMBNAIL_SIZE);
        $thumb_size->setInfo($this->language->txt('asq_description_thumb_size'));
        $thumb_size->setSuffix($this->language->txt('asq_pixel'));
        $thumb_size->setMinValue(20);
        $thumb_size->setDecimals(0);
        $thumb_size->setSize(6);
        $fields[self::VAR_THUMBNAIL_SIZE] = $thumb_size;

        $optionLabel = $this->GenerateOptionLabelField($value);
        $fields[self::VAR_LABEL_TYPE] = $optionLabel;

        if ($value !== null) {
            $shuffle->setChecked($value->isShuffleAnswers());
            $thumb_size->setValue($value->getThumbnailSize());
        }
        else {
            $shuffle->setChecked(true);
        }

        return $fields;
    }

    /**
     * @param AbstractConfiguration $config
     * @return \ilRadioGroupInputGUI
     */
    private function GenerateOptionLabelField(?KprimChoiceEditorConfiguration $config) : ilRadioGroupInputGUI
    {
        $optionLabel = new ilRadioGroupInputGUI(
            $this->language->txt('asq_label_obtion_labels'),
            self::VAR_LABEL_TYPE);
        $optionLabel->setInfo($this->language->txt('asq_description_options'));
        $optionLabel->setRequired(true);

        $right_wrong = new ilRadioOption(
            $this->language->txt('asq_label_right_wrong'),
            self::LABEL_RIGHT_WRONG);
        $optionLabel->addOption($right_wrong);

        $plus_minus = new ilRadioOption(
            $this->language->txt('asq_label_plus_minus'),
            self::LABEL_PLUS_MINUS);
        $optionLabel->addOption($plus_minus);

        $applicable = new ilRadioOption(
            $this->language->txt('asq_label_applicable'),
            self::LABEL_APPLICABLE);
        $optionLabel->addOption($applicable);

        $adequate = new ilRadioOption(
            $this->language->txt('asq_label_adequate'),
            self::LABEL_ADEQUATE);
        $optionLabel->addOption($adequate);

        $custom = new ilRadioOption(
            $this->language->txt('asq_label_userdefined'),
            self::LABEL_CUSTOM);
        $optionLabel->addOption($custom);

        $customLabelTrue = new ilTextInputGUI(
            $this->language->txt('asq_label_user_true'),
            self::VAR_LABEL_TRUE);
        $custom->addSubItem($customLabelTrue);

        $customLabelFalse = new ilTextInputGUI(
            $this->language->txt('asq_label_user_false'),
            self::VAR_LABEL_FALSE);
        $custom->addSubItem($customLabelFalse);

        if ($config !== null) {
            if($config->getLabelTrue() === self::STR_RIGHT && $config->getLabelFalse() === self::STR_WRONG) {
                $optionLabel->setValue(self::LABEL_RIGHT_WRONG);
            }
            else if ($config->getLabelTrue() === self::STR_PLUS && $config->getLabelFalse() === self::STR_MINUS) {
                $optionLabel->setValue(self::LABEL_PLUS_MINUS);
            }
            else if ($config->getLabelTrue() === self::STR_APPLICABLE && $config->getLabelFalse() === self::STR_NOT_APPLICABLE) {
                $optionLabel->setValue(self::LABEL_APPLICABLE);
            }
            else if ($config->getLabelTrue() === self::STR_ADEQUATE && $config->getLabelFalse() === self::STR_NOT_ADEQUATE) {
                $optionLabel->setValue(self::LABEL_ADEQUATE);
            } else if (empty($config->getLabelTrue())) {
                $optionLabel->setValue(self::LABEL_RIGHT_WRONG);
            }
            else {
                $optionLabel->setValue(self::LABEL_CUSTOM);
                $customLabelTrue->setValue($config->getLabelTrue());
                $customLabelFalse->setValue($config->getLabelFalse());
            }
        }

        return $optionLabel;
    }

    /**
     * @return KprimChoiceEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        switch ($this->readString(self::VAR_LABEL_TYPE)) {
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
                $label_true = $this->readString(self::VAR_LABEL_TRUE);
                $label_false = $this->readString(self::VAR_LABEL_FALSE);
                break;
        }

        $thumbsize = $this->readInt(self::VAR_THUMBNAIL_SIZE);

        return KprimChoiceEditorConfiguration::create(
            $this->readString(self::VAR_SHUFFLE_ANSWERS) === self::STR_TRUE,
            $this->readString(self::VAR_SINGLE_LINE) === self::STR_TRUE,
            $thumbsize,
            $label_true,
            $label_false);
    }

    /**
     * @return KprimChoiceEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return KprimChoiceEditorConfiguration::create(null, null, null, self::STR_RIGHT, self::STR_WRONG);
    }
}