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
$xoopsOption['template_main'] = 'myconference_index.html';

//if (($HTTP_POST_VARS)) {
//    foreach ( $HTTP_POST_VARS as $k => $v ) {
//        echo "k ($k) = v ($v)<br>";
//    }
//}

if (isset($HTTP_GET_VARS['cid'])) {
    $cid = $HTTP_GET_VARS['cid'];
} elseif (isset($HTTP_POST_VARS['cid'])) {
    $cid = $HTTP_POST_VARS['cid'];
} else {
    $cid = "";
}

if (empty($cid)) {
    $result = $xoopsDB->query("SELECT cid FROM ".$xoopsDB->prefix("myconference_main")." WHERE isdefault=1") or $eh->show("1001");
    list($cid) = $xoopsDB->fetchRow($result);
}

if (empty($cid)) {
    xoops_header();
    xoops_error(_MA_NOCONGRESS);
    xoops_footer();
    exit();
}
$result = $xoopsDB->query("SELECT title, subtitle, subsubtitle, abstract FROM ".$xoopsDB->prefix("myconference_main")." WHERE cid=$cid") or $eh->show("0013");

list($title, $subtitle, $subsubtitle, $abstract) = $xoopsDB->fetchRow($result);

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

if (isset($HTTP_GET_VARS['sid'])) {
    $result = $xoopsDB->query("SELECT abstract FROM ".$xoopsDB->prefix("myconference_sections")." WHERE sid=$sid") or $eh->show("0013");
    list($abstract) = $xoopsDB->fetchRow($result);
}

$xoopsTpl->append('sections', array('data' => $myts->displayTarea($abstract)));

include XOOPS_ROOT_PATH.'/footer.php';

?>
