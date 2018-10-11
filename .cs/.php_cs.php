<?php

return PhpCsFixer\Config::create()
                        ->setRules(
                            [
                                '@Symfony' => true,
                                '@Symfony:risky' => true,
                                'concat_space' => [ 'spacing' => 'one' ],
                                'array_syntax' => [ 'syntax' => 'short' ],
                                'binary_operator_spaces' => [
                                    'align_equals' => true,
                                    'align_double_arrow' => true,
                                ],
                                'ordered_imports' => true,
                                'phpdoc_order' => true,
                                'linebreak_after_opening_tag' => true,
                                'phpdoc_no_package' => false,
                                'cast_spaces' => false,
                            ]
                        )
                        ->setRiskyAllowed( true )
                        ->setFinder(
                            PhpCsFixer\Finder::create()
                                             ->in( __DIR__ . '/../src' )
                                             ->files()->name( '*.php' )
                        );
