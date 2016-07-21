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

function getFile($file)
{
    include_once XOOPS_ROOT_PATH . '/class/uploader.php';
    $mimetypes = array(
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/x-png',
        'image/png',
        'text/html',
        'text/plain',
        'text/rtf',
        'text/x-tex',
        'text/sgml',
        'application/pdf',
        'application/postscript',
        'application/x-texinfo',
        'application/x-troff',
        'application/vnd.sun.xml.writer',
        'application/vnd.sun.xml.impress',
        'application/vnd.sun.xml.calc',
        'application/vnd.stardivision.writer',
        'application/vnd.stardivision.impress',
        'application/vnd.stardivision.calc',
        'application/x-kword',
        'application/x-killustrator',
        'application/x-kpresenter',
        'application/x-kchart',
        'application/x-latex',
        'application/x-gnumeric',
        'application/sgml',
        'application/xhtml+xml',
        'application/xml',
        'application/xml-dtd',
        'application/zip',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.ms-project',
        'application/vnd.ms-works',
        'application/msword',
        'application/ogg',
        'application/x-gtar',
        'application/x-dvi',
        'audio/mpeg'
    );

    $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, $mimetypes, 50000, 500, 500);
    $uploader->setPrefix('cnfr');
    if ($uploader->fetchMedia($file)) {
        if (!$uploader->upload()) {
            xoops_cp_header();
            xoops_error($uploader->getErrors());
            xoops_cp_footer();
            exit();
        } else {
            $file = $uploader->getSavedFileName();
        }
    } else {
        xoops_cp_header();
        xoops_error($uploader->getErrors());
        xoops_cp_footer();
        exit();
    }

    return $file;
}

function XoopsFormDateTimeI($caption, $name, $size = 15, $value = 0, $interval = 10)
{
    $fe       = new XoopsFormElementTray($caption, '&nbsp;');
    $value    = (int)$value;
    $interval = (int)$interval;
    $value    = ($value > 0) ? $value : time();
    $datetime = getdate($value);
    $fe->addElement(new XoopsFormTextDateSelect('', $name . '[date]', $size, $value));
    $timearray = array();
    for ($i = 0; $i < 24; ++$i) {
        for ($j = 0; $j < 60; $j += $interval) {
            $key             = ($i * 3600) + ($j * 60);
            $timearray[$key] = ($j != 0) ? $i . ':' . $j : $i . ':0' . $j;
        }
    }
    ksort($timearray);
    $timeselect = new XoopsFormSelect('', $name . '[time]', $datetime['hours'] * 3600 + 600 * ceil($datetime['minutes'] / 10));
    $timeselect->addOptionArray($timearray);
    $fe->addElement($timeselect);

    return $fe;
}

function showSection($section)
{
}
