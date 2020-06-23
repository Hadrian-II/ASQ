<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ImageMap\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\ImageMap\ImageMapEditorConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqImageUpload;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTextInputGUI;

/**
 * Class ImageMapFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_IMAGE = 'ime_image';
    const VAR_MULTIPLE_CHOICE = 'ime_multiple_choice';
    const VAR_MAX_ANSWERS = 'ime_max_answers';
    const POPUP_FIELD = 'ime_popup';

    const STR_MULTICHOICE = 'Multichoice';
    const STR_SINGLECHOICE = 'Singlechoice';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $mode = new ilRadioGroupInputGUI($this->language->txt('asq_label_mode'), self::VAR_MULTIPLE_CHOICE);
        $mode->addOption(new ilRadioOption($this->language->txt('asq_label_single_choice'), self::STR_SINGLECHOICE));
        $multi = new ilRadioOption($this->language->txt('asq_label_multiple_choice'), self::STR_MULTICHOICE);
        $max_answers = new ilTextInputGUI($this->language->txt('asq_label_answering_limitation'), self::VAR_MAX_ANSWERS);
        $max_answers->setInfo($this->language->txt('asq_info_answering_limitation'));
        $multi->addSubItem($max_answers);
        $mode->addOption($multi);

        $fields[self::VAR_MULTIPLE_CHOICE] = $mode;

        $image = new AsqImageUpload($this->language->txt('asq_label_image'), self::VAR_IMAGE);
        $image->setRequired(true);
        $fields[self::VAR_IMAGE] = $image;

        $popup = new ImageFormPopup();
        $fields[self::POPUP_FIELD] = $popup;

        if ($value !== null) {
            $mode->setValue($value->isMultipleChoice() ? self::STR_MULTICHOICE : self::STR_SINGLECHOICE);
            $image->setImagePath($value->getImage());
            $popup->setValue($value->getImage());
            $max_answers->setValue($value->getMaxAnswers());
        }

        return $fields;
    }

    /**
     * @return ImageMapEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return ImageMapEditorConfiguration::create(
            $this->readImage(self::VAR_IMAGE),
            $_POST[self::VAR_MULTIPLE_CHOICE] === self::STR_MULTICHOICE,
            $_POST[self::VAR_MULTIPLE_CHOICE] === self::STR_MULTICHOICE ?
                $this->readInt(self::VAR_MAX_ANSWERS) : 1);
    }

    /**
     * @return ImageMapEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return ImageMapEditorConfiguration::create(null, null, null);
    }
}