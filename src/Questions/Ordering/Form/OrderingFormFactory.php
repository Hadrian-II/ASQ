<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDefinitionFactory;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;

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
    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new OrderingEditorConfigurationFactory($DIC->language()),
            new OrderingScoringConfigurationFactory($DIC->language()),
            new ImageAndTextDefinitionFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()));
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getAnswerOptionConfiguration()
     */
    public function getAnswerOptionConfiguration() : array
    {
        return [ AsqTableInput::OPTION_ORDER => true ];
    }
}