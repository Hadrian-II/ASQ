<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\ErrorText\Form\Editor\ErrorTextEditorConfigurationFactory;
use srag\asq\Questions\ErrorText\Form\Scoring\ErrorTextScoringConfigurationFactory;
use srag\asq\Questions\ErrorText\Form\Scoring\ErrorTextScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;
use srag\asq\Application\Service\UIService;

/**
 * Class ErrorTextFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new ErrorTextEditorConfigurationFactory($language, $ui, $asq_ui),
            new ErrorTextScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new ErrorTextScoringDefinitionFactory($language, $ui)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/ErrorText/Form/ErrorTextAuthoring.js' ];
    }
}
