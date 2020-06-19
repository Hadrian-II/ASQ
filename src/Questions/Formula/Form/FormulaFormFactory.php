<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form;

use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\UserInterface\Web\PathHelper;

/**
 * Class FormulaFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new FormulaEditorConfigurationFactory($DIC->language()),
            new FormulaScoringConfigurationFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()),
            new FormulaScoringDefinitionFactory($DIC->language()));
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getScripts()
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Formula/FormulaAuthoring.js' ];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getAnswerOptionConfiguration()
     */
    public function getAnswerOptionConfiguration() : array
    {
        return [ AsqTableInput::OPTION_HIDE_ADD_REMOVE => true ];
    }
}