<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\Essay\EssayEditorConfiguration;
use ilNumberInputGUI;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value): array
    {
        $fields = [];

        $max_length = new ilNumberInputGUI($this->language->txt('asq_label_max_length'), self::VAR_MAX_LENGTH);
        $max_length->setSize(2);
        $max_length->setInfo($this->language->txt('asq_info_max_length'));
        $fields[self::VAR_MAX_LENGTH] = $max_length;

        if (!is_null($value)) {
            $max_length->setValue($value->getMaxLength());
        }

        return $fields;
    }

    /**
     * @return EssayEditorConfiguration
     */
    public function readObjectFromPost(): AbstractValueObject
    {
        return EssayEditorConfiguration::create($this->readInt(self::VAR_MAX_LENGTH));
    }

    /**
     * @return EssayEditorConfiguration
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return EssayEditorConfiguration::create();
    }
}