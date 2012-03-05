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

function template_student_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-primary" href="', build_url(array('student', 'edit')), '">Add Student</a>
			</div>
			<h2>Student List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Student name</th>
					<th>Student surname</th>
					<th>Student class</th>
					<th>Total</th>
					<th>Plus</th>
					<th>Minus</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['students']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="6">There are not any student added yet!</td>
				</tr>';
	}

	foreach ($template['students'] as $student)
	{
		echo '
				<tr>
					<td>', $student['name'], '</td>
					<td>', $student['surname'], '</td>
					<td>', $student['class'], '</td>
					<td>', $student['plus'] + $student['minus'], '</td>
					<td>', $student['plus'], '</td>
					<td>', $student['minus'], '</td>
					<td class="span2 align_center">
						<a class="btn btn-info" href="', build_url(array('student', 'edit', $student['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('student', 'delete', $student['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_student_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('student', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['student']['is_new'] ? 'Edit' : 'Add'), ' Student</legend>
				<div class="control-group">
					<label class="control-label" for="student_name">Student name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="student_name" name="student_name" value="', $template['student']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="student_surname">Student surname:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="student_surname" name="student_surname" value="', $template['student']['surname'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_class">Student class:</label>
					<div class="controls">
						<select id="id_class" name="id_class">
							<option value="0"', ($template['student']['class'] == 0 ? ' selected="selected"' : ''), '>Select class</option>';

	foreach ($template['classes'] as $class)
	{
		echo '
							<option value="', $class['id'], '"', ($template['student']['class'] == $class['id'] ? ' selected="selected"' : ''), '>', $class['name'], '</option>';
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
			<input type="hidden" name="student" value="', $template['student']['id'], '" />
		</form>';
}