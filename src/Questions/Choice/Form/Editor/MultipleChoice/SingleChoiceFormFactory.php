<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\MultipleChoice;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Application\Service\UIService;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Choice\Form\Scoring\MultipleChoiceScoringConfigurationFactory;
use srag\asq\Questions\Choice\Form\Scoring\SingleChoiceScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\ImageAndTextDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

/**
 * Class SingleChoiceFormFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SingleChoiceFormFactory extends QuestionFormFactory
{
    use ChoiceQuestionPostProcessing;
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new SingleChoiceEditorConfigurationFactory($language, $ui, $asq_ui),
            new MultipleChoiceScoringConfigurationFactory($language, $ui, $asq_ui),
            new ImageAndTextDefinitionFactory($language, $ui),
            new SingleChoiceScoringDefinitionFactory($language, $ui)
        );
    }

    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Choice/Form/Editor/MultipleChoice/MultipleChoiceAuthoring.js' ];
    }
}
