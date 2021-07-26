<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Helpers;

/**
 * Trait PathHelper
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
trait PathHelper
{
    public function getBasePath(string $fullpath) : string
    {
        $dir = substr($fullpath, strpos($fullpath, "/Customizing/") + 1);

        if (strpos($dir, "/src")) {
            return substr($dir, 0, strpos($dir, "/src") + 1);
        } else {
            return substr($dir, 0, strpos($dir, "/classes") + 1);
        }
    }
}
