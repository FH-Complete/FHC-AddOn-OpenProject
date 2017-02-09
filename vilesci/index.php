<?php
/* Copyright (C) 2013 FH Technikum-Wien
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
 *
 */
require_once('../../../config/vilesci.config.inc.php');
require_once('../../../include/benutzerberechtigung.class.php');

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../../../skin/fhcomplete.css" type="text/css">
    <link rel="stylesheet" href="../../../skin/vilesci.css" type="text/css">
    <title>OP</title>
</head>
<body>
<h1>OpenProject Sync</h1>';

$uid = (new authentication())->getUser();
$rechte = new benutzerberechtigung();
$rechte->getBerechtigungen($uid);

if(!$rechte->isBerechtigt('basis/addon'))
{
    die('Sie haben keine Berechtigung fuer diese Seite');
}

echo '

<div>
    <p>This plugin can export a planner project to an OpenProject instance.</p>
</div>

<h2>Setup</h2>
<ul>
    <li><a href="./index.dist.php/Install">Install script</a>
        <ul>
            <li>Adds new permissions and grants them to admin.</li>
        </ul>
    </li>

    <li><a href="./index.dist.php/Configure">Config page</a>
        <ul>
            <li>Set the url, api-key and mapping.</li>
        </ul>
    </li>
</ul>

<br>

<h2>Use</h2>
<ul>
    <li><a href="./index.dist.php/Sync" target="_blank">Sync</a>
        <ul>
            <li>GET parameter "projekt_kurzbz" to specify fhcomplete project</li>
            <li>GET parameter "user_check=false" to ignore missing users in OpenProject</li>
        <ul>
    </li>
</ul>
';
?>
