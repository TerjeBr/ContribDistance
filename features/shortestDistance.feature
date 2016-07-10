Feature: Get shortest distance between two github contributors to a package

  Scenario: Three contributors that have location information
    Given the github project "abraham/twitteroauth" has these contributors:
      | username | location          |
      | abraham  | Madison, WI       |
      | paazmaya | Helsinki, Finland |
      | Swop     | Paris, France     |
    When I send a "GET" request to "/shortest/abraham/twitteroauth"
    Then the response status code should be 200
    And the response should be in JSON
    And print last JSON response
    And the JSON node "distance" should be equal to "123"
    And the JSON node "unit" should be equal to "km"
