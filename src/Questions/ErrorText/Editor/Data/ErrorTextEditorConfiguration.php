<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;


use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class ErrorTextEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?int
     */
    protected $text_size;
    /**
     * @var ?string
     */
    protected $error_text;

    /**
     *
     * @param string $error_text
     * @param int $text_size
     * @return ErrorTextEditorConfiguration
     */
    public static function create(?string $error_text, ?int $text_size) : ErrorTextEditorConfiguration
    {
        $object = new ErrorTextEditorConfiguration();
        $object->error_text = $error_text;
        $object->text_size = $text_size;
        return $object;
    }

    /**
     * @return ?int
     */
    public function getTextSize() : ?int
    {
        return $this->text_size;
    }

    /**
     * @return ?string
     */
    public function getErrorText() : ?string
    {
        return $this->error_text;
    }

    /**
     * @return string
     */
    public function getSanitizedErrorText() : string
    {
        if ($this->error_text === null) {
            return '';
        }

        $error_text = $this->error_text;
        $error_text = str_replace('#', '', $error_text);
        $error_text = str_replace('((', '', $error_text);
        $error_text = str_replace('))', '', $error_text);
        return $error_text;
    }
}