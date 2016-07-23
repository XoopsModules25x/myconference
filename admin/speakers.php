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

//if (!empty($_POST['fct'])) {
//    $fct = trim($_POST['fct']);
//} else {
//    $fct = 'nada';
//}

$fct = XoopsRequest::getString('fct', 'nada', 'POST');

//if (isset($_POST)) {
//    foreach ($_POST as $k => $v) {
//        "k ($k) = v ($v)<br>"; //echo
//        if (is_array($v)) {
//            foreach ($v as $j => $w) {
//                "j ($j) = w ($w)<br>"; //echo
//            }
//        }
//    }
//}

//if (isset($_FILES)) {
//    foreach ($_FILES as $k => $v) {
//        "<b>k ($k) = v ($v)</b><br>"; //echo
//        if (is_array($v)) {
//            foreach ($v as $j => $w) {
//                "<b>j ($j) = w ($w)</b><br>"; //echo
//            }
//        }
//    }
//}

$eh = new ErrorHandler;

switch ($fct) {
    case 'updspeaker':
        $eh       = new ErrorHandler;
        $name     = XoopsRequest::getString('speakerName', '', 'POST');//$_POST['speakerName'];
        $email    = XoopsRequest::getEmail('speakerEmail', '', 'POST');//$_POST['speakerEmail'];
        $descrip  = XoopsRequest::getText('speakerMiniBio', '', 'POST');//$_POST['speakerMiniBio'];
        $company  = XoopsRequest::getString('speakerCompanyName', '', 'POST');//$_POST['speakerCompanyName'];
        $location = XoopsRequest::getString('speakerCompanyLocation', '', 'POST');//$_POST['speakerCompanyLocation'];
        $url      = XoopsRequest::getUrl('speakerSite', '', 'POST');//$_POST['speakerSite'];
        $photo    = XoopsRequest::getArray('xoops_upload_file', array(), 'POST')[0];//$_POST['xoops_upload_file'][0];
        if (isset($_FILES['attachedfile'])) {
            $photo = getFile($photo);
        }
        if (isset(XoopsRequest::getArray('xoops_upload_file', array(), 'POST')[1])//$_POST['xoops_upload_file'][1])
            && !empty(XoopsRequest::getString('photo2', null, 'POST')) //$_POST['photo2'])
        ) {
            $photo2 = XoopsRequest::getString('xoops_upload_file', null, 'POST')[1];//$_POST['xoops_upload_file'][1];
            $photo2 = getFile($photo2);
        }
        $speakerid = XoopsRequest::getInt('speakerid', 0, 'POST');//$_POST['speakerid'];
        $result = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('myconference_speakers') . " SET name='$name', email='$email', descrip='$descrip', location='$location', company='$company', photo='$photo', url='$url' WHERE speakerid=$speakerid");// or $eh::show('0013');
        if ($result) {
            redirect_header('speakers.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    case
    'delspeaker':
        $eh = new ErrorHandler;
        break;
    case 'editspeaker':
        xoops_cp_header();

        $action = XoopsRequest::getString('action', 0, 'POST');//$_POST['action'];
        if ($action === 'upd') {
            $speakerid = XoopsRequest::getInt('speakerid', 0, 'POST');//trim($_POST['speakerid']) or $eh::show('1001');
            $result = $xoopsDB->query('SELECT name,email,company,location,url,photo,descrip FROM ' . $xoopsDB->prefix('myconference_speakers') . " WHERE speakerid=$speakerid");// or $eh::show('0013');
            list($name_v, $email_v, $company_v, $location_v, $url_v, $photo_v, $minibio_v) = $xoopsDB->fetchRow($result);

            $name      = new XoopsFormText(_AM_MYCONFERENCE_NAME, 'speakerName', 50, 100, $name_v);
            $email     = new XoopsFormText(_AM_MYCONFERENCE_EMAIL, 'speakerEmail', 50, 100, $email_v);
            $company   = new XoopsFormText(_AM_MYCONFERENCE_COMPANY, 'speakerCompanyName', 50, 100, $company_v);
            $location  = new XoopsFormText(_AM_MYCONFERENCE_LOCATION, 'speakerCompanyLocation', 50, 100, $location_v);
            $url       = new XoopsFormText(_AM_MYCONFERENCE_URL, 'speakerSite', 50, 100, $url_v);
            $fct       = new XoopsFormHidden('fct', 'updspeaker');
            $speakerid = new XoopsFormHidden('speakerid', $speakerid);

            $photo = new XoopsFormFile('', 'speakersPhoto', 1000000);

//            $uploadirectory = MYCONFERENCE_UPLOAD_PATH . '/images';
            $uploadirectory =  MYCONFERENCE_UPLOAD_URL .'/images';

            $photo_label = new XoopsFormLabel('', '<img src="' . $uploadirectory . '/' . $photo_v . '" alt="" valign="top" align="right" />');
            $photo_tray  = new XoopsFormElementTray(_AM_MYCONFERENCE_PHOTO_WARN, '&nbsp;');
            $photo_tray->addElement($photo);
            $photo_tray->addElement($photo_label);

            //            if ( $edit && trim($item->getVar('birthday_photo')) != '' && $item->pictureExists() ) {

            $pictureTray = new XoopsFormElementTray(_AM_MYCONFERENCE_CURRENT_PICTURE, '<br>');
            //          $pictureTray->addElement(new XoopsFormLabel('', "<img src='".$item->getPictureUrl()."' alt='' border='0' />"));
            $pictureTray->addElement(new XoopsFormLabel('', "<img src='" .$uploadirectory . '/' . $photo_v . "' alt='' border='0' />"));
            $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            /*          $form->addElement($pictureTray);
                        unset($pictureTray, $deleteCheckbox);*/
            //}

            $minibio = new XoopsFormTextArea(_AM_MYCONFERENCE_MINI_BIO, 'speakerMiniBio', '', 25, 100);
            $minibio->setValue($minibio_v);
            $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_UPDATE, 'submit');

            $form = new XoopsThemeForm(_AM_MYCONFERENCE_UPD_SPEAKER, 'editspeakerform', 'speakers.php');
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement($name, true);
            $form->addElement($email);
            $form->addElement($url);
            //            $form->addElement($pictureTray);
            $form->addElement($company);
            $form->addElement($location);
            $form->addElement($minibio, true);
            $form->addElement($fct);
            $form->addElement($speakerid);

            $form->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
            $form->addElement(new XoopsFormFile(_AM_MYCONFERENCE_PICTURE, 'attachedfile', $xoopsModuleConfig['max_imgsize']), false);

            $form->addElement($submit_button);

            $form->display();

            xoops_cp_footer();
        } elseif ($action === 'del') {
            $speakerid = XoopsRequest::getInt('speakerid', 0, 'POST');//trim($_POST['speakerid']) or $eh::show('1001');
            xoops_confirm(array('fct' => 'delspeakerok', 'speakerid' => $speakerid), 'speakers.php', _AM_MYCONFERENCE_DEL_SPEAKER);
            xoops_cp_footer();
        }
        break;

    case 'delspeakerok':
        $speakerid = XoopsRequest::getInt('speakerid', 0, 'POST');//trim($_POST['speakerid']) or $eh::show('1001');
        $result = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('myconference_speakers') . " WHERE speakerid=$speakerid");// or $eh::show('0013');
        redirect_header('speakers.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        break;
    case 'addspeaker':
        $name     = $myts->stripslashesGPC(trim(XoopsRequest::getString('speakerName', '', 'POST')));//$_POST['speakerName']));
        $email    = $myts->stripslashesGPC(trim(XoopsRequest::getEmail('speakerEmail', '', 'POST')));//$_POST['speakerEmail']));
        $descrip  = $myts->stripslashesGPC(trim(XoopsRequest::getText('speakerMiniBio', '', 'POST')));//$_POST['speakerMiniBio']));
        $company  = $myts->stripslashesGPC(trim(XoopsRequest::getString('speakerCompanyName', '', 'POST')));//$_POST['speakerCompanyName']));
        $location = $myts->stripslashesGPC(trim(XoopsRequest::getString('speakerCompanyLocation', '', 'POST')));//$_POST['speakerCompanyLocation']));
        $url      = $myts->stripslashesGPC(trim(XoopsRequest::getUrl('speakerSite', '', 'POST')));//$_POST['speakerSite']));
        //$file = $myts->stripslashesGPC(trim($_POST['xoops_upload_file'][0]));
        //echo "file ($file)";
        /*        if (!empty($_FILES['speakersPhoto']['name'])) {
                    echo "geeeeeeeeettt it !? ...";
                    $photo = getFile($file);
                }*/

        //        if (isset($_POST['xoops_upload_file'][0]) && !empty($_POST['photo'])) {
        //            $photo = $_POST['xoops_upload_file'][0];
        //            $photo = getFile($photo);
        //        }

        if (isset(XoopsRequest::getString('xoops_upload_file', null, 'POST')[0]) //$_POST['xoops_upload_file'][0])
            && !empty(XoopsRequest::getString('photo', null, 'POST')) //$_POST['photo'])
        ) {
            $photo = XoopsRequest::getString('xoops_upload_file', null, 'POST')[0]; //$_POST['xoops_upload_file'][0];
            $photo = getFile($photo);
        }

        $result = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('myconference_speakers') . " (name,email,descrip,location,company,photo,url) VALUES ('$name','$email','$descrip','$location','$company','$photo','$url')");// or $eh::show('0013');
        if ($result) {
            redirect_header('speakers.php', 2, _AM_MYCONFERENCE_DBUPDATED);
        }
        break;
    default:
        xoops_cp_header();

        $result = $xoopsDB->query('SELECT speakerid, name FROM ' . $xoopsDB->prefix('myconference_speakers') . ' ORDER BY Name ASC');// or $eh::show('0013');
        $speakerSelect = new XoopsFormSelect(_AM_MYCONFERENCE_NAME, 'speakerid');
        while (list($speakerid, $name) = $xoopsDB->fetchRow($result)) {
            $speakerSelect->addOption($speakerid, $name);
        }
        $action_select = new XoopsFormSelect(_AM_MYCONFERENCE_ACTION, 'action');
        $action_select->addOption('upd', _AM_MYCONFERENCE_EDIT);
        $action_select->addOption('del', _AM_MYCONFERENCE_DELE);
        $fct           = new XoopsFormHidden('fct', 'editspeaker');
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $editform = new XoopsThemeForm(_AM_MYCONFERENCE_EDIT_SPEAKER, 'editspeakerform', 'speakers.php');
        $editform->addElement($fct);
        $editform->addElement($speakerSelect);
        $editform->addElement($action_select);
        $editform->addElement($submit_button);

        $editform->display();

        $name     = new XoopsFormText(_AM_MYCONFERENCE_NAME, 'speakerName', 50, 100);
        $email    = new XoopsFormText(_AM_MYCONFERENCE_EMAIL, 'speakerEmail', 50, 100);
        $company  = new XoopsFormText(_AM_MYCONFERENCE_COMPANY, 'speakerCompanyName', 50, 100);
        $location = new XoopsFormText(_AM_MYCONFERENCE_LOCATION, 'speakerCompanyLocation', 50, 100);
        $url      = new XoopsFormText(_AM_MYCONFERENCE_URL, 'speakerSite', 50, 100);
        $fct      = new XoopsFormHidden('fct', 'addspeaker');

        $photo         = new XoopsFormFile(_AM_MYCONFERENCE_PHOTO, 'speakersPhoto', 1000000);
        $minibio       = new XoopsFormTextArea(_AM_MYCONFERENCE_MINI_BIO, 'speakerMiniBio', '', 25, 100);
        $submit_button = new XoopsFormButton('', 'submit', _AM_MYCONFERENCE_SUBMIT, 'submit');

        $form = new XoopsThemeForm(_AM_MYCONFERENCE_ADD_SPEAKER, 'addspeakerform', 'speakers.php');
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement($name, true);
        $form->addElement($email);
        $form->addElement($url);
        $form->addElement($company);
        $form->addElement($location);
        $form->addElement($minibio, true);
        $form->addElement($photo);
        $form->addElement(new XoopsFormFile(_AM_MYCONFERENCE_PICUPLOAD, 'filename', $xoopsModuleConfig['max_imgsize']), true);

        $form->addElement($fct);
        $form->addElement($submit_button);

        $form->display();

        xoops_cp_footer();
}
