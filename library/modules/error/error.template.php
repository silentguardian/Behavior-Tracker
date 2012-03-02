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

function template_error_main()
{
	global $template;

	echo '
		<div class="alert alert-error">
			<h4 class="alert-heading">Error!</h4>
			', $template['error'], '
		</div>';
}