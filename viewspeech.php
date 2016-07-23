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
$xoopsOption['template_main'] = 'myconference_speech.tpl';
include XOOPS_ROOT_PATH . '/header.php';
$eh   = new ErrorHandler;
$myts = MyTextSanitizer::getInstance();

//if (isset($_GET['sid'])) {
//    $sid = (int)$_GET['sid'];
//} elseif (isset($_POST['sid'])) {
//    $sid = (int)$_POST['sid'];
//}

$sid     = XoopsRequest::getInt('sid', XoopsRequest::getInt('sid', 0, 'GET'), 'POST');

if (empty($sid)) {
    $eh::show('0013');
}

$width = 0;
$xoopsTpl->assign('lang_speaker', _MD_MYCONFERENCE_SPEAKER);
$xoopsTpl->assign('lang_date', _MD_MYCONFERENCE_DATE);
$xoopsTpl->assign('lang_time', _MD_MYCONFERENCE_TIME);
$xoopsTpl->assign('lang_duration', _MD_MYCONFERENCE_DURATION);
$xoopsTpl->assign('lang_summary', _MD_MYCONFERENCE_SUMMARY);
$labels = array(_MD_MYCONFERENCE_SPEAKER, _MD_MYCONFERENCE_DATE, _MD_MYCONFERENCE_TIME, _MD_MYCONFERENCE_DURATION, _MD_MYCONFERENCE_SUMMARY);
foreach ($labels as $v) {
    $width = (strlen($v) > $width) ? strlen($v) : $width;
}
$xoopsTpl->assign('width', $width);

$xoopsTpl->assign('lang_minutes', _MD_MYCONFERENCE_MINUTES);
$rv = $xoopsDB->query('SELECT cid, title, speakerid, stime, duration, summary FROM ' . $xoopsDB->prefix('myconference_speeches') . " WHERE sid=$sid") or $eh::show('0013');
list($cid, $stitle, $speakerid, $stime, $duration, $summary) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('stitle', $stitle);
if (isset($speakerid)) {
    $rv = $xoopsDB->query('SELECT name FROM ' . $xoopsDB->prefix('myconference_speakers') . " WHERE speakerid=$speakerid") or $eh::show('0013');
    list($sname) = $xoopsDB->fetchRow($rv);
    $xoopsTpl->assign('sname', $sname);
    $xoopsTpl->assign('speakerid', $speakerid);
}
$xoopsTpl->assign('date', date('D, d M', $stime));
$xoopsTpl->assign('stime', date('H:i', $stime));
$xoopsTpl->assign('duration', $duration);
$xoopsTpl->assign('summary', $myts->displayTarea($summary));

$rv = $xoopsDB->query('SELECT title, subtitle, subsubtitle FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid") or $eh::show('0013');
list($title, $subtitle, $subsubtitle) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('title', $title);
$xoopsTpl->assign('subtitle', $subtitle);
$xoopsTpl->assign('subsubtitle', $subsubtitle);

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
