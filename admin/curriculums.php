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

if (!empty($HTTP_POST_VARS['fct'])) {
	$fct = trim($HTTP_POST_VARS['fct']);
} else {
    $fct = "nada";
} 
//if (isset($HTTP_POST_VARS)) {
//    foreach ( $HTTP_POST_VARS as $k => $v ) {
//        echo "k ($k) = v ($v)<br>";
//        if (is_array($v)) {
//            foreach ( $v as $j => $w ) {
//                echo "j ($j) = w ($w)<br>";
//            }
//        }
//    }
//}
//
//if (isset($HTTP_POST_FILES)) {
//    foreach ( $HTTP_POST_FILES as $k => $v ) {
//        echo "<b>k ($k) = v ($v)</b><br>";
//        if (is_array($v)) {
//            foreach ( $v as $j => $w ) {
//                echo "<b>j ($j) = w ($w)</b><br>";
//            }
//        }
//    }
//}

$eh = new ErrorHandler; 

switch ( $fct ) {
    case "updcv":
        $eh = new ErrorHandler;
        $name = $HTTP_POST_VARS['speakerName'];
        $email = $HTTP_POST_VARS['speakerEmail'];
        $descrip = $HTTP_POST_VARS['speakerMiniCV'];
        $company = $HTTP_POST_VARS['speakerCompanyName'];
        $location = $HTTP_POST_VARS['speakerCompanyLocation'];
        $url = $HTTP_POST_VARS['speakerSite'];
        $photo = $HTTP_POST_VARS['xoops_upload_file'][0];
        if (isset($HTTP_POST_FILES['speakersPhoto'])) {
            $photo = getFile($photo);
        }
        $cvid = $HTTP_POST_VARS['cvid'];
        $result = $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myconference_cvs")." SET name='$name', email='$email', descrip='$descrip', location='$location', company='$company', photo='$photo', url='$url' WHERE cvid=$cvid") or $eh->show("0013");
        if ($result) {
            redirect_header("curriculums.php",2,_MD_DBUPDATED);
        }
        break;
    case "delcv":
        $eh = new ErrorHandler;
        break;
    case "editcv":
        xoops_cp_header();
        showAdmin();
        $action = $HTTP_POST_VARS['action'];
        if ($action == 'upd') {
            $cvid = trim($HTTP_POST_VARS['cvid']) or $eh->show("1001");
            $result = $xoopsDB->query("SELECT name,email,company,location,url,photo,descrip FROM ".$xoopsDB->prefix("myconference_cvs")." WHERE cvid=$cvid") or $eh->show("0013");
            list($name_v, $email_v, $company_v, $location_v, $url_v, $photo_v, $minicv_v) = $xoopsDB->fetchRow($result);

            $name = new XoopsFormText(_MD_NAME, "speakerName", 50, 100, $name_v);
            $email = new XoopsFormText(_MD_EMAIL, "speakerEmail", 50, 100, $email_v);
            $company = new XoopsFormText(_MD_COMPANY, "speakerCompanyName", 50, 100, $company_v);
            $location = new XoopsFormText(_MD_LOCATION, "speakerCompanyLocation", 50, 100, $location_v);
            $url = new XoopsFormText(_MD_URL, "speakerSite", 50, 100, $url_v);
            $fct = new XoopsFormHidden("fct", "updcv");
            $cvid = new XoopsFormHidden("cvid", $cvid);
            $photo = new XoopsFormFile('', "speakersPhoto", 50000);
            $photo_label = new XoopsFormLabel('', '<img src="'.XOOPS_UPLOAD_URL.'/'.$photo_v.'" alt="" valign="top" align="right" />');
            $photo_tray = new XoopsFormElementTray(_MD_PHOTO_WARN, '&nbsp;');
            $photo_tray->addElement($photo);
            $photo_tray->addElement($photo_label);

            $minicv = new XoopsFormTextArea(_MD_MINICV, "speakerMiniCV", "", 25, 100);
            $minicv->setValue($minicv_v);
            $submit_button = new XoopsFormButton("", "submit", _MD_UPDATE, "submit");

            $form = new XoopsThemeForm(_MD_UPDCV, "editcvform", "curriculums.php");
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement($name, true);
            $form->addElement($email);
            $form->addElement($url);
            $form->addElement($photo_tray);
            $form->addElement($company);
            $form->addElement($location);
            $form->addElement($minicv, true);
            $form->addElement($fct);
            $form->addElement($cvid);
            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action == 'del') {
            $cvid = trim($HTTP_POST_VARS['cvid']) or $eh->show("1001");
            xoops_confirm(array('fct' => 'delcvok', 'cvid' => $cvid), 'curriculums.php', _MD_DELCV);
            xoops_cp_footer();
        } 
        break;

    case "delcvok":
        $cvid = trim($HTTP_POST_VARS['cvid']) or $eh->show("1001");
        $result = $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myconference_cvs")." WHERE cvid=$cvid") or $eh->show("0013");
        redirect_header("curriculums.php",2,_MD_DBUPDATED);
        break;
    case "addcv":
        $name = $HTTP_POST_VARS['speakerName'];
        $email = $HTTP_POST_VARS['speakerEmail'];
        $descrip = $HTTP_POST_VARS['speakerMiniCV'];
        $company = $HTTP_POST_VARS['speakerCompanyName'];
        $location = $HTTP_POST_VARS['speakerCompanyLocation'];
        $url = $HTTP_POST_VARS['speakerSite'];
        $file = $HTTP_POST_VARS['xoops_upload_file'][0];
        echo "file ($file)";
        if (!empty($HTTP_POST_FILES['speakersPhoto']['name'])) {
            $photo = getFile($file);
        }
        $result = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myconference_cvs")." (name,email,descrip,location,company,photo,url) VALUES (\"$name\",\"$email\",\"$descrip\",\"$location\",\"$company\",\"$photo\",\"$url\")") or $eh->show("0013");
        if ($result) {
            redirect_header("curriculums.php",2,_MD_DBUPDATED);
        }
        break;
    default:
        xoops_cp_header();
        showAdmin();
        $result = $xoopsDB->query("SELECT cvid, name FROM ".$xoopsDB->prefix("myconference_cvs")." ORDER BY Name ASC") or $eh->show("0013");
        $cv_select = new XoopsFormSelect(_MD_NAME, "cvid");
        while (list($cvid, $name) = $xoopsDB->fetchRow($result) ) {
            $cv_select->addOption($cvid, $name);
        }
        $action_select = new XoopsFormSelect(_MD_ACTION, "action");
        $action_select->addOption("upd", _MD_EDIT);
        $action_select->addOption("del", _MD_DELE);
        $fct = new XoopsFormHidden("fct", "editcv");
        $submit_button = new XoopsFormButton("", "submit", _MD_SUBMIT, "submit");

        $editform = new XoopsThemeForm(_MD_EDITCV, "editcvform", "curriculums.php");
        $editform->setExtra('enctype="multipart/form-data"');
        $editform->addElement($fct);
        $editform->addElement($cv_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $name = new XoopsFormText(_MD_NAME, "speakerName", 50, 100);
        $email = new XoopsFormText(_MD_EMAIL, "speakerEmail", 50, 100);
        $company = new XoopsFormText(_MD_COMPANY, "speakerCompanyName", 50, 100);
        $location = new XoopsFormText(_MD_LOCATION, "speakerCompanyLocation", 50, 100);
        $url = new XoopsFormText(_MD_URL, "speakerSite", 50, 100);
        $fct = new XoopsFormHidden("fct", "addcv");
        $photo = new XoopsFormFile(_MD_PHOTO, "speakersPhoto", 50000);
        $minicv = new XoopsFormTextArea(_MD_MINICV, "speakerMiniCV", "", 25, 100);
        $submit_button = new XoopsFormButton("", "submit", _MD_SUBMIT, "submit");

        $form = new XoopsThemeForm(_MD_ADDCV, "addcvform", "curriculums.php");
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement($name, true);
        $form->addElement($email);
        $form->addElement($url);
        $form->addElement($photo);
        $form->addElement($company);
        $form->addElement($location);
        $form->addElement($minicv, true);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}

?>
