<?php
/**
 * @see https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */
$excluded_folders = [
    'vendor',
    'src/bootstrap',
];
$finder = PhpCsFixer\Finder::create()
    ->exclude($excluded_folders)
    ->notName('_ide_helper.php')
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/.php-cs.cache')
    ->setRules([
        '@Symfony'                               => true,
        '@PhpCsFixer'                            => true,
        'binary_operator_spaces'                 => ['default' => 'single_space', 'operators' => ['=' => 'align_single_space_minimal', '=>' => 'align_single_space_minimal']],
        'not_operator_with_successor_space'      => true,
        'concat_space'                           => ['spacing' => 'one'],
        'no_superfluous_phpdoc_tags'             => false,
        'single_line_throw'                      => false,
        'return_assignment'                      => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'blank_line_before_statement'            => ['statements' => ['break', 'continue', 'default', 'exit', 'goto', 'include', 'include_once', 'phpdoc', 'require', 'require_once', 'return', 'switch', 'throw', 'try']],
        'phpdoc_to_comment'                      => ['ignored_tags' => ['var']],
        'global_namespace_import'                => true,
        'ordered_types'                          => false,
        'fully_qualified_strict_types'           => false,
        'string_implicit_backslashes'            => false,
    ])->setFinder($finder);
