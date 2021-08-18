<?php

use const OBLAK\NPG\BASENAME;
use const OBLAK\NPG\PATH;
use const OBLAK\NPG\VERSION;

return [
    'version'   => VERSION,
    'priority'  => 10101,
    'dist_path' => PATH . 'dist',
    'dist_uri'  => plugins_url('dist', BASENAME),
    'assets'    => [
        'admin' => [
            'styles'  => ['styles/admin.css'],
            'scripts' => ['scripts/admin.js'],
        ],
        'front' => [
            'styles'  => [],
            'scripts' => ['scripts/main.js'],
        ]
    ],
];
