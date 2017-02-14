<?php

use Symfony\CS;

return
    CS\Config::create()
        ->level(CS\FixerInterface::PSR2_LEVEL)
        ->fixers([
            'blankline_after_open_tag',
            'concat_with_spaces',
            'join_function',
            'native_function_casing',
            'no_blank_lines_after_class_opening',
            'ordered_use',
            'phpdoc_no_access',
            'remove_leading_slash_use',
            'remove_leading_slash_uses',
            'self_accessor',
            'short_array_syntax',
            'spaces_cast',
            'unused_use',
            'whitespacy_lines'
        ])
        ->finder(
            CS\Finder::create()
                ->files()
                    ->name(__FILE__)
                    ->name('*.php')
                    ->name('*.php.dist')
                    ->in(__DIR__)
        );
