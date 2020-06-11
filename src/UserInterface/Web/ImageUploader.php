<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web;

use ILIAS\FileUpload\Location;
use ILIAS\FileUpload\DTO\ProcessingStatus;
use ILIAS\Data\UUID\Factory;

/**
 * Class ImageUploader
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageUploader {
    const BASE_PATH = 'asq/images/%d/%d/';

    /**
     * @var array
     */
    private $request_uploads;

    /**
     * @var Factory
     */
    private $guid_factory;

    public function __construct() {
        $this->request_uploads = [];
        $this->guid_factory = new Factory();
    }

    /**
     * @return string
     */
    public function processImage(string $image_key) : string {
        global $DIC;
        $upload = $DIC->upload();
        $target_file = "";

        if ($upload->hasUploads() && !$upload->hasBeenProcessed()) {
            $upload->process();

            foreach ($upload->getResults() as $result)
            {
                if ($result && $result->getStatus()->getCode() === ProcessingStatus::OK) {
                    $pathinfo    = pathinfo($result->getName());
                    $target_file = $this->guid_factory->uuid4AsString() . "." . $pathinfo['extension'];
                    $upload->moveOneFileTo(
                        $result,
                        self::processBasePath($target_file),
                        Location::WEB,
                        $target_file);

                    foreach ($_FILES as $key => $value) {
                        if ($value['name'] === $result->getName()) {
                            $this->request_uploads[$key] = $this->getImagePath($target_file);
                        }
                    }
                }
            }
        }

        // delete selected
        //TODO search ilias source for hopefully existing _delete constant
        if (array_key_exists($image_key . '_delete', $_POST)) {
            return '';
        }

        // new file uploaded
        if (array_key_exists($image_key, $this->request_uploads)) {
            return $this->request_uploads[$image_key];
        }

        // old file exists
        if (!empty($_POST[$image_key])) {
            return $_POST[$image_key];
        }

        // no file
        return '';
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getImagePath(string $filename) : string {
        return ILIAS_HTTP_PATH . '/' . ILIAS_WEB_DIR . '/' . CLIENT_ID .  '/' . self::processBasePath($filename) . $filename;
    }

    /**
     * @param string $filename
     * @return string
     */
    private function processBasePath(string $filename) : string {
        if (strlen($filename) < 2) {
            $first = '0';
            $second = '0';
        }
        else {
            $first = $filename[0];
            $second = $filename[1];
        }

        return sprintf(self::BASE_PATH, $first, $second);
    }
}