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

function class_main()
{
	global $core;

	$actions = array('list', 'plus', 'minus', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function class_list()
{
	global $core, $template;

	$request = db_query("
		SELECT
			c.id_class, c.class_name,
			COUNT(s.id_student) AS class_size
		FROM class AS c
			LEFT JOIN student AS s ON (s.id_class = c.id_class)
		GROUP BY c.id_class
		ORDER BY c.id_class");
	$template['classes'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['classes'][] = array(
			'id' => $row['id_class'],
			'name' => $row['class_name'],
			'size' => $row['class_size'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Class List';
	$core['current_template'] = 'class_list';
}

function class_plus()
{
	class_entry(1);
}

function class_minus()
{
	class_entry(2);
}

function class_entry($type = 0)
{
	global $core, $template, $user;

	$id_class = !empty($_REQUEST['class']) ? (int) $_REQUEST['class'] : 0;

	$request = db_query("
		SELECT id_class, class_name
		FROM class
		WHERE id_class = $id_class
		LIMIT 1");
	list ($id_class, $class_name) = db_fetch_row($request);
	db_free_result($request);

	if (empty($id_class))
		fatal_error('The class requested does not exist!');

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'students' => 'array_integer',
			'id_reason' => 'integer',
			'date_day' => 'integer',
			'date_month' => 'integer',
			'date_year' => 'integer',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'integer')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
			elseif ($type === 'array_integer')
			{
				$values[$field] = array();
				if (!empty($_POST[$field]) && is_array($_POST[$field]))
				{
					foreach ($_POST[$field] as $value)
					{
						if ((int) $value > 0)
							$values[$field][] = (int) $value;
					}
				}
			}
		}

		$values['entry_date'] = mktime(0, 0, 0, $values['date_month'], $values['date_day'], $values['date_year']);
		unset($values['date_month'], $values['date_day'], $values['date_year']);

		if (empty($values['students']))
			fatal_error('No students were selected!');

		if ($values['id_reason'] === 0)
			fatal_error('Reason field cannot be empty!');

		$values['id_teacher'] = $user['id'];

		$inserts = array();
		foreach ($values['students'] as $student)
			$inserts[] = "($values[id_reason], $student, $values[id_teacher], $values[entry_date])";

		db_query("
			INSERT INTO entry
				(id_reason, id_student, id_teacher, entry_date)
			VALUES
				" . implode(",
				", $inserts));
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect('./?module=class');

	$today = getdate();

	$template['entry'] = array(
		'class' => array(
			'id' => $id_class,
			'name' => $class_name,
		),
		'type' => $type == 1 ? 'plus' : 'minus',
		'reason' => 0,
		'date' => array(
			'day' => $today['mday'],
			'month' => $today['mon'],
			'year' => $today['year'],
		),
	);

	if ($type === 0)
		fatal_error('Type is not selected!');

	$request = db_query("
		SELECT id_student, student_name, student_surname
		FROM student
		WHERE id_class = $id_class
		ORDER BY id_student");
	$template['students'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['students'][] = array(
			'id' => $row['id_student'],
			'name' => $row['student_surname'] . ' ' . $row['student_name'],
		);
	}
	db_free_result($request);

	if (empty($template['students']))
		fatal_error('There are no students added yet! You cannot add entries without students!');

	$request = db_query("
		SELECT id_reason, reason_name
		FROM reason
		WHERE reason_type = $type
		ORDER BY id_reason");
	$template['reasons'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['reasons'][] = array(
			'id' => $row['id_reason'],
			'name' => $row['reason_name'],
		);
	}
	db_free_result($request);

	if (empty($template['reasons']))
		fatal_error('There are no reasons added yet! You cannot add entries without reasons!');

	$template['page_title'] = 'Add Class Entry - ' . $template['entry']['class']['name'];
	$core['current_template'] = 'class_entry';
}

function class_edit()
{
	global $core, $template;

	$id_class = !empty($_REQUEST['class']) ? (int) $_REQUEST['class'] : 0;
	$is_new = empty($id_class);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'class_name' => 'string',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
		}

		if ($values['class_name'] === '')
			fatal_error('Class name field cannot be empty!');

		$request = db_query("
			SELECT COUNT(*)
			FROM class
			WHERE class_name = '$values[class_name]'
				AND id_class != $id_class
			LIMIT 1");
		list ($duplicate) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate))
			fatal_error('The class name given is already in use!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO class
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
				UPDATE class
				SET " . implode(', ', $update) . "
				WHERE id_class = $id_class
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect('./?module=class');

	if ($is_new)
	{
		$template['class'] = array(
			'is_new' => true,
			'id' => 0,
			'name' => '',
		);
	}
	else
	{
		$request = db_query("
			SELECT id_class, class_name
			FROM class
			WHERE id_class = $id_class
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['class'] = array(
				'is_new' => false,
				'id' => $row['id_class'],
				'name' => $row['class_name'],
			);
		}
		db_free_result($request);

		if (!isset($template['class']))
			fatal_error('The class requested does not exist!');
	}

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Class';
	$core['current_template'] = 'class_edit';
}

function class_delete()
{
	$id_class = !empty($_REQUEST['class']) ? (int) $_REQUEST['class'] : 0;

	$request = db_query("
		SELECT id_class
		FROM class
		WHERE id_class = $id_class
		LIMIT 1");
	list ($id_class) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_class))
	{
		$request = db_query("
			SELECT s.id_student
			FROM class AS c
				INNER JOIN student AS s ON (s.id_class = c.id_class)
			WHERE c.id_class = $id_class");
		$students = array();
		while ($row = db_fetch_assoc($request))
			$students[] = $row['id_student'];
		db_free_result($request);

		if (!empty($students))
		{
			db_query("
				DELETE FROM student
				WHERE id_student IN (" . implode(",", $students) . ")");

			db_query("
				DELETE FROM entry
				WHERE id_student IN (" . implode(",", $students) . ")");
		}

		db_query("
			DELETE FROM class
			WHERE id_class = $id_class
			LIMIT 1");

		redirect('./?module=class');
	}
	else
		fatal_error('The class requested does not exist!');
}