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

$myts = MyTextSanitizer::getInstance();

$eh = new ErrorHandler;

//if (isset($_POST['fct'])) {
//    $fct = trim($_POST['fct']);
//}
//if (isset($_GET['fct'])) {
//    $fct = trim($_GET['fct']);
//}

$fct = XoopsRequest::getString('fct', XoopsRequest::getString('fct', '', 'GET'), 'POST');

//if (isset($_POST)) {
//    foreach ($_POST as $k => $v) {
//        echo "k ($k) = v ($v)<br>";
//    }
//}

//if (!isset($fct)) {
//    $fct = '';
//}
switch ($fct) {
    case 'updtrack':
        $eh      = new ErrorHandler;
        $tid     = XoopsRequest::getInt('tid', 0, 'POST');//$_POST['tid'];
        $cid     = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        $title   = XoopsRequest::getString('title', '', 'POST');//$_POST['title'];
        $summary = XoopsRequest::getText('summary', '', 'POST');//$_POST['summary'];
        $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_tracks') . " SET title='$title', summary='$summary', cid='$cid' WHERE tid=$tid");// or $eh::show('0013');
        if ($result) {
            redirect_header('tracks.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    case 'edittrack':
        xoops_cp_header();

        $action = $action = XoopsRequest::getString('action', 0, 'POST');//$_POST['action'];
        $tid    = $tid     = XoopsRequest::getInt('tid', 0, 'POST');//$_POST['tid'];
        $cid    = $cid     = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        if ($action === 'upd') {
            $tid = XoopsRequest::getInt('tid', 0, 'POST');//trim($_POST['tid']) or $eh::show('1001');
            $result = $xoopsDB->query('SELECT cid,title,summary FROM ' . $xoopsDB->prefix('myconference_tracks') . " WHERE tid=$tid");// or $eh::show('0013');
            list($cid_v, $title_v, $summary_v) = $xoopsDB->fetchRow($result);

            // Get the available congress
            $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY Title ASC');// or $eh::show('0013');
            $cid_select = new XoopsFormSelect(_AM_MYCONFERENCE_CONFERENCESTITLE, 'cid', $cid_v);
            $cid_select->addOption(0, _AM_MYCONFERENCE_NONE);
            while (list($cid, $title) = $xoopsDB->fetchRow($result)) {
                $cid_select->addOption($cid, $title);
            }

            $title   = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 100, $title_v);
            $fct     = new XoopsFormHidden('fct', 'updtrack');
            $tid     = new XoopsFormHidden('tid', $tid);
            $summary = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
            $summary->setValue($summary_v);
            $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

            $form = new XoopsThemeForm(_AM_MYCONFERENCE_UPDTRACK, 'edittrackform', 'tracks.php');
            $form->addElement($title, true);
            $form->addElement($cid_select);
            $form->addElement($summary);
            $form->addElement($fct);
            $form->addElement($tid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action === 'del') {
            $tid = XoopsRequest::getInt('tid', 0, 'POST');//trim($_POST['tid']) or $eh::show('1001');
            xoops_confirm(array('fct' => 'deltrackok', 'tid' => $tid), 'tracks.php', _AM_MYCONFERENCE_DELTRACK);
            xoops_cp_footer();
        }
        break;

    case 'deltrackok':
        $tid = XoopsRequest::getInt('tid', 0, 'POST');//trim($_POST['tid']) or $eh::show('1001');
        $result = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('myconference_tracks') . " WHERE tid=$tid");// or $eh::show('0013');
        redirect_header('tracks.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        break;

    case 'addtrack':
        $cid     = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        $title   = $myts->stripslashesGPC(trim(XoopsRequest::getString('title', '', 'POST')));//$_POST['title']));
        $summary = $myts->stripslashesGPC(trim(XoopsRequest::getString('summary', '', 'POST')));//$_POST['summary']));

        $eh = new ErrorHandler;

        $result = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('myconference_tracks') . " (cid, title, summary) VALUES ('$cid', '$title', '$summary')");// or $eh::show('0013');

        if ($result) {
            redirect_header('tracks.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        // Get available tracks for the Update/Delete form
        $result = $xoopsDB->query('SELECT tid, title FROM ' . $xoopsDB->prefix('myconference_tracks') . ' ORDER BY title ASC');// or $eh::show('0013');
        $track_select = new XoopsFormSelect(_AM_MYCONFERENCE_TITLE, 'tid');
        while (list($tid, $title) = $xoopsDB->fetchRow($result)) {
            $track_select->addOption($tid, $title);
        }
        $action_select = new XoopsFormSelect(_AM_MYCONFERENCE_ACTION, 'action');
        $action_select->addOption('upd', _AM_MYCONFERENCE_EDIT);
        $action_select->addOption('del', _AM_MYCONFERENCE_DELE);
        $fct           = new XoopsFormHidden('fct', 'edittrack');
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $editform = new XoopsThemeForm(_AM_MYCONFERENCE_EDITTRACK, 'edittrackform', 'tracks.php');
        $editform->addElement($fct);
        $editform->addElement($track_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $title         = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 100);
        $fct           = new XoopsFormHidden('fct', 'addtrack');
        $summary       = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_ADD, 'submit');

        // Get the available congress
        $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY Title ASC');// or $eh::show('0013');
        $cid_select = new XoopsFormSelect(_AM_MYCONFERENCE_CONFERENCESTITLE, 'cid', $cid_v);
        $cid_select->addOption(0, _AM_MYCONFERENCE_NONE);
        while (list($cid, $cid_title) = $xoopsDB->fetchRow($result)) {
            $cid_select->addOption($cid, $cid_title);
        }

        $form = new XoopsThemeForm(_AM_MYCONFERENCE_ADDTRACK, 'addtrackform', 'tracks.php');
        $form->addElement($title, true);
        $form->addElement($cid_select);
        $form->addElement($summary);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}
