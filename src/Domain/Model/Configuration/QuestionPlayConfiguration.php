<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Configuration;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionPlayConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionPlayConfiguration extends AbstractValueObject
{
    /**
     * @var AbstractValueObject
     */
    protected $editor_configuration;

    /**
     * @var AbstractValueObject
     */
    protected $scoring_configuration;

    /**
     * @param AbstractValueObject $editor_configuration
     * @param AbstractValueObject $scoring_configuration
     * @return QuestionPlayConfiguration
     */
    public function __construct(
        AbstractValueObject $editor_configuration = null,
        AbstractValueObject $scoring_configuration = null
    ) {
        $this->editor_configuration = $editor_configuration;
        $this->scoring_configuration = $scoring_configuration;
    }

    /**
     * @return AbstractValueObject
     */
    public function getEditorConfiguration() : ?AbstractValueObject
    {
        return $this->editor_configuration;
    }

    /**
     * @return AbstractValueObject
     */
    public function getScoringConfiguration() : ?AbstractValueObject
    {
        return $this->scoring_configuration;
    }
}
