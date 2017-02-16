Feature: Task

  Scenario: I send and consume a task
    Given I send a random task
    Then I should consume the random task