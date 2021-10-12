<?php

use PhpCsFixer as CS;

$finder = CS\Finder::create()
    ->files()
        ->name(__FILE__)
        ->name('*.php')
        ->name('*.php.dist')
        ->notPath('resources/templates')
        ->in(__DIR__)
;

return
    (new CS\Config())
        ->setRules([
            '@PSR12'                             => true,
            'array_syntax'                       => ['syntax' => 'short'],
            'blank_line_after_opening_tag'       => true,
            'cast_spaces'                        => true,
            'concat_space'                       => ['spacing' => 'one'],
            'native_function_casing'             => true,
            'no_alias_functions'                 => true,
            'no_blank_lines_after_class_opening' => true,
            'no_leading_import_slash'            => true,
            'no_unused_imports'                  => true,
            'no_whitespace_in_blank_line'        => true,
            'ordered_imports'                    => true,
            'phpdoc_no_access'                   => true,
            'self_accessor'                      => true,
        ])
        ->setRiskyAllowed(true)
        ->setFinder($finder)
;
