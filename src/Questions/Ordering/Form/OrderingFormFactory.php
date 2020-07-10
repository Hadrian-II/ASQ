<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use ilLanguage;
use srag\asq\Questions\Generic\Data\ImageAndTextDefinitionFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Ordering\Form\Editor\OrderingEditorConfigurationFactory;
use srag\asq\Questions\Ordering\Form\Scoring\OrderingScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

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
    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new OrderingEditorConfigurationFactory($language),
            new OrderingScoringConfigurationFactory($language),
            new ImageAndTextDefinitionFactory($language),
            new EmptyDefinitionFactory($language)
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
