<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class FileUploadStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return FileUploadConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        // does not exist
        return '';
    }

    public function getSetup(): string
    {
        return SetupFileUpload::class;
    }
}