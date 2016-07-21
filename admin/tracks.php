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
    case "updtrack":
        $eh = new ErrorHandler;
        $tid = $HTTP_POST_VARS['tid'];
        $cid = $HTTP_POST_VARS['cid'];
        $title = $HTTP_POST_VARS['title'];
        $abstract = $HTTP_POST_VARS['abstract'];
        $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_tracks")." SET title='$title', abstract='$abstract', cid='$cid' WHERE tid=$tid") or $eh->show("0013");
        if ($result) {
            redirect_header("tracks.php",2,_MD_DBUPDATED);
        }
        break;
    case "edittrack":
        xoops_cp_header();
        showAdmin();
        $action = $HTTP_POST_VARS['action'];
        $tid = $HTTP_POST_VARS['tid'];
        $cid = $HTTP_POST_VARS['cid'];
        if ($action == 'upd') {
            $tid = trim($HTTP_POST_VARS['tid']) or $eh->show("1001");
            $result = $xoopsDB->query("SELECT cid,title,abstract FROM ".$xoopsDB->prefix("myconference_tracks")." WHERE tid=$tid") or $eh->show("0013");
            list($cid_v, $title_v, $abstract_v) = $xoopsDB->fetchRow($result);


            // Get the available congress
            $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY Title ASC") or $eh->show("0013");
            $cid_select = new XoopsFormSelect(_MD_CONFERENCESTITLE, "cid",$cid_v);
            $cid_select->addOption(0, _MD_NONE);
            while (list($cid, $title) = $xoopsDB->fetchRow($result) ) {
                $cid_select->addOption($cid, $title);
            }

            $title = new XoopsFormText(_MD_TITLE, "title", 50, 100, $title_v);
            $fct = new XoopsFormHidden("fct", "updtrack");
            $tid = new XoopsFormHidden("tid", $tid);
            $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
            $abstract->setValue($abstract_v);
            $submit_button = new XoopsFormButton("", "submit", _MD_UPDATE, "submit");

            $form = new XoopsThemeForm(_MD_UPDTRACK, "edittrackform", "tracks.php");
            $form->addElement($title, true);
            $form->addElement($cid_select);
            $form->addElement($abstract);
            $form->addElement($fct);
            $form->addElement($tid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action == 'del') {
            $tid = trim($HTTP_POST_VARS['tid']) or $eh->show("1001");
            xoops_confirm(array('fct' => 'deltrackok', 'tid' => $tid), 'tracks.php', _MD_DELTRACK);
            xoops_cp_footer();
        } 
        break;

    case "deltrackok":
        $tid = trim($HTTP_POST_VARS['tid']) or $eh->show("1001");
        $result = $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myconference_tracks")." WHERE tid=$tid") or $eh->show("0013");
        redirect_header("tracks.php",2,_MD_DBUPDATED);
        break;

    case "addtrack":
        global $HTTP_POST_VARS;
        $eh = new ErrorHandler;

        $result = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myconference_tracks")." (cid,title,abstract) VALUES (\"$cid\",\"$title\",\"$abstract\")") or $eh->show("0013");
        if ($result) {
            redirect_header("tracks.php",2,_MD_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        showAdmin();
        // Get available tracks for the Update/Delete form
        $result = $xoopsDB->query("SELECT tid, title FROM ".$xoopsDB->prefix("myconference_tracks")." ORDER BY title ASC") or $eh->show("0013");
        $track_select = new XoopsFormSelect(_MD_TITLE, "tid");
        while (list($tid, $title) = $xoopsDB->fetchRow($result) ) {
            $track_select->addOption($tid, $title);
        }
        $action_select = new XoopsFormSelect(_MD_ACTION, "action");
        $action_select->addOption("upd", _MD_EDIT);
        $action_select->addOption("del", _MD_DELE);
        $fct = new XoopsFormHidden("fct", "edittrack");
        $submit_button = new XoopsFormButton("", "submit", _MD_SUBMIT, "submit");

        $editform = new XoopsThemeForm(_MD_EDITTRACK, "edittrackform", "tracks.php");
        $editform->addElement($fct);
        $editform->addElement($track_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $title = new XoopsFormText(_MD_TITLE, "title", 50, 100);
        $fct = new XoopsFormHidden("fct", "addtrack");
        $abstract = new XoopsFormTextArea(_MD_ABSTRACT, "abstract", "", 25, 100);
        $submit_button = new XoopsFormButton("", "submit", _MD_ADD, "submit");

        // Get the available congress
        $result = $xoopsDB->query("SELECT cid, title FROM ".$xoopsDB->prefix("myconference_main")." ORDER BY Title ASC") or $eh->show("0013");
        $cid_select = new XoopsFormSelect(_MD_CONFERENCESTITLE, "cid",$cid_v);
        $cid_select->addOption(0, _MD_NONE);
        while (list($cid, $cid_title) = $xoopsDB->fetchRow($result) ) {
            $cid_select->addOption($cid, $cid_title);
        }

        $form = new XoopsThemeForm(_MD_ADDTRACK, "addtrackform", "tracks.php");
        $form->addElement($title, true);
        $form->addElement($cid_select);
        $form->addElement($abstract);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}

?>
