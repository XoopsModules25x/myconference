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
/******************************************************************************
 * Function: b_mylinks_top_show
 * Input   : $options[0] = date for the most recent links
 *                    hits for the most popular links
 *           $block['content'] = The optional above content
 *           $options[1]   = How many reviews are displayes
 * Output  : Returns the desired most recent or most popular links
 *****************************************************************************
 * @param $options
 * @return array
 */
function b_myconference_show($options)
{
    global $xoopsDB;
    $block = array();
    $cid   = '';
    if (empty($cid)) {
        $rv = $xoopsDB->query('SELECT cid FROM ' . $xoopsDB->prefix('myconference_main') . ' WHERE isdefault=1') OR $eh::show('1001');
        list($cid) = $xoopsDB->fetchRow($rv);
    }
    $section['sid']      = 0;
    $section['title']    = _MB_MYCONFERENCE_PROGRAM;
    $block['sections'][] = $section;
    $rv                  = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE cid=$cid ORDER BY title");
    while (list($sid, $title) = $xoopsDB->fetchRow($rv)) {
        $section['sid']      = $sid;
        $section['title']    = $title;
        $block['sections'][] = $section;
    }

    return $block;
}

function b_myconference_edit($options)
{
}
