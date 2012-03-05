<?php

/**
 * @package Behavior Tracker
 *
 * @author Selman "silentguardian" Eser
 * @copyright 2012 Selman "silentguardian" Eser
 * @license BSD 3-clause 
 *
 * @version 1.0
 */

if (!defined('CORE'))
	exit();

function login_main()
{
	global $core, $template;

	if (!empty($_POST['submit']))
	{
		$alias = !empty($_POST['teacher_alias']) ? $_POST['teacher_alias'] : '';
		$password = !empty($_POST['teacher_password']) ? $_POST['teacher_password'] : '';

		if ($alias === '' || preg_match('~[^A-Za-z0-9\._]~', $alias) || $password === '')
			fatal_error('Invalid alias or password!');

		$request = db_query("
			SELECT id_teacher, teacher_password
			FROM teacher
			WHERE teacher_alias = '$alias'
			LIMIT 1");
		list ($id, $real_password) = db_fetch_row($request);
		db_free_result($request);

		$hash = sha1($password);
		if ($hash !== $real_password)
			fatal_error('Invalid alias or password!');

		create_cookie(60 * 3153600, $id, $hash);

		redirect(build_url());
	}

	$template['page_title'] = 'Teacher Login';
	$core['current_template'] = 'login_main';
}