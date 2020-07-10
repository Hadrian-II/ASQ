<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use ilCheckboxInputGUI;
use ilHiddenInputGUI;
use ilNumberInputGUI;
use ilSelectInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Choice\Editor\MultipleChoice\Data\MultipleChoiceEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class SingleChoiceEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SingleChoiceEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MCE_SHUFFLE = 'shuffle';
    const VAR_MCE_MAX_ANSWERS = 'max_answers';
    const VAR_MCE_THUMB_SIZE = 'thumbsize';
    const VAR_MCE_IS_SINGLELINE = 'singleline';

    const SINGLE_CHOICE = 1;
    const STR_TRUE = "true";
    const STR_FALSE = "false";

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $shuffle = new ilCheckboxInputGUI($this->language->txt('asq_label_shuffle'), self::VAR_MCE_SHUFFLE);

        $shuffle->setValue(self::STR_TRUE);
        $fields[self::VAR_MCE_SHUFFLE] = $shuffle;

        $singleline = new ilSelectInputGUI($this->language->txt('asq_label_editor'), self::VAR_MCE_IS_SINGLELINE);

        $singleline->setOptions([
            self::STR_TRUE => $this->language->txt('asq_option_single_line'),
            self::STR_FALSE => $this->language->txt('asq_option_multi_line')
        ]);

        $fields[self::VAR_MCE_IS_SINGLELINE] = $singleline;

        if ($value === null || $value->isSingleLine()) {
            $thumb_size = new ilNumberInputGUI($this->language->txt('asq_label_thumb_size'), self::VAR_MCE_THUMB_SIZE);
            $thumb_size->setInfo($this->language->txt('asq_description_thumb_size'));
            $thumb_size->setSuffix($this->language->txt('asq_pixel'));
            $thumb_size->setMinValue(20);
            $thumb_size->setDecimals(0);
            $thumb_size->setSize(6);
            $fields[self::VAR_MCE_THUMB_SIZE] = $thumb_size;
        } else {
            $thumb_size = new ilHiddenInputGUI(self::VAR_MCE_THUMB_SIZE);
            $fields[self::VAR_MCE_THUMB_SIZE] = $thumb_size;
        }

        if ($value !== null) {
            $shuffle->setChecked($value->isShuffleAnswers());
            $thumb_size->setValue($value->getThumbnailSize());
            $singleline->setValue($value->isSingleLine() ? self::STR_TRUE : self::STR_FALSE);
        } else {
            $shuffle->setChecked(true);
        }

        return $fields;
    }

    /**
     * @return MultipleChoiceEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return MultipleChoiceEditorConfiguration::create(
            $this->readString(self::VAR_MCE_SHUFFLE) === self::STR_TRUE,
            self::SINGLE_CHOICE,
            $this->readInt(self::VAR_MCE_THUMB_SIZE),
            $this->readString(self::VAR_MCE_IS_SINGLELINE) === self::STR_TRUE
        );
    }

    /**
     * @return MultipleChoiceEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return MultipleChoiceEditorConfiguration::create();
    }
}
