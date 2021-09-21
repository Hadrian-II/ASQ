<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Cloze\Scoring\Data\ClozeScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class ClozeScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeScoringConfigurationFactory extends AbstractObjectFactory
{
    public function getFormfields(?AbstractValueObject $value) : array
    {
        return [];
    }

    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new ClozeScoringConfiguration();
    }

    public function getDefaultValue() : AbstractValueObject
    {
        return new ClozeScoringConfiguration();
    }
}
