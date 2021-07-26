<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use ILIAS\UI\Implementation\Component\Input\Field\Input;

/**
 * Interface IQuestionFormFactory
 *
 * Contains all the factories needed to create a Question object
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IQuestionFormFactory
{

    /**
     * Gets the form fields of the question type
     *
     * @param QuestionPlayConfiguration $config
     * @return Input[]
     */
    public function getFormFields(QuestionPlayConfiguration $config) : array;

    /**
     * Reads QuestionConfiguration from Inputdata
     *
     * @param array $postdata
     * @return QuestionPlayConfiguration
     */
    public function readQuestionPlayConfiguration(array $postdata) : QuestionPlayConfiguration;

    /**
     * Get Default configuration of Question Type
     *
     * @return QuestionPlayConfiguration
     */
    public function getDefaultPlayConfiguration() : QuestionPlayConfiguration;

    /**
     * Return Answer option Table field definitions of object
     *
     * @return AsqTableInputFieldDefinition[]
     */
    public function getAnswerOptionDefinitions(?QuestionPlayConfiguration $play) : array;

    /**
     * Return true if Question has Answer Options (displays answer option table)
     *
     * @return bool
     */
    public function hasAnswerOptions() : bool;

    /**
     * Returns AsqTableInput Options array
     * Used to allow/disallow moving of positions of answer options, add/delete
     *
     * @return array
     */
    public function getAnswerOptionConfiguration() : array;

    /**
     * Gets the raw values from the answer option objects to display in form
     *
     * @param ?array $options
     * @return array
     */
    public function getAnswerOptionValues(?array $options) : array;

    /**
     * Reads answer option objects from raw data entered in form
     *
     * @param array $values
     * @return array
     */
    public function readAnswerOptions(array $values) : array;

    /**
     * Returns an array of .JS files that are to be used in the authoring form
     *
     * @return array
     */
    public function getScripts() : array;

    /**
     * Method that gets called after question has ben read from the form
     *
     * @param QuestionDto $question
     * @return QuestionDto
     */
    public function performQuestionPostProcessing(QuestionDto $question) : QuestionDto;
}
