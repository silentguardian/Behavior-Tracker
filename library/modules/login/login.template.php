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

function template_login_main()
{
	echo '
		<form class="form-horizontal" action="./?module=login" method="post">
			<fieldset>
				<legend>Teacher Login</legend>
				<div class="control-group">
					<label class="control-label" for="teacher_alias">Teacher alias:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="teacher_alias" name="teacher_alias" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="teacher_password">Teacher password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="teacher_password" name="teacher_password" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="submit" value="Submit" />
				</div>
			</fieldset>
		</form>';
}