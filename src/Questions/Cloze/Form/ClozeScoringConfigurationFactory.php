<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Cloze\ClozeScoringConfiguration;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;

/**
 * Class ClozeScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeScoringConfigurationFactory extends AbstractObjectFactory
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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::readObjectFromPost()
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return ClozeScoringConfiguration::create();
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getDefaultValue()
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return ClozeScoringConfiguration::create();
    }
}