# Get questions

There are two ways to fetch questions:
* Get all questions of a container by their ILIAS Object ID
* Get a single question by their UUID 

<br>
<br>


## Table of contents

- [Get a single question by UUID](#get-a-single-question-by-uuid)  
- [Get question component for test runs](#get-question-component-for-test-runs)  
    
<br>
<br>



## Get a single question by UUID

### Note

The ASQ identifies questions with a Version 4 UUID. You may get any questions of the installation regardless of whether the current application has created the question or not by using the UUID.

### Usage

```php
$question_dto = $ASQDIC->asq()->question()->getQuestionByQuestionId($uuid_object);
```
    
<br>
<br>


## Get question component for test runs

### Note

The ASQ provides a question component without any form container. You have to enclose the component with your own form container.

The component can be provided with an answer through the method setAnswer().

The answer of the user can be extracted from the component using the method readAnswer();

### Usage

```php
$question_component = $ASQDIC->asq()->ui()->getQuestionComponent($question_dto);
        
$save_button = ilSubmitButton::getInstance();
$save_button->setCaption($DIC->language()->txt('submit_answer'), false);
$save_button->setCommand(self::CMD_RUN_TEST);

$DIC->ui()->mainTemplate()->setContent(
    '<form method="post" action="' . 
                $DIC->ctrl()->getFormAction(
                    $this, self::CMD_RUN_TEST
                ) . '">' .
                $DIC->ui()->renderer()->render($question_component). '<br />' .
                $save_button->render() .
    '</form>'
);
```





