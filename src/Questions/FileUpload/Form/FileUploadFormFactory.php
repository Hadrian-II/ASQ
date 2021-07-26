<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form;

use ilLanguage;
use srag\asq\Questions\FileUpload\Form\Editor\FileUploadEditorConfigurationFactory;
use srag\asq\Questions\FileUpload\Form\Scoring\FileUploadScoringConfigurationFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;
use srag\asq\Application\Service\UIService;

/**
 * Class FileUploadFormFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new FileUploadEditorConfigurationFactory($language, $ui, $asq_ui),
            new FileUploadScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }
}
