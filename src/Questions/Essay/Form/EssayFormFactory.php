<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form;

use ilLanguage;
use srag\asq\Questions\Essay\Form\Editor\EssayEditorConfigurationFactory;
use srag\asq\Questions\Essay\Form\Scoring\EssayScoringConfigurationFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

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
    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new EssayEditorConfigurationFactory($language),
            new EssayScoringConfigurationFactory($language),
            new EmptyDefinitionFactory($language),
            new EmptyDefinitionFactory($language)
        );
    }
}
