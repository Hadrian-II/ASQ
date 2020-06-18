<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form;

use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\QuestionFormFactory;

/**
 * Class EssayFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayFormFactory extends QuestionFormFactory
{
    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new EssayEditorConfigurationFactory($DIC->language()),
            new EssayScoringConfigurationFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()));
    }
}