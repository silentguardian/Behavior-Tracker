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

function template_entry_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-success" href="', build_url(array('entry', 'plus')), '">Add Plus Entry</a>
				<a class="btn btn-warning" href="', build_url(array('entry', 'minus')), '">Add Minus Entry</a>
			</div>
			<h2>Entry List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Student name</th>
					<th>Student class</th>
					<th>Entry reason</th>
					<th>Entry type</th>
					<th>Teacher name</th>
					<th>Entry date</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['entries']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="7">There are not any entry added yet!</td>
				</tr>';
	}

	foreach ($template['entries'] as $entry)
	{
		echo '
				<tr>
					<td>', $entry['student'], '</td>
					<td>', $entry['class'], '</td>
					<td>', $entry['reason'], '</td>
					<td>', $entry['type'], '</td>
					<td>', $entry['teacher'], '</td>
					<td>', $entry['date'], '</td>
					<td class="span2 align_center">
						<a class="btn btn-info" href="', build_url(array('entry', 'edit', $entry['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('entry', 'delete', $entry['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_entry_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('entry', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['entry']['is_new'] ? 'Edit' : 'Add'), ' Entry</legend>
				<div class="control-group">
					<label class="control-label" for="id_student">Student:</label>
					<div class="controls">
						<select id="id_student" name="id_student">
							<option value="0"', ($template['entry']['student'] == 0 ? ' selected="selected"' : ''), '>Select student</option>';

	foreach ($template['students'] as $student)
	{
		echo '
							<option value="', $student['id'], '"', ($template['entry']['student'] == $student['id'] ? ' selected="selected"' : ''), '>', $student['name'], '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_reason">Reason:</label>
					<div class="controls">
						<select id="id_reason" name="id_reason">
							<option value="0"', ($template['entry']['reason'] == 0 ? ' selected="selected"' : ''), '>Select reason</option>';

	foreach ($template['reasons'] as $reason)
	{
		echo '
							<option value="', $reason['id'], '"', ($template['entry']['reason'] == $reason['id'] ? ' selected="selected"' : ''), '>', $reason['name'], '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Date:</label>
					<div class="controls">
						<select class="span1" name="date_day">';

	for ($day = 1; $day < 32; $day++)
	{
		echo '
							<option value="', $day, '"', ($template['entry']['date']['day'] == $day ? ' selected="selected"' : ''), '>', $day, '</option>';
	}

	echo '
						</select>
						<select class="span1" name="date_month">';

	for ($month = 1; $month < 13; $month++)
	{
		echo '
							<option value="', $month, '"', ($template['entry']['date']['month'] == $month ? ' selected="selected"' : ''), '>', $month, '</option>';
	}

	echo '
						</select>
						<select class="span1" name="date_year">';

	for ($year = 2011; $year < 2022; $year++)
	{
		echo '
							<option value="', $year, '"', ($template['entry']['date']['year'] == $year ? ' selected="selected"' : ''), '>', $year, '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="entry" value="', $template['entry']['id'], '" />
		</form>';
}