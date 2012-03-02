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

function template_reason_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-primary" href="./?module=reason&amp;action=edit">Add Reason</a>
			</div>
			<h2>Reason List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Reason name</th>
					<th>Reason type</th>
					<th>Entries</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['reasons']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="4">There are not any reason added yet!</td>
				</tr>';
	}

	foreach ($template['reasons'] as $reason)
	{
		echo '
				<tr>
					<td>', $reason['name'], '</td>
					<td>', $reason['type'], '</td>
					<td>', $reason['entries'], '</td>
					<td class="span2 align_center">
						<a class="btn btn-info" href="./?module=reason&amp;action=edit&amp;reason=', $reason['id'], '">Edit</a>
						<a class="btn btn-danger" href="./?module=reason&amp;action=delete&amp;reason=', $reason['id'], '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_reason_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="./?module=reason&amp;action=edit" method="post">
			<fieldset>
				<legend>', (!$template['reason']['is_new'] ? 'Edit' : 'Add'), ' Reason</legend>
				<div class="control-group">
					<label class="control-label" for="reason_name">Reason name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="reason_name" name="reason_name" value="', $template['reason']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="reason_type">Reason type:</label>
					<div class="controls">
						<select id="reason_type" name="reason_type">
							<option value="0"', ($template['reason']['type'] === 0 ? ' selected="selected"' : ''), '>Select type</option>
							<option value="1"', ($template['reason']['type'] === 1 ? ' selected="selected"' : ''), '>Plus</option>
							<option value="2"', ($template['reason']['type'] === 2 ? ' selected="selected"' : ''), '>Minus</option>
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="reason" value="', $template['reason']['id'], '" />
		</form>';
}