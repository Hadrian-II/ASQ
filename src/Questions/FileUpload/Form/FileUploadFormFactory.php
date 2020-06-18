<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Questions\FileUpload\FileUploadEditorConfiguration;
use srag\asq\Questions\FileUpload\FileUploadScoringConfiguration;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;

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
    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new FileUploadEditorConfigurationFactory($DIC->language()),
            new FileUploadScoringConfigurationFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()),
            new EmptyDefinitionFactory($DIC->language()));
    }
}