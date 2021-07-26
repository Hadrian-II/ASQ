<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Application\Service\UIService;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Formula\Form\Editor\FormulaEditorConfigurationFactory;
use srag\asq\Questions\Formula\Form\Scoring\FormulaScoringConfigurationFactory;
use srag\asq\Questions\Formula\Form\Scoring\FormulaScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

/**
 * Class FormulaFormFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new FormulaEditorConfigurationFactory($language, $ui, $asq_ui),
            new FormulaScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new FormulaScoringDefinitionFactory($language, $ui)
        );
    }

    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Formula/Form/FormulaAuthoring.js' ];
    }

    public function getAnswerOptionConfiguration() : array
    {
        return [
            AsqTableInput::OPTION_HIDE_ADD_REMOVE => true,
            AsqTableInput::OPTION_ADDITIONAL_ON_LOAD =>
                function($id) {
                    return "il.ASQ.Formula.setResultsTable($($id));";
                }
        ];
    }
}
