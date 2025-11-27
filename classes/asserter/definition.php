<?php

namespace atoum\atoum\asserter;

use atoum\atoum;

interface definition
{
    public function setLocale(?atoum\locale $locale = null): static;
    public function setGenerator(?atoum\asserter\generator $generator = null): static;
    public function setWithTest(atoum\test $test): static;
    public function setWith(mixed $mixed): static;
    public function setWithArguments(array $arguments): static;
}
