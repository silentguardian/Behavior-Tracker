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

function entry_main()
{
	global $core;

	$actions = array('list', 'plus', 'minus', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function entry_list()
{
	global $core, $template;

	$types = array(
		0 => 'Unknown',
		1 => 'Plus',
		2 => 'Minus',
	);

	$request = db_query("
		SELECT
			e.id_entry, s.student_name, s.student_surname,
			c.class_name, r.reason_name, r.reason_type,
			t.teacher_name, t.teacher_surname, e.entry_date
		FROM entry AS e
			INNER JOIN student AS s ON (s.id_student = e.id_student)
			INNER JOIN class AS c ON (c.id_class = s.id_class)
			INNER JOIN reason AS r ON (r.id_reason = e.id_reason)
			INNER JOIN teacher AS t ON (t.id_teacher = e.id_teacher)
		ORDER BY e.id_entry");
	$template['entries'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['entries'][] = array(
			'id' => $row['id_entry'],
			'student' => $row['student_name'] . ' ' . $row['student_surname'],
			'class' => $row['class_name'],
			'reason' => $row['reason_name'],
			'type' => $types[(int) $row['reason_type']],
			'teacher' => $row['teacher_name'] . ' ' . $row['teacher_surname'],
			'date' => strftime('%d/%m/%y', $row['entry_date']),
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Entry List';
	$core['current_template'] = 'entry_list';
}

function entry_plus()
{
	entry_edit(1);
}

function entry_minus()
{
	entry_edit(2);
}

function entry_edit($type = 0)
{
	global $core, $template, $user;

	$id_entry = !empty($_REQUEST['entry']) ? (int) $_REQUEST['entry'] : 0;
	$is_new = empty($id_entry);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'id_student' => 'integer',
			'id_reason' => 'integer',
			'date_day' => 'integer',
			'date_month' => 'integer',
			'date_year' => 'integer',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'integer')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		$values['entry_date'] = mktime(0, 0, 0, $values['date_month'], $values['date_day'], $values['date_year']);
		unset($values['date_month'], $values['date_day'], $values['date_year']);

		if ($values['id_student'] === 0)
			fatal_error('Student field cannot be empty!');

		if ($values['id_reason'] === 0)
			fatal_error('Reason field cannot be empty!');

		if ($is_new)
		{
			$values['id_teacher'] = $user['id'];

			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO entry
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
				UPDATE entry
				SET " . implode(', ', $update) . "
				WHERE id_entry = $id_entry
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect('./?module=entry');

	if ($is_new)
	{
		$today = getdate();

		$template['entry'] = array(
			'is_new' => true,
			'id' => 0,
			'student' => 0,
			'reason' => 0,
			'date' => array(
				'day' => $today['mday'],
				'month' => $today['mon'],
				'year' => $today['year'],
			),
		);
	}
	else
	{
		$request = db_query("
			SELECT
				e.id_entry, e.id_student, e.id_reason,
				e.entry_date, r.reason_type
			FROM entry AS e
				INNER JOIN reason AS r ON (r.id_reason = e.id_reason)
			WHERE e.id_entry = $id_entry
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$type = $row['reason_type'];
			$date = explode('-', strftime('%d-%m-%Y', $row['entry_date']));

			$template['entry'] = array(
				'is_new' => false,
				'id' => $row['id_entry'],
				'student' => $row['id_student'],
				'reason' => $row['id_reason'],
				'date' => array(
					'day' => $date[0],
					'month' => $date[1],
					'year' => $date[2],
				),
			);
		}
		db_free_result($request);

		if (!isset($template['entry']))
			fatal_error('The entry requested does not exist!');
	}

	if ($type === 0)
		fatal_error('Type is not selected!');

	$request = db_query("
		SELECT id_student, student_name, student_surname
		FROM student
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

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Entry';
	$core['current_template'] = 'entry_edit';
}

function entry_delete()
{
	$id_entry = !empty($_REQUEST['entry']) ? (int) $_REQUEST['entry'] : 0;

	$request = db_query("
		SELECT id_entry
		FROM entry
		WHERE id_entry = $id_entry
		LIMIT 1");
	list ($id_entry) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_entry))
	{
		db_query("
			DELETE FROM entry
			WHERE id_entry = $id_entry
			LIMIT 1");

		redirect('./?module=entry');
	}
	else
		fatal_error('The entry requested does not exist!');
}