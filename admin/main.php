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

//if (isset($HTTP_POST_VARS)) {
//    foreach ( $HTTP_POST_VARS as $k => $v ) {
//        echo "k ($k) = v ($v)<br>";
//    }
//}

if (!isset($fct)){
    $fct = '';
}
switch ( $fct ) {
    case "updconference":
        $eh = new ErrorHandler;
        $cid = $HTTP_POST_VARS['cid'];
        $isdefault = $HTTP_POST_VARS['isdefault'];
        $title = $HTTP_POST_VARS['title'];
        $subtitle = $HTTP_POST_VARS['subtitle'];
        $subsubtitle = $HTTP_POST_VARS['subsubtitle'];
        $sdate = $HTTP_POST_VARS['sdate'];
        $edate = $HTTP_POST_VARS['edate'];
        $abstract = $HTTP_POST_VARS['abstract'];
        if ($isdefault) {
            // Since this guy is our default congress, will update all the other congresses out there
            $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_main")." SET isdefault=0") or $eh->show("0013");
        }
        $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_main")." SET isdefault='$isdefault', title='$title', subtitle='$subtitle', subsubtitle='$subsubtitle', sdate='$sdate', edate='$edate', abstract='$abstract' WHERE cid=$cid") or $eh->show("0013");
        if ($result) {
            redirect_header("main.php",2,_MD_DBUPDATED);
        }
        break;
    case "editconference":
        xoops_cp_header();
        showAdmin();
        $action = $HTTP_POST_VARS['action'];
        $cid = $HTTP_POST_VARS['cid'];
        if ($action == 'upd') {
            $cid = trim($HTTP_POST_VARS['cid']) or $eh->show("1001");
            $result = $xoopsDB->query("SELECT isdefault,title,subtitle,subsubtitle,sdate,edate,abstract FROM ".$xoopsDB->prefix("myconference_main")." WHERE cid=$cid") or $eh->show("0013");
            list($isdefault_v, $title_v, $subtitle_v, $subsubtitle_v, $sdate_v, $edate_v, $abstract_v) = $xoopsDB->fetchRow($result);

            $title = new XoopsFormText(_MD_TITLE, "title", 50, 200, $title_v);
            $isdefault = new XoopsFormRadioYN(_MD_ISDEFAULT, "isdefault", $isdefault_v);
            $subtitle = new XoopsFormText(_MD_SUBTITLE, "subtitle", 50, 200, $subtitle_v);
            $subsubtitle = new XoopsFormText(_MD_SUBSUBTITLE, "subsubtitle", 50, 200, $subsubtitle_v);
            $sdate = new XoopsFormText(_MD_SDATE, "sdate", 10, 10, $sdate_v);
            $edate = new XoopsFormText(_MD_EDATE, "edate", 10, 10, $edate_v);
            $fct = new XoopsFormHidden("fct", "updconference");
            $cid = new XoopsFormHidden("cid", $cid);
            $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
            $abstract->setValue($abstract_v);
            $submit_button = new XoopsFormButton("", "submit", _MD_UPDATE, "submit");

            $form = new XoopsThemeForm(_MD_UPDCONFERENCE, "", "main.php");
            $form->addElement($title, true);
            $form->addElement($isdefault);
            $form->addElement($subtitle);
            $form->addElement($subsubtitle);
            $form->addElement($sdate);
            $form->addElement($edate);
            $form->addElement($abstract);
            $form->addElement($fct);
            $form->addElement($cid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action == 'del') {
            $cid = trim($HTTP_POST_VARS['cid']) or $eh->show("1001");
            xoops_confirm(array('fct' => 'delconferenceok', 'cid' => $cid), 'main.php', _MD_DELCONFERENCE);
            xoops_cp_footer();
        } 
        break;

    case "delconferenceok":
        $cid = trim($HTTP_POST_VARS['cid']) or $eh->show("1001");
        $result = $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myconference_main")." WHERE cid=$cid") or $eh->show("0013");
        redirect_header("main.php",2,_MD_DBUPDATED);
        break;

    case "addconference":
        global $HTTP_POST_VARS;
        $eh = new ErrorHandler;
        $sdate = array_shift($sdate);
        $edate = array_shift($edate);

        if ($isdefault) {
            // Since this guy is our default congress, will update all the other congresses out there
            $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_main")." SET isdefault=0") or $eh->show("0013");
        }
        $result = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myconference_main")." (isdefault,title,subtitle,subsubtitle,sdate,edate,abstract) VALUES (\"$isdefault\",\"$title\",\"$subtitle\",\"$subsubtitle\",\"$sdate\",\"$edate\",\"$abstract\")") or $eh->show("0013");
        if ($result) {
            redirect_header("main.php",2,_MD_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        showAdmin();
        // Get available conference for the Update/Delete form
        $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY title ASC") or $eh->show("0013");
        $conference_select = new XoopsFormSelect(_MD_TITLE, "cid");
        while (list($cid, $title) = $xoopsDB->fetchRow($result) ) {
            $conference_select->addOption($cid, $title);
        }
        $action_select = new XoopsFormSelect(_MD_ACTION, "action");
        $action_select->addOption("upd", _MD_EDIT);
        $action_select->addOption("del", _MD_DELE);
        $fct = new XoopsFormHidden("fct", "editconference");
        $submit_button = new XoopsFormButton("", "submit", _MD_SUBMIT, "submit");

        $editform = new XoopsThemeForm(_MD_EDITCONFERENCE, "", "main.php");
        $editform->addElement($fct);
        $editform->addElement($conference_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $title = new XoopsFormText(_MD_TITLE, "title", 50, 200);
        $isdefault = new XoopsFormRadioYN(_MD_ISDEFAULT, "isdefault", 0);
        $subtitle = new XoopsFormText(_MD_SUBTITLE, "subtitle", 50, 200);
        $subsubtitle = new XoopsFormText(_MD_SUBSUBTITLE, "subsubtitle", 50, 200);
        $sdate = new XoopsFormDateTime(_MD_SDATE, "sdate", 10);
        $edate = new XoopsFormDateTime(_MD_EDATE, "edate", 10);
        $fct = new XoopsFormHidden("fct", "addconference");
        $cid = new XoopsFormHidden("cid", $cid);
        $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
        $submit_button = new XoopsFormButton("", "submit", _MD_ADD, "submit");

        $form = new XoopsThemeForm(_MD_ADDCONFERENCE, "", "main.php");
        $form->addElement($title, true);
        $form->addElement($isdefault);
        $form->addElement($subtitle);
        $form->addElement($subsubtitle);
        $form->addElement($sdate);
        $form->addElement($edate);
        $form->addElement($abstract);
        $form->addElement($fct);
        $form->addElement($cid);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}

?>
