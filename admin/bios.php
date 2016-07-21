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

if (!empty($_POST['fct'])) {
    $fct = trim($_POST['fct']);
} else {
    $fct = 'nada';
}
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        "k ($k) = v ($v)<br>"; //echo
        if (is_array($v)) {
            foreach ($v as $j => $w) {
                "j ($j) = w ($w)<br>"; //echo
            }
        }
    }
}

if (isset($_FILES)) {
    foreach ($_FILES as $k => $v) {
        "<b>k ($k) = v ($v)</b><br>"; //echo
        if (is_array($v)) {
            foreach ($v as $j => $w) {
                "<b>j ($j) = w ($w)</b><br>"; //echo
            }
        }
    }
}

$eh = new ErrorHandler;

switch ($fct) {
    case 'updcv':
        $eh       = new ErrorHandler;
        $name     = $_POST['speakerName'];
        $email    = $_POST['speakerEmail'];
        $descrip  = $_POST['speakerMiniCV'];
        $company  = $_POST['speakerCompanyName'];
        $location = $_POST['speakerCompanyLocation'];
        $url      = $_POST['speakerSite'];
        $photo    = $_POST['xoops_upload_file'][0];
        //        if (isset($_FILES['speakersPhoto'])) {
        //            $photo = getFile($photo);

        if (isset($_POST['xoops_upload_file'][1]) && !empty($_POST['photo2'])) {
            $photo = $_POST['xoops_upload_file'][1];
            $photo = getFile($photo);
        }
        $cvid   = $_POST['cvid'];
        $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_bios') . " SET name='$name', email='$email', descrip='$descrip', location='$location', company='$company', photo='$photo', url='$url' WHERE cvid=$cvid") OR $eh::show('0013');
        if ($result) {
            redirect_header('bios.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    case 'delcv':
        $eh = new ErrorHandler;
        break;
    case 'editcv':
        xoops_cp_header();

        $action = $_POST['action'];
        if ($action === 'upd') {
            $cvid   = trim($_POST['cvid']) OR $eh::show('1001');
            $result = $xoopsDB->query('SELECT name,email,company,location,url,photo,descrip FROM ' . $xoopsDB->prefix('myconference_bios') . " WHERE cvid=$cvid") OR $eh::show('0013');
            list($name_v, $email_v, $company_v, $location_v, $url_v, $photo_v, $minicv_v) = $xoopsDB->fetchRow($result);

            $name     = new XoopsFormText(_AM_MYCONFERENCE_NAME, 'speakerName', 50, 100, $name_v);
            $email    = new XoopsFormText(_AM_MYCONFERENCE_EMAIL, 'speakerEmail', 50, 100, $email_v);
            $company  = new XoopsFormText(_AM_MYCONFERENCE_COMPANY, 'speakerCompanyName', 50, 100, $company_v);
            $location = new XoopsFormText(_AM_MYCONFERENCE_LOCATION, 'speakerCompanyLocation', 50, 100, $location_v);
            $url      = new XoopsFormText(_AM_MYCONFERENCE_URL, 'speakerSite', 50, 100, $url_v);
            $fct      = new XoopsFormHidden('fct', 'updcv');
            $cvid     = new XoopsFormHidden('cvid', $cvid);

            /* $photo = new XoopsFormFile('', "speakersPhoto", 50000);
             $photo_label = new XoopsFormLabel('', '<img src="'.XOOPS_UPLOAD_URL.'/myconference/'.$photo_v.'" alt="" valign="top" align="right" />');
             $photo_tray = new XoopsFormElementTray(_AM_MYCONFERENCE_PHOTO_WARN, '&nbsp;');
             $photo_tray->addElement($photo);
             $photo_tray->addElement($photo_label);
             */

            //            if ( $edit && trim($item->getVar('birthday_photo')) != '' && $item->pictureExists() ) {

            $pictureTray = new XoopsFormElementTray(_AM_MYCONFERENCE_CURRENT_PICTURE, '<br>');
            //          $pictureTray->addElement(new XoopsFormLabel('', "<img src='".$item->getPictureUrl()."' alt='' border='0' />"));
            $pictureTray->addElement(new XoopsFormLabel('', "<img src='" . "' alt='' border='0' />"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            /*          $form->addElement($pictureTray);
                        unset($pictureTray, $deleteCheckbox);*/
            //}

            $minicv = new XoopsFormTextArea(_AM_MYCONFERENCE_MINICV, 'speakerMiniCV', '', 25, 100);
            $minicv->setValue($minicv_v);
            $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

            $form = new XoopsThemeForm(_AM_MYCONFERENCE_UPDCV, 'editcvform', 'bios.php');
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

            $form->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
            $form->addElement(new XoopsFormFile(_AM_MYCONFERENCE_PICTURE, 'attachedfile', $xoopsModuleConfig['maxfilesize']), false);

            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action === 'del') {
            $cvid = trim($_POST['cvid']) OR $eh::show('1001');
            xoops_confirm(array('fct' => 'delcvok', 'cvid' => $cvid), 'bios.php', _AM_MYCONFERENCE_DELCV);
            xoops_cp_footer();
        }
        break;

    case 'delcvok':
        $cvid   = trim($_POST['cvid']) OR $eh::show('1001');
        $result = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('myconference_bios') . " WHERE cvid=$cvid") OR $eh::show('0013');
        redirect_header('bios.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        break;
    case 'addcv':
        $name     = $myts->stripslashesGPC(trim($_POST['speakerName']));
        $email    = $myts->stripslashesGPC(trim($_POST['speakerEmail']));
        $descrip  = $myts->stripslashesGPC(trim($_POST['speakerMiniCV']));
        $company  = $myts->stripslashesGPC(trim($_POST['speakerCompanyName']));
        $location = $myts->stripslashesGPC(trim($_POST['speakerCompanyLocation']));
        $url      = $myts->stripslashesGPC(trim($_POST['speakerSite']));
        //$file = $myts->stripslashesGPC(trim($_POST['xoops_upload_file'][0]));
        //echo "file ($file)";
        /*        if (!empty($_FILES['speakersPhoto']['name'])) {
                    echo "geeeeeeeeettt it !? ...";
                    $photo = getFile($file);
                }*/

        if (isset($_POST['xoops_upload_file'][0]) && !empty($_POST['photo'])) {
            $photo = $_POST['xoops_upload_file'][0];
            $photo = getFile($photo);
        }

        $result = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('myconference_bios') . " (name,email,descrip,location,company,photo,url) VALUES ('$name','$email','$descrip','$location','$company','$photo','$url')") OR $eh::show('0013');
        if ($result) {
            redirect_header('bios.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    default:
        xoops_cp_header();

        $result    = $xoopsDB->query('SELECT cvid, name FROM ' . $xoopsDB->prefix('myconference_bios') . ' ORDER BY Name ASC') OR $eh::show('0013');
        $cv_select = new XoopsFormSelect(_AM_MYCONFERENCE_NAME, 'cvid');
        while (list($cvid, $name) = $xoopsDB->fetchRow($result)) {
            $cv_select->addOption($cvid, $name);
        }
        $action_select = new XoopsFormSelect(_AM_MYCONFERENCE_ACTION, 'action');
        $action_select->addOption('upd', _AM_MYCONFERENCE_EDIT);
        $action_select->addOption('del', _AM_MYCONFERENCE_DELE);
        $fct           = new XoopsFormHidden('fct', 'editcv');
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $editform = new XoopsThemeForm(_AM_MYCONFERENCE_EDITCV, 'editcvform', 'bios.php');
        $editform->addElement($fct);
        $editform->addElement($cv_select);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $name     = new XoopsFormText(_AM_MYCONFERENCE_NAME, 'speakerName', 50, 100);
        $email    = new XoopsFormText(_AM_MYCONFERENCE_EMAIL, 'speakerEmail', 50, 100);
        $company  = new XoopsFormText(_AM_MYCONFERENCE_COMPANY, 'speakerCompanyName', 50, 100);
        $location = new XoopsFormText(_AM_MYCONFERENCE_LOCATION, 'speakerCompanyLocation', 50, 100);
        $url      = new XoopsFormText(_AM_MYCONFERENCE_URL, 'speakerSite', 50, 100);
        $fct      = new XoopsFormHidden('fct', 'addcv');

        $photo         = new XoopsFormFile(_AM_MYCONFERENCE_PHOTO, 'speakersPhoto', 50000);
        $minicv        = new XoopsFormTextArea(_AM_MYCONFERENCE_MINICV, 'speakerMiniCV', '', 25, 100);
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $form = new XoopsThemeForm(_AM_MYCONFERENCE_ADDCV, 'addcvform', 'bios.php');
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement($name, true);
        $form->addElement($email);
        $form->addElement($url);
        //$form->addElement($photo);

        $form->addElement(new XoopsFormFile(_AM_MYCONFERENCE_PICUPLOAD, 'filename', $xoopsModuleConfig['maxfilesize']), true);

        $form->addElement($company);
        $form->addElement($location);
        $form->addElement($minicv, true);
        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}
