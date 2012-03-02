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

function home_main()
{
	global $core, $template;

	$types = array(
		0 => 'Unknown',
		1 => 'Plus',
		2 => 'Minus',
	);

	$request = db_query("
		SELECT
			s.student_name, s.student_surname,
			c.class_name, r.reason_type, e.entry_date
		FROM entry AS e
			INNER JOIN student AS s ON (s.id_student = e.id_student)
			INNER JOIN class AS c ON (c.id_class = s.id_class)
			INNER JOIN reason AS r ON (r.id_reason = e.id_reason)
		ORDER BY e.id_entry DESC
		LIMIT 5");
	$template['recent'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['recent'][] = array(
			'student' => $row['student_name'] . ' ' . $row['student_surname'],
			'class' => $row['class_name'],
			'type' => $types[(int) $row['reason_type']],
			'date' => strftime('%d/%m/%y', $row['entry_date']),
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT
			s.student_name, s.student_surname,
			c.class_name, COUNT(e.id_entry) AS entry_count
		FROM student AS s
			INNER JOIN class AS c ON (c.id_class = s.id_class)
			INNER JOIN entry AS e ON (e.id_student = s.id_student)
			INNER JOIN reason AS r ON (r.id_reason = e.id_reason)
		WHERE r.reason_type = 1
		GROUP BY s.id_student
		ORDER BY entry_count DESC
		LIMIT 5");
	$template['top'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['top'][] = array(
			'name' => $row['student_name'] . ' ' . $row['student_surname'],
			'class' => $row['class_name'],
			'pluses' => $row['entry_count'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT r.reason_name, COUNT(e.id_entry) AS entries
		FROM reason AS r
			INNER JOIN entry AS e ON (e.id_reason = r.id_reason)
		WHERE r.reason_type = 1
		GROUP BY r.id_reason
		ORDER BY entries DESC
		LIMIT 5");
	$template['plus'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['plus'][] = array(
			'name' => $row['reason_name'],
			'entries' => $row['entries'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT r.reason_name, COUNT(e.id_entry) AS entries
		FROM reason AS r
			INNER JOIN entry AS e ON (e.id_reason = r.id_reason)
		WHERE r.reason_type = 2
		GROUP BY r.id_reason
		ORDER BY entries DESC
		LIMIT 5");
	$template['minus'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['minus'][] = array(
			'name' => $row['reason_name'],
			'entries' => $row['entries'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_class)
		FROM class
		LIMIT 1");
	list ($template['total_class']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_student)
		FROM student
		LIMIT 1");
	list ($template['total_student']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_teacher)
		FROM teacher
		LIMIT 1");
	list ($template['total_teacher']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_reason)
		FROM reason
		LIMIT 1");
	list ($template['total_reason']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_entry)
		FROM entry
		LIMIT 1");
	list ($template['total_entry']) = db_fetch_row($request);
	db_free_result($request);

	$template['page_title'] = 'Home';
	$core['current_template'] = 'home_main';
}