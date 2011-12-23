# features/version.feature
Feature: version
  I need to be able to see the atoum's version from the command line when i use an atoum PHAR archive

Scenario: using short version argument
  Given i have an atoum PHAR archive
  When i run atoum PHAR archive with "-v" argument
  Then the output must match "/^atoum version [^ ]+ by Frédéric Hardy \([^)]+\)$/"

Scenario: using long version argument
  Given i have an atoum PHAR archive
  When i run atoum PHAR archive with "--version" argument
  Then the output must match "/^atoum version [^ ]+ by Frédéric Hardy \([^)]+\)$/"
