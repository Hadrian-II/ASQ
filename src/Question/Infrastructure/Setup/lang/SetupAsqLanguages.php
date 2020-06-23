<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\lang;

/**
 * Class SetupAsqLanguages
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class SetupAsqLanguages extends SetupLanguages
{
    public function getLanguagePrefix(): string
    {
        return "asq";
    }
}

