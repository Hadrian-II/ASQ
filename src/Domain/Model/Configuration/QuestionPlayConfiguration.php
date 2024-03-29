<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Configuration;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionPlayConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionPlayConfiguration extends AbstractValueObject
{
    protected ?AbstractValueObject $editor_configuration;

    protected ?AbstractValueObject $scoring_configuration;

    public function __construct(
        AbstractValueObject $editor_configuration = null,
        AbstractValueObject $scoring_configuration = null
    ) {
        $this->editor_configuration = $editor_configuration;
        $this->scoring_configuration = $scoring_configuration;
    }

    public function getEditorConfiguration() : ?AbstractValueObject
    {
        return $this->editor_configuration;
    }

    public function getScoringConfiguration() : ?AbstractValueObject
    {
        return $this->scoring_configuration;
    }
}
