<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web;

/**
 * Trait PostAccess
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
trait PostAccess
{
    /**
     * @var array
     */
    private $post;

    /**
     * @return array
     */
    private function getPost() : array
    {
        if (is_null($this->post))
        {
            global $DIC;

            $this->post = $DIC->http()->request()->getParsedBody();
        }

        return $this->post;
    }

    /**
     * @var AsqHtmlPurifier
     */
    private $purifier;

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
     * @param string $variable_name
     * @return bool
     */
    public function isPostVarSet(string $variable_name) : bool
    {
        return array_key_exists($variable_name, $this->post);
    }

    /**
     * @param string $variable_name
     * @return string
     */
    public function getPostValue(string $variable_name) : ?string
    {
        if (!$this->isVarSet($variable_name)) {
            return null;
        }

        return $this->getPurifier()->purify($this->post[$variable_name]);
    }
}