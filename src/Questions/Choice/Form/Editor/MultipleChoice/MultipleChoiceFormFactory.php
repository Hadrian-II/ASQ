<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\Choice\Form\Scoring\MultipleChoiceScoringConfigurationFactory;
use srag\asq\Questions\Choice\Form\Scoring\MultipleChoiceScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\ImageAndTextDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;

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

    public function __construct(ilLanguage $language, UIServices $ui)
    {
        parent::__construct(
            new MultipleChoiceEditorConfigurationFactory($language, $ui),
            new MultipleChoiceScoringConfigurationFactory($language, $ui),
            new ImageAndTextDefinitionFactory($language, $ui),
            new MultipleChoiceScoringDefinitionFactory($language, $ui)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/MultipleChoice/MultipleChoiceAuthoring.js' ];
    }
}
