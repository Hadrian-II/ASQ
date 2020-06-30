<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDefinitionFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use ilLanguage;

/**
 * Class KprimFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new KprimChoiceEditorConfigurationFactory($language),
            new KprimChoiceScoringConfigurationFactory($language),
            new ImageAndTextDefinitionFactory($language),
            new KprimChoiceScoringDefinitionFactory($language)
        );
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getAnswerOptionConfiguration()
     */
    public function getAnswerOptionConfiguration() : array
    {
        return [
            AsqTableInput::OPTION_ORDER => true,
            AsqTableInput::OPTION_HIDE_ADD_REMOVE => true,
            AsqTableInput::OPTION_MIN_ROWS => 4
        ];
    }
}
