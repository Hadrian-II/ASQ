# Score answer

ASQ provides the following information and possibilites for scoring answers.
* Get the answer associated with the given UUID
* Get the score of a given Answer
* Get the max score of a question
* Get the best possible answer

## Table of contents

- [Get answer](#get-answer)
- [Get score](#get-score)
- [Get max score](#get-max-score)  
- [Get best answer](#get-best-answer)  
    
<br>
<br>

## Get answer

### Note
Get the answer associated to the provided UUID.

### Usage

```php
$answer = $ASQDIC->asq()->answer()->getAnswer($uuid)
```
    
<br>
<br>


## Get score

### Note

Get the achieved score for the provided answer and question.

### Usage

```php
$score = $ASQDIC->asq()->answer()->getScore($question, $answer)
```
    
<br>
<br>


## Get max score

### Note

Get the maximum possible score of a question.

### Usage

```php
$max_score =  $ASQDIC->asq()->answer()->getMaxScore($question)
```
    
<br>
<br>


## Get best answer

### Note

Gets the best answer that is possible on that question.
A caveat is that some question types do not have a best possible answer (FileUploadQuestions, ...)

### Usage

```php
$best_answer =  $ASQDIC->asq()->answer()->getBestAnswer($question)
```
<br>
<br>