<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Application\Service\UIService;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Essay\Form\Editor\EssayEditorConfigurationFactory;
use srag\asq\Questions\Essay\Form\Scoring\EssayScoringConfigurationFactory;
use srag\asq\Questions\Essay\Form\Scoring\EssayScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;

/**
 * Class EssayFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new EssayEditorConfigurationFactory($language, $ui, $asq_ui),
            new EssayScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new EssayScoringDefinitionFactory($language, $ui)
        );
    }

    /**
     * Returns AsqTableInput Options array
     *
     * @return array
     */
    public function getAnswerOptionConfiguration() : array
    {
        return [
            AsqTableInput::OPTION_ADDITIONAL_ON_LOAD =>
                function($id) {
                    return "il.ASQ.Essay.setAnswersInput($($id));";
                }
        ];
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Essay/Form/EssayAuthoring.js' ];
    }
}
