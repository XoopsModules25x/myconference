<?php
// ------------------------------------------------------------------------- //
// Copyright 2004, Daniel Coletti (dcoletti@xtech.com.ar)                    //
// This file is part of myconference XOOPS' module.                          //
//                                                                           //
// This program is free software; you can redistribute it and/or modify      //
// it under the terms of the GNU General Public License as published by      //
// the Free Software Foundation; either version 2 of the License, or         //
// (at your option) any later version.                                       //
//                                                                           //
// This program is distributed in the hope that it will be useful,           //
// but WITHOUT ANY WARRANTY; without even the implied warranty of            //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             //
// GNU General Public License for more details.                              //
//                                                                           //
// You should have received a copy of the GNU General Public License         //
// along with This program; if not, write to the Free Software               //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA //
// ------------------------------------------------------------------------- //

$xoopsOption['template_main'] = 'myconference_index.tpl';
include __DIR__ . '/header.php';
$eh   = new ErrorHandler;
$myts = MyTextSanitizer::getInstance();

//if (($_POST)) {
//    foreach ($_POST as $k => $v) {
//        echo "k ($k) = v ($v)<br>";
//    }
//}

//if (isset($_GET['cid'])) {
//    $cid = (int)$_GET['cid'];
//} elseif (isset($_POST['cid'])) {
//    $cid = (int)$_POST['cid'];
//} else {
//    $cid = '';
//}

//global $xoopsDB;

$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
$cid     = XoopsRequest::getInt('cid', XoopsRequest::getInt('cid', 0, 'GET'), 'POST');

if (0 === $cid) {
    $result = $xoopsDB->queryF('SELECT cid FROM ' . $xoopsDB->prefix('myconference_main') . ' WHERE isdefault = 1') OR $eh::show('1001');
    if ($result) {
        list($cid) = $xoopsDB->fetchRow($result);
    }
}

if (empty($cid)) {
    xoops_header();
    xoops_error(_MD_MYCONFERENCE_NOCONGRESS);
    xoops_footer();
    exit();
}
$result = $xoopsDB->query('SELECT title, subtitle, subsubtitle, summary FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid") OR $eh::show('0013');

list($title, $subtitle, $subsubtitle, $summary) = $xoopsDB->fetchRow($result);

$xoopsTpl->assign('title', $title);
$xoopsTpl->assign('subtitle', $subtitle);
$xoopsTpl->assign('subsubtitle', $subsubtitle);

$result = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE cid=$cid ORDER BY title") OR $eh::show('0013');

$count = 1;
while ($section = $xoopsDB->fetchArray($result)) {
    $xoopsTpl->append('sections', array('id' => $section['sid'], 'title' => $section['title'], 'count' => $count));
    ++$count;
}
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->append('sections', array('id' => 0, 'title' => _MD_MYCONFERENCE_PROGRAM, 'count' => $count));
++$count;

if (isset($_GET['sid'])) {
    $sid = (int)$_GET['sid'];
} elseif (isset($_POST['sid'])) {
    $sid = (int)$_POST['sid'];
}

if (isset($_GET['sid'])) {
    $result = $xoopsDB->query('SELECT summary FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE sid=$sid") OR $eh::show('0013');
    list($summary) = $xoopsDB->fetchRow($result);
}

$xoopsTpl->append('sections', array('data' => $myts->displayTarea($summary)));

//include XOOPS_ROOT_PATH.'/footer.php';
include $GLOBALS['xoops']->path('footer.php');
