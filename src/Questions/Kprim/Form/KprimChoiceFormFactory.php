<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form;

use ilLanguage;
use srag\asq\Questions\Generic\Form\ImageAndTextDefinitionFactory;
use srag\asq\Questions\Kprim\Form\Editor\KprimChoiceEditorConfigurationFactory;
use srag\asq\Questions\Kprim\Form\Scoring\KprimChoiceScoringConfigurationFactory;
use srag\asq\Questions\Kprim\Form\Scoring\KprimChoiceScoringDefinitionFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;
use srag\asq\Application\Service\UIService;

/**
 * Class KprimFormFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new KprimChoiceEditorConfigurationFactory($language, $ui, $asq_ui),
            new KprimChoiceScoringConfigurationFactory($language, $ui, $asq_ui),
            new ImageAndTextDefinitionFactory($language, $ui),
            new KprimChoiceScoringDefinitionFactory($language, $ui)
        );
    }

    public function getAnswerOptionConfiguration() : array
    {
        return [
            AsqTableInput::OPTION_ORDER => true,
            AsqTableInput::OPTION_HIDE_ADD_REMOVE => true,
            AsqTableInput::OPTION_MIN_ROWS => 4
        ];
    }
}
