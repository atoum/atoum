# features/configuration.feature
Feature: configuration
  I need to be able to use a configuration file when i use an atoum PHAR archive

Scenario: using short configuration file argument
  Given i have an atoum PHAR archive
  And i have a configuration file "configuration.php" which contents "<?php echo PHP_EOL . 'The configuration file is included' . PHP_EOL; ?>"
  When i run atoum PHAR archive with "-c configuration.php" argument
  Then the output must match "/The configuration file is included/"

Scenario: using short configuration file argument and several configuration files
  Given i have an atoum PHAR archive
  And i have a configuration file "configuration1.php" which contents "<?php echo PHP_EOL . 'The configuration file 1 is included' . PHP_EOL; ?>"
  And i have a configuration file "configuration2.php" which contents "<?php echo PHP_EOL . 'The configuration file 2 is included' . PHP_EOL; ?>"
  When i run atoum PHAR archive with "-c configuration1.php configuration2.php" argument
  Then the output must match "/The configuration file 1 is included/"
  And the output must match "/The configuration file 2 is included/"

Scenario: using long configuration file argument
  Given i have an atoum PHAR archive
  And i have a configuration file "configuration.php" which contents "<?php echo PHP_EOL . 'The configuration file is included' . PHP_EOL; ?>"
  When i run atoum PHAR archive with "--configuration-files configuration.php" argument
  Then the output must match "/The configuration file is included/"

Scenario: using short configuration file argument and several configuration files
  Given i have an atoum PHAR archive
  And i have a configuration file "configuration1.php" which contents "<?php echo PHP_EOL . 'The configuration file 1 is included' . PHP_EOL; ?>"
  And i have a configuration file "configuration2.php" which contents "<?php echo PHP_EOL . 'The configuration file 2 is included' . PHP_EOL; ?>"
  When i run atoum PHAR archive with "--configuration-files configuration1.php configuration2.php" argument
  Then the output must match "/The configuration file 1 is included/"
  And the output must match "/The configuration file 2 is included/"
