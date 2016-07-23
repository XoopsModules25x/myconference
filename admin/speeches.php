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

include __DIR__ . '/admin_header.php';
include __DIR__ . '/conference.php';
include_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';

$eh = new ErrorHandler;

//if (isset($_POST['fct'])) {
//    $fct = trim($_POST['fct']);
//}
//if (isset($_GET['fct'])) {
//    $fct = trim($_GET['fct']);
//}
//
//if (!isset($fct)) {
//    $fct = '';
//}

$fct = XoopsRequest::getString('fct', XoopsRequest::getString('fct', '', 'GET'), 'POST');

switch ($fct) {
    case 'updspeech':
        $eh      = new ErrorHandler;
        $title   = XoopsRequest::getString('title', '', 'POST');//$_POST['title'];
        $summary = XoopsRequest::getText('summary', '', 'POST');//$_POST['summary'];
        $stime   = XoopsRequest::getText('stime', '', 'POST');//$_POST['stime'];
        $date    = strtotime(array_shift($stime));
        $date += array_shift($stime);
        $stime     = $date;
        $speakerid = XoopsRequest::getInt('speakerid', 0, 'POST');//(int)$_POST['speakerid'];
        $cid       = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        $tid       = XoopsRequest::getInt('tid', 0, 'POST');//(int)$_POST['tid'];
        $duration  = XoopsRequest::getInt('duration', 0, 'POST');//$_POST['duration'];
        $etime     = $stime + $duration * 60;
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[0]) //$_POST['xoops_upload_file'][0])
            && !empty(XoopsRequest::getString('slides1', null, 'POST')) //$_POST['slides1'])
        ) {
            $slides1 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[0]; //$_POST['xoops_upload_file'][0];
            $slides1 = getFile($slides1);
        }
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[1]) //$_POST['xoops_upload_file'][1])
            && !empty(XoopsRequest::getString('slides2', null, 'POST')) //$_POST['slides2'])
        ) {
            $slides2 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[1]; //$_POST['xoops_upload_file'][1];
            $slides2 = getFile($slides2);
        }
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[2]) //$_POST['xoops_upload_file'][2])
            && !empty(XoopsRequest::getString('slides3', null, 'POST')) //$_POST['slides3'])
        ) {
            $slides3 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[2]; //$_POST['xoops_upload_file'][2];
            $slides3 = getFile($slides3);
        }
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[3]) //$_POST['xoops_upload_file'][3])
            && !empty(XoopsRequest::getString('slides4', null, 'POST')) //$_POST['slides4'])
        ) {
            $slides4 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[3]; //$_POST['xoops_upload_file'][3];
            $slides4 = getFile($slides4);
        }
        $sid = XoopsRequest::getInt('sid', 0, 'POST');//$_POST['sid'];
        $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_speeches')
                                  . " SET title='$title', summary='$summary', stime='$stime', etime='$etime', speakerid='$speakerid', cid='$cid', tid='$tid',duration='$duration', slides1='$slides1', slides2='$slides2', slides3='$slides3', slides4='$slides4' WHERE sid=$sid") or $eh::show('0013');
        if ($result) {
            redirect_header('speeches.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    case 'editspeech':
        xoops_cp_header();

        $action = $action = XoopsRequest::getString('action', 0, 'POST');//$_POST['action'];
        if ($action === 'upd') {
            $sid = XoopsRequest::getInt('sid', 0, 'POST');//trim($_POST['sid']) or $eh::show('1001');
            $result = $xoopsDB->query('SELECT title,summary,stime,etime,speakerid,cid,tid,duration,slides1,slides2,slides3,slides4 FROM ' . $xoopsDB->prefix('myconference_speeches') . " WHERE sid=$sid") or $eh::show('0013');
            list($title_v, $summary_v, $stime_v, $etime_v, $speakerid_v, $cid_v, $tid_v, $duration_v, $slides1_v, $slides2_v, $slides3_v, $slides4_v) = $xoopsDB->fetchRow($result);

            // Get the available Speakers
            $result = $xoopsDB->query('SELECT speakerid, name FROM ' . $xoopsDB->prefix('myconference_speakers') . ' ORDER BY Name ASC') or $eh::show('0013');
            $speakerSelect = new XoopsFormSelect(_AM_MYCONFERENCE_SPEAKERSNAME, 'speakerid', $speakerid_v);
            $speakerSelect->addOption(0, _AM_MYCONFERENCE_NONE);
            while (list($speakerid, $name) = $xoopsDB->fetchRow($result)) {
                $speakerSelect->addOption($speakerid, $name);
            }

            // Get the available congress
            $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY Title ASC') or $eh::show('0013');
            $cid_select = new XoopsFormSelect(_AM_MYCONFERENCE_CONFERENCESTITLE, 'cid', $cid_v);
            $cid_select->addOption(0, _AM_MYCONFERENCE_NONE);
            while (list($cid, $title) = $xoopsDB->fetchRow($result)) {
                $cid_select->addOption($cid, $title);
            }

            // Get the available tracks
            $result = $xoopsDB->query('SELECT tid, title FROM ' . $xoopsDB->prefix('myconference_tracks') . ' ORDER BY Title ASC') or $eh::show('0013');
            $trk_select = new XoopsFormSelect(_AM_MYCONFERENCE_TRACKSTITLE, 'tid', $tid_v);
            $trk_select->addOption(0, _AM_MYCONFERENCE_NONE);
            while (list($tid, $title) = $xoopsDB->fetchRow($result)) {
                $trk_select->addOption($tid, $title);
            }

            $title = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 100, $title_v);
            // $stime = new XoopsFormText(_AM_MYCONFERENCE_STIME, "stime", 14, 16, date("Y-m-d H:i", $stime_v));
            $stime    = XoopsFormDateTimeI(_AM_MYCONFERENCE_STIME, 'stime', 10, $stime_v, 30);
            $duration = new XoopsFormText(_AM_MYCONFERENCE_DURATION, 'duration', 3, 3, $duration_v);
            $fct      = new XoopsFormHidden('fct', 'updspeech');
            $sid      = new XoopsFormHidden('sid', $sid);
            $slides1  = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES1, 'slides1', 2048000);
            $slides2  = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES2, 'slides2', 2048000);
            $slides3  = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES3, 'slides3', 2048000);
            $slides4  = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES4, 'slides4', 2048000);
            $summary  = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
            $summary->setValue($summary_v);
            $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

            $form = new XoopsThemeForm(_AM_MYCONFERENCE_UPDSPEECH, 'editspeechform', 'speeches.php');
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement($title, true);
            $form->addElement($stime);
            $form->addElement($speakerSelect, true);
            $form->addElement($cid_select);
            $form->addElement($trk_select);
            $form->addElement($duration, true);
            $form->addElement($slides1);
            $form->addElement($slides2);
            $form->addElement($slides3);
            $form->addElement($slides4);
            $form->addElement($summary, true);
            $form->addElement($fct);
            $form->addElement($sid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action === 'del') {
            $sid = XoopsRequest::getInt('sid', 0, 'POST');//trim($_POST['sid']) or $eh::show('1001');
            xoops_confirm(array('fct' => 'delspeechok', 'sid' => $sid), 'speeches.php', _AM_MYCONFERENCE_DELSPEECH);
            xoops_cp_footer();
        }
        break;

    case 'delspeechok':
        $speakerid = XoopsRequest::getInt('sid', 0, 'POST');//trim($_POST['sid']) or $eh::show('1001');
        $result = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('myconference_speeches') . " WHERE sid=$sid") or $eh::show('0013');
        redirect_header('speeches.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        break;
    case 'addspeech':
        $eh      = new ErrorHandler;
        $title   = XoopsRequest::getString('title', '', 'POST');//$_POST['title'];
        $summary = XoopsRequest::getText('summary', '', 'POST');//$_POST['summary'];

        $stime = XoopsRequest::getString('stime', '', 'POST');//$_POST['stime'];
        $date  = strtotime(array_shift($stime));
        $date += array_shift($stime);
        $stime     = $date;
        $duration  = XoopsRequest::getInt('duration', 0, 'POST');//$_POST['duration'];
        $etime     = $stime + $duration * 60;
        $speakerid = XoopsRequest::getInt('speakerid', 0, 'POST');//$_POST['speakerid'];
        $cid       = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        $tid       = XoopsRequest::getInt('tid', 0, 'POST');//$_POST['tid'];

        // strip time and date
        //reset($stime);
        //$date = strtotime(array_shift($stime));
        //$date += array_shift($stime);
        //$stime = $date;

        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[0]) //$_POST['xoops_upload_file'][0])
            && !empty(XoopsRequest::getString('slides1', null, 'POST')) //$_POST['slides1'])
        ) {
            $slides1 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[0]; //$_POST['xoops_upload_file'][0];
            $slides1 = getFile($slides1);
        }
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[1]) //$_POST['xoops_upload_file'][1])
            && !empty(XoopsRequest::getString('slides2', null, 'POST')) //$_POST['slides2'])
        ) {
            $slides2 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[1]; //$_POST['xoops_upload_file'][1];
            $slides2 = getFile($slides2);
        }
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[2]) //$_POST['xoops_upload_file'][2])
            && !empty(XoopsRequest::getString('slides3', null, 'POST')) //$_POST['slides3'])
        ) {
            $slides3 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[2]; //$_POST['xoops_upload_file'][2];
            $slides3 = getFile($slides3);
        }
        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[3]) //$_POST['xoops_upload_file'][3])
            && !empty(XoopsRequest::getString('slides4', null, 'POST')) //$_POST['slides4'])
        ) {
            $slides4 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[3]; //$_POST['xoops_upload_file'][3];
            $slides4 = getFile($slides4);
        }
        $sid = XoopsRequest::getInt('sid', 0, 'POST');//$_POST['sid'];

        $result = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('myconference_speeches')
                                  . " (title,summary,stime,etime,speakerid,cid,tid,slides1,slides2,slides3,slides4,duration) VALUES (\"$title\",\"$summary\",\"$stime\",\"$etime\", \"$speakerid\",\"$cid\",\"$tid\",\"$slides1\",\"$slides2\",\"$slides3\",\"$slides4\",\"$duration\")")
        or $eh::show('0013');
        if ($result) {
            redirect_header('speeches.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    default:
        xoops_cp_header();

        // Get available speeches for the Update/Delete form
        $result = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_speeches') . ' ORDER BY Title ASC') or $eh::show('0013');
        $speech_select = new XoopsFormSelect(_AM_MYCONFERENCE_TITLE, 'sid');
        while (list($sid, $title) = $xoopsDB->fetchRow($result)) {
            $speech_select->addOption($sid, $title);
        }
        $action_select = new XoopsFormSelect(_AM_MYCONFERENCE_ACTION, 'action');
        $action_select->addOption('upd', _AM_MYCONFERENCE_EDIT);
        $action_select->addOption('del', _AM_MYCONFERENCE_DELE);
        $fct           = new XoopsFormHidden('fct', 'editspeech');
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $editform = new XoopsThemeForm(_AM_MYCONFERENCE_EDITSPEECH, 'editspeechform', 'speeches.php');
        $editform->setExtra('enctype="multipart/form-data"');
        $editform->addElement($fct);
        $editform->addElement($speech_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $cid_v = '';
        // Get the available Speakers
        $result = $xoopsDB->query('SELECT speakerid, name FROM ' . $xoopsDB->prefix('myconference_speakers') . ' ORDER BY Name ASC') or $eh::show('0013');
        $speakerSelect = new XoopsFormSelect(_AM_MYCONFERENCE_SPEAKERSNAME, 'speakerid');
        $speakerSelect->addOption(0, _AM_MYCONFERENCE_NONE);
        while (list($speakerid, $name) = $xoopsDB->fetchRow($result)) {
            $speakerSelect->addOption($speakerid, $name);
        }

        // Get the available congress
        $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY Title ASC') or $eh::show('0013');
        $cid_select = new XoopsFormSelect(_AM_MYCONFERENCE_CONFERENCESTITLE, 'cid', $cid_v);
        $cid_select->addOption(0, _AM_MYCONFERENCE_NONE);
        while (list($cid, $title) = $xoopsDB->fetchRow($result)) {
            $cid_select->addOption($cid, $title);
        }

        // Get the available tracks
        $result = $xoopsDB->query('SELECT tid, title FROM ' . $xoopsDB->prefix('myconference_tracks') . ' ORDER BY Title ASC') or $eh::show('0013');
        $trk_select = new XoopsFormSelect(_AM_MYCONFERENCE_TRACKSTITLE, 'tid', 0);
        $trk_select->addOption(0, _AM_MYCONFERENCE_NONE);
        while (list($tid, $title) = $xoopsDB->fetchRow($result)) {
            $trk_select->addOption($tid, $title);
        }

        $title         = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 100);
        $stime         = XoopsFormDateTimeI(_AM_MYCONFERENCE_STIME, 'stime', 10, 0, 30);
        $duration      = new XoopsFormText(_AM_MYCONFERENCE_DURATION, 'duration', 3, 3);
        $fct           = new XoopsFormHidden('fct', 'addspeech');
        $slides1       = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES1, 'slides1', 2048000);
        $slides2       = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES2, 'slides2', 2048000);
        $slides3       = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES3, 'slides3', 2048000);
        $slides4       = new XoopsFormFile(_AM_MYCONFERENCE_SLIDES4, 'slides4', 2048000);
        $summary       = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

        $form = new XoopsThemeForm(_AM_MYCONFERENCE_ADDSPEECH, 'addspeechform', 'speeches.php');
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement($title, true);
        $form->addElement($speakerSelect, true);
        $form->addElement($cid_select);
        $form->addElement($trk_select);
        $form->addElement($stime, true);
        $form->addElement($duration, true);
        $form->addElement($slides1);
        $form->addElement($slides2);
        $form->addElement($slides3);
        $form->addElement($slides4);
        $form->addElement($summary, true);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}
