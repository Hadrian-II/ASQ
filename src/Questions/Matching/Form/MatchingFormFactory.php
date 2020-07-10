<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Matching\Form\Editor\MatchingEditorConfigurationFactory;
use srag\asq\Questions\Matching\Form\Scoring\MatchingScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

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

    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new MatchingEditorConfigurationFactory($language),
            new MatchingScoringConfigurationFactory($language),
            new EmptyDefinitionFactory($language),
            new EmptyDefinitionFactory($language)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Matching/MatchingAuthoring.js' ];
    }
}
