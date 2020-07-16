<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Generic\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class ImageAndTextDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageAndTextDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_MCDD_TEXT = 'mcdd_text';
    const VAR_MCDD_IMAGE = 'mcdd_image';

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $columns = [];

        $columns[self::VAR_MCDD_TEXT] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_answer_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_MCDD_TEXT
        );

        $columns[self::VAR_MCDD_IMAGE] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_answer_image'),
            AsqTableInputFieldDefinition::TYPE_IMAGE,
            self::VAR_MCDD_IMAGE
        );

        return $columns;
    }

    /**
     * @param array $values
     * @return AbstractValueObject
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return ImageAndTextDisplayDefinition::create(
            $values[self::VAR_MCDD_TEXT],
            $values[self::VAR_MCDD_IMAGE]
        );
    }

    public function getDefaultValue() : AbstractValueObject
    {
        return ImageAndTextDisplayDefinition::create();
    }

    /**
     * @param ImageAndTextDisplayDefinition $definition
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_MCDD_TEXT => $definition->getText(),
            self::VAR_MCDD_IMAGE => $definition->getImage()
        ];
    }
}
