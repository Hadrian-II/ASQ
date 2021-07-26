<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

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
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new MultipleChoiceScoringConfiguration();
    }

    public function getDefaultValue() : AbstractValueObject
    {
        return new MultipleChoiceScoringConfiguration();
    }
}
