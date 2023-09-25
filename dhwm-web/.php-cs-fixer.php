#!/usr/bin/env php
<?php
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$header = <<<'EOF'
稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。

@author  Yiba <yibafun@gmail.com>
@link    https://github.com/daohua-co/daohua-waimai
@license MIT (http://opensource.org/licenses/MIT)
EOF;

$rules = [
    '@PSR12' => true,
    '@Symfony' => true,
    '@DoctrineAnnotation' => true,
    '@PhpCsFixer' => true,
    'header_comment' => [
        'comment_type' => 'PHPDoc',
        'header' => $header,
        'separate' => 'none',
        'location' => 'after_open',
    ],
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'list_syntax' => [
        'syntax' => 'short',
    ],
    'concat_space' => [
        'spacing' => 'one',
    ],
    'blank_line_before_statement' => [
        'statements' => [
            'declare',
        ],
    ],
    'general_phpdoc_annotation_remove' => [
        'annotations' => [
            'author',
        ],
    ],
    'ordered_imports' => [
        'imports_order' => [
            'class', 'function', 'const',
        ],
        'sort_algorithm' => 'alpha',
    ],
    'single_line_comment_style' => [
        'comment_types' => [
        ],
    ],
    'yoda_style' => [
        'always_move_variable' => false,
        'equal' => false,
        'identical' => false,
    ],
    'phpdoc_align' => [
        'align' => 'left',
    ],
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'no_multi_line',
    ],
    'constant_case' => [
        'case' => 'lower',
    ],
    'class_attributes_separation' => true,
    'combine_consecutive_unsets' => true,
    'declare_strict_types' => true,
    'linebreak_after_opening_tag' => false,
    'lowercase_static_reference' => true,
    'no_useless_else' => true,
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => true,
    'not_operator_with_space' => false,
    'ordered_class_elements' => true,
    'php_unit_strict' => false,
    'phpdoc_separation' => false,
    'single_quote' => true,
    'standardize_not_equals' => true,
    'multiline_comment_opening_closing' => true,
];

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->exclude(__DIR__ . '/vendor')
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(false);
