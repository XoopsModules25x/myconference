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

$xoopsOption['template_main'] = 'myconference_bios.tpl';
include __DIR__ . '/header.php';
$eh   = new ErrorHandler;
$myts = MyTextSanitizer::getInstance();

if (isset($_GET['cvid'])) {
    $cvid = (int)$_GET['cvid'];
} elseif (isset($_POST['cvid'])) {
    $cvid = (int)$_POST['cvid'];
}

if (empty($cvid)) {
    $eh::show('0013');
}

$width = 0;
$xoopsTpl->assign('lang_name', _MD_MYCONFERENCE_NAME);
$xoopsTpl->assign('lang_email', _MD_MYCONFERENCE_EMAIL);
$xoopsTpl->assign('lang_url', _MD_MYCONFERENCE_URL);
$xoopsTpl->assign('lang_picture', _MD_MYCONFERENCE_PICTURE);
$xoopsTpl->assign('lang_company', _MD_MYCONFERENCE_COMPANY);
$xoopsTpl->assign('lang_location', _MD_MYCONFERENCE_LOCATION);
$xoopsTpl->assign('lang_minicv', _MD_MYCONFERENCE_MINICV);
$labels = array(_MD_MYCONFERENCE_NAME, _MD_MYCONFERENCE_EMAIL, _MD_MYCONFERENCE_URL, _MD_MYCONFERENCE_PICTURE, _MD_MYCONFERENCE_COMPANY, _MD_MYCONFERENCE_LOCATION, _MD_MYCONFERENCE_MINICV);
foreach ($labels as $v) {
    $width = (strlen($v) > $width) ? strlen($v) : $width;
}
$xoopsTpl->assign('width', $width);

$rv = $xoopsDB->query('SELECT name, email, descrip, location, company, photo, url FROM ' . $xoopsDB->prefix('myconference_bios') . " WHERE cvid=$cvid") OR $eh::show('0013');
list($sname, $semail, $sminicv, $slocation, $scompany, $sphoto, $surl) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('sname', $sname);
$xoopsTpl->assign('semail', $semail);
$xoopsTpl->assign('surl', $surl);
$xoopsTpl->assign('sphoto', '/uploads/' . $sphoto);
$xoopsTpl->assign('scompany', $scompany);
$xoopsTpl->assign('slocation', $slocation);
$xoopsTpl->assign('sminicv', $myts->displayTarea($sminicv));

if (empty($cid)) {
    $result = $xoopsDB->query('SELECT cid FROM ' . $xoopsDB->prefix('myconference_main') . ' WHERE isdefault=1') OR $eh::show('1001');
    list($cid) = $xoopsDB->fetchRow($result);
}

$rv = $xoopsDB->query('SELECT title, subtitle, subsubtitle FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid") OR $eh::show('0013');
list($title, $subtitle, $subsubtitle) = $xoopsDB->fetchRow($rv);
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

include XOOPS_ROOT_PATH . '/footer.php';
