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

function template_teacher_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-primary" href="', build_url(array('teacher', 'edit')), '">Add Teacher</a>
			</div>
			<h2>Teacher List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Teacher name</th>
					<th>Teacher surname</th>
					<th>Teacher alias</th>
					<th>Entries</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['teachers']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="5">There are not any teacher added yet!</td>
				</tr>';
	}

	foreach ($template['teachers'] as $teacher)
	{
		echo '
				<tr>
					<td>', $teacher['name'], '</td>
					<td>', $teacher['surname'], '</td>
					<td>', $teacher['alias'], '</td>
					<td>', $teacher['entries'], '</td>
					<td class="span2 align_center">
						<a class="btn btn-info" href="', build_url(array('teacher', 'edit', $teacher['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('teacher', 'delete', $teacher['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_teacher_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('teacher', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['teacher']['is_new'] ? 'Edit' : 'Add'), ' Teacher</legend>
				<div class="control-group">
					<label class="control-label" for="teacher_name">Teacher name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="teacher_name" name="teacher_name" value="', $template['teacher']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="teacher_surname">Teacher surname:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="teacher_surname" name="teacher_surname" value="', $template['teacher']['surname'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="teacher_alias">Teacher alias:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="teacher_alias" name="teacher_alias" value="', $template['teacher']['alias'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="teacher_password">Teacher password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="teacher_password" name="teacher_password" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="teacher" value="', $template['teacher']['id'], '" />
		</form>';
}