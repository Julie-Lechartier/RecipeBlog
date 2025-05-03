<?php
return [
    'app' => [
        'path' => 'assets/app.js', // Removed './' prefix for consistency
        'entrypoint' => true,
    ],
    'jquery' => [
        'path' => 'node_modules/jquery/dist/jquery.min.js',
    ],
    'bootstrap' => [
        'path' => 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    ],
    'select2' => [
        'path' => 'node_modules/select2/dist/js/select2.min.js',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'path' => 'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'type' => 'css',
    ],
    'select2/dist/css/select2.min.css' => [
        'path' => 'node_modules/select2/dist/css/select2.min.css',
        'type' => 'css',
    ],
    'select2-bootstrap-5-theme' => [
        'path' => 'node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => 'vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
];