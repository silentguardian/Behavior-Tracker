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

function template_profile_main()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url('profile'), '" method="post">
			<fieldset>
				<legend>Edit Profile</legend>
				<div class="control-group">
					<label class="control-label" for="choose_password">Choose password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="choose_password" name="choose_password" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="verify_password">Verify password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="verify_password" name="verify_password" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="current_password">Current password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="current_password" name="current_password" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
				</div>
			</fieldset>
		</form>';
}