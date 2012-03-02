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

function template_home_main()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				', $template['total_class'], ' classes &bull; ', $template['total_student'], ' students &bull; ', $template['total_teacher'], ' teachers &bull; ', $template['total_reason'], ' reasons &bull; ', $template['total_entry'], ' entries
			</div>
			<h2>Behavior Tracker</h2>
		</div>
		<div class="pull-left half">
			<div class="page-header">
				<h3>Recent entries</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Student name</th>
						<th>Student class</th>
						<th>Entry type</th>
						<th>Entry date</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['recent']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="4">There are not any recent entries!</td>
					</tr>';
	}

	foreach ($template['recent'] as $entry)
	{
		echo '
					<tr>
						<td>', $entry['student'], '</td>
						<td>', $entry['class'], '</td>
						<td>', $entry['type'], '</td>
						<td>', $entry['date'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>';

	echo '
		<div class="pull-right half">
			<div class="page-header">
				<h3>Top students</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Student name</th>
						<th>Student class</th>
						<th>Pluses</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['top']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="3">There are not any top students!</td>
					</tr>';
	}

	foreach ($template['top'] as $student)
	{
		echo '
					<tr>
						<td>', $student['name'], '</td>
						<td>', $student['class'], '</td>
						<td>', $student['pluses'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<br class="clear" />';

	echo '
		<div class="pull-left half">
			<div class="page-header">
				<h3>Top plus reasons</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Reason</th>
						<th>Entries</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['plus']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any top reasons!</td>
					</tr>';
	}

	foreach ($template['plus'] as $reason)
	{
		echo '
					<tr>
						<td>', $reason['name'], '</td>
						<td>', $reason['entries'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>';

	echo '
		<div class="pull-right half">
			<div class="page-header">
				<h3>Top minus reasons</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Reason</th>
						<th>Entries</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['minus']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any top reasons!</td>
					</tr>';
	}

	foreach ($template['minus'] as $reason)
	{
		echo '
					<tr>
						<td>', $reason['name'], '</td>
						<td>', $reason['entries'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<br class="clear" />';
}