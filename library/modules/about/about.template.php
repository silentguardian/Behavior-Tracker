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

function template_about_main()
{
	global $template;

	echo '
		<div class="page-header">
			<h2>About</h2>
		</div>
		<p class="content">
			Behavior Tracker is an online tool designed to track behavior of students through a plus/minus system.
		</p>
		<p class="content">
			This tool is coded in <a href="http://php.net">PHP</a> and uses <a href="http://twitter.github.com/bootstrap">Bootstrap</a> CSS framework. The banner image is from one of the works of <a href="http://www.pixiv.net/member.php?id=2230272">hazfirst</a>.
		</p>';
}