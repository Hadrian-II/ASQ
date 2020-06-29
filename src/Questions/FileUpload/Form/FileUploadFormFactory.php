<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form;

use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;
use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use ilLanguage;

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
            new EmptyDefinitionFactory($language));
    }
}