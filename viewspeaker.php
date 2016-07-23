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
$xoopsOption['template_main'] = 'myconference_speaker.tpl';
include XOOPS_ROOT_PATH . '/header.php';
//$eh   = new ErrorHandler;
$myts = MyTextSanitizer::getInstance();

//if (isset($_GET['speakerid'])) {
//    $speakerid = (int)$_GET['speakerid'];
//} elseif (isset($_POST['speakerid'])) {
//    $speakerid = (int)$_POST['speakerid'];
//}

$speakerid = XoopsRequest::getInt('speakerid', XoopsRequest::getInt('speakerid', 0, 'GET'), 'POST');

//if (0 === $speakerid) {
//    $eh::show('0013');
//}

$width = 0;
$xoopsTpl->assign('lang_name', _MD_MYCONFERENCE_NAME);
$xoopsTpl->assign('lang_email', _MD_MYCONFERENCE_EMAIL);
$xoopsTpl->assign('lang_url', _MD_MYCONFERENCE_URL);
$xoopsTpl->assign('lang_picture', _MD_MYCONFERENCE_PICTURE);
$xoopsTpl->assign('lang_company', _MD_MYCONFERENCE_COMPANY);
$xoopsTpl->assign('lang_location', _MD_MYCONFERENCE_LOCATION);
$xoopsTpl->assign('lang_minibio', _MD_MYCONFERENCE_MINI_BIO);
$labels = array(_MD_MYCONFERENCE_NAME, _MD_MYCONFERENCE_EMAIL, _MD_MYCONFERENCE_URL, _MD_MYCONFERENCE_PICTURE, _MD_MYCONFERENCE_COMPANY, _MD_MYCONFERENCE_LOCATION, _MD_MYCONFERENCE_MINI_BIO);
foreach ($labels as $v) {
    $width = (strlen($v) > $width) ? strlen($v) : $width;
}
$xoopsTpl->assign('width', $width);
$uploadirectory =  'uploads/' . MYCONFERENCE_DIRNAME .'/images';

$rv = $xoopsDB->query('SELECT name, email, descrip, location, company, photo, url FROM ' . $xoopsDB->prefix('myconference_speakers') . " WHERE speakerid=$speakerid");// or $eh::show('0013');
list($sname, $semail, $sminibio, $slocation, $scompany, $sphoto, $surl) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('sname', $sname);
$xoopsTpl->assign('semail', $semail);
$xoopsTpl->assign('surl', $surl);
$xoopsTpl->assign('sphoto', $uploadirectory .'/' . $sphoto);
$xoopsTpl->assign('scompany', $scompany);
$xoopsTpl->assign('slocation', $slocation);
$xoopsTpl->assign('sminibio', $myts->displayTarea($sminibio));


$cid     = XoopsRequest::getInt('cid', XoopsRequest::getInt('cid', 0, 'GET'), 'POST');
if (0 === $cid) {
    $result = $xoopsDB->query('SELECT cid FROM ' . $xoopsDB->prefix('myconference_main') . ' WHERE isdefault=1');// or $eh::show('1001');
    list($cid) = $xoopsDB->fetchRow($result);
}

$rv = $xoopsDB->query('SELECT title, subtitle, subsubtitle FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid");// or $eh::show('0013');
list($title, $subtitle, $subsubtitle) = $xoopsDB->fetchRow($rv);
$xoopsTpl->assign('title', $title);
$xoopsTpl->assign('subtitle', $subtitle);
$xoopsTpl->assign('subsubtitle', $subsubtitle);

$result = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE cid=$cid ORDER BY title");// or $eh::show('0013');

$count = 1;
while ($section = $xoopsDB->fetchArray($result)) {
    $xoopsTpl->append('sections', array('id' => $section['sid'], 'title' => $section['title'], 'count' => $count));
    ++$count;
}
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->append('sections', array('id' => 0, 'title' => _MD_MYCONFERENCE_PROGRAM, 'count' => $count));
++$count;

include XOOPS_ROOT_PATH . '/footer.php';
