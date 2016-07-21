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

if (isset($_POST['fct'])) {
    $fct = trim($_POST['fct']);
}
if (isset($_GET['fct'])) {
    $fct = trim($_GET['fct']);
}

//if (isset($_POST)) {
//    foreach ($_POST as $k => $v) {
//        echo "k ($k) = v ($v)<br>";
//    }
//}

if (!isset($fct)) {
    $fct = '';
}
switch ($fct) {
    case 'updsection':
        $eh      = new ErrorHandler;
        $sid     = $_POST['sid'];
        $title   = $_POST['title'];
        $summary = $_POST['summary'];
        $cid     = $_POST['cid'];
        $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_sections') . " SET title='$title', summary='$summary', cid='$cid' WHERE sid=$sid") OR $eh::show('0013');
        if ($result) {
            redirect_header('sections.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    case 'editsection':
        xoops_cp_header();

        $action = $_POST['action'];
        $sid    = $_POST['sid'];
        if ($action === 'upd') {
            $sid = trim($_POST['sid']) OR $eh::show('1001');
            $result = $xoopsDB->query('SELECT title, cid, summary FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE sid=$sid") OR $eh::show('0013');
            list($title_v, $cid_v, $summary_v) = $xoopsDB->fetchRow($result);

            $title = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 100, $title_v);
            // Get all congresses defined
            $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY title ASC') OR $eh::show('0013');
            $conferences_select = new XoopsFormSelect(_AM_MYCONFERENCE_CONGRESS, 'cid', $cid_v);
            while (list($cid, $ctitle) = $xoopsDB->fetchRow($result)) {
                $conferences_select->addOption($cid, $ctitle);
            }

            $fct     = new XoopsFormHidden('fct', 'updsection');
            $sid     = new XoopsFormHidden('sid', $sid);
            $summary = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
            $summary->setValue($summary_v);
            $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

            $form = new XoopsThemeForm(_AM_MYCONFERENCE_UPDSECTION, 'editsectionform', 'sections.php');
            $form->addElement($title, true);
            $form->addElement($conferences_select, true);
            $form->addElement($summary);
            $form->addElement($fct);
            $form->addElement($sid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action === 'del') {
            $sid = trim($_POST['sid']) OR $eh::show('1001');
            xoops_confirm(array('fct' => 'delsectionok', 'sid' => $sid), 'sections.php', _AM_MYCONFERENCE_DELSECTION);
            xoops_cp_footer();
        }
        break;

    case 'delsectionok':
        $sid = trim($_POST['sid']) OR $eh::show('1001');
        $result = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE sid=$sid") OR $eh::show('0013');
        redirect_header('sections.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        break;

    case 'addsection':

        $cid     = (int)$_POST['cid'];
        $title   = $myts->stripslashesGPC(trim($_POST['title']));
        $summary = $myts->stripslashesGPC(trim($_POST['summary']));

        $eh = new ErrorHandler;

        $result = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('myconference_sections') . " (cid, title, summary) VALUES ('$cid', '$title', '$summary')") OR $eh::show('0013');

        if ($result) {
            redirect_header('sections.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();

        // Get available sections for the Update/Delete form
        $result = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_sections') . ' ORDER BY title ASC') OR $eh::show('0013');
        $section_select = new XoopsFormSelect(_AM_MYCONFERENCE_TITLE, 'sid');
        while (list($sid, $title) = $xoopsDB->fetchRow($result)) {
            $section_select->addOption($sid, $title);
        }
        //set two actions: Edit and Delete
        $action_select = new XoopsFormSelect(_AM_MYCONFERENCE_ACTION, 'action');
        $action_select->addOption('upd', _AM_MYCONFERENCE_EDIT);
        $action_select->addOption('del', _AM_MYCONFERENCE_DELE);
        $fct           = new XoopsFormHidden('fct', 'editsection');
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        // if editing
        $editform = new XoopsThemeForm(_AM_MYCONFERENCE_EDITSECTION, 'editsectionform', 'sections.php');
        $editform->addElement($fct);
        $editform->addElement($section_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display(); //if editing, this is the end

        //----------------- add new -----------------

        $title         = new XoopsFormText(_AM_MYCONFERENCE_TITLE, 'title', 50, 100);
        $fct           = new XoopsFormHidden('fct', 'addsection');
        $summary       = new XoopsFormTextArea(_AM_MYCONFERENCE_SUMMARY, 'summary', '', 25, 100);
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_ADD, 'submit');
        // Get all congresses defined
        $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('myconference_main') . ' ORDER BY title ASC') OR $eh::show('0013');
        $conferences_select = new XoopsFormSelect(_AM_MYCONFERENCE_CONGRESS, 'cid');
        while (list($cid, $ctitle) = $xoopsDB->fetchRow($result)) {
            $conferences_select->addOption($cid, $ctitle);
        }

        $form = new XoopsThemeForm(_AM_MYCONFERENCE_ADDSECTION, 'addsectionform', 'sections.php');
        $form->addElement($title, true);
        $form->addElement($conferences_select, true);
        $form->addElement($summary);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}
