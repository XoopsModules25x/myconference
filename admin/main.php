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

//if (isset($_POST)) {
//    foreach ($_POST as $k => $v) {
//        echo "k ($k) = v ($v)<br>";
//    }
//}

//if (!isset($fct)) {
//    $fct = '';
//}

$fct = XoopsRequest::getString('fct', XoopsRequest::getString('fct', '', 'GET'), 'POST');

switch ($fct) {
    case 'updconference':
        $eh          = new ErrorHandler;
        $cid         = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        $isdefault   = XoopsRequest::getInt('isdefault', 0, 'POST');//$_POST['isdefault'];
        $title       = XoopsRequest::getString('title', '', 'POST');//$_POST['title'];
        $subtitle    = XoopsRequest::getString('subtitle', '', 'POST');//$_POST['subtitle'];
        $subsubtitle = XoopsRequest::getString('subsubtitle', '', 'POST');//$_POST['subsubtitle'];
        $sdate       = XoopsRequest::getString('sdate', '', 'POST');//$_POST['sdate'];
        $edate       = XoopsRequest::getString('edate', '', 'POST');//$_POST['edate'];
        $summary     = XoopsRequest::getString('summary', '', 'POST');//$_POST['summary'];
        if ($isdefault) {
            // Since this is our default congress, we will update all the other congresses out there
            $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_main') . ' SET isdefault=0');// or $eh::show('0013');
        }
        $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_main') . " SET isdefault='$isdefault', title='$title', subtitle='$subtitle', subsubtitle='$subsubtitle', sdate='$sdate', edate='$edate', summary='$summary' WHERE cid=$cid");// or $eh::show('0013');
        if ($result) {
            redirect_header('main.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    case 'editconference':
        xoops_cp_header();

        $action = $action = XoopsRequest::getString('action', 0, 'POST');//$_POST['action'];
        $cid    = XoopsRequest::getInt('cid', 0, 'POST');//$_POST['cid'];
        if ($action === 'upd') {
            $cid = XoopsRequest::getInt('cid', 0, 'POST');//trim($_POST['cid']) or $eh::show('1001');
            $result = $xoopsDB->query('SELECT isdefault,title,subtitle,subsubtitle,sdate,edate,summary FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid");// or $eh::show('0013');
            list($isdefault_v, $title_v, $subtitle_v, $subsubtitle_v, $sdate_v, $edate_v, $summary_v) = $xoopsDB->fetchRow($result);

            $title       = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 200, $title_v);
            $isdefault   = new XoopsFormRadioYN(_AM_MYCONFERENCE_ISDEFAULT, 'isdefault', $isdefault_v);
            $subtitle    = new XoopsFormText(_AM_MYCONFERENCE_SUBTITLE, 'subtitle', 50, 200, $subtitle_v);
            $subsubtitle = new XoopsFormText(_AM_MYCONFERENCE_SUBSUBTITLE, 'subsubtitle', 50, 200, $subsubtitle_v);
            $sdate       = new XoopsFormTextDateSelect(_AM_MYCONFERENCE_SDATE, 'sdate', 10, strtotime($sdate_v));
            $edate       = new XoopsFormTextDateSelect(_AM_MYCONFERENCE_SDATE, 'edate', 10, strtotime($edate_v));

            $fct     = new XoopsFormHidden('fct', 'updconference');
            $cid     = new XoopsFormHidden('cid', $cid);
            $summary = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
            $summary->setValue($summary_v);
            $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

            $form = new XoopsThemeForm(_AM_MYCONFERENCE_UPDCONFERENCE, '', 'main.php');
            $form->addElement($title, true);
            $form->addElement($isdefault);
            $form->addElement($subtitle);
            $form->addElement($subsubtitle);
            $form->addElement($sdate);
            $form->addElement($edate);
            $form->addElement($summary);
            $form->addElement($fct);
            $form->addElement($cid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action === 'del') {
            $cid = XoopsRequest::getInt('cid', 0, 'POST');//trim($_POST['cid']) or $eh::show('1001');
            xoops_confirm(array('fct' => 'delconferenceok', 'cid' => $cid), 'main.php', _AM_MYCONFERENCE_DELCONFERENCE);
            xoops_cp_footer();
        }
        break;

    case 'delconferenceok':
        $cid = XoopsRequest::getInt('cid', 0, 'POST');//trim($_POST['cid']) or $eh::show('1001');
        $result = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid");// or $eh::show('0013');
        redirect_header('main.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        break;

    case 'addconference':
        //global $_POST;
        $eh = new ErrorHandler;

        $sdate       = XoopsRequest::getString('sdate', '', 'POST');//$_POST['sdate'];
        $edate       = XoopsRequest::getString('edate', '', 'POST');//$_POST['edate'];
        $isdefault   = XoopsRequest::getInt('isdefault', 0, 'POST');//$_POST['isdefault'];
        $title       = $myts->stripslashesGPC(trim(XoopsRequest::getString('title', '', 'POST')));//$_POST['title']));
        $subtitle    = $myts->stripslashesGPC(trim(XoopsRequest::getString('subtitle', '', 'POST')));//$_POST['subtitle']));
        $subsubtitle = $myts->stripslashesGPC(trim(XoopsRequest::getString('subsubtitle', '', 'POST')));//$_POST['subsubtitle']));
        $summary     = $myts->stripslashesGPC(trim(XoopsRequest::getString('summary', '', 'POST')));//$_POST['summary']));

        if ($isdefault) {
            // Since this guy is our default congress, will update all the other congresses out there
            $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_main') . ' SET isdefault=0');// or $eh::show('0013');
        }
        $result = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('myconference_main') . " (isdefault,title,subtitle,subsubtitle,sdate,edate,summary) VALUES ('$isdefault','$title','$subtitle','$subsubtitle','$sdate','$edate','$summary')");// or $eh::show('0013');
        if ($result) {
            redirect_header('main.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();

        // Get available conference for the Update/Delete form
        $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY title ASC');// or $eh::show('0013');
        $conference_select = new XoopsFormSelect(_AM_MYCONFERENCE_TITLE, 'cid');
        while (list($cid, $title) = $xoopsDB->fetchRow($result)) {
            $conference_select->addOption($cid, $title);
        }
        $action_select = new XoopsFormSelect(_AM_MYCONFERENCE_ACTION, 'action');
        $action_select->addOption('upd', _AM_MYCONFERENCE_EDIT);
        $action_select->addOption('del', _AM_MYCONFERENCE_DELE);
        $fct           = new XoopsFormHidden('fct', 'editconference');
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $editform = new XoopsThemeForm(_AM_MYCONFERENCE_EDITCONFERENCE, '', 'main.php');
        $editform->addElement($fct);
        $editform->addElement($conference_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $title         = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 200);
        $isdefault     = new XoopsFormRadioYN(_AM_MYCONFERENCE_ISDEFAULT, 'isdefault', 0);
        $subtitle      = new XoopsFormText(_AM_MYCONFERENCE_SUBTITLE, 'subtitle', 50, 200);
        $subsubtitle   = new XoopsFormText(_AM_MYCONFERENCE_SUBSUBTITLE, 'subsubtitle', 50, 200);
        $sdate         = new XoopsFormTextDateSelect(_AM_MYCONFERENCE_SDATE, 'sdate', 10, time());
        $edate         = new XoopsFormTextDateSelect(_AM_MYCONFERENCE_EDATE, 'edate', 10, time());
        $fct           = new XoopsFormHidden('fct', 'addconference');
        $cid           = new XoopsFormHidden('cid', $cid);
        $summary       = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_ADD, 'submit');

        $form = new XoopsThemeForm(_AM_MYCONFERENCE_ADDCONFERENCE, '', 'main.php');
        $form->addElement($title, true);
        $form->addElement($isdefault);
        $form->addElement($subtitle);
        $form->addElement($subsubtitle);
        $form->addElement($sdate);
        $form->addElement($edate);
        $form->addElement($summary);
        $form->addElement($fct);
        $form->addElement($cid);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}
