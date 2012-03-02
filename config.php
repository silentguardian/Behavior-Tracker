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

$core = array();

$core['version'] = '1.0';
$core['cookie'] = 'bt1976';

$core['site_dir'] = dirname(__FILE__);
$core['root_dir'] = $core['site_dir'] . '/library';
$core['includes_dir'] = $core['site_dir'] . '/library/includes';
$core['modules_dir'] = $core['site_dir'] . '/library/modules';

$db = array();

$db['server'] = '';
$db['name'] = '';
$db['user'] = '';
$db['password'] = '';