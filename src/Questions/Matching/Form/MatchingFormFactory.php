<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Application\Service\UIService;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Matching\Form\Editor\MatchingEditorConfigurationFactory;
use srag\asq\Questions\Matching\Form\Scoring\MatchingScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

/**
 * Class MatchingFormFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new MatchingEditorConfigurationFactory($language, $ui, $asq_ui),
            new MatchingScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }

    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Matching/Form/MatchingAuthoring.js' ];
    }
}
