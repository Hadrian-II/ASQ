<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Generic\Form\ImageAndTextDefinitionFactory;
use srag\asq\Questions\Ordering\Form\Editor\OrderingEditorConfigurationFactory;
use srag\asq\Questions\Ordering\Form\Scoring\OrderingScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use srag\asq\Application\Service\UIService;

/**
 * Class OrderingFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new OrderingEditorConfigurationFactory($language, $ui, $asq_ui),
            new OrderingScoringConfigurationFactory($language, $ui, $asq_ui),
            new ImageAndTextDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }

    /**
     * @return array
     */
    public function getAnswerOptionConfiguration() : array
    {
        return [ AsqTableInput::OPTION_ORDER => true ];
    }
}
