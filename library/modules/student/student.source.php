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

function student_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function student_list()
{
	global $core, $template;

	$request = db_query("
		SELECT
			s.id_student, s.student_name, s.student_surname,
			c.class_name, r.reason_type, COUNT(e.id_entry) AS entry_count
		FROM student AS s
			INNER JOIN class AS c ON (c.id_class = s.id_class)
			LEFT JOIN entry AS e ON (e.id_student = s.id_student)
			LEFT JOIN reason AS r ON (r.id_reason = e.id_reason)
		GROUP BY s.id_student, r.reason_type
		ORDER BY s.id_student");
	$template['students'] = array();
	while ($row = db_fetch_assoc($request))
	{
		if (!isset($template['students'][$row['id_student']]))
		{
			$template['students'][$row['id_student']] = array(
				'id' => $row['id_student'],
				'name' => $row['student_name'],
				'surname' => $row['student_surname'],
				'class' => $row['class_name'],
				'plus' => 0,
				'minus' => 0,
			);
		}

		if ($row['reason_type'] == 1)
			$template['students'][$row['id_student']]['plus'] = $row['entry_count'];
		elseif ($row['reason_type'] == 2)
			$template['students'][$row['id_student']]['minus'] = $row['entry_count'];
	}
	db_free_result($request);

	$template['page_title'] = 'Student List';
	$core['current_template'] = 'student_list';
}

function student_edit()
{
	global $core, $template;

	$id_student = !empty($_REQUEST['student']) ? (int) $_REQUEST['student'] : 0;
	$is_new = empty($id_student);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'student_name' => 'string',
			'student_surname' => 'string',
			'id_class' => 'integer',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'integer')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		foreach (array('name', 'surname') as $field)
		{
			if ($values['student_' . $field] === '')
				fatal_error(sprintf('Student %s field cannot be empty!', $field));
		}

		$request = db_query("
			SELECT COUNT(*)
			FROM student
			WHERE student_name = '$values[student_name]'
				AND student_surname = '$values[student_surname]'
				AND id_student != $id_student
			LIMIT 1");
		list ($duplicate) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate))
			fatal_error('The student name given is already in use!');

		if ($values['id_class'] === 0)
			fatal_error('Student class field cannot be empty!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO student
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
				UPDATE student
				SET " . implode(', ', $update) . "
				WHERE id_student = $id_student
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect('./?module=student');

	if ($is_new)
	{
		$template['student'] = array(
			'is_new' => true,
			'id' => 0,
			'name' => '',
			'surname' => '',
			'class' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT
				id_student, id_class,
				student_name, student_surname
			FROM student
			WHERE id_student = $id_student
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['student'] = array(
				'is_new' => false,
				'id' => $row['id_student'],
				'name' => $row['student_name'],
				'surname' => $row['student_surname'],
				'class' => $row['id_class'],
			);
		}
		db_free_result($request);

		if (!isset($template['student']))
			fatal_error('The student requested does not exist!');
	}

	$request = db_query("
		SELECT id_class, class_name
		FROM class
		ORDER BY id_class");
	$template['classes'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['classes'][] = array(
			'id' => $row['id_class'],
			'name' => $row['class_name'],
		);
	}
	db_free_result($request);

	if (empty($template['classes']))
		fatal_error('There are no classes added yet! You cannot add students without classes!');

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Student';
	$core['current_template'] = 'student_edit';
}

function student_delete()
{
	$id_student = !empty($_REQUEST['student']) ? (int) $_REQUEST['student'] : 0;

	$request = db_query("
		SELECT id_student
		FROM student
		WHERE id_student = $id_student
		LIMIT 1");
	list ($id_student) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_student))
	{
		db_query("
			DELETE FROM student
			WHERE id_student = $id_student
			LIMIT 1");

		db_query("
			DELETE FROM entry
			WHERE id_student = $id_student");

		redirect('./?module=student');
	}
	else
		fatal_error('The student requested does not exist!');
}