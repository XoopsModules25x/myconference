<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 * Pedigree module for XOOPS
 *
 * @copyright       {@link http://xoops.org/  XOOPS Project}
 * @license         GPL 2.0 or later
 * @package         pedigree
 * @author          XOOPS Module Dev Team (http://xoops.org)
 */

require_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

if (!defined('MYCONFERENCE_DIRNAME')) {
    define('MYCONFERENCE_DIRNAME', $GLOBALS['xoopsModule']->dirname());
    define('MYCONFERENCE_PATH', XOOPS_ROOT_PATH . '/modules/' . MYCONFERENCE_DIRNAME);
    define('MYCONFERENCE_URL', XOOPS_URL . '/modules/' . MYCONFERENCE_DIRNAME);
    define('MYCONFERENCE_ADMIN', MYCONFERENCE_URL . '/admin/index.php');
    define('MYCONFERENCE_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . MYCONFERENCE_DIRNAME);
    //@todo - the image xoopsproject_logo.png doesn't exist... Either create it or reference
    //        something like $GLOBALS['xoops']->url("www/{$pathIcon32}/xoopsmicrobutton.gif")
    //    define('MYCONFERENCE_AUTHOR_LOGOIMG', MYCONFERENCE_URL . '/assets/images/xoopsproject_logo.png');
}

// Define the main upload path
define('MYCONFERENCE_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . MYCONFERENCE_DIRNAME); // WITHOUT Trailing slash
define('MYCONFERENCE_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . MYCONFERENCE_DIRNAME); // WITHOUT Trailing slash

$uploadFolders = array(
    MYCONFERENCE_UPLOAD_PATH,
    MYCONFERENCE_UPLOAD_PATH . '/images',
    MYCONFERENCE_UPLOAD_PATH . '/images/thumbnails'
);

// module information
//$mod_copyright = "<a href='http://xoops.org' title='XOOPS Project' target='_blank'>
//                     <img src='" . MYCONFERENCE_AUTHOR_LOGOIMG . "' alt='XOOPS Project' /></a>";
