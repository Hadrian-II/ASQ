<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilAsqService
 *
 * @author    Björn Heyser <info@bjoernheyser.de>
 * @version    $Id$
 *
 * @package    Services/AssessmentQuestion
 */
class ilAsqService
{
	/**
	 * @param ilCtrl $ctrl
	 * @return string
	 */
	public function fetchNextAuthoringCommandClass($nextClass)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$row = $DIC->database()->fetchAssoc($DIC->database()->queryF(
			"SELECT COUNT(question_type_id) cnt FROM qpl_qst_type WHERE ctrl_class = %s",
			array('text'), array($nextClass)
		));
		
		if( $row['cnt'] )
		{
			// current next class is indeed an authoring ctrl class,
			// return it to have the switch(nextclass) case matching
			return $nextClass;
		}
		
		// the interface that NOT represents a valid ctrl class,
		// this will lead to a non matching switch(nextclass) case
		return 'ilasqquestionauthoring';
	}
	
	/**
	 * @param ilQTIItem $qtiItem
	 * @return string
	 */
	public function determineQuestionTypeByQtiItem(ilQTIItem $qtiItem)
	{
		// the qti service parses ILIAS question types, so use it
		// although this may get changed in the future 
		return $qtiItem->getQuestiontype();
	}
}