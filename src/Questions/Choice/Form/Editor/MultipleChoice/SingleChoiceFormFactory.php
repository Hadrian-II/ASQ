<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\Choice\Form\Scoring\MultipleChoiceScoringConfigurationFactory;
use srag\asq\Questions\Choice\Form\Scoring\SingleChoiceScoringDefinitionFactory;
use srag\asq\Questions\Generic\Data\ImageAndTextDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

/**
 * Class SingleChoiceFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SingleChoiceFormFactory extends QuestionFormFactory
{
    use ChoiceQuestionPostProcessing;
    use PathHelper;

    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new SingleChoiceEditorConfigurationFactory($language),
            new MultipleChoiceScoringConfigurationFactory($language),
            new ImageAndTextDefinitionFactory($language),
            new SingleChoiceScoringDefinitionFactory($language)
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
