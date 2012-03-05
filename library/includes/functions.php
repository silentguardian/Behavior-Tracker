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

function load_module($module)
{
	global $core;

	require_once($core['modules_dir'] . '/' . $module . '/' . $module . '.source.php');
	require_once($core['modules_dir'] . '/' . $module . '/' . $module . '.template.php');
}

function load_user()
{
	global $core, $user;

	if (isset($_COOKIE[$core['cookie']]))
	{
		$_COOKIE[$core['cookie']] = stripslashes($_COOKIE[$core['cookie']]);

		if (preg_match('~^a:[34]:\{i:0;(i:\d{1,6}|s:[1-8]:"\d{1,8}");i:1;s:(0|40):"([a-fA-F0-9]{40})?";i:2;[id]:\d{1,14};(i:3;i:\d;)?\}$~', $_COOKIE[$core['cookie']]) == 1)
			list ($user['id'], $pass) = @unserialize($_COOKIE[$core['cookie']]);

		$user['id'] = !empty($user['id']) && !empty($pass) ? (int) $user['id'] : 0;
	}
	elseif (isset($_SESSION['login_' . $core['cookie']]))
	{
		list ($user['id'], $pass, $login) = @unserialize(stripslashes($_SESSION['login_' . $core['cookie']]));
		$user['id'] = !empty($user['id']) && strlen($pass) == 40 && $login > time() ? (int) $user['id'] : 0;
	}

	if (!empty($user['id']))
	{
		$request = db_query("
			SELECT
				id_teacher, teacher_name, teacher_surname,
				teacher_alias, teacher_password, is_admin
			FROM teacher
			WHERE id_teacher = $user[id]
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$real_password = $row['teacher_password'];

			$temp = array(
				'id' => (int) $row['id_teacher'],
				'name' => $row['teacher_name'],
				'surname' => $row['teacher_surname'],
				'alias' => $row['teacher_alias'],
				'admin' => !empty($row['is_admin']),
				'teacher' => true,
			);
		}
		db_free_result($request);

		if (empty($temp) || strlen($pass) != 40 || $pass !== $real_password)
			$user['id'] = 0;
	}

	if (empty($user['id']))
	{
		$user = array(
			'id' => 0,
			'teacher' => false,
			'admin' => false,
		);
	}
	else
	{
		$user = $temp;

		if (isset($_COOKIE[$core['cookie']]))
			$_COOKIE[$core['cookie']] = '';
	}
}

function start_session()
{
	global $core;

	if (session_id() == '')
		session_start();

	if (!isset($_SESSION['session_id']))
		$_SESSION['session_id'] = md5(session_id() . mt_rand() . (string) microtime());

	$core['session_id'] = $_SESSION['session_id'];
}

