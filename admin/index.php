<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @author       XOOPS Development Team
 */

include_once __DIR__ . '/admin_header.php';
include_once dirname(__DIR__) . '/class/utilities.php';
// Display Admin header
xoops_cp_header();

$indexAdmin = new ModuleAdmin();

foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
    MyconferenceUtilities::prepareFolder($uploadFolders[$i]);
    $indexAdmin->addConfigBoxLine($uploadFolders[$i], 'folder');
    //    $indexAdmin->addConfigBoxLine(array($folder[$i], '777'), 'chmod');
}

echo $indexAdmin->addNavigation(basename(__FILE__));
echo $indexAdmin->renderIndex();

include_once __DIR__ . '/admin_footer.php';
