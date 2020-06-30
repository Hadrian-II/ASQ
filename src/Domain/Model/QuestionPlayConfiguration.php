<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

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
     * @var AbstractConfiguration
     */
    protected $presenter_configuration;

    /**
     * @var AbstractConfiguration
     */
    protected $editor_configuration;

    /**
     * @var AbstractConfiguration
     */
    protected $scoring_configuration;

    /**
     * @param AbstractConfiguration $editor_configuration
     * @param AbstractConfiguration $scoring_configuration
     * @param AbstractConfiguration $presenter_configuration
     * @return QuestionPlayConfiguration
     */
    public static function create(
        AbstractConfiguration $editor_configuration = null,
        AbstractConfiguration $scoring_configuration = null,
        AbstractConfiguration $presenter_configuration = null
    ) : QuestionPlayConfiguration {
        $object = new QuestionPlayConfiguration();
        $object->editor_configuration = $editor_configuration;
        $object->presenter_configuration = $presenter_configuration;
        $object->scoring_configuration = $scoring_configuration;
        return $object;
    }

    /**
     * @return AbstractValueObject
     */
    public function getEditorConfiguration() : ?AbstractConfiguration
    {
        return $this->editor_configuration;
    }

    /**
     * @return AbstractValueObject
     */
    public function getPresenterConfiguration() : ?AbstractConfiguration
    {
        return $this->presenter_configuration;
    }

    /**
     * @return AbstractValueObject
     */
    public function getScoringConfiguration() : ?AbstractConfiguration
    {
        return $this->scoring_configuration;
    }
}
