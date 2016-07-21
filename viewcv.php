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
$xoopsOption['template_main'] = 'myconference_cv.html';

if (isset($HTTP_GET_VARS['cvid'])) {
    $sid = $HTTP_GET_VARS['cvid'];
} elseif (isset($HTTP_POST_VARS['cvid'])) { 
    $sid = $HTTP_POST_VARS['cvid'];
}

if (empty($cvid)) {
    $eh->show("0013");
}

$width=0;
$xoopsTpl->assign('lang_name', _MA_NAME);
$xoopsTpl->assign('lang_email', _MA_EMAIL);
$xoopsTpl->assign('lang_url', _MA_URL);
$xoopsTpl->assign('lang_picture', _MA_PICTURE);
$xoopsTpl->assign('lang_company', _MA_COMPANY);
$xoopsTpl->assign('lang_location', _MA_LOCATION);
$xoopsTpl->assign('lang_minicv', _MA_MINICV);
$labels = array(_MA_NAME, _MA_EMAIL, _MA_URL, _MA_PICTURE, _MA_COMPANY, _MA_LOCATION, _MA_MINICV);
foreach ($labels as $v) {
    $width = (strlen($v) > $width) ? strlen($v) : $width;
}
$xoopsTpl->assign('width', $width);

$rv = $xoopsDB->query("SELECT name, email, descrip, location, company, photo, url FROM ".$xoopsDB->prefix("myconference_cvs")." WHERE cvid=$cvid") or $eh->show("0013");
list($sname, $semail, $sminicv, $slocation, $scompany, $sphoto, $surl) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('sname', $sname);
$xoopsTpl->assign('semail', $semail);
$xoopsTpl->assign('surl', $surl);
$xoopsTpl->assign('sphoto', "/uploads/" . $sphoto);
$xoopsTpl->assign('scompany', $scompany);
$xoopsTpl->assign('slocation', $slocation);
$xoopsTpl->assign('sminicv', $myts->displayTarea($sminicv));

if (empty($cid)) {
    $result = $xoopsDB->query("SELECT cid FROM ".$xoopsDB->prefix("myconference_main")." WHERE isdefault=1") or $eh->show("1001");
    list($cid) = $xoopsDB->fetchRow($result);
}

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
