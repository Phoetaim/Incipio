Feature: Default Doctypes
  The provided doctypes can be published out of the box.

  # the "@createSchema" annotation provided by Behat creates a temporary SQLite database for testing the API
  @createSchema
  Scenario: I can export the default AP
    Given I am logged in as "admin"
    Given I am on "/Documents/Publiposter/AP/etude/2"
    Then the response status code should not be 500
    Then the response status code should be 200


  # The "@dropSchema" annotation must be added on the last scenario of the feature file to drop the temporary SQLite database
  @dropSchema
  Scenario: I can export the default CC
    Given I am logged in as "admin"
    Given I am on "/Documents/Publiposter/CC/etude/2"
    Then the response status code should not be 500
    Then the response status code should be 200
