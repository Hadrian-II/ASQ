<?php
declare(strict_types = 1);

namespace srag\asq\Questions\MultipleChoice\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDefinitionFactory;
use srag\asq\UserInterface\Web\PathHelper;

/**
 * Class MultipleChoiceFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceFormFactory extends QuestionFormFactory
{
    use ChoiceQuestionPostProcessing;
    use PathHelper;

    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new MultipleChoiceEditorConfigurationFactory($DIC->language()),
            new MultipleChoiceScoringConfigurationFactory($DIC->language()),
            new ImageAndTextDefinitionFactory($DIC->language()),
            new MultipleChoiceScoringDefinitionFactory($DIC->language()));
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getScripts()
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/MultipleChoice/MultipleChoiceAuthoring.js' ];
    }
}