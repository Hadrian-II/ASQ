<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FileUploadAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadAnswer extends AbstractValueObject
{
    /**
     * @var ?string[]
     */
    protected array $files;

    public function __construct(?array $files = [])
    {
        $this->files = $files;
    }

    public function getFiles() : ?array
    {
        return $this->files;
    }
}
