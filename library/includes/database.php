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

function db_initiate()
{
	global $db;

	$connection = @mysql_connect($db['server'], $db['user'], $db['password']);

	if (!$connection)
		fatal_error('Could not connect to database.');

	$select = @mysql_select_db($db['name'], $connection);

	if (!$select)
		fatal_error('Could not select the database.');

	$db['connection'] = $connection;

	db_query("SET NAMES utf8");
}

function db_query($sql)
{
	global $db;

	$db['debug'][] = $sql;

	$result = @mysql_query($sql, $db['connection']);

	if ($result === false)
		fatal_error('Database error: [' . mysql_errno($db['connection']) . '] ' . mysql_error($db['connection']));

	return $result;
}

function db_affected_rows()
{
	global $db;

	return mysql_affected_rows($db['connection']);
}

function db_insert_id()
{
	global $db;

	return mysql_insert_id($db['connection']);
}

function db_fetch_row($resource)
{
	return $resource ? mysql_fetch_row($resource) : false;
}

function db_fetch_assoc($resource)
{
	return $resource ? mysql_fetch_assoc($resource) : false;
}

function db_free_result($resource)
{
	if ($resource)
		mysql_free_result($resource);
}