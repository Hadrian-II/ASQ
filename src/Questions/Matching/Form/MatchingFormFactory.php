<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\PathHelper;
use ilLanguage;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getScripts()
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Matching/MatchingAuthoring.js' ];
    }
}
