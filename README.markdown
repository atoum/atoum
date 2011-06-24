#atoum

## Un framework de tests unitaires pour PHP simple, moderne et intuitif !

Tout comme SimpleTest ou PHPUnit, *atoum* est un framework de tests unitaires spécifique au langage [PHP](http://www.php.net).
Cependant, il a la particularité d'avoir été conçu dès le départ pour :

* Être *Rapide* à mettre en œuvre ;
* *Simplifier* le développement des tests ;
* Permettre l'écriture de tests unitaires *fiables, lisibles et explicites* ;

Pour cela, il utilise massivement les possibilités offertent par *PHP 5.3*, pour fournir au développeur *une nouvelle façon* d'écrire des tests unitaires.
Ainsi, il s'installe et s'intégre très facilement dans un projet puisqu'il se présente sous la forme d'une *unique archive PHAR*, qui est le seul et unique point d'entrée du développeur.
De plus, grâce à son *interface fluide*, il permet la rédaction des tests unitaires en langage quasiment naturel.
Il facilite également la mise en œuvre du bouchonnage au sein des tests, grâce à une utilisation intelligente des *fonctions anonymes et des fermetures*.
*atoum* propose nativement et par défaut d'exécuter chaque test unitaire au sein d'un processus [PHP](http://www.php.net) séparé afin d'en garantir *l'isolation*.
Et évidemment, son utilisation dans le cadre d'un processus d'intégration continue ne pose aucun problème, et de part sa conception, il est très facile de l'adapter à des besoins spécifiques.
*atoum* réalise de plus tout cela sans sacrifier les performances, puisqu'il a été développé pour avoir une empreinte mémoire réduite tout en autorisant une exécution rapide des tests.
Il est de plus à même de générer des rapports d'exécution des tests unitaires au format Xunit, ce qui lui permet d'être compatible avec des outils d'intégration continue tel que [Jenkins](http://jenkins-ci.org/).
*atoum* permet de plus de générer des rapports de couverture de code, afin de permettre la supervision des tests unitaires.
Enfin, même s'il est actuellement développé principalement sous UNIX, il est également fonctionnel sous Windows.

## Prérequis pour utiliser atoum

*atoum* nécéssite *absolument* une version de PHP supérieure ou égale à 5.3 pour fonctionner.
Si vous souhaitez utiliser *atoum* via son archive PHAR, il faut de plus que [PHP](http://www.php.net] dispose du module `phar`, Normalement disponible par défaut.
Afin de vérifier que vous disposez de ce module sous UNIX, il vous suffit d'exécuter la commande suivante dans votre terminal :

	# php -m | grep -i phar

Si `Phar` ou un équivalent s'affiche, le module est installé.
La génération des rapports au format Xunit nécessite le module `xml`.
L'extension [Xdebug](http://xdebug.org/) est quand à elle requise si vous désirer surveiller le taux de couverture de votre code par vos tests unitaires.

## Un framework de tests unitaires opérationnel en 5 minutes !

### Étape 1 : Installez *atoum*

Il suffit pour cela que vous téléchargiez [son archive PHAR](http://downloads.atoum.org/nightly/mageekguy.atoum.phar) et la stockiez à l'emplacement de votre choix, par exemple dans `/path/to/project/tests/mageekguy.atoum.phar`.
Cette archive PHAR contient la dernière version de développement ayant passé avec succès l'intégralité des tests unitaires d'*atoum*.
Le code source d'*atoum* est également disponible via [son dépôt sur github](https://github.com/mageekguy/atoum).

### Étape 2 : Écrivez votre test

À l'aide de votre éditeur favori, créé le fichier `path/to/project/tests/units/helloWorld.php` et ajoutez-y le code suivant :

	<?php

	namespace vendor\project\tests\units;

	require 'path/to/mageekguy.atoum.phar';

	include 'path/to/project/classes/helloWorld.php';

	use \mageekguy\atoum;
	use \vendor\project;

	class helloWorld extends atoum\test
	{
		public function testSay()
		{
			$helloWorld = new project\helloWorld();

			$this->assert
				->string($helloWorld->say())->isEqualTo('Hello World !')
			;
		}
	}

	?>

### Étape 3 : Exécutez votre test en ligne de commande

Lancer votre terminal et exécutez l'instruction suivante :

	# php path/to/test/file[enter]

Vous devez obtenir le résultat suivant, ou équivalent :

	> Atoum version XXX by Frédéric Hardy.
	Error: Unattended exception: Tested class 'vendor\project\helloWorld' does not exist for test class 'vendor\project\tests\units\helloWorld'

### Étape 4 : Écrivez la classe correspondant à votre test

À nouveau à l'aide de votre éditeur favori, créé le fichier `path/to/project/classes/helloWorld.php` et ajoutez-y le code suivant :

	<?php

	namespace vendor\project;

	class helloWorld
	{
		public function say()
		{
			return 'Hello World !';
		}
	}

	?>

### Étape 5 : Exécutez à nouveau votre test

Toujours  votre terminal, exécutez une nouvelle fois l'instruction suivante :

	# php path/to/test/file[enter]

Vous devez cette fois obtenir le résultat suivant, ou équivalent :

	> Atoum version 288 by Frédéric Hardy.
	> Run vendor\project\tests\units\helloWorld...
	[S___________________________________________________________][1/1]
	=> Test duration: 0.00 second.
	=> Memory usage: 0.25 Mb.
	> Total test duration: 0.00 second.
	> Total test memory usage: 0.25 Mb.
	> Code coverage value: 100.00%
	> Running duration: 0.08 second.
	> Success (1 test, 1 method, 2 assertions, 0 error, 0 exception) !

### Étape 6 : Complétez vos tests et recommencez le cycle à partir de l'étape 3

	<?php

	namespace vendor\project\tests\units;

	require 'path/to/mageekguy.atoum.phar';

	use \mageekguy\atoum;
	use \vendor\project;

	class helloWorld extends atoum\test
	{
		public function test__construct()
		{
			$helloWorld = new project\helloWorld();

			$this->assert
				->string($helloWorld->say())->isEqualTo('Hello !')
				->string($helloWorld->say($name = 'Frédéric Hardy'))->isEqualTo('Hello ' . $name . ' !')
			;
		}
	}

	?>

## Pour aller plus loin

La documentation d'*atoum* est en cours de rédaction et la seule ressource disponible actuellement est le présent document.
Cependant, si vous désirez explorer plus en avant et immédiatement les possibilités d'*atoum*, nous vous conseillons :

* D'exécuter dans votre terminal soit la commande `php mageekguy.atoum.phar -h`, soit `php scripts/runner.php -h` ;
* D'explorer le contenu du répértoire `configurations` des sources d'*atoum*, car il contient des exemples de fichier de configuration ;
* D'explorer le contenu du répértoire `tests/units/classes` des sources d'*atoum*, car il contient l'ensemble de ses tests unitaires ;
* De lire [les supports de conférence](http://www.slideshare.net/impossiblium/atoum-le-framework-de-tests-unitaires-pour-php-53-simple-moderne-et-intuitif) à son sujet disponible en ligne.
