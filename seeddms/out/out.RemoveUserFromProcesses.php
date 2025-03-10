<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005 Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010-2016 Uwe Steinmann
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

if(!isset($settings))
	require_once("../inc/inc.Settings.php");
require_once("inc/inc.Utils.php");
require_once("inc/inc.LogInit.php");
require_once("inc/inc.Language.php");
require_once("inc/inc.Init.php");
require_once("inc/inc.Extension.php");
require_once("inc/inc.DBInit.php");
require_once("inc/inc.ClassUI.php");
require_once("inc/inc.Authentication.php");

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user));
$accessop = new SeedDMS_AccessOperation($dms, $user, $settings);
if (!$accessop->check_view_access($view, $_GET)) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"), false, $isajax);
}

if (!isset($_GET["userid"]) || !is_numeric($_GET["userid"]) || intval($_GET["userid"])<1) {
	UI::exitError(getMLText("rm_user"),getMLText("invalid_user_id"));
}

$rmuser = $dms->getUser(intval($_GET["userid"]));
if (!is_object($rmuser)) {
	UI::exitError(getMLText("rm_user"),getMLText("invalid_user_id"));
}

//if ($rmuser->getID()==$user->getID()) {
//	UI::exitError(getMLText("rm_user"),getMLText("cannot_delete_yourself"));
//}

$task = null;
if (isset($_GET["task"])) {
  $task = $_GET['task'];
}

$type = null;
if (isset($_GET["type"])) {
  $type = $_GET['type'];
}

$allusers = $dms->getAllUsers($settings->_sortUsersInList);

if($view) {
	$view->setParam('showtree', showtree());
	$view->setParam('rmuser', $rmuser);
	$view->setParam('allusers', $allusers);
	$view->setParam('task', $task);
	$view->setParam('type', $type);
	$view->setParam('cachedir', $settings->_cacheDir);
	$view->setParam('rootfolder', $dms->getFolder($settings->_rootFolderID));
	$view->setParam('conversionmgr', $conversionmgr);
	$view->setParam('previewWidthList', $settings->_previewWidthList);
	$view->setParam('previewconverters', $settings->_converters['preview']);
	$view->setParam('convertToPdf', $settings->_convertToPdf);
	$view->setParam('timeout', $settings->_cmdTimeout);
	$view->setParam('accessobject', $accessop);
	$view($_GET);
	exit;
}
