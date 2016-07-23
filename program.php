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

include __DIR__ . '/header.php';
include XOOPS_ROOT_PATH . '/header.php';
//$eh = new ErrorHandler;

function printRow($ctime, $row, $tpos, $class)
{
    global $xoopsDB;
    $i = 0;
    while ($i < count($tpos)) {
        if (isset($row[$i])) {
            if ($row[$i] > 0) {
                $sid = $row[$i];
                $sd  = $xoopsDB->query('SELECT stime, etime, duration, title FROM ' . $xoopsDB->prefix('myconference_speeches') . " WHERE sid=$sid");
                list($stime, $etime, $duration, $title) = $xoopsDB->fetchRow($sd);
                $rowspan = ($etime - $stime) / (30 * 60);
                echo "<td class=\"$class\" rowspan=\"$rowspan\" align=\"center\" style=\"vertical-align: middle;\"><a href=\"viewspeech.php?sid=$sid\">$title</a></td>\n";
            }
        } else {
            echo "<td class=$class></td>\n";
        }
        ++$i;
    }
    echo "</tr>\n";
}

//if (isset($_GET['cid'])) {
//    $cid = $_GET['cid'];
//} elseif (isset($_POST['cid'])) {
//    $cid = $_POST['cid'];
//}

$cid     = XoopsRequest::getInt('cid', XoopsRequest::getInt('cid', 0, 'GET'), 'POST');

if (0 === $cid) {
    $result = $xoopsDB->query('SELECT cid FROM ' . $xoopsDB->prefix('myconference_main') . ' WHERE isdefault=1');// or $eh::show("1001");
    list($cid) = $xoopsDB->fetchRow($result);
}

// Get the days of this congress
$cr = $xoopsDB->query('SELECT title, subtitle, subsubtitle, sdate, edate FROM ' . $xoopsDB->prefix('myconference_main') . " WHERE cid=$cid");// or $eh::show("0013");
list($ctitle, $subtitle, $subsubtitle, $sdate, $edate) = $xoopsDB->fetchRow($cr);
$sdate = strtotime($sdate . ' 0:00:00');
$edate = strtotime($edate . ' 23:59:59');

echo "
    <hr width=50% align='center'>
    <center>
    <font size=+1><b>$ctitle</b></font><br>
    <b><i>$subtitle</i></b><br>
    <font size=+1>$subsubtitle</font><br>
    </center>
    <hr width=50% align='center'>
";

$result = $xoopsDB->query('SELECT sid, title FROM ' . $xoopsDB->prefix('myconference_sections') . " WHERE cid=$cid ORDER BY title");// or $eh::show("0013");

echo " <table class='outer' border='0' cellspacing='5' cellpadding='0' align='center' width='100%'> <tr>\n";
$count = 1;
while (list($sid, $title) = $xoopsDB->fetchRow($result)) {
    echo "<td class=\"itemHead\" valign=\"top\" ><a href=\"" . XOOPS_URL . "/modules/myconference/index.php?sid=$sid\"><b>$title</b></a></td>";
}
echo "<td class=\"itemHead\" valign=\"top\" ><a href=\"" . XOOPS_URL . "/modules/myconference/program.php\"><b>" . _MD_MYCONFERENCE_PROGRAM . '</b></a></td>';
echo "</tr></table>\n";

$header = '';
$oneday = 86400;
for ($d = $sdate; $d <= $edate; $d += $oneday) {
    echo "<table width=100%>\n";
    echo "<tr>\n";
    echo "<td align='center' class='head'>" . date('D, j M Y', $d) . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    echo "<table width=100%>\n";

    // Get tracks
    $t_result = $xoopsDB->query('SELECT tid, title FROM ' . $xoopsDB->prefix('myconference_tracks') . " WHERE cid=$cid order by tid");// or $eh::show("0013");
    $i        = 0;
    while (list($tid, $tit) = $xoopsDB->fetchRow($t_result)) {
        $tpos[$tid]  = ++$i;
        $title[$tid] = $tit;
    }
    if (empty($tid)) {
        $tpos[0]  = 0;
        $title[0] = '';
    }

    // Get speeches ordered by time
    $s_result = $xoopsDB->query('SELECT sid, tid, stime, etime FROM ' . $xoopsDB->prefix('myconference_speeches') . " WHERE cid=$cid AND stime BETWEEN $d AND $d+$oneday ORDER BY stime");// or $eh::show("0013");
    $earliest = 9999999999;
    $oldest   = -1;

    while (list($sid, $tid, $stime, $etime) = $xoopsDB->fetchRow($s_result)) {
        $earliest = ($stime < $earliest) ? $stime : $earliest;
        $oldest   = ($etime > $oldest) ? $etime : $oldest;
    }

    $swidth = count(_MD_MYCONFERENCE_SCHEDULE);
    echo "<tr>\n<td align=center class=head width=$swidth>" . _MD_MYCONFERENCE_SCHEDULE . "</td>\n";
    $ntitle = array_reverse($title);
    foreach ($title as $tid => $v) {
        echo '<td align=center class=head>';
        if ($v) {
            echo "$v <a href=\"viewtrack.php?tid=$tid\"><sup>(" . _MD_MYCONFERENCE_INFOINITIAL . ')</sup></a>';
        }
        echo " </td>\n";
    }
    echo "</tr>\n";
    $interval = 30 * 60;
    $class    = 'odd';
    for ($i = $earliest; $i < $oldest; $i += $interval) {
        echo "<tr>\n";
        $class = ($class === 'even') ? 'odd' : 'even';
        echo "<td class=$class width=$swidth align=\"center\" style=\"vertical-align: middle;\"> " . date('H:i', $i) . "</td>\n";
        //query de charlas que tienen este horario en su rango
        $s_result = $xoopsDB->query('SELECT sid, tid, stime, duration FROM ' . $xoopsDB->prefix('myconference_speeches') . " WHERE cid=$cid AND $i BETWEEN stime AND etime ORDER BY stime, tid");// or $eh::show("0013");
        $printpos = array();
        $row      = array();
        while (list($sid, $tid, $stime, $duration) = $xoopsDB->fetchRow($s_result)) {
            //loop de tracks
            $etime = $stime + $duration * 60;
            foreach ($tpos as $k => $v) {
                //print de los que empiecen en este horario (los que estan transcurriendo -lo se por el select- se saltean
                //y los tracks que no tienen nada se pone un "<td></td>";
                if ($stime == $i) {
                    $row[$tpos[$tid]] = $sid;
                }
                if ($i > $stime && $i < $etime) {
                    // We're at a speech that started already
                    $row[$tpos[$tid]] = -1;
                }
            }
        }
        printRow($i, $row, $tpos, $class);
    }
    echo "</table><br><hr width=50% align='center'><br>\n";
}

include XOOPS_ROOT_PATH . '/footer.php';
