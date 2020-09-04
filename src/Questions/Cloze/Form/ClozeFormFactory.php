<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Form;

use ilLanguage;
use srag\asq\PathHelper;
use srag\asq\Questions\Cloze\Form\Editor\ClozeEditorConfigurationFactory;
use srag\asq\Questions\Cloze\Form\Scoring\ClozeScoringConfigurationFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;

/**
 * Class ClozeFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui)
    {
        parent::__construct(
            new ClozeEditorConfigurationFactory($language, $ui),
            new ClozeScoringConfigurationFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Cloze/Form/ClozeAuthoring.js' ];
    }
}
