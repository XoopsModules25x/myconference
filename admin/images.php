<?php
/**
 * $Id: images.php v 1.0 8 May 2004 hsalazar Exp $
 * Module: Wordbook - a multicategory glossary
 * Version: v 1.00
 * Release Date: 8 May 2004
 * Author: hsalazar
 * Licence: GNU
 */

// Includes
include('admin_header.php');

xoops_cp_header();

// Funciones
function imageEdit()
{
    // Function that displays the formula, is already empty to upload
    // a new image, or to fill it with data for editing
    // an existing image.
}

function imageList()
{
    // Function that displays the existing images. From here
    // you could call the function to visualize an image (using
    // Javascript), to edit, and to delete.
}

function imageSave()
{
    // Function that takes the data from the form and, in case of a
    // new image, uploads the file to the appropriate directory and
    // creates the record in the database; if it is an edit, the
    // modified fields are only kept in the database.
    $result = $xoopsDB->query('SELECT * from ' . $xoopsDB->prefix('fm_pics') . " WHERE filename = '$filename'");
    if ($xoopsDB->getRowsNum($result) == 0) {
        if (!$picID) {
            $sqlquery = 'INSERT INTO ' . $xoopsDB->prefix('fm_pics') . " ( picID, name, filename, caption, uploaded ) values ( '', '$name', '$filename', '$caption', '$date' )";
            // if entry was successfully inserted
            if ($xoopsDB->query($sqlquery)) {
                redirect_header('images.php', 1, _AM_MYCONFERENCE_IMAGEUPLOADED);
            } else {
                redirect_header('images.php', 1, _AM_MYCONFERENCE_IMAGE_NOT_UPLOADED);
            }
        } else {
            if ($xoopsDB->query('UPDATE ' . $xoopsDB->prefix('fm_pics') . " SET name = '$name', filename = '$filename', caption = '$caption' WHERE picID = '$picID'")) {
                redirect_header('images.php', 1, _AM_MYCONFERENCE_IMAGEMODIFIED);
            } else {
                redirect_header('images.php', 1, _AM_MYCONFERENCE_IMAGENOTUPDATED);
            }
        }
    } else {
        redirect_header('images.php', 1, _AM_MYCONFERENCE_FILENAMEEXISTS);
    }
}

function imageDelete()
{
    // Function that deletes an image from both the physical directory
    // and from the databse
}

// Switch operations
switch ($op) {
    case 'form':
    case 'default':
    default:
        // enter the page; form

    case 'save':
        // Save the data; upload image

    case 'edit':
        // Recover data; load it to the form

    case 'del':
        // Delete the data; delete file

    case 'list':
        // Show the list of images
}

xoops_cp_footer();
