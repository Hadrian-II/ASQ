<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

/**
 * Class AsqTableInputFieldDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqTableInputFieldDefinition
{
    const TYPE_TEXT = 1;
    const TYPE_TEXT_AREA = 2;
    const TYPE_NUMBER = 3;
    const TYPE_IMAGE = 4;
    const TYPE_RADIO = 5;
    const TYPE_DROPDOWN = 6;
    const TYPE_BUTTON = 7;
    const TYPE_HIDDEN = 8;
    const TYPE_LABEL = 9;
    const TYPE_CHECKBOX = 10;
    const TYPE_REALTEXT = 11;

    const OPTION_MAX_LENGTH = 'max_length';

    private string $header;

    private int $type;

    private string $post_var;

    private ?array $options;

    public function __construct(string $header, int $type, string $post_var, array $options = null)
    {
        $this->header = $header;
        $this->type = $type;
        $this->post_var = $post_var;
        $this->options = $options;
    }

    public function getHeader() : string
    {
        return $this->header;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function getPostVar() : string
    {
        return $this->post_var;
    }

    public function getOptions() : ?array
    {
        return $this->options;
    }
}
