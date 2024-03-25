<?php

// https://cs.symfony.com/doc/rules/
$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'increment_style' => ['style' => 'post'],
    ])
;

return $config;
