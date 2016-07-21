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
include "conference.php";
include_once XOOPS_ROOT_PATH."/class/module.errorhandler.php";

$eh = new ErrorHandler; 

if (isset($HTTP_POST_VARS['fct'])) {
    $fct = trim($HTTP_POST_VARS['fct']);
}
if (isset($HTTP_GET_VARS['fct'])) {
    $fct = trim($HTTP_GET_VARS['fct']);
}

if (!isset($fct)){
    $fct = '';
}

switch ( $fct ) {
    case "updspeech":
        $eh = new ErrorHandler;
        $title = $HTTP_POST_VARS['title'];
        $abstract = $HTTP_POST_VARS['abstract'];
        $stime = $HTTP_POST_VARS['stime'];
        $date = strtotime(array_shift($stime));
        $date += array_shift($stime);
        $stime = $date;
        $duration = $HTTP_POST_VARS['duration'];
        $etime = $stime + $duration * 60;
        if (isset($HTTP_POST_VARS['xoops_upload_file'][0]) && !empty($HTTP_POST_VARS['slides1'])) {
            $slides1 = $HTTP_POST_VARS['xoops_upload_file'][0];
            $slides1 = getFile($slides1);
        }
        if (isset($HTTP_POST_VARS['xoops_upload_file'][1]) && !empty($HTTP_POST_VARS['slides2'])) {
            $slides2 = $HTTP_POST_VARS['xoops_upload_file'][1];
            $slides2 = getFile($slides2);
        }
        if (isset($HTTP_POST_VARS['xoops_upload_file'][2]) && !empty($HTTP_POST_VARS['slides3'])) {
            $slides3 = $HTTP_POST_VARS['xoops_upload_file'][2];
            $slides3 = getFile($slides3);
        }
        if (isset($HTTP_POST_VARS['xoops_upload_file'][3]) && !empty($HTTP_POST_VARS['slides4'])) {
            $slides4 = $HTTP_POST_VARS['xoops_upload_file'][3];
            $slides4 = getFile($slides4);
        }
        $sid = $HTTP_POST_VARS['sid'];
        $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_speeches")." SET title='$title', abstract='$abstract', stime='$stime', etime='$etime', cvid='$cvid', cid='$cid', tid='$tid',duration='$duration', slides1='$slides1', slides2='$slides2', slides3='$slides3', slides4='$slides4' WHERE sid=$sid") or $eh->show("0013");
        if ($result) {
            redirect_header("speeches.php",2,_MD_DBUPDATED);
        }
        break;
    case "editspeech":
        xoops_cp_header();
        showAdmin();
        $action = $HTTP_POST_VARS['action'];
        if ($action == 'upd') {
            $sid = trim($HTTP_POST_VARS['sid']) or $eh->show("1001");
            $result = $xoopsDB->query("SELECT title,abstract,stime,etime,cvid,cid,tid,duration,slides1,slides2,slides3,slides4 FROM ".$xoopsDB->prefix("myconference_speeches")." WHERE sid=$sid") or $eh->show("0013");
            list($title_v, $abstract_v, $stime_v, $etime_v, $cvid_v, $cid_v, $tid_v, $duration_v, $slides1_v, $slides2_v, $slides3_v, $slides4_v) = $xoopsDB->fetchRow($result);

            // Get the available CVs
            $result = $xoopsDB->query("SELECT cvid, name FROM ".$xoopsDB->prefix("myconference_cvs")." ORDER BY Name ASC") or $eh->show("0013");
            $cv_select = new XoopsFormSelect(_MD_SPEAKERSNAME, "cvid", $cvid_v);
            $cv_select->addOption(0, _MD_NONE);
            while (list($cvid, $name) = $xoopsDB->fetchRow($result) ) {
                $cv_select->addOption($cvid, $name);
            }

            // Get the available congress
            $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY Title ASC") or $eh->show("0013");
            $cid_select = new XoopsFormSelect(_MD_CONFERENCESTITLE, "cid",$cid_v);
            $cid_select->addOption(0, _MD_NONE);
            while (list($cid, $title) = $xoopsDB->fetchRow($result) ) {
                $cid_select->addOption($cid, $title);
            }

            // Get the available tracks
            $result = $xoopsDB->query("SELECT tid, title FROM ".$xoopsDB->prefix("myconference_tracks")." ORDER BY Title ASC") or $eh->show("0013");
            $trk_select = new XoopsFormSelect(_MD_TRACKSTITLE, "tid",$tid_v);
            $trk_select->addOption(0, _MD_NONE);
            while (list($tid, $title) = $xoopsDB->fetchRow($result) ) {
                $trk_select->addOption($tid, $title);
            }

            $title = new XoopsFormText(_MD_TITLE, "title", 50, 100, $title_v);
            // $stime = new XoopsFormText(_MD_STIME, "stime", 14, 16, date("Y-m-d H:i", $stime_v));
            $stime = XoopsFormDateTimeI(_MD_STIME, "stime", 10, $stime_v, 30);
            $duration = new XoopsFormText(_MD_DURATION, "duration", 3, 3, $duration_v);
            $fct = new XoopsFormHidden("fct", "updspeech");
            $sid = new XoopsFormHidden("sid", $sid);
            $slides1 = new XoopsFormFile(_MD_SLIDES1, "slides1", 2048000);
            $slides2 = new XoopsFormFile(_MD_SLIDES2, "slides2", 2048000);
            $slides3 = new XoopsFormFile(_MD_SLIDES3, "slides3", 2048000);
            $slides4 = new XoopsFormFile(_MD_SLIDES4, "slides4", 2048000);
            $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
            $abstract->setValue($abstract_v);
            $submit_button = new XoopsFormButton("", "submit", _MD_UPDATE, "submit");

            $form = new XoopsThemeForm(_MD_UPDSPEECH, "editspeechform", "speeches.php");
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement($title, true);
            $form->addElement($stime);
            $form->addElement($cv_select, true);
            $form->addElement($cid_select);
            $form->addElement($trk_select);
            $form->addElement($duration, true);
            $form->addElement($slides1);
            $form->addElement($slides2);
            $form->addElement($slides3);
            $form->addElement($slides4);
            $form->addElement($abstract, true);
            $form->addElement($fct);
            $form->addElement($sid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action == 'del') {
            $sid = trim($HTTP_POST_VARS['sid']) or $eh->show("1001");
            xoops_confirm(array('fct' => 'delspeechok', 'sid' => $sid), 'speeches.php', _MD_DELSPEECH);
            xoops_cp_footer();
        } 
        break;

    case "delspeechok":
        $cvid = trim($HTTP_POST_VARS['sid']) or $eh->show("1001");
        $result = $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myconference_speeches")." WHERE sid=$sid") or $eh->show("0013");
        redirect_header("speeches.php",2,_MD_DBUPDATED);
        break;
    case "addspeech":
        global $HTTP_POST_VARS;
        $eh = new ErrorHandler;
        // strip time and date
        reset($stime);
        $date = strtotime(array_shift($stime));
        $date += array_shift($stime);
        $stime = $date;
        if (isset($HTTP_POST_VARS['xoops_upload_file'][0]) && !empty($HTTP_POST_VARS['slides1'])) {
            $slides1 = $HTTP_POST_VARS['xoops_upload_file'][0];
            $slides1 = getFile($slides1);
        }
        if (isset($HTTP_POST_VARS['xoops_upload_file'][1]) && !empty($HTTP_POST_VARS['slides2'])) {
            $slides2 = $HTTP_POST_VARS['xoops_upload_file'][1];
            $slides2 = getFile($slides2);
        }
        if (isset($HTTP_POST_VARS['xoops_upload_file'][2]) && !empty($HTTP_POST_VARS['slides3'])) {
            $slides3 = $HTTP_POST_VARS['xoops_upload_file'][2];
            $slides3 = getFile($slides3);
        }
        if (isset($HTTP_POST_VARS['xoops_upload_file'][3]) && !empty($HTTP_POST_VARS['slides4'])) {
            $slides4 = $HTTP_POST_VARS['xoops_upload_file'][3];
            $slides4 = getFile($slides4);
        }
        $etime = $stime + $duration * 60;

        $result = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myconference_speeches")." (title,abstract,stime,etime,cvid,cid,tid,slides1,slides2,slides3,slides4,duration) VALUES (\"$title\",\"$abstract\",\"$stime\",\"$etime\", \"$cvid\",\"$cid\",\"$tid\",\"$slides1\",\"$slides2\",\"$slides3\",\"$slides4\",\"$duration\")") or $eh->show("0013");
        if ($result) {
            redirect_header("speeches.php",2,_MD_DBUPDATED);
        }
        break;
    default:
        xoops_cp_header();
        showAdmin();
        // Get available speeches for the Update/Delete form
        $result = $xoopsDB->query("SELECT sid, title FROM ".$xoopsDB->prefix("myconference_speeches")." ORDER BY Title ASC") or $eh->show("0013");
        $speech_select = new XoopsFormSelect(_MD_TITLE, "sid");
        while (list($sid, $title) = $xoopsDB->fetchRow($result) ) {
            $speech_select->addOption($sid, $title);
        }
        $action_select = new XoopsFormSelect(_MD_ACTION, "action");
        $action_select->addOption("upd", _MD_EDIT);
        $action_select->addOption("del", _MD_DELE);
        $fct = new XoopsFormHidden("fct", "editspeech");
        $submit_button = new XoopsFormButton("", "submit", _MD_SUBMIT, "submit");

        $editform = new XoopsThemeForm(_MD_EDITSPEECH, "editspeechform", "speeches.php");
        $editform->setExtra('enctype="multipart/form-data"');
        $editform->addElement($fct);
        $editform->addElement($speech_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $cid_v = "";
        // Get the available CVs
        $result = $xoopsDB->query("SELECT cvid, name FROM ".$xoopsDB->prefix("myconference_cvs")." ORDER BY Name ASC") or $eh->show("0013");
        $cv_select = new XoopsFormSelect(_MD_SPEAKERSNAME, "cvid");
        $cv_select->addOption(0, _MD_NONE);
        while (list($cvid, $name) = $xoopsDB->fetchRow($result) ) {
            $cv_select->addOption($cvid, $name);
        }

        // Get the available congress
        $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY Title ASC") or $eh->show("0013");
        $cid_select = new XoopsFormSelect(_MD_CONFERENCESTITLE, "cid", $cid_v);
        $cid_select->addOption(0, _MD_NONE);
        while (list($cid, $title) = $xoopsDB->fetchRow($result) ) {
            $cid_select->addOption($cid, $title);
        }

        // Get the available tracks
        $result = $xoopsDB->query("SELECT tid, title FROM ".$xoopsDB->prefix("myconference_tracks")." ORDER BY Title ASC") or $eh->show("0013");
        $trk_select = new XoopsFormSelect(_MD_TRACKSTITLE, "tid",0);
        $trk_select->addOption(0, _MD_NONE);
        while (list($tid, $title) = $xoopsDB->fetchRow($result) ) {
            $trk_select->addOption($tid, $title);
        }

        $title = new XoopsFormText(_MD_TITLE, "title", 50, 100);
        $stime = XoopsFormDateTimeI(_MD_STIME, "stime", 10, 0, 30);
        $duration = new XoopsFormText(_MD_DURATION, "duration", 3, 3);
        $fct = new XoopsFormHidden("fct", "addspeech");
        $slides1 = new XoopsFormFile(_MD_SLIDES1, "slides1", 2048000);
        $slides2 = new XoopsFormFile(_MD_SLIDES2, "slides2", 2048000);
        $slides3 = new XoopsFormFile(_MD_SLIDES3, "slides3", 2048000);
        $slides4 = new XoopsFormFile(_MD_SLIDES4, "slides4", 2048000);
        $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
        $submit_button = new XoopsFormButton("", "submit", _MD_UPDATE, "submit");

        $form = new XoopsThemeForm(_MD_ADDSPEECH, "addspeechform", "speeches.php");
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement($title, true);
        $form->addElement($cv_select, true);
        $form->addElement($cid_select);
        $form->addElement($trk_select);
        $form->addElement($stime, true);
        $form->addElement($duration, true);
        $form->addElement($slides1);
        $form->addElement($slides2);
        $form->addElement($slides3);
        $form->addElement($slides4);
        $form->addElement($abstract, true);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}

?>
