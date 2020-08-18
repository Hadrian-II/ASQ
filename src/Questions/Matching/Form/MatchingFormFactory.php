<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Matching\Form\Editor\MatchingEditorConfigurationFactory;
use srag\asq\Questions\Matching\Form\Scoring\MatchingScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;

/**
 * Class MatchingFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui)
    {
        parent::__construct(
            new MatchingEditorConfigurationFactory($language, $ui),
            new MatchingScoringConfigurationFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Matching/Form/MatchingAuthoring.js' ];
    }
}
