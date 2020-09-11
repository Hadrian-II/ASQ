<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Questions\Essay\Form\Editor\EssayEditorConfigurationFactory;
use srag\asq\Questions\Essay\Form\Scoring\EssayScoringConfigurationFactory;
use srag\asq\Questions\Essay\Form\Scoring\EssayScoringDefinitionFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use srag\asq\PathHelper;
use srag\asq\Application\Service\UIService;

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
    use PathHelper;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new EssayEditorConfigurationFactory($language, $ui, $asq_ui),
            new EssayScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new EssayScoringDefinitionFactory($language, $ui)
        );
    }

    /**
     * @return array
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/Essay/Form/EssayAuthoring.js' ];
    }
}
