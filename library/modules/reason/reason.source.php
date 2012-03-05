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

function reason_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function reason_list()
{
	global $core, $template;

	$types = array(
		0 => 'Unknown',
		1 => 'Plus',
		2 => 'Minus',
	);

	$request = db_query("
		SELECT
			r.id_reason, r.reason_name,
			r.reason_type, COUNT(e.id_entry) AS entries
		FROM reason AS r
			LEFT JOIN entry AS e ON (e.id_reason = r.id_reason)
		GROUP BY r.id_reason
		ORDER BY r.id_reason");
	$template['reasons'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['reasons'][] = array(
			'id' => $row['id_reason'],
			'name' => $row['reason_name'],
			'type' => $types[(int) $row['reason_type']],
			'entries' => $row['entries'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Reason List';
	$core['current_template'] = 'reason_list';
}

function reason_edit()
{
	global $core, $template;

	$id_reason = !empty($_REQUEST['reason']) ? (int) $_REQUEST['reason'] : 0;
	$is_new = empty($id_reason);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'reason_name' => 'string',
			'reason_type' => 'integer',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'integer')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		if ($values['reason_name'] === '')
			fatal_error('Reason name field cannot be empty!');

		$request = db_query("
			SELECT COUNT(*)
			FROM reason
			WHERE reason_name = '$values[reason_name]'
				AND id_reason != $id_reason
			LIMIT 1");
		list ($duplicate) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate))
			fatal_error('The reason name given is already in use!');

		if ($values['reason_type'] === 0)
			fatal_error('Reason type field cannot be empty!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO reason
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
				UPDATE reason
				SET " . implode(', ', $update) . "
				WHERE id_reason = $id_reason
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('reason'));

	if ($is_new)
	{
		$template['reason'] = array(
			'is_new' => true,
			'id' => 0,
			'name' => '',
			'type' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT id_reason, reason_name, reason_type
			FROM reason
			WHERE id_reason = $id_reason
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['reason'] = array(
				'is_new' => false,
				'id' => $row['id_reason'],
				'name' => $row['reason_name'],
				'type' => (int) $row['reason_type'],
			);
		}
		db_free_result($request);

		if (!isset($template['reason']))
			fatal_error('The reason requested does not exist!');
	}

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Reason';
	$core['current_template'] = 'reason_edit';
}

function reason_delete()
{
	$id_reason = !empty($_REQUEST['reason']) ? (int) $_REQUEST['reason'] : 0;

	$request = db_query("
		SELECT id_reason
		FROM reason
		WHERE id_reason = $id_reason
		LIMIT 1");
	list ($id_reason) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_reason))
	{
		db_query("
			DELETE FROM reason
			WHERE id_reason = $id_reason
			LIMIT 1");

		db_query("
			DELETE FROM entry
			WHERE id_reason = $id_reason");

		redirect(build_url('reason'));
	}
	else
		fatal_error('The reason requested does not exist!');
}