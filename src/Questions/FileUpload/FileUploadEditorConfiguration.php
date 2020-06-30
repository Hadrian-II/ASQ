<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class FileUploadEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadEditorConfiguration extends AbstractConfiguration
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
     * @return FileUploadEditorConfiguration
     */
    public static function create(?int $maximum_size = null, ?string $allowed_extensions = null) : FileUploadEditorConfiguration
    {
        $object = new FileUploadEditorConfiguration();
        $object->maximum_size = $maximum_size;
        $object->allowed_extensions = $allowed_extensions;
        return $object;
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

    public function equals(AbstractValueObject $other) : bool
    {
        /** @var FileUploadEditorConfiguration $other */
        return get_class($this) === get_class($other) &&
               $this->maximum_size === $other->maximum_size &&
               $this->allowed_extensions === $other->allowed_extensions;
    }
}
