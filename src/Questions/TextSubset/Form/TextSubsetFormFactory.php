<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;

/**
 * Class TextSubsetFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetFormFactory extends QuestionFormFactory
{
    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new TextSubsetEditorConfigurationFactory($DIC->language()),
            new TextSubsetScoringConfigurationFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()),
            new TextSubsetScoringDefinitionFactory($DIC->language()));
    }
}