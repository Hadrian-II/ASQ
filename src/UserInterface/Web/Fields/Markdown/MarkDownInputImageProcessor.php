<?php

/* Copyright (c) 2021 Adrian Lüthi <adi.l@bluewin.ch> Extended GPL, see docs/LICENSE */

namespace srag\asq\UserInterface\Web\Fields\Markdown;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\UI\Component as C;
use ILIAS\Data\UUID\Factory;

/**
 * Class MarkDownInputImageProcessor
 *
 * Processes base64 files back to file files
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MarkDownInputImageProcessor
{
    /**
     * @var string
     */
    private $old_markup;

    /**
     * @var string
     */
    private $new_markup = '';

    /**
     * @var array
     */
    private $images = [];

    private $uuid_factory;

    public function __construct(string $markup)
    {
        $this->old_markup = $markup;
        $this->uuid_factory = new Factory();
    }

    private $image_match = '/!\[([^\]]*)\]\(([^\)]*\))/';

    public function process()
    {
        $this->new_markup = preg_replace_callback($this->image_match, [$this, 'processMatch'], $this->old_markup);
    }

    private function processMatch(array $match) : string
    {
        $description = $match[1];
        $image = $match[2];

        if (strpos($image, 'data') === 0) {
            $image = $this->processImage($image);
        }

        return sprintf('![%s](%s)', $description, $image);
    }

    private $base64_match = "/data:[^\/]*\/([^;]*);base64,([^)]*)\)/";

    private function processImage(string $image_data) : string
    {
        $matches = [];

        preg_match($this->base64_match, $image_data, $matches);

        $extension = $matches[1];
        $data = base64_decode($matches[2]);

        global $DIC;

        $filename = sprintf('rte/%s.%s', $this->uuid_factory->uuid4(), $extension);

        $DIC->filesystem()->web()->write($filename, $data);

        return 'http://' . $_SERVER['SERVER_NAME'] . '/' . ILIAS_WEB_DIR . '/' . CLIENT_ID . '/' . $filename;
    }

    public function getProcessedMarkup() : string
    {
        return $this->new_markup;
    }

    public function getImages() : array
    {
        return $this->images;
    }
}