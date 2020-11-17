<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Cloze\Scoring\Data\ClozeScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

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
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\Factory\IObjectFactory::readObjectFromPost()
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new ClozeScoringConfiguration();
    }

    /**
     * @return AbstractValueObject
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new ClozeScoringConfiguration();
    }
}
