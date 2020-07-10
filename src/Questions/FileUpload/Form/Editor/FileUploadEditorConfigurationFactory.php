<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form\Editor;

use ilNumberInputGUI;
use ilTextInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\FileUpload\Editor\Data\FileUploadEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class FileUploadEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MAX_UPLOAD = 'fue_max_upload';
    const VAR_ALLOWED_EXTENSIONS = 'fue_extensions';

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $max_upload = new ilNumberInputGUI($this->language->txt('asq_label_max_upload'), self::VAR_MAX_UPLOAD);
        $max_upload->setInfo($this->language->txt('asq_description_max_upload'));
        $fields[self::VAR_MAX_UPLOAD] = $max_upload;

        $allowed_extensions = new ilTextInputGUI(
            $this->language->txt('asq_label_allowed_extensions'),
            self::VAR_ALLOWED_EXTENSIONS
        );
        $allowed_extensions->setInfo($this->language->txt('asq_description_allowed_extensions'));
        $fields[self::VAR_ALLOWED_EXTENSIONS] = $allowed_extensions;

        if ($value !== null) {
            $max_upload->setValue($value->getMaximumSize());
            $allowed_extensions->setValue($value->getAllowedExtensions());
        }

        return $fields;
    }

    /**
     * @return FileUploadEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return FileUploadEditorConfiguration::create(
            $this->readInt(self::VAR_MAX_UPLOAD),
            str_replace(' ', '', $this->readString(self::VAR_ALLOWED_EXTENSIONS))
        );
    }

    /**
     * @return FileUploadEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return FileUploadEditorConfiguration::create();
    }
}
