<?php

use function \Deployer\{host, set, task, run, before, after};

require_once 'recipe/silverstripe.php';

$host="";
$user="";
$path = "";
$repo = "";

set('default_stage', 'prod');
set('keep_releases', 10);
set('shared_dirs', ['public/assets']);
set('writable_dirs', ['public/assets']);
set('repository', $repo);

host($host)
    ->user($user)
    ->port(22)
    ->set('deploy_path', $path)
    ->stage('prod')
    ->set('branch', 'master')
    ->multiplexing(false);


task('copy:env', function (){
    $env='.env';
    if (\Deployer\test('[ -f /var/environments/'.$env.' ]')) {
        run('cat /var/environments/'.$env.' > {{release_path}}/.env');
    }else{
        throw new Exception('Environment file does not exist');
    }
})->desc('copying env contents into project');
before('silverstripe:buildflush', 'copy:env');

task('permission:change', function(){
    run('chmod -R 755 {{deploy_path}}');
})->desc('Correct file permissions');
before('deploy:symlink', 'permission:change');

task('fileowner:change', function(){
    run('chown -R www-data:www-data {{deploy_path}}');
})->desc('Correct file and directory owners');
after('permission:change', 'fileowner:change');

task('reload:php-fpm', function () {
    run('sudo /etc/init.d/php7.2-fpm restart');
})->desc('Restart php-fpm service');
after('success', 'reload:php-fpm');