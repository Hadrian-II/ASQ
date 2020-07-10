<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Formula\Form;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\Formula\Form\Editor\FormulaEditorConfigurationFactory;
use srag\asq\Questions\Formula\Form\Scoring\FormulaScoringConfigurationFactory;
use srag\asq\Questions\Formula\Form\Scoring\FormulaScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

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

    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new FormulaEditorConfigurationFactory($language),
            new FormulaScoringConfigurationFactory($language),
            new EmptyDefinitionFactory($language),
            new FormulaScoringDefinitionFactory($language)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Formula/FormulaAuthoring.js' ];
    }

    /**
     * @return array
     */
    public function getAnswerOptionConfiguration() : array
    {
        return [ AsqTableInput::OPTION_HIDE_ADD_REMOVE => true ];
    }
}
