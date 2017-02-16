Feature: Domain Events

  @wip
  Scenario: I send and consume a domain event
    Given I send a random domain event
    Then I should consume the random domain event