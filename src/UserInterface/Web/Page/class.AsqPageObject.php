<?php

/**
 * Class AsqPageObject
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqPageObject extends \ilPageObject
{
    public $parent_type = 'asqq';

    /**
     * @return string parent type
     */
    public function getParentType() : string
    {
        return $this->parent_type;
    }

    /**
     * @return string parent type
     */
    public function setParentType($parent_type) : void
    {
        $this->parent_type = $parent_type;
    }

    /**
     * @param int $questionIntId
     *
     * @return string
     */
    public function getXMLContent($a_incl_head = false)
    {
        $xml = "<PageObject>";
        $xml .= "<PageContent>";
        $xml .= "<Question QRef=\"il__qst_{$this->getId()}\"/>";
        $xml .= "</PageContent>";
        $xml .= "</PageObject>";

        return $xml;
    }
}
