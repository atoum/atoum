<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\atoum;

use mageekguy\atoum;
use mageekguy\atoum\report\fields\runner\atoum\logo as testedClass;
use mageekguy\atoum\runner;
use mageekguy\atoum\runner\score;

require_once __DIR__ . '/../../../../../runner.php';

class logo extends \mageekguy\atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\report\fields\runner\atoum\cli::class);
    }

    public function testHandleEvent()
    {
        $this
            ->if($score = new score())
            ->and(
                $score
                    ->setAtoumPath($atoumPath = uniqid())
                    ->setAtoumVersion($atoumVersion = uniqid())
            )
            ->and($runner = new runner())
            ->and($runner->setScore($score))
            ->and($field = new testedClass())
            ->then
                ->variable($field->getAuthor())->isNull()
                ->variable($field->getPath())->isNull()
                ->variable($field->getVersion())->isNull()
                ->boolean($field->handleEvent(runner::runStart, $runner))->isTrue()
                ->string($field->getAuthor())->isEqualTo(\mageekguy\atoum\author)
                ->string($field->getPath())->isEqualTo($atoumPath)
                ->string($field->getVersion())->isEqualTo($atoumVersion)
        ;
    }

    public function test__toString()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->castToString($field)->isEqualTo("
              \033[48;5;16m  \033[0m   \033[0m                             \033[0m \033[48;5;16m  \033[0m
            \033[48;5;16m    \033[0m                                 \033[48;5;16m   \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;250m \033[48;5;16m  \033[0m                             \033[48;5;16m  \033[48;5;250m \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;250m   \033[48;5;16m                             \033[48;5;250m   \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;250m            \033[48;5;16m  \033[48;5;153m       \033[48;5;16m  \033[48;5;250m            \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;240m           \033[48;5;16m  \033[48;5;153m \033[48;5;111m         \033[48;5;153m \033[48;5;16m  \033[48;5;240m           \033[48;5;16m \033[0m
              \033[48;5;16m           \033[48;5;153m \033[48;5;111m             \033[48;5;153m \033[48;5;16m           \033[0m
                      \033[0m \033[48;5;16m  \033[48;5;153m \033[48;5;111m             \033[48;5;153m \033[48;5;16m  \033[0m
                      \033[48;5;16m   \033[48;5;153m \033[48;5;111m   \033[48;5;16m  \033[48;5;111m   \033[48;5;16m  \033[48;5;111m   \033[48;5;153m \033[48;5;16m   \033[0m
                    \033[48;5;16m  \033[48;5;68m \033[48;5;16m  \033[48;5;153m \033[48;5;111m   \033[48;5;16m  \033[48;5;111m   \033[48;5;16m  \033[48;5;111m   \033[48;5;153m \033[48;5;16m  \033[48;5;68m \033[48;5;16m  \033[0m
                    \033[48;5;16m     \033[48;5;153m \033[48;5;111m             \033[48;5;153m \033[48;5;16m     \033[0m
                      \033[0m \033[48;5;16m    \033[48;5;153m \033[48;5;111m         \033[48;5;153m \033[48;5;16m    \033[0m
                        \033[0m \033[48;5;16m  \033[48;5;153m \033[48;5;111m         \033[48;5;153m \033[48;5;16m  \033[0m
                        \033[0m \033[48;5;16m  \033[48;5;68m           \033[48;5;16m  \033[0m
                        \033[0m \033[48;5;16m  \033[48;5;68m  \033[48;5;16m   \033[48;5;68m \033[48;5;16m   \033[48;5;68m  \033[48;5;16m  \033[0m
                          \033[0m \033[48;5;16m  \033[48;5;68m       \033[48;5;16m  \033[0m
                            \033[0m \033[48;5;16m       \033[0m
            \033[0m" . PHP_EOL)
        ;
    }
}
