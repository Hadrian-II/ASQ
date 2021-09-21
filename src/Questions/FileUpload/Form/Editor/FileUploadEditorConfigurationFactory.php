<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form\Editor;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\FileUpload\Editor\Data\FileUploadEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class FileUploadEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MAX_UPLOAD = 'fue_max_upload';
    const VAR_ALLOWED_EXTENSIONS = 'fue_extensions';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $max_upload = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_max_upload'),
            $this->language->txt('asq_description_max_upload')
        );

        $allowed_extensions = $this->factory->input()->field()->text(
            $this->language->txt('asq_label_allowed_extensions'),
            $this->language->txt('asq_description_allowed_extensions')
            )->withMaxLength(32);;


        if ($value !== null) {
            $max_upload = $max_upload->withValue($value->getMaximumSize());
            $allowed_extensions = $allowed_extensions->withValue($value->getAllowedExtensions() ?? '');
        }

        $fields[self::VAR_MAX_UPLOAD] = $max_upload;
        $fields[self::VAR_ALLOWED_EXTENSIONS] = $allowed_extensions;

        return $fields;
    }

    /**
     * @return FileUploadEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new FileUploadEditorConfiguration(
            $postdata[self::VAR_MAX_UPLOAD],
            str_replace(' ', '', $this->readString($postdata[self::VAR_ALLOWED_EXTENSIONS]))
        );
    }

    /**
     * @return FileUploadEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new FileUploadEditorConfiguration();
    }
}
