<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\ImageUploader;

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
    /**
     * @var ImageUploader
     */
    protected $image_uploader;

    /**
     * @return ImageUploader
     */
    private function getUploader() : ImageUploader
    {
        if (is_null($this->image_uploader)) {
            $this->image_uploader = new ImageUploader();
        }

        return $this->image_uploader;
    }

    /**
     * @var AsqHtmlPurifier
     */
    protected $purifier;

    /**
     * @return AsqHtmlPurifier
     */
    private function getPurifier() : AsqHtmlPurifier
    {
        if (is_null($this->purifier)) {
            $this->purifier = new AsqHtmlPurifier();
        }

        return $this->purifier;
    }

    /**
     * Reads float value from POST
     *
     * @param string $value
     * @return ?float
     */
    protected function readFloat(string $value) : ?float
    {
        if (empty($value) ||
            ! is_numeric($value)) {
            return null;
        }

        return floatval($value);
    }

    /**
     * Reads int value from POST
     *
     * @param string $value
     * @return ?int
     */
    protected function readInt(string $value) : ?int
    {
        if (empty($value) ||
            ! is_numeric($value)) {
            return null;
        }

        return intval($value);
    }

    /**
     * @param string $postvar
     * @return ?string
     */
    protected function readString(string $value) : ?string
    {
        return $this->getPurifier()->purify($value);
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
