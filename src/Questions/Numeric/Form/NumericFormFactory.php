<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Numeric\Form;

use ilLanguage;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Numeric\Form\Editor\NumericEditorConfigurationFactory;
use srag\asq\Questions\Numeric\Form\Scoring\NumericScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;

/**
 * Class NumericFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui)
    {
        parent::__construct(
            new NumericEditorConfigurationFactory($language, $ui),
            new NumericScoringConfigurationFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }
}
