<?php

use PhpCsFixer\Config;
use PhpCsFixerCustomFixers\Fixer\NoSuperfluousConcatenationFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessCommentFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessParenthesisFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessStrlenFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocParamTypeFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceAfterStatementFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceBeforeStatementFixer;
use PhpCsFixerCustomFixers\Fixers;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$header = <<<EOF
(c) shopware AG <info@shopware.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return (new Config())
    ->registerCustomFixers(new Fixers())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,

        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => false,
        'class_attributes_separation' => ['elements' => ['method' => 'one', 'property' => 'one']],
        'concat_space' => ['spacing' => 'one'],
        'doctrine_annotation_indentation' => true,
        'doctrine_annotation_spaces' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['copyright', 'category']],
        'header_comment' => ['header' => $header, 'separate' => 'bottom', 'comment_type' => 'PHPDoc'],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'native_constant_invocation' => true,
        'native_function_invocation' => ['scope' => 'all', 'strict' => false],
        'no_superfluous_phpdoc_tags' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'operator_linebreak' => ['only_booleans' => true],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'phpdoc_summary' => false,
        'phpdoc_var_annotation_correct_order' => true,
        'php_unit_test_case_static_method_calls' => true,
        'single_line_throw' => false,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],

        NoSuperfluousConcatenationFixer::name() => true,
        NoUselessCommentFixer::name() => true,
        NoUselessStrlenFixer::name() => true,
        NoUselessParenthesisFixer::name() => true,
        PhpdocParamTypeFixer::name() => true,
        SingleSpaceAfterStatementFixer::name() => true,
        SingleSpaceBeforeStatementFixer::name() => true,
    ])->setFinder($finder);
