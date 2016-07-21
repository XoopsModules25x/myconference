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
    case "updsection":
        $eh = new ErrorHandler;
        $sid = $HTTP_POST_VARS['sid'];
        $title = $HTTP_POST_VARS['title'];
        $abstract = $HTTP_POST_VARS['abstract'];
        $cid = $HTTP_POST_VARS['cid'];
        $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_sections")." SET title='$title', abstract='$abstract', cid='$cid' WHERE sid=$sid") or $eh->show("0013");
        if ($result) {
            redirect_header("sections.php",2,_MD_DBUPDATED);
        }
        break;
    case "editsection":
        xoops_cp_header();
        showAdmin();
        $action = $HTTP_POST_VARS['action'];
        $sid = $HTTP_POST_VARS['sid'];
        if ($action == 'upd') {
            $sid = trim($HTTP_POST_VARS['sid']) or $eh->show("1001");
            $result = $xoopsDB->query("SELECT title, cid, abstract FROM ".$xoopsDB->prefix("myconference_sections")." WHERE sid=$sid") or $eh->show("0013");
            list($title_v, $cid_v, $abstract_v) = $xoopsDB->fetchRow($result);

            $title = new XoopsFormText(_MD_TITLE, "title", 50, 100, $title_v);
            // Get all congresses defined
            $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY title ASC") or $eh->show("0013");
            $conferences_select = new XoopsFormSelect(_MD_CONGRESS, "cid", $cid_v);
            while (list($cid, $ctitle) = $xoopsDB->fetchRow($result) ) {
                $conferences_select->addOption($cid, $ctitle);
            }

            $fct = new XoopsFormHidden("fct", "updsection");
            $sid = new XoopsFormHidden("sid", $sid);
            $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
            $abstract->setValue($abstract_v);
            $submit_button = new XoopsFormButton("", "submit", _MD_UPDATE, "submit");

            $form = new XoopsThemeForm(_MD_UPDSECTION, "editsectionform", "sections.php");
            $form->addElement($title, true);
            $form->addElement($conferences_select, true);
            $form->addElement($abstract);
            $form->addElement($fct);
            $form->addElement($sid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action == 'del') {
            $sid = trim($HTTP_POST_VARS['sid']) or $eh->show("1001");
            xoops_confirm(array('fct' => 'delsectionok', 'sid' => $sid), 'sections.php', _MD_DELSECTION);
            xoops_cp_footer();
        } 
        break;

    case "delsectionok":
        $sid = trim($HTTP_POST_VARS['sid']) or $eh->show("1001");
        $result = $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myconference_sections")." WHERE sid=$sid") or $eh->show("0013");
        redirect_header("sections.php",2,_MD_DBUPDATED);
        break;

    case "addsection":
        global $HTTP_POST_VARS;
        $eh = new ErrorHandler;

        $result = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myconference_sections")." (title,cid,abstract) VALUES (\"$title\",\"$cid\",\"$abstract\")") or $eh->show("0013");
        if ($result) {
            redirect_header("sections.php",2,_MD_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        showAdmin();
        // Get available sections for the Update/Delete form
        $result = $xoopsDB->query("SELECT sid, title FROM ".$xoopsDB->prefix("myconference_sections")." ORDER BY title ASC") or $eh->show("0013");
        $section_select = new XoopsFormSelect(_MD_TITLE, "sid");
        while (list($sid, $title) = $xoopsDB->fetchRow($result) ) {
            $section_select->addOption($sid, $title);
        }
        $action_select = new XoopsFormSelect(_MD_ACTION, "action");
        $action_select->addOption("upd", _MD_EDIT);
        $action_select->addOption("del", _MD_DELE);
        $fct = new XoopsFormHidden("fct", "editsection");
        $submit_button = new XoopsFormButton("", "submit", _MD_SUBMIT, "submit");

        $editform = new XoopsThemeForm(_MD_EDITSECTION, "editsectionform", "sections.php");
        $editform->addElement($fct);
        $editform->addElement($section_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $title = new XoopsFormText(_MD_TITLE, "title", 50, 100);
        $fct = new XoopsFormHidden("fct", "addsection");
        $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
        $submit_button = new XoopsFormButton("", "submit", _MD_ADD, "submit");
        // Get all congresses defined
        $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY title ASC") or $eh->show("0013");
        $conferences_select = new XoopsFormSelect(_MD_CONGRESS, "cid");
        while (list($cid, $ctitle) = $xoopsDB->fetchRow($result) ) {
            $conferences_select->addOption($cid, $ctitle);
        }


        $form = new XoopsThemeForm(_MD_ADDSECTION, "addsectionform", "sections.php");
        $form->addElement($title, true);
        $form->addElement($conferences_select, true);
        $form->addElement($abstract);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}

?>