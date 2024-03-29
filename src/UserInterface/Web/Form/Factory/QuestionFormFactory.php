<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;

/**
 * Class QuestionFormFactory
 *
 * Contains all the factories needed to create a Question object
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionFormFactory implements IQuestionFormFactory
{
    protected IObjectFactory $editor_config_factory;

    protected IObjectFactory $scoring_config_factory;

    protected IAnswerOptionFactory $editor_definition_factory;

    protected IAnswerOptionFactory $scoring_definition_factory;

    public function __construct(
        IObjectFactory $editor_config_factory,
        IObjectFactory $scoring_config_factory,
        IAnswerOptionFactory $editor_definition_factory,
        IAnswerOptionFactory $scoring_definition_factory
    ) {
        $this->editor_config_factory = $editor_config_factory;
        $this->scoring_config_factory = $scoring_config_factory;
        $this->editor_definition_factory = $editor_definition_factory;
        $this->scoring_definition_factory = $scoring_definition_factory;
    }

    public function getFormFields(QuestionPlayConfiguration $config) : array
    {
        return array_merge(
            $this->editor_config_factory->getFormfields($config->getEditorConfiguration()),
            $this->scoring_config_factory->getFormfields($config->getScoringConfiguration())
        );
    }

    public function readQuestionPlayConfiguration(array $postdata) : QuestionPlayConfiguration
    {
        return new QuestionPlayConfiguration(
            $this->editor_config_factory->readObjectFromPost($postdata),
            $this->scoring_config_factory->readObjectFromPost($postdata)
        );
    }

    public function getDefaultPlayConfiguration() : QuestionPlayConfiguration
    {
        return new QuestionPlayConfiguration(
            $this->editor_config_factory->getDefaultValue(),
            $this->scoring_config_factory->getDefaultValue()
        );
    }

    /**
     * @return AsqTableInputFieldDefinition[]
     */
    public function getAnswerOptionDefinitions(?QuestionPlayConfiguration $play) : array
    {
        return array_merge(
            $this->editor_definition_factory->getTableColumns($play),
            $this->scoring_definition_factory->getTableColumns($play)
        );
    }

    public function hasAnswerOptions() : bool
    {
        return count($this->getAnswerOptionDefinitions(null)) > 0;
    }

    public function getAnswerOptionConfiguration() : array
    {
        return [];
    }

    public function getAnswerOptionValues(?array $options) : array
    {
        if (is_null($options)) {
            return [];
        }

        return array_map(function ($option) {
            /** @var $option AnswerOption */
            return array_merge(
                $this->editor_definition_factory->getValues($option->getDisplayDefinition()),
                $this->scoring_definition_factory->getValues($option->getScoringDefinition())
            );
        }, $options);
    }

    public function readAnswerOptions(array $values) : array
    {
        $options = [];
        $i = 0;
        foreach ($values as $value) {
            $i += 1;

            $options[] = new AnswerOption(
                strval($i),
                $this->editor_definition_factory->readObjectFromValues($value),
                $this->scoring_definition_factory->readObjectFromValues($value)
            );
        }

        return $options;
    }

    public function getScripts() : array
    {
        return[];
    }

    public function performQuestionPostProcessing(QuestionDto $question) : QuestionDto
    {
        // virtual method
        return $question;
    }
}
