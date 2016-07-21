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

include "header.php";
$eh = new ErrorHandler; 
$myts =& MyTextSanitizer::getInstance();
$xoopsOption['template_main'] = 'myconference_speech.html';

if (isset($HTTP_GET_VARS['sid'])) {
    $sid = $HTTP_GET_VARS['sid'];
} elseif (isset($HTTP_POST_VARS['sid'])) { 
    $sid = $HTTP_POST_VARS['sid'];
}

if (empty($sid)) {
    $eh->show("0013");
}

$width=0;
$xoopsTpl->assign('lang_speaker', _MA_SPEAKER);
$xoopsTpl->assign('lang_date', _MA_DATE);
$xoopsTpl->assign('lang_time', _MA_TIME);
$xoopsTpl->assign('lang_duration', _MA_DURATION);
$xoopsTpl->assign('lang_abstract', _MA_ABSTRACT);
$labels = array(_MA_SPEAKER,_MA_DATE,_MA_TIME,_MA_DURATION,_MA_ABSTRACT);
foreach ($labels as $v) {
    $width = (strlen($v) > $width) ? strlen($v) : $width;
}
$xoopsTpl->assign('width', $width);

$xoopsTpl->assign('lang_minutes', _MA_MINUTES);
$rv = $xoopsDB->query("SELECT cid, title, cvid, stime, duration, abstract FROM ".$xoopsDB->prefix("myconference_speeches")." WHERE sid=$sid") or $eh->show("0013");
list($cid, $stitle, $cvid, $stime, $duration, $abstract) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('stitle', $stitle);
if (isset($cvid)) {
    $rv = $xoopsDB->query("SELECT name FROM ".$xoopsDB->prefix("myconference_cvs")." WHERE cvid=$cvid") or $eh->show("0013");
    list($sname) = $xoopsDB->fetchRow($rv);
    $xoopsTpl->assign('sname', $sname);
    $xoopsTpl->assign('cvid', $cvid);
}
$xoopsTpl->assign('date', date('D, d M', $stime));
$xoopsTpl->assign('stime', date('H:i', $stime));
$xoopsTpl->assign('duration', $duration);
$xoopsTpl->assign('abstract', $myts->displayTarea($abstract));

$rv = $xoopsDB->query("SELECT title, subtitle, subsubtitle FROM ".$xoopsDB->prefix("myconference_main")." WHERE cid=$cid") or $eh->show("0013");
list($title, $subtitle, $subsubtitle) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('title', $title);
$xoopsTpl->assign('subtitle', $subtitle);
$xoopsTpl->assign('subsubtitle', $subsubtitle);

$result = $xoopsDB->query("SELECT sid, title FROM ".$xoopsDB->prefix("myconference_sections")." WHERE cid=$cid ORDER BY title") or $eh->show("0013");

$count = 1;
while($section = $xoopsDB->fetchArray($result)) {
    $xoopsTpl->append('sections', array('id' => $section['sid'], 'title' => $section['title'], 'count' => $count));
    $count++;
}
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->append('sections', array('id' => 0, 'title' => _MA_PROGRAM, 'count' => $count));
$count++;

include XOOPS_ROOT_PATH.'/footer.php';

?>
