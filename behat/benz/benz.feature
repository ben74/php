@contextNotFoundSoDefaultOne @benz
Feature:
  Just a dummy feature to check for its right mapping and execution with a non existing context, takes screenshots since the second test fails
  
  Background:
    Given baseurl is "https://alpow.fr/"
  Scenario:
      When Page "/" contains "La technologie au service"
      When Page "/" contains "/!\ something that isnt here at all /!\"

    When nothing happens
    When mayday
