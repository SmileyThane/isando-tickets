<?php

return [

    'git_path' => env('DEPLOY_GIT_PATH', 'git'),
    'php_path' => env('DEPLOY_PHP_PATH', 'php'),
    'composer_path' => env('DEPLOY_COMPOSER_PATH', 'composer'),
    
    'secret' => env('DEPLOY_SECRET'),

];
