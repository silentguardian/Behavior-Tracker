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

function list_main()
{
	global $core;

	$actions = array('list', 'detail', 'filter');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function list_list($filter = array())
{
	global $core, $template, $user;

	$class_query = $teacher_query = "";
	$template['class_filter'] = $template['teacher_filter'] = 0;

	if (!empty($filter['class']))
	{
		$class_query = " AND c.id_class = {$filter['class']['id']}";
		$template['extra_title'][] = $filter['class']['name'];
		$template['class_filter'] = $filter['class']['id'];
	}
	if (!empty($filter['teacher']))
	{
		$teacher_query = " AND e.id_teacher = {$filter['teacher']['id']}";
		$template['extra_title'][] = $filter['teacher']['name'];
		$template['teacher_filter'] = $filter['teacher']['id'];
	}

	$request = db_query("
		SELECT
			s.id_student, s.student_name, s.student_surname,
			c.class_name, r.reason_type, COUNT(e.id_entry) AS entry_count
		FROM student AS s
			INNER JOIN class AS c ON (c.id_class = s.id_class{$class_query})
			LEFT JOIN entry AS e ON (e.id_student = s.id_student{$teacher_query})
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

	$template['page_title'] = 'Plus/Minus List' . (!empty($template['extra_title']) ? ' - ' . implode(' - ', $template['extra_title']) : '');
	$core['current_template'] = 'list_list';
}

function list_detail()
{
	global $core, $template;

	$id_student = !empty($_REQUEST['student']) ? (int) $_REQUEST['student'] : 0;

	$request = db_query("
		SELECT id_student, student_name, student_surname
		FROM student
		WHERE id_student = $id_student
		LIMIT 1");
	list ($id_student, $student_name, $student_surname) = db_fetch_row($request);
	db_free_result($request);

	if (empty($id_student))
		fatal_error('The student requested does not exist!');

	$types = array(
		0 => 'Unknown',
		1 => 'Plus',
		2 => 'Minus',
	);

	$request = db_query("
		SELECT
			e.id_entry, r.reason_name, r.reason_type, t.id_teacher,
			t.teacher_name, t.teacher_surname, e.entry_date
		FROM entry AS e
			INNER JOIN reason AS r ON (r.id_reason = e.id_reason)
			INNER JOIN teacher AS t ON (t.id_teacher = e.id_teacher)
		WHERE e.id_student = $id_student
		ORDER BY e.id_entry");
	$template['teachers'] = array();
	while ($row = db_fetch_assoc($request))
	{
		if (empty($template['teachers'][$row['id_teacher']]))
		{
			$template['teachers'][$row['id_teacher']] = array(
				'id' => $row['id_teacher'],
				'name' => $row['teacher_name'],
				'surname' => $row['teacher_surname'],
				'plus' => 0,
				'minus' => 0,
				'entries' => array(),
			);
		}

		$template['teachers'][$row['id_teacher']]['entries'][] = array(
			'id' => $row['id_entry'],
			'reason' => $row['reason_name'],
			'type' => $types[(int) $row['reason_type']],
			'date' => strftime('%d/%m/%y', $row['entry_date']),
		);

		if ($row['reason_type'] == 1)
			$template['teachers'][$row['id_teacher']]['plus']++;
		elseif ($row['reason_type'] == 2)
			$template['teachers'][$row['id_teacher']]['minus']++;
	}
	db_free_result($request);

	$template['student'] = $student_name . ' ' . $student_surname;
	$template['page_title'] = 'Student Details - ' . $template['student'];
	$core['current_template'] = 'list_detail';
}

function list_filter()
{
	global $template, $user;

	$filter = array();

	$id_class = !empty($_REQUEST['class']) ? (int) $_REQUEST['class'] : 0;
	$id_teacher = !empty($_REQUEST['teacher']) ? (int) $_REQUEST['teacher'] : 0;

	if ($id_class > 0)
	{
		$request = db_query("
			SELECT id_class, class_name
			FROM class
			WHERE id_class = $id_class
			LIMIT 1");
		list ($id_class, $class_name) = db_fetch_row($request);
		db_free_result($request);

		if (empty($id_class))
			fatal_error('The class requested does not exist!');

		$filter['class'] = array(
			'id' => $id_class,
			'name' => $class_name,
		);
	}

	if ($id_teacher > 0)
	{
		$request = db_query("
			SELECT id_teacher, teacher_name, teacher_surname
			FROM teacher
			WHERE id_teacher = $id_teacher
			LIMIT 1");
		list ($id_teacher, $teacher_name, $teacher_surname) = db_fetch_row($request);
		db_free_result($request);

		if (empty($id_teacher))
			fatal_error('The teacher requested does not exist!');

		$filter['teacher'] = array(
			'id' => $id_teacher,
			'name' => $teacher_name . ' ' . $teacher_surname,
		);
	}

	list_list($filter);
}