# Edit questions

ASQ provides the following links to the authoring environment:
* Edit Question
* Preview Question
* Edit Page
* Edit Feedback

Additionally, it's possible to use a single independent edit form.
<br>
<br>


## Table of contents

- [Usage of the Edit Links](#usage-of-the-edit-links)
- [Get question edit form](#get-question-edit-form)
    
<br>
<br>


## Usage of the edit links

### Note

You can get the links by passing the Question UUID:

```php
$ASQDIC->asq()->link()->getEditLink($uuid_object)
```

## Get question edit form

### Note
Returns the form used to edit the question. The form is of the type ilPropertyFormGUI and works like one.
It is easier to use the provided classes listed above.

### Usage
```php
$form = $ASQDIC->asq()->ui()->getQuestionEditForm($question_dto, $DIC->ctrl()->getFormAction($this, self::CMD_SHOW_FORM));
$DIC->ui()->mainTemplate()->setContent($form->getHTML());
```

To save:

```php
$question = $form->getQuestion();
$ASQDIC->asq()->question()->saveQuestion($question);
```