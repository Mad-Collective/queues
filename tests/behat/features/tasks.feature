Feature: Task

  Scenario: I send and consume a task
    Given I send a random task
    Then I should consume the random task

  Scenario: I send and consume a delayed task
    Given I send a random delayed task
    Then I should consume the random delayed task on time