<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface ilAsqQuestionSolution
 *
 * @author    Björn Heyser <info@bjoernheyser.de>
 * @version    $Id$
 *
 * @package    Services/AssessmentQuestion
 */
interface ilAsqQuestionSolution
{
	/**
	 * @param integer $solutionId
	 */
	public function setSolutionId($solutionId) : void;
	
	/**
	 * @return integer
	 */
	public function getSolutionId() : int;
	
	/**
	 * @param integer $questionId
	 */
	public function setQuestionId($questionId) : void;
	
	/**
	 * @return integer
	 */
	public function getQuestionId() : int;
	
	/**
	 * Loads soluton data
	 */
	public function load() : void;
	
	/**
	 * Saves solution data
	 */
	public function save() : void;
	
	/**
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 */
	public function initFromServerRequest(\Psr\Http\Message\ServerRequestInterface $request) : void;
	
	/**
	 * @return bool
	 */
	public function isEmpty() : bool;
}