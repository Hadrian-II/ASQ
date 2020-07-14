<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form;

use ilLanguage;
use srag\asq\Questions\FileUpload\Form\Editor\FileUploadEditorConfigurationFactory;
use srag\asq\Questions\FileUpload\Form\Scoring\FileUploadScoringConfigurationFactory;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

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
    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new FileUploadEditorConfigurationFactory($language),
            new FileUploadScoringConfigurationFactory($language),
            new EmptyDefinitionFactory($language),
            new EmptyDefinitionFactory($language)
        );
    }
}
