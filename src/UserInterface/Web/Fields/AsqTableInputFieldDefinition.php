<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields;

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

    /**
     * @var string
     */
    private $header;
    /**
     * @var int
     */
    private $type;
    /**
     * @var string
     */
    private $post_var;
    /**
     * @var ?array
     */
    private $options;


    /**
     * AnswerOptionFormFieldDefinition constructor.
     *
     * @param string     $header
     * @param int     $type
     * @param string     $post_var
     * @param array|null $options
     */
    public function __construct(string $header, int $type, string $post_var, array $options = null)
    {
        $this->header = $header;
        $this->type = $type;
        $this->post_var = $post_var;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getHeader() : string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPostVar() : string
    {
        return $this->post_var;
    }

    /**
     * @return array
     */
    public function getOptions() : ?array
    {
        return $this->options;
    }
}
