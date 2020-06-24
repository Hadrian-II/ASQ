<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\PathHelper;

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

    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new MatchingEditorConfigurationFactory($DIC->language()),
            new MatchingScoringConfigurationFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()));
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