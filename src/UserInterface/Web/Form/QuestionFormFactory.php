<?php

namespace srag\asq\UserInterface\Web\Form;

use srag\asq\Domain\Model\QuestionPlayConfiguration;

/**
 * Class QuestionFormFactory
 *
 * Contains all the factories needed to create a Question object
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFormFactory
{
    /**
     * @var IQuestionFormObjectFactory
     */
    protected $editor_config_factory;

    /**
     * @var IQuestionFormObjectFactory
     */
    protected $scoring_config_factory;

    /**
     * @var IQuestionFormObjectFactory
     */
    protected $editor_definition_factory;

    /**
     * @var IQuestionFormObjectFactory
     */
    protected $scoring_definition_factory;

    /**
     * @param IQuestionFormObjectFactory $editor_config_factory
     * @param IQuestionFormObjectFactory $scoring_config_factory
     * @param IQuestionFormObjectFactory $editor_definition_factory
     * @param IQuestionFormObjectFactory $scoring_definition_factory
     */
    public function __construct(
        IQuestionFormObjectFactory $editor_config_factory,
        IQuestionFormObjectFactory $scoring_config_factory,
        IQuestionFormObjectFactory $editor_definition_factory,
        IQuestionFormObjectFactory $scoring_definition_factory)
    {
        $this->editor_config_factory = $editor_config_factory;
        $this->scoring_config_factory = $scoring_config_factory;
        $this->editor_definition_factory = $editor_definition_factory;
        $this->scoring_definition_factory = $scoring_definition_factory;
    }

    /**
     * @param QuestionPlayConfiguration $config
     * @return array
     */
    public function getFormFields(QuestionPlayConfiguration $config) : array
    {
        return array_merge(
            $this->editor_config_factory->getFormfields($config->getEditorConfiguration()),
            $this->scoring_config_factory->getFormfields($config->getScoringConfiguration()));
    }

    /**
     * @return QuestionPlayConfiguration
     */
    public function readQuestionPlayConfiguration() : QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            $this->editor_config_factory->readObjectFromPost(),
            $this->scoring_config_factory->readObjectFromPost());
    }
}