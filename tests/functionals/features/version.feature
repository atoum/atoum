# features/version.feature
Feature: version
  I need to be able to see the atoum's version from the command line

Scenario: with short argument
  Given I am in a directory "atoum"
  And I have a PHAR archive named "mageekguy.atoum.phar"
  When I run "php mageekguy.atoum.phar -v"
  Then the output must be "atoum version DEVELOPMENT by Frédéric Hardy (phar:///Users/fch/Tmp/mageekguy.atoum.phar)"
