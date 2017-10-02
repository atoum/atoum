<?php

use PhpCsFixer as CS;

$finder = CS\Finder::create()
    ->files()
        ->name(__FILE__)
        ->name('*.php')
        ->name('*.php.dist')
        ->in(__DIR__)
;

return
    CS\Config::create()
        ->setRules([
            '@PSR2'                              => true,
            'array_syntax'                       => ['syntax' => 'short'],
            'blank_line_after_opening_tag'       => true,
            'concat_space'                       => ['spacing' => 'one'],
            'native_function_casing'             => true,
            'no_blank_lines_after_class_opening' => true,
            'no_unused_imports'                  => true,
            'no_unused_imports'                  => true,
            'no_whitespace_in_blank_line'        => true,
            'ordered_imports'                    => true,
            'phpdoc_no_access'                   => true,
        ])
        ->setFinder($finder)
;
