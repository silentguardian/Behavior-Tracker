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

function template_list_list()
{
	global $template, $user;

	echo '
		<div class="page-header">
			<div class="pull-right">';

	if ($user['teacher'])
	{
		if (empty($template['teacher_filter']))
		{
			echo '
			<a class="btn btn-info" href="./?module=list&amp;action=filter&amp;class=', $template['class_filter'], '&amp;teacher=', $user['id'], '">Filter My Entries</a>';
		}
		else
		{
			echo '
				<a class="btn btn-danger" href="./?module=list&amp;action=filter&amp;class=', $template['class_filter'], '&amp;teacher=0">Remove Teacher Filter</a>';
		}
	}

	foreach ($template['classes'] as $class)
	{
		if (!empty($template['class_filter']) && $template['class_filter'] === $class['id'])
		{
			echo '
				<a class="btn btn-danger" href="./?module=list&amp;action=filter&amp;class=0&amp;teacher=', $template['teacher_filter'], '">Remove Class Filter</a>';
		}
		else
		{
			echo '
				<a class="btn" href="./?module=list&amp;action=filter&amp;class=', $class['id'], '&amp;teacher=', $template['teacher_filter'], '">Filter ', $class['name'], ' Class</a>';
		}
	}

	echo '
			</div>
			<h2>Plus/Minus List', (!empty($template['extra_title']) ? ' - ' . implode(' - ', $template['extra_title']) : ''), '</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Student name</th>
					<th>Student surname</th>
					<th class="align_center">Student class</th>
					<th class="align_center">Total</th>
					<th class="align_center">Plus</th>
					<th class="align_center">Minus</th>
					<th class="align_center">Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['students']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="7">There are not any student added yet!</td>
				</tr>';
	}

	foreach ($template['students'] as $student)
	{
		echo '
				<tr>
					<td>', $student['name'], '</td>
					<td>', $student['surname'], '</td>
					<td class="span2 align_center">', $student['class'], '</td>
					<td class="span1 align_center">', $student['plus'] + $student['minus'], '</td>
					<td class="span1 align_center">', $student['plus'], '</td>
					<td class="span1 align_center">', $student['minus'], '</td>
					<td class="span2 align_center">
						<a class="btn" href="./?module=list&amp;action=detail&amp;student=', $student['id'], '">Details</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_list_detail()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn" href="./?module=list">Back to List</a>
			</div>
			<h2>Student Details - ', $template['student'], '</h2>
		</div>';

	if (empty($template['teachers']))
	{
		echo '
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Entry reason</th>
					<th>Entry type</th>
					<th>Entry date</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="align_center" colspan="3">There are not any entry added yet!</td>
				</tr>
			</tbody>
		</table>';
	}

	foreach ($template['teachers'] as $teacher)
	{
		echo '
		<div class="page-header">
			<h3>', $teacher['name'], ' ', $teacher['surname'], ' <small>(<b>', $teacher['plus'], '</b> plus ; <b>', $teacher['minus'], '</b> minus ; <b>', $teacher['plus'] + $teacher['minus'], '</b> total)</small></h3>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Entry reason</th>
					<th>Entry type</th>
					<th>Entry date</th>
				</tr>
			</thead>
			<tbody>';

		foreach ($teacher['entries'] as $entry)
		{
			echo '
				<tr>
					<td>', $entry['reason'], '</td>
					<td class="span2">', $entry['type'], '</td>
					<td class="span2">', $entry['date'], '</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>';
	}
}