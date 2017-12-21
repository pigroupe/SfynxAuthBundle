@mink:my_session_selenium
Feature: I would like to log in to the system

  Background: Anonymous access to login page
    Given I am logged as "anonymous"
    And I go to "/login"

  @priority_hight
  Scenario: Log in as user
    Given I am logged as "anonymous"
    And I go to "/en/"
    Then the response should contain "form-connexion"
    Then I should not see "user"
    And I fill in "_username" with "user"
    And I fill in "_password" with "user"
    And I press "OK"
    When I wait for 3 seconds
    Then I should see "user"
    Given I am logged as "user"
    When I follow "Logout"
    When I wait for 2 seconds
    Then the response should contain "form-connexion"
