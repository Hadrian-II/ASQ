<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form;

use ilLanguage;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\TextSubset\Form\Editor\TextSubsetEditorConfigurationFactory;
use srag\asq\Questions\TextSubset\Form\Scoring\TextSubsetScoringConfigurationFactory;
use srag\asq\Questions\TextSubset\Form\Scoring\TextSubsetScoringDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;
use srag\asq\Application\Service\UIService;

/**
 * Class TextSubsetFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new TextSubsetEditorConfigurationFactory($language, $ui, $asq_ui),
            new TextSubsetScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new TextSubsetScoringDefinitionFactory($language, $ui)
        );
    }
}
