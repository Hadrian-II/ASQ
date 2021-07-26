<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

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
    protected ImageUploader $image_uploader;

    private function getUploader() : ImageUploader
    {
        if (is_null($this->image_uploader)) {
            $this->image_uploader = new ImageUploader();
        }

        return $this->image_uploader;
    }

    protected function readBool(?string $value) : ?bool
    {
        if (!(is_bool($value) || is_numeric($value))) {
            return null;
        }

        return boolval($value);
    }

    protected function readFloat(?string $value) : ?float
    {
        $value = str_replace(',', '.', $value);

        if (!is_numeric($value)) {
            return null;
        }

        return floatval($value);
    }

    protected function readInt(?string $value) : ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        return intval($value);
    }

    protected function readString(?string $value) : ?string
    {
        return strip_tags($value);
    }

    protected function readImage(string $postvar) : ?string
    {
        return $this->getUploader()->processImage($postvar);
    }
}
