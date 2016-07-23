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

include __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'myconference_track.tpl';
include XOOPS_ROOT_PATH . '/header.php';
$eh   = new ErrorHandler;
$myts = MyTextSanitizer::getInstance();

//if (isset($_GET['tid'])) {
//    $tid = $_GET['tid'];
//} elseif (isset($_POST['tid'])) {
//    $tid = $_POST['tid'];
//}

$tid     = XoopsRequest::getInt('tid', XoopsRequest::getInt('tid', 0, 'GET'), 'POST');

if (0 === $tid) {
    $eh::show('0013');
}

$xoopsTpl->assign('lang_summary', _MD_MYCONFERENCE_SUMMARY);
$xoopsTpl->assign('width', strlen(_MD_MYCONFERENCE_SUMMARY));

$rv = $xoopsDB->query('SELECT title, cid, summary FROM ' . $xoopsDB->prefix('myconference_tracks') . " WHERE tid=$tid") or $eh::show('0013');
list($ttitle, $cid, $summary) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('ttitle', $ttitle);
$xoopsTpl->assign('summary', $myts->displayTarea($summary));

if (isset($cid)) {
    $rv = $xoopsDB->query('SELECT title, subtitle, subsubtitle FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid") or $eh::show('0013');
    list($title, $subtitle, $subsubtitle) = $xoopsDB->fetchRow($rv);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('subtitle', $subtitle);
    $xoopsTpl->assign('subsubtitle', $subsubtitle);
}

$result = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE cid=$cid ORDER BY title") or $eh::show('0013');

$count = 1;
while ($section = $xoopsDB->fetchArray($result)) {
    $xoopsTpl->append('sections', array('id' => $section['sid'], 'title' => $section['title'], 'count' => $count));
    ++$count;
}
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->append('sections', array('id' => 0, 'title' => _MD_MYCONFERENCE_PROGRAM, 'count' => $count));
++$count;

include XOOPS_ROOT_PATH . '/footer.php';
