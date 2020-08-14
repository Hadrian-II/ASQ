<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form;

use ilLanguage;
use srag\asq\Questions\FileUpload\Form\Editor\FileUploadEditorConfigurationFactory;
use srag\asq\Questions\FileUpload\Form\Scoring\FileUploadScoringConfigurationFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use ILIAS\DI\UIServices;

/**
 * Class FileUploadFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui)
    {
        parent::__construct(
            new FileUploadEditorConfigurationFactory($language, $ui),
            new FileUploadScoringConfigurationFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }
}
