<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['openproject'] = [
	'server' => '',
	'api_key' => '',
	'api_path' => '/api/v3/',

	// work package type mapping
	// can be set from the config fontend
	'type_mapping' => [
		'Arbeitspaket' => [
			'href' => '',
			'title' => ''
		],
		'Milestone' => [
			'href' => '',
			'title' => ''
		],
		'Task' => [
			'href' => '',
			'title' => ''
		],
		'Projektphase' => [
			'href' => '',
			'title' => ''
		],
	],

	// default modules for new projects
	// comment out to disable
	'default_modules' => [
		'work_package_tracking',
		'time_tracking',
		'news',
		'wiki',
		'repository',
		'boards',
		'calendar',
		'timelines',
		'documents',
		'costs_module',
		'reporting_module',
		'meetings',
		'backlogs',
		'activity',
	],
];