function create_cookie($length, $user, $pass = '')
{
	global $core;

	$data = serialize(empty($user) ? array(0, '', 0) : array($user, $pass, time() + $length));
	$url = parse_url('http://' . (empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST']) . (strtr(dirname($_SERVER['PHP_SELF']), '\\', '/') == '/' ? '' : strtr(dirname($_SERVER['PHP_SELF']), '\\', '/')));

	setcookie($core['cookie'], $data, time() + $length, $url['path'], '', 0);

	$_COOKIE[$core['cookie']] = $data;

	if (!isset($_SESSION['login_' . $core['cookie']]) || $_SESSION['login_' . $core['cookie']] !== $data)
	{
		$old_session = $_SESSION;
		$_SESSION = array();
		session_destroy();

		start_session();
		session_regenerate_id();
		$_SESSION = $old_session;

		setcookie(session_name(), session_id(), time() + $length, $url['path'], '', 0);

		$_SESSION['login_' . $core['cookie']] = $data;
	}
}

function clean_request()
{
	unset($GLOBALS['HTTP_POST_VARS'], $GLOBALS['HTTP_POST_VARS']);
	unset($GLOBALS['HTTP_POST_FILES'], $GLOBALS['HTTP_POST_FILES']);

	if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']))
		fatal_error('Invalid request!');

	foreach (array_merge(array_keys($_POST), array_keys($_GET), array_keys($_FILES)) as $key)
	{
		if (is_numeric($key))
			fatal_error('Invalid request!');
	}

	foreach ($_COOKIE as $key => $value)
	{
		if (is_numeric($key))
			unset($_COOKIE[$key]);
	}

	foreach (array('module', 'action') as $index)
	{
		if (isset($_GET[$index]))
			$_GET[$index] = (string) $_GET[$index];
	}

	$_REQUEST = $_POST + $_GET;
}

function build_url($parts = array(), $quick = true)
{
	global $core;

	$url = './';

	if (!is_array($parts))
		$parts = array($parts);
	if (empty($parts) || $parts == array('home'))
		return $url;

	if ($core['clean_url'] === true)
		$url .= implode('/', $parts);
	else
	{
		if ($quick)
		{
			foreach ($parts as $level => $part)
			{
				if ($level == 0)
					$url .= '?module=' . $part;
				elseif ($level == 1)
					$url .= '&amp;action=' . $part;
				elseif ($level == 2)
					$url .= '&amp;' . $parts[0] . '=' . $part;
			}
		}
		else
		{
			$temp_parts = array();

			foreach ($parts as $key => $value)
				$temp_parts[] = $key . '=' . $value;

			$url .= '?' . implode('&amp;', $temp_parts);
		}
	}

	return $url;
}

function template_menu()
{
	global $core, $user, $modules;

	$items = array(
		'',
		'home' => 'Home',
		'list' => 'List',
		'login' => 'Login',
		'logout' => 'Logout',
		'about' => 'About',
		'',
		'entry' => 'Entries',
		'class' => 'Classes',
		'teacher' => 'Teachers',
		'student' => 'Students',
		'reason' => 'Reasons',
	);

	echo '
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="', build_url(), '">Behavior Tracker</a>
				<div class="nav-collapse">
					<ul class="nav">';

	foreach ($items as $key => $value)
	{
		if ($value === '')
		{
			echo '
						<li class="divider-vertical"></li>';
		}
		elseif (!empty($modules) && in_array($key, $modules))
		{
			echo '
						<li', ($key === $core['current_module'] ? ' class="active"' : ''), '><a href="', build_url($key), '">', $value, '</a></li>';
		}
	}

	echo '
					</ul>';

	if ($user['teacher'])
	{
		echo '
					<p class="navbar-text pull-right">
						Logged in as <b>', $user['name'], ' ', $user['surname'], '</b>
					</p>';
	}

	echo '
				</div>
			</div>
		</div>
	</div>';
}

function template_header()
{
	global $core, $template;

	echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>', $template['page_title'], '</title>
	<link href="interface/css/bootstrap.min.css" rel="stylesheet">
	<link href="interface/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="interface/css/style.css" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>';

	template_menu();

	echo '
	<div class="container">';
}

function template_footer()
{
	global $core, $db, $start_time;

	$time = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $start_time)), 3);
	$queries = !empty($db['debug']) ? count($db['debug']) : 0;

	echo '
		<p class="pull-right">
			<small>Behavior Tracker ', $core['version'], ' &copy; 2012, Selman Eser | Time: ', $time, ' Queries: ', $queries, '</small>
		</p>
	</div>
	<script src="interface/js/jquery.js"></script>
	<script src="interface/js/bootstrap.min.js"></script>
</body>
</html>';
}

function redirect($location)
{
	header('Location: ' . str_replace(' ', '%20', $location));

	ob_end_flush();

	exit();
}

function fatal_error($error)
{
	global $core, $template;

	$template['error'] = $error;
	$core['current_module'] = 'error';

	load_module('error');

	call_user_func('error_main');

	ob_exit();
}

function ob_exit()
{
	global $core, $template;

	if (empty($template['page_title']))
		$template['page_title'] = 'Behavior Tracker';
	else
		$template['page_title'] .= ' - Behavior Tracker';

	template_header();

	call_user_func('template_' . $core['current_template']);

	template_footer();

	ob_end_flush();

	exit();
}