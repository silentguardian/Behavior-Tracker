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

function teacher_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function teacher_list()
{
	global $core, $template;

	$request = db_query("
		SELECT
			t.id_teacher, t.teacher_name,
			t.teacher_surname, t.teacher_alias,
			COUNT(e.id_entry) AS teacher_entries
		FROM teacher AS t
			LEFT JOIN entry AS e ON (e.id_teacher = t.id_teacher)
		GROUP BY t.id_teacher
		ORDER BY t.id_teacher");
	$template['teachers'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['teachers'][] = array(
			'id' => $row['id_teacher'],
			'name' => $row['teacher_name'],
			'surname' => $row['teacher_surname'],
			'alias' => $row['teacher_alias'],
			'entries' => $row['teacher_entries'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Teacher List';
	$core['current_template'] = 'teacher_list';
}

function teacher_edit()
{
	global $core, $template;

	$id_teacher = !empty($_REQUEST['teacher']) ? (int) $_REQUEST['teacher'] : 0;
	$is_new = empty($id_teacher);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'teacher_name' => 'string',
			'teacher_surname' => 'string',
			'teacher_alias' => 'string',
			'teacher_password' => 'password',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'password')
				$values[$field] = !empty($_POST[$field]) ? sha1($_POST[$field]) : '';
		}

		foreach (array('name', 'surname', 'alias') as $field)
		{
			if ($values['teacher_' . $field] === '')
				fatal_error(sprintf('Teacher %s field cannot be empty!', $field));
		}

		if (preg_match('~[^A-Za-z0-9\._]~', $values['teacher_alias']))
			fatal_error('Teacher alias can only contain letters, numbers, underscore or full stop.');

		if ($values['teacher_password'] === '')
		{
			if ($is_new)
				fatal_error('Teacher password field cannot be empty!');
			else
				unset($values['teacher_password']);
		}

		$request = db_query("
			SELECT COUNT(*)
			FROM teacher
			WHERE teacher_name = '$values[teacher_name]'
				AND teacher_surname = '$values[teacher_surname]'
				AND id_teacher != $id_teacher
			LIMIT 1");
		list ($duplicate) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate))
			fatal_error('The teacher name given is already in use!');

		$request = db_query("
			SELECT COUNT(*)
			FROM teacher
			WHERE teacher_alias = '$values[teacher_alias]'
				AND id_teacher != $id_teacher
			LIMIT 1");
		list ($duplicate) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate))
			fatal_error('The teacher alias given is already in use!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO teacher
					(" . implode(', ', array_keys($insert)) . ")
				VALUES
					(" . implode(', ', $insert) . ")");
		}
		else
		{
			$update = array();
			foreach ($values as $field => $value)
				$update[] = $field . " = '" . $value . "'";

			db_query("
				UPDATE teacher
				SET " . implode(', ', $update) . "
				WHERE id_teacher = $id_teacher
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect('./?module=teacher');

	if ($is_new)
	{
		$template['teacher'] = array(
			'is_new' => true,
			'id' => 0,
			'name' => '',
			'surname' => '',
			'alias' => '',
		);
	}
	else
	{
		$request = db_query("
			SELECT
				id_teacher, teacher_name,
				teacher_surname, teacher_alias
			FROM teacher
			WHERE id_teacher = $id_teacher
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['teacher'] = array(
				'is_new' => false,
				'id' => $row['id_teacher'],
				'name' => $row['teacher_name'],
				'surname' => $row['teacher_surname'],
				'alias' => $row['teacher_alias'],
			);
		}
		db_free_result($request);

		if (!isset($template['teacher']))
			fatal_error('The teacher requested does not exist!');
	}

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Teacher';
	$core['current_template'] = 'teacher_edit';
}

function teacher_delete()
{
	$id_teacher = !empty($_REQUEST['teacher']) ? (int) $_REQUEST['teacher'] : 0;

	$request = db_query("
		SELECT id_teacher
		FROM teacher
		WHERE id_teacher = $id_teacher
		LIMIT 1");
	list ($id_teacher) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_teacher))
	{
		db_query("
			DELETE FROM teacher
			WHERE id_teacher = $id_teacher
			LIMIT 1");

		db_query("
			DELETE FROM entry
			WHERE id_teacher = $id_teacher");

		redirect('./?module=teacher');
	}
	else
		fatal_error('The teacher requested does not exist!');
}