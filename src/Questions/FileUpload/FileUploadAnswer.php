<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FileUploadAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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
