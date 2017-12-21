@mink:my_session_selenium
Feature: I would like to log in to the system

  Background: Anonymous access to login page
    Given I am logged as "anonymous"
    And I go to "/login"

  @priority_hight
  Scenario: Unsuccessful login
    Given I go to "/login"
    Then the response should contain "behatFormLogin"
    Then I should not see "Logged in as admin" and "Connecté en tant que admin"
    And I fill in "username" with "wrong username"
    And I fill in "password" with "wrong password"
    And I press "Connexion"
    Then I should see "Username or password incorrect" or "utilisateur ou mot de passe incorrect"