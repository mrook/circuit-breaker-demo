Feature: Basic functionality

  Scenario: Last book
    Given there are books
    When I look at the overview
    Then I should see the last book

  Scenario: Last review
    Given there are reviews
    When I look at the overview
    Then I should see the last review
