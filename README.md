# ILIAS - ASQ - Assessment Question Service

![](2020-07-07_asq_ddd.png)

The ILIAS ASQ is designed as a component that offers services around assessment questions. The way other components can interact with ASQ is as easy and flexible as possible. The ASQ provides a complete authoring and scoring environment for assessment questions.

![](asq_authoring_environment.png)

The ASQ provides no higher level business logic. Those must be handled by the consumer. E.g. the business logic that a single question can only be answered once or the business logic for handling a group of questions so that questions can only have a single answer. 
  
<br>
<br>


# Status
BETA - Feature Complete

<br>
<br>


# History
[History](docs/0_History/README.md)

<br>
<br>


# Features

## Available question types

At the Moment ASQ implements the following Question Types. The available types are identical with the question types offered by the default ILIAS Test. The two old Cloze style Questions have been rolled in one Type. 

It is easyly able to extend and change the existing question types for different usages. See as example the implementation of the old Singlechoice and Multiplechoice questions which are not internally handled as the same Question Type, but offer different configuration GUIs for backward compatibility. (Same with Ordering and TextOrdering Question)

* Cloze	(ConfigForm move to Kitchen Sink UI is broken ATM)
* Error
* Essay
* FileUpload
* Formula
* Kprim
* Matching
* MultipleChoice
* Numeric
* Ordering
* TextSubset

For every Question type exists an Authoring form, Integration into QuestionControl for display purposes and an automatic Scoring Module.
  
# Architecture

The fundamental architecture is a CQRS type inplementation using Event Sourcing for Data Storage.

## Event Sourcing

Event Sourcing is used to preserve History of Data as is requested by some Stakeholders. ([See Feature Wiki](https://docu.ilias.de/goto_docu_wiki_wpage_5312_1357.html)) In discussions about that Feature with the Stakeholders it was clear that some actually want a history and not a Versioning Scheme. But Eventsourced is also the best way to allow for different Versions, as every state of an object that has ever existed can without problems be restored.

Also as test results sometimes are challenged legally the ability to prove what state was at which moment is very important to users. It would even to be possibly to implement forward hashing on an eventsourced storage to be able to guarantee the integrity of the data with absolute certanity.

## CQRS

CQRS and the work with projections follows automatically from Eventsourcing, as performance can be increased enormously by using that approach. Also a Domain driven approach makes the code easyer to understand as the usage and not theoretical abstractions are paramount.
  
## The ASQ Interface

Interaction with ASQ is through the four service it offers to the user. It is important to note that ASQ is only about the question, and things like keeping the References to the questions have to be handled by the object using the question. At the moment there is no functionality avilable used to browse all existing questions in general, but if needed such functionality could easily be added.

### Question Service

The Service used to interact with questions allows creation, reading and updating operations on question objects. At the moment there is no deletion command as we are keeping the data anyway, but there is functionality in our CQRS library that makes every Aggregate deletable (The object is flagged as deleted and no now events are allowed).

Also the service allows the function to create Question Revisions. A revision is a fixed snapshot of a question so that if the question is done after the question has been changed in authoring the test still has the question with the state that is used by the test. Also revisions are projected as full object to increase performance

### Answer Service

This service is mostly used to score Answers and to get the best possible Answer to a question. Scoring is done by Providing a Question and an Answer to that Question. At the moment it is the users task to make sure that the answer is actually the an answer to the question he provides. This functionality coud easily be extended with additional checks if needed, but the prefered path is that in a future move to the QTI standard that answers will correspond to the types defined by the public standard.

Also the answer service allows to get maximum, minimum score of a question. Also most question types allow the automatic generation of a best possible answer that could then be used by test players to show the correct solution.

There is also a possability to store and load answers to the database, but it is strongly recommended to store the answers in the context they are used in. As example our first implementation of a Test player uses a QTI style AssessmentResult aggregate object as answer storage.

### Link Service

The Link Service can be used to generate links to the given question in our authoring environment.

### UI Service

This Service serves as a Factory used to create the ASQ UI elements. Most importantly the Question Component itself which is a Kitchen Sink UI element which can be used to display the question for users to Answer.

## Structure of a Question

A question is structured in the following way. It is an event sourced Aggregate object as defined in our CQRS Library. The basic parts of a question are its Uuid which identifies it and its question type.

The Question Type is an object that contains the classes that are used by that type, on the question object itself is only the key to the type stored, so that if parts of that question are replaced the question is still functioning. New types can be added over the ASQ question type, so it is very easy to create new Questiontypes as plugins.

The Question Data is the basic data that is used by every question type and contains things like the text and author of the question.

The Question Play configuration is the type specific information of a question which is split in an editor and a scoring configuration object. The idea is that editor configuration consists info used for the editor meaning the display of the question to the user, and scoring configuration consists of the info needed to score a question.

Answer options is a list of AnswerOptions used by the Question which is internally also split in data for Editor and Scoring.

The split between Editor ans Scoring allows to reuse parts of a question in different types, as an example the Multiplechoice scoring object is used by both the imagemap and the multiple choice question.

## Question Component

The Quesiton Component is the element of ASQ which is used to display the question. Every question type contains an Editor object as it is defined by the IAsqQuestionEditor interface. Which is then created by the QuestionComponent to display the question. The QuestionComponent can be given an answer through the function ->withAnswer, also ->withAnswerFromPost triggers loading the answer that was answered by the user in the UI.

## Question Authoring

Every questiontype contains a reference to its form factory object which contains object factories that can be used by the ASQ Authoring environment to edit the question.

# Requirements
* PHP 7.3
* ILIAS 7 -
* https://github.com/studer-raimann/cqrs
  
<br>
<br>


# How to use?
* [1 Setting Up](docs/1_Setting_Up/README.md)
* [2 Create Questions](docs/2_Create_Questions/README.md)
* [3 Edit Questions](docs/3_Edit_Questions/README.md)
* [4 Get Questions](docs/4_Get_Questions/README.md)
* [5 Score Answer](docs/5_Score_Answer/README.md)
 
<br>
<br>


# Authors
This is an OpenSource project by studer + raimann ag (https://studer-raimann.ch)
 
<br>
<br>


# License
This project is licensed under the GPL v3 License
 
<br>
<br>


# Credits

## Coordination of Funding
*  toedt@leifos.com

## Funding
* DHBW Karlsruhe
* FH Aachen
* HS Bremen
* ILIAS e.V. Advisory Council
* ILIAS e.V. Technical Board
* PH Freiburg (DE)
* studer + raimann ag, Burgdorf
* Universität Bern
* Universität Freiburg (DE)
* Universität Hohenheim
* Universität Marburg
* Universität zu Köln

## Development and software architecture
* al@studer-raimann.ch
* bh@bjoernheyser.de
* ms@studer-raimann.ch
* tt@studer-raimann.ch

## Quality control
* dw@studer-raimann.ch

## Supervision
* ILIAS SIG E-Assessment, https://docu.ilias.de/goto_docu_grp_5174.html - first of all denis.strassner@uni-hohenheim.de
* ILIAS Technical Board, https://docu.ilias.de/goto_docu_grp_5089.html - first of all stephan.winiker@hslu.ch
