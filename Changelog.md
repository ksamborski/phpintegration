## 0.9.1

Bugfixes:

  - fixed setting proper exit code

## 0.9

Features:

  - measure function for measuring execution time of specified test parts
  
## 0.8

Features:

  - pre and post group function now take run parameters as an argument

## 0.7

Features:

  - test groups

## 0.6

Features:

  - random multidimensional array generator in RandomHelper

## 0.5.1

Bugfixes:

  - fixed checking for different number of array's elements in ArrayHelper::equal
  - added different comparison for float values in ArrayHelper::equal

## 0.5

Features:

  - added test description

Bugfixes:

  - fixed catching exceptions

## 0.4.1

Bugfixes:

  - fixed setting bad status code when last test succeeded and at least one of the earlier tests failed

## 0.4

Features:

  - RandomHelper::randomDate
  - TreeHelper class for path generation
  - ArrayHelper::unsetPath and ArrayHelper::updatePath for working with paths
  - paths example in the examples folder

## 0.3.1

Bugfixes:

  - fixed passing multiple parameters

## 0.3

Features:

  - ArrayHelper::equal and ObjectHelper::equal for array and object comparision
  - Eq interface and trait that can be used in ArrayHelper::equal and ObjectHelper::equal
  - unit tests for RandomHelper and for equality checking

Bugfixes:

  - fixed invalid email generation
  - fixed RandomHelper::randomOneOf
