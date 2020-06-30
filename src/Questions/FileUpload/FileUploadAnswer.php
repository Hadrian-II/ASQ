<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use srag\asq\Domain\Model\Answer\Answer;

/**
 * Class FileUploadAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadAnswer extends Answer
{
    /**
     * @var ?string[]
     */
    protected $files;
    
    public static function create(?array $files = [])
    {
        $object = new FileUploadAnswer();
        $object->files = $files;
        return $object;
    }
    
    public function getFiles() : ?array
    {
        return $this->files;
    }
}
