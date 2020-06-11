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
     * @var AsqHtmlPurifier
     */
    protected $purifier;

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
        if (! array_key_exists($postvar, $_POST) ||
            ! is_numeric($_POST[$postvar]))
        {
            return null;
        }

        return floatval($_POST[$postvar]);
    }

    /**
     * Reads int value from POST
     *
     * @param string $postvar
     * @return ?int
     */
    protected function readInt(string $postvar) : ?int
    {
        if (! array_key_exists($postvar, $_POST) ||
            ! is_numeric($_POST[$postvar]))
        {
            return null;
        }

        return intval($_POST[$postvar]);
    }

    /**
     * @return AsqHtmlPurifier
     */
    private function getPurifier() : AsqHtmlPurifier
    {
        if (is_null($this->purifier))
        {
            $this->purifier = new AsqHtmlPurifier();
        }

        return $this->purifier;
    }

    /**
     * @param string $postvar
     * @return ?string
     */
    protected function readString(string $postvar) : ?string
    {
        if (! array_key_exists($postvar, $_POST))
        {
            return null;
        }

        return $this->getPurifier()->purify($_POST[$postvar]);
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