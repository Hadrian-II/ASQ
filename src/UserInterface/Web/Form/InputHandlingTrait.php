<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use srag\asq\UserInterface\Web\ImageUploader;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Trait Input Handling
 *
 * Method to extract values from Post
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
trait InputHandlingTrait
{
    use PostAccess;

    /**
     * @var ImageUploader
     */
    protected $image_uploader;

    /**
     * Reads float value from POST
     *
     * @param string $postvar
     * @return ?float
     */
    protected function readFloat(string $postvar) : ?float
    {
        if (! $this->isPostVarSet($postvar) ||
            ! is_numeric($this->getPostValue($postvar)))
        {
            return null;
        }

        return floatval($this->getPostValue($postvar));
    }

    /**
     * Reads int value from POST
     *
     * @param string $postvar
     * @return ?int
     */
    protected function readInt(string $postvar) : ?int
    {
        if (! $this->isPostVarSet($postvar) ||
            ! is_numeric($this->getPostValue($postvar)))
        {
            return null;
        }

        return intval($this->getPostValue($postvar));
    }

    /**
     * @param string $postvar
     * @return ?string
     */
    protected function readString(string $postvar) : ?string
    {
        return $this->getPostValue($postvar);
    }

    /**
     * @return ImageUploader
     */
    private function getUploader() : ImageUploader
    {
        if (is_null($this->image_uploader))
        {
            $this->image_uploader = new ImageUploader();
        }

        return $this->image_uploader;
    }

    /**
     * @param string $postvar
     * @return ?string
     */
    protected function readImage(string $postvar) : ?string
    {
        return $this->getUploader()->processImage($postvar);
    }
}