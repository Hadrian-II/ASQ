<?php
declare(strict_types = 1);

namespace srag\asq\Questions\MultipleChoice\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\MultipleChoice\MultipleChoiceScoringConfiguration;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;

/**
 * Class MultipleChoiceScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringConfigurationFactory extends AbstractObjectFactory
{
    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    /**
     * @return MultipleChoiceScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return MultipleChoiceScoringConfiguration::create();
    }

    /**
     * @return MultipleChoiceScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return MultipleChoiceScoringConfiguration::create();
    }
}
