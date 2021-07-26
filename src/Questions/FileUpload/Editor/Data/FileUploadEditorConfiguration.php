<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FileUploadEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadEditorConfiguration extends AbstractValueObject
{
    protected ?int $maximum_size;

    protected ?string $allowed_extensions;

    public function __construct(?int $maximum_size = null, ?string $allowed_extensions = null)
    {
        $this->maximum_size = $maximum_size;
        $this->allowed_extensions = $allowed_extensions;
    }

    public function getMaximumSize() : ?int
    {
        return $this->maximum_size;
    }

    public function getAllowedExtensions() : ?string
    {
        return $this->allowed_extensions;
    }
}
