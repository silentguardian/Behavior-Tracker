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

function template_class_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-primary" href="./?module=class&amp;action=edit">Add Class</a>
			</div>
			<h2>Class List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Class name</th>
					<th>Class size</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['classes']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="3">There are not any class added yet!</td>
				</tr>';
	}

	foreach ($template['classes'] as $class)
	{
		echo '
				<tr>
					<td>', $class['name'], '</td>
					<td>', $class['size'], '</td>
					<td class="span4 align_center">
						<a class="btn btn-success" href="./?module=class&amp;action=plus&amp;class=', $class['id'], '">Add Plus</a>
						<a class="btn btn-warning" href="./?module=class&amp;action=minus&amp;class=', $class['id'], '">Add Minus</a>
						<a class="btn btn-info" href="./?module=class&amp;action=edit&amp;class=', $class['id'], '">Edit</a>
						<a class="btn btn-danger" href="./?module=class&amp;action=delete&amp;class=', $class['id'], '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_class_entry()
{
	global $template;

	echo '
		<form class="form-horizontal" action="./?module=class&amp;action=', $template['entry']['type'], '" method="post">
			<fieldset>
				<legend>Add Class Entry - ', $template['entry']['class']['name'], '</legend>
				<div class="control-group">
					<label class="control-label" for="id_student">Students:</label>
					<div class="controls">';

	foreach ($template['students'] as $student)
	{
		echo '
						<label class="checkbox">
							<input type="checkbox" name="students[]" value="', $student['id'], '">
							', $student['name'], '
						</label>';
	}

	echo '
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_reason">Reason:</label>
					<div class="controls">
						<select id="id_reason" name="id_reason">
							<option value="0" selected="selected">Select reason</option>';

	foreach ($template['reasons'] as $reason)
	{
		echo '
							<option value="', $reason['id'], '">', $reason['name'], '</option>';
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
			<input type="hidden" name="class" value="', $template['entry']['class']['id'], '" />
		</form>';
}

function template_class_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="./?module=class&amp;action=edit" method="post">
			<fieldset>
				<legend>', (!$template['class']['is_new'] ? 'Edit' : 'Add'), ' Class</legend>
				<div class="control-group">
					<label class="control-label" for="class_name">Class name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="class_name" name="class_name" value="', $template['class']['name'], '" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="class" value="', $template['class']['id'], '" />
		</form>';
}