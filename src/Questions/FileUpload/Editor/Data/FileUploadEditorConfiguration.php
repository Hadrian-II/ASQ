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

    /**
     * @var ?int
     */
    protected $maximum_size;

    /**
     * @var ?string
     */
    protected $allowed_extensions;

    /**
     * @param int $maximum_size
     * @param string $allowed_extensions
     * @param int $upload_type
     */
    public function __construct(?int $maximum_size = null, ?string $allowed_extensions = null)
    {
        $this->maximum_size = $maximum_size;
        $this->allowed_extensions = $allowed_extensions;
    }

    /**
     * @return int|NULL
     */
    public function getMaximumSize() : ?int
    {
        return $this->maximum_size;
    }

    /**
     * @return string|NULL
     */
    public function getAllowedExtensions() : ?string
    {
        return $this->allowed_extensions;
    }
}
