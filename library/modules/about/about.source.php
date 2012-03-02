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

function about_main()
{
	global $core, $template;

	$template['page_title'] = 'About';
	$core['current_template'] = 'about_main';
}