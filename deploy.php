<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'big-board');

// Project repository
set('repository', '');

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server
set('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

// host('project.com')
// 	->set('deploy_path', '~/{{application}}');

// Tasks

desc('Rebuild model');
task('rebuild', function () {
		$result = run('propel model:build');
		writeln($result);
		$result2 = run('propel sql:build --overwrite');
		writeln($result2);
		$result3 = run('composer dump-autoload');
		writeln($result3);
	});

desc('Generate SQL changes');
task('sqldiff', function () {
		$result = run('propel diff --skip-removed-table');
		writeln($result);
	});

desc('Migrate SQL changes');
task('migrate', function () {
		$result = run('propel migrate');
		writeln($result);
	});

desc('Migrate on heroku');
task('heroku-migrate', function () {
		$result = run('heroku run propel migrate');
		writeln($result);
	});

// desc('Deploy your project');
// task('deploy', [
// 		'deploy:info',
// 		'deploy:prepare',
// 		'deploy:lock',
// 		'deploy:release',
// 		'deploy:update_code',
// 		'deploy:shared',
// 		'deploy:writable',
// 		'deploy:vendors',
// 		'deploy:clear_paths',
// 		'deploy:symlink',
// 		'deploy:unlock',
// 		'cleanup',
// 		'success'
// 	]);

// [Optional] If deploy fails automatically unlock.
// after('deploy:failed', 'deploy:unlock');
