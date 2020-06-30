<?php
declare(strict_types = 1);

namespace srag\asq\Domain\Model\Answer\Option;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\Domain\Model\QuestionPlayConfiguration;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::readObjectFromPost()
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
