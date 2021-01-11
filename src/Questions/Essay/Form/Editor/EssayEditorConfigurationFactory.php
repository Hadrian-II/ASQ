<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Essay\Editor\Data\EssayEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class EssayEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_MAX_LENGTH = "ee_max_length";

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $max_length = $this->factory->input()->field()->numeric(
            $this->language->txt('asq_label_max_length'),
            $this->language->txt('asq_info_max_length')
        );

        if (!is_null($value)) {
            $max_length = $max_length->withValue($value->getMaxLength());
        }

        $fields[self::VAR_MAX_LENGTH] = $max_length;

        return $fields;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\Factory\IObjectFactory::readObjectFromPost()
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new EssayEditorConfiguration($this->readInt($postdata[self::VAR_MAX_LENGTH]));
    }

    /**
     * @return EssayEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new EssayEditorConfiguration();
    }
}
