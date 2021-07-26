<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\ImageMap;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorConfiguration;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

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
    use InputHandlingTrait;

    const VAR_IMAGE = 'ime_image';
    const VAR_MULTIPLE_CHOICE = 'ime_multiple_choice';
    const VAR_MAX_ANSWERS = 'ime_max_answers';
    const POPUP_FIELD = 'ime_popup';

    const STR_MULTICHOICE = 'Multichoice';
    const STR_SINGLECHOICE = 'Singlechoice';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $max_answers = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_answering_limitation'),
            $this->language->txt('asq_info_answering_limitation')
        );

        if (!is_null($value)) {
            $max_answers = $max_answers->withValue($value->getMaxAnswers());
        }

        $mode = $this->factory->input()->field()->switchableGroup(
            [
                self::STR_SINGLECHOICE =>
                $this->factory->input()->field()->group(
                    [],
                    $this->language->txt('asq_label_single_choice')
                ),
                self::STR_MULTICHOICE =>
                $this->factory->input()->field()->group(
                    [
                        self::VAR_MAX_ANSWERS => $max_answers
                    ],
                    $this->language->txt('asq_label_multiple_choice')
                ),
            ],
            $this->language->txt('asq_label_mode')
        );

        $image = $this->asq_ui->getImageUpload($this->language->txt('asq_label_image'));

        $popup = $this->asq_ui->getImageFormPopup();


        if ($value !== null) {
            $mode = $mode->withValue($value->isMultipleChoice() ? self::STR_MULTICHOICE : self::STR_SINGLECHOICE);
            $image = $image->withValue(strval($value->getImage()));
            $popup = $popup->withValue(strval($value->getImage()));
        }

        $fields[self::VAR_MULTIPLE_CHOICE] = $mode;
        $fields[self::VAR_IMAGE] = $image;
        $fields[self::POPUP_FIELD] = $popup;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return ImageMapEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        $multiple_choice = $this->readString($postdata[self::VAR_MULTIPLE_CHOICE][0]);

        return new ImageMapEditorConfiguration(
            $postdata[self::VAR_IMAGE],
            $multiple_choice === self::STR_MULTICHOICE,
            $multiple_choice === self::STR_MULTICHOICE ?
                $this->readInt($postdata[self::VAR_MULTIPLE_CHOICE][1][self::VAR_MAX_ANSWERS]) : 1
        );
    }

    /**
     * @return ImageMapEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new ImageMapEditorConfiguration();
    }
}
