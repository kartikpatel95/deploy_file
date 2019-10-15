<?php

use function \Deployer\{host, set};

require_once 'recipe/common.php';
require_once 'recipe/composer.php';

$host="";
$user="";
$path = "";
$repo = "";

set('default_stage', 'prod');
set('keep_releases', 10);
set('shared_dirs', ['public/assets']);
set('repository', $repo);

host($host)
    ->user($user)
    ->port(22)
    ->set('deploy_path', $path)
    ->stage('prod')
    ->set('branch', 'master')
    ->multiplexing(false);
