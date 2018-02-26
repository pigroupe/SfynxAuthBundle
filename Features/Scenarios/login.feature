@mink:my_session_selenium
Feature: I would like to log in to the system

  Background: Anonymous access to login page
    Given I am logged as "anonymous"
    And I go to "/login"

  @priority_hight @connection
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

  @priority_hight @connection
  Scenario: Log in as admin
    Given I am on "/login"
    Then the response should contain "behatFormLogin"
    Then I should not see "Logged in as admin" and "Connecté en tant que admin"
    And I fill in "username" with "admin"
    And I fill in "password" with "admin"
    And I press "Connexion"
    Then I should see "Logged in as admin" or "Connecté en tant que admin"
    Given I am logged as "admin"
    Then the response should not contain "behatFormLogin"
    When I wait for 2 seconds
    And I click on ".connexion-my-account"
    When I wait for 2 seconds
    When I follow "behatLinkProfile"
    When I wait for 2 seconds
    Then the response should contain "user_from"
    When I wait for 2 seconds
    And I click on ".connexion-my-account"
    When I wait for 2 seconds
    When I follow "behatLinkUsers"
    When I wait for 3 seconds
    Then the response should contain "grid_customer_wrapper"
    When I follow "logout"
    Then I should not see "Logged in as admin" and "Connecté en tant que admin"
    Then the response should contain "form-connexion"

  @priority_hight @connection
  Scenario: Unsuccessful login
    Given I go to "/login"
    Then the response should contain "behatFormLogin"
    Then I should not see "Logged in as admin" and "Connecté en tant que admin"
    And I fill in "username" with "wrong username"
    And I fill in "password" with "wrong password"
    And I press "Connexion"
    Then I should see "Username or password incorrect" or "utilisateur ou mot de passe incorrect"