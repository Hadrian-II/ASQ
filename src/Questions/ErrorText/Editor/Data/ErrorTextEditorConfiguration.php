<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Editor\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ErrorTextEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextEditorConfiguration extends AbstractValueObject
{
    protected ?int $text_size;

    protected ?string $error_text;

    public function __construct(?string $error_text = null, ?int $text_size = null)
    {
        $this->error_text = $error_text;
        $this->text_size = $text_size;
    }

    public function getTextSize() : ?int
    {
        return $this->text_size;
    }

    public function getErrorText() : ?string
    {
        return $this->error_text;
    }

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
