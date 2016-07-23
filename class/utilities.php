<?php

/**
 * Created by PhpStorm.
 * User: Mamba
 * Date: 2014-11-19
 * Time: 3:05
 */

$moduleDirName = basename(dirname(__DIR__));
//require_once $GLOBALS['xoops']->path("modules/{$moduleDirName}/class/field.php");
require_once $GLOBALS['xoops']->path("modules/{$moduleDirName}/include/config.php");
//if (!class_exists('PedigreeAnimal')) {
//    require_once $GLOBALS['xoops']->path("modules/{$moduleDirName}/class/animal.php");
//}
//if (!class_exists('PedigreeField')) {
//    $GLOBALS['xoops']->path("modules/{$moduleDirName}/class/field.php");
//}
/*
//get module configuration
$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname($moduleDirName);
$configHandler = xoops_getHandler('config');
$moduleConfig  = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
*/

/**
 * Class PedigreeUtilities
 */
class MyconferenceUtilities
{

    /**
     *
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkXoopsVer(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        //check for minimum XOOPS version
        $currentVer  = substr(XOOPS_VERSION, 6); // get the numeric part of string
        $currArray   = explode('.', $currentVer);
        $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        $reqArray    = explode('.', $requiredVer);
        $success     = true;
        foreach ($reqArray as $k => $v) {
            if (isset($currArray[$k])) {
                if ($currArray[$k] > $v) {
                    break;
                } elseif ($currArray[$k] == $v) {
                    continue;
                } else {
                    $success = false;
                    break;
                }
            } else {
                if ((int)$v > 0) { // handles things like x.x.x.0_RC2
                    $success = false;
                    break;
                }
            }
        }

        if (!$success) {
            $module->setErrors(sprintf(_AM_MYCONFERENCE_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }

    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkPhpVer(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        // check for minimum PHP version
        $success = true;
        $verNum  = phpversion();
        $reqVer  =& $module->getInfo('min_php');
        if (false !== $reqVer && '' !== $reqVer) {
            if (version_compare($verNum, $reqVer, '<')) {
                $module->setErrors(sprintf(_AM_MYCONFERENCE_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Function responsible for checking if a directory exists, we can also write in and create an index.html file
     *
     * @param string $folder The full path of the directory to check
     *
     * @return void
     */
    public static function prepareFolder($folder)
    {
        //        $filteredFolder = XoopsFilterInput::clean($folder, 'PATH');
        if (!is_dir($folder)) {
            mkdir($folder);
            file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
        }
        //        chmod($filteredFolder, 0777);
    }

    /**
     * @param $columncount
     *
     * @return string
     */
    public static function sortTable($columncount)
    {
        $ttemp = '';
        if ($columncount > 1) {
            for ($t = 1; $t < $columncount; ++$t) {
                $ttemp .= "'S',";
            }
            $tsarray = "initSortTable('Result', Array({$ttemp}'S'));";
        } else {
            $tsarray = "initSortTable('Result',Array('S'));";
        }

        return $tsarray;
    }

    /**
     * @param $num
     *
     * @return string
     */
    public static function uploadPicture($num)
    {
        $max_imgsize       = $GLOBALS['xoopsModuleConfig']['maxfilesize']; //1024000;
        $max_imgwidth      = $GLOBALS['xoopsModuleConfig']['maximgwidth']; //1500;
        $max_imgheight     = $GLOBALS['xoopsModuleConfig']['maximgheight']; //1000;
        $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
        //    $img_dir = XOOPS_ROOT_PATH . "/modules/" . $GLOBALS['xoopsModule']->dirname() . "/images" ;
        $img_dir = $GLOBALS['xoopsModuleConfig']['uploaddir'] . '/images';
        include_once $GLOBALS['xoops']->path('class/uploader.php');
        $field = $_POST['xoops_upload_file'][$num];
        if (!empty($field) || $field != '') {
            $uploader = new XoopsMediaUploader($img_dir, $allowed_mimetypes, $max_imgsize, $max_imgwidth, $max_imgheight);
            $uploader->setPrefix('img');
            if ($uploader->fetchMedia($field) && $uploader->upload()) {
                $photo = $uploader->getSavedFileName();
            } else {
                echo $uploader->getErrors();
            }
            static::createThumbs($photo);

            return $photo;
        }
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    public static function createThumbs($filename)
    {/*
    require_once('phpthumb/phpthumb.class.php');
    $thumbnail_widths = array(150, 400);
    foreach ($thumbnail_widths as $thumbnail_width) {
        $phpThumb = new phpThumb();
        // set data
        $phpThumb->setSourceFilename('images/' . $filename);
        $phpThumb->w                    = $thumbnail_width;
        $phpThumb->config_output_format = 'jpeg';
        // generate & output thumbnail
        $output_filename = MYCONFERENCE_UPLOAD_URL . '/thumbnails/' . basename($filename) . '_' . $thumbnail_width . '.' . $phpThumb->config_output_format;
        if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
            if ($output_filename) {
                if ($phpThumb->RenderToFile($output_filename)) {
                    // do something on success
                    //echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
                } else {
                    echo 'Failed (size=' . $thumbnail_width . '):<pre>' . implode("\n\n", $phpThumb->debugmessages) . '</pre>';
                }
            }
        } else {
            echo 'Failed (size=' . $thumbnail_width . '):<pre>' . implode("\n\n", $phpThumb->debugmessages) . '</pre>';
        }
        unset($phpThumb);
    }

    return true;

    */

        // load the image
        require_once $GLOBALS['xoops']->path('modules/' . $GLOBALS['xoopsModule']->dirname() . '/library/Zebra_Image.php');
        $thumbnail_widths = array(150, 400);

        // indicate a target image
        // note that there's no extra property to set in order to specify the target
        // image's type -simply by writing '.jpg' as extension will instruct the script
        // to create a 'jpg' file
        $config_output_format = 'jpeg';

        // create a new instance of the class
        $image = new Zebra_Image();
        // indicate a source image (a GIF, PNG or JPEG file)
        $image->source_path = MYCONFERENCE_UPLOAD_PATH . "/images/{$filename}";

        foreach ($thumbnail_widths as $thumbnail_width) {

            // generate & output thumbnail
            $output_filename    = MYCONFERENCE_UPLOAD_PATH . '/images/thumbnails/' . basename($filename) . "_{$thumbnail_width}.{$config_output_format}";
            $image->target_path = $output_filename;
            // since in this example we're going to have a jpeg file, let's set the output
            // image's quality
            $image->jpeg_quality = 100;
            // some additional properties that can be set
            // read about them in the documentation
            $image->preserve_aspect_ratio  = true;
            $image->enlarge_smaller_images = true;
            $image->preserve_time          = true;

            // resize the image to exactly 100x100 pixels by using the "crop from center" method
            // (read more in the overview section or in the documentation)
            //  and if there is an error, check what the error is about
            if (!$image->resize($thumbnail_width, 0)) {
                // if there was an error, let's see what the error is about
                switch ($image->error) {

                    case 1:
                        echo 'Source file could not be found!';
                        break;
                    case 2:
                        echo 'Source file is not readable!';
                        break;
                    case 3:
                        echo 'Could not write target file!';
                        break;
                    case 4:
                        echo 'Unsupported source file format!';
                        break;
                    case 5:
                        echo 'Unsupported target file format!';
                        break;
                    case 6:
                        echo 'GD library version does not support target file format!';
                        break;
                    case 7:
                        echo 'GD library is not installed!';
                        break;
                    case 8:
                        echo '"chmod" command is disabled via configuration!';
                        break;
                }

                // if no errors
            } else {
                echo 'Success!';
            }

            /*
                    if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
                        if ($output_filename) {
                            if ($phpThumb->RenderToFile($output_filename)) {
                                // do something on success
                                //echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
                            } else {
                                echo 'Failed (size='.$thumbnail_width.'):<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
                            }
                        }
                    } else {
                        echo 'Failed (size='.$thumbnail_width.'):<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
                    }
     */
        }

        unset($image);
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function unHtmlEntities($string)
    {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);

        return strtr($string, $trans_tbl);
    }


    /**
     * Create download by letter choice bar/menu
     * updated starting from this idea http://xoops.org/modules/news/article.php?storyid=6497
     *
     * @param PedigreePedigree $myObject
     * @param                  $activeObject
     * @param                  $criteria
     * @param                  $name
     * @param                  $file
     * @param                  $file2
     * @return string html
     *
     * @access  public
     * @author  luciorota
     */
    public static function lettersChoice($myObject, $activeObject, $criteria, $name, $link, $link2 = null)
    {
        /*
        $pedigree = PedigreePedigree::getInstance();
        xoops_load('XoopsLocal');

        $criteria = $pedigree->getHandler('tree')->getActiveCriteria();
        $criteria->setGroupby('UPPER(LEFT(naam,1))');
        $countsByLetters = $pedigree->getHandler('tree')->getCounts($criteria);
        // Fill alphabet array
        $alphabet       = XoopsLocal::getAlphabet();
        $alphabet_array = array();
        foreach ($alphabet as $letter) {
            $letter_array = array();
            if (isset($countsByLetters[$letter])) {
                $letter_array['letter'] = $letter;
                $letter_array['count']  = $countsByLetters[$letter];
                //            $letter_array['url']    = "" . XOOPS_URL . "/modules/" . $pedigree->getModule()->dirname() . "/viewcat.php?list={$letter}";
                $letter_array['url'] = '' . XOOPS_URL . '/modules/' . $pedigree->getModule()->dirname() . "/result.php?f=naam&amp;l=1&amp;w={$letter}%25&amp;o=naam";
            } else {
                $letter_array['letter'] = $letter;
                $letter_array['count']  = 0;
                $letter_array['url']    = '';
            }
            $alphabet_array[$letter] = $letter_array;
            unset($letter_array);
        }
        // Render output
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            include_once $GLOBALS['xoops']->path('class/theme.php');
            $GLOBALS['xoTheme'] = new xos_opal_Theme();
        }
        require_once $GLOBALS['xoops']->path('class/template.php');
        $letterschoiceTpl          = new XoopsTpl();
        $letterschoiceTpl->caching = false; // Disable cache
        $letterschoiceTpl->assign('alphabet', $alphabet_array);
        $html = $letterschoiceTpl->fetch('db:' . $pedigree->getModule()->dirname() . '_common_letterschoice.tpl');
        unset($letterschoiceTpl);
        return $html;
*/

        //        $pedigree = PedigreePedigree::getInstance();
        //        xoops_load('XoopsLocal');

        $criteria = $myObject->getHandler($activeObject)->getActiveCriteria();
        $criteria->setGroupby('UPPER(LEFT(' . $name . ',1))');
        $countsByLetters = $myObject->getHandler($activeObject)->getCounts($criteria);
        // Fill alphabet array

        //@todo getAlphabet method doesn't exist anywhere
        //$alphabet       = XoopsLocal::getAlphabet();
        
//        xoops_load('XoopsLocal');
//        $xLocale        = new XoopsLocal;
//        $alphabet       = $xLocale->getAlphabet();
        $alphabet       = pedigreeGetAlphabet();
        $alphabet_array = array();
        foreach ($alphabet as $letter) {
            /*
                        if (isset($countsByLetters[$letter])) {
                            $letter_array['letter'] = $letter;
                            $letter_array['count']  = $countsByLetters[$letter];
                            //            $letter_array['url']    = "" . XOOPS_URL . "/modules/" . $pedigree->getModule()->dirname() . "/viewcat.php?list={$letter}";
                            //                $letter_array['url'] = '' . XOOPS_URL . '/modules/' . $myObject->getModule()->dirname() . '/'.$file.'?f='.$name."&amp;l=1&amp;w={$letter}%25&amp;o=".$name;
                            $letter_array['url'] = '' . XOOPS_URL . '/modules/' . $myObject->getModule()->dirname() . '/' . $file2;
                        } else {
                            $letter_array['letter'] = $letter;
                            $letter_array['count']  = 0;
                            $letter_array['url']    = '';
                        }
                        $alphabet_array[$letter] = $letter_array;
                        unset($letter_array);
                    }


                            $alphabet_array = array();
                            //        foreach ($alphabet as $letter) {
                            foreach (range('A', 'Z') as $letter) {
            */
            $letter_array = array();
            if (isset($countsByLetters[$letter])) {
                $letter_array['letter'] = $letter;
                $letter_array['count']  = $countsByLetters[$letter];
                //            $letter_array['url']    = "" . XOOPS_URL . "/modules/" . $pedigree->getModule()->dirname() . "/viewcat.php?list={$letter}";
                //                $letter_array['url'] = '' . XOOPS_URL . '/modules/' . $myObject->getModule()->dirname() . '/'.$file.'?f='.$name."&amp;l=1&amp;w={$letter}%25&amp;o=".$name;
                $letter_array['url'] = '' . XOOPS_URL . '/modules/' . $myObject->getModule()->dirname() . '/' . $link . $letter . $link2;
            } else {
                $letter_array['letter'] = $letter;
                $letter_array['count']  = 0;
                $letter_array['url']    = '';
            }
            $alphabet_array[$letter] = $letter_array;
            unset($letter_array);
        }

        // Render output
        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            include_once $GLOBALS['xoops']->path('class/theme.php');
            $GLOBALS['xoTheme'] = new xos_opal_Theme();
        }
        require_once $GLOBALS['xoops']->path('class/template.php');
        $letterschoiceTpl          = new XoopsTpl();
        $letterschoiceTpl->caching = false; // Disable cache
        $letterschoiceTpl->assign('alphabet', $alphabet_array);
        $html = $letterschoiceTpl->fetch('db:' . $myObject->getModule()->dirname() . '_common_letterschoice.tpl');
        unset($letterschoiceTpl);
        return $html;
    }


    public static function getXoopsCpHeader()
    {
        xoops_cp_header();
    }

    /**
     * Detemines if a table exists in the current db
     *
     * @param string $table the table name (without XOOPS prefix)
     *
     * @return bool True if table exists, false if not
     *
     * @access public
     * @author xhelp development team
     */
    public static function hasTable($table)
    {
        $bRetVal = false;
        //Verifies that a MySQL table exists
        $GLOBALS['xoopsDB'] = XoopsDatabaseFactory::getDatabaseConnection();
        $realName           = $GLOBALS['xoopsDB']->prefix($table);

        $sql = 'SHOW TABLES FROM ' . XOOPS_DB_NAME;
        $ret = $GLOBALS['xoopsDB']->queryF($sql);

        while (false !== (list($m_table) = $GLOBALS['xoopsDB']->fetchRow($ret))) {
            if ($m_table == $realName) {
                $bRetVal = true;
                break;
            }
        }
        $GLOBALS['xoopsDB']->freeRecordSet($ret);

        return $bRetVal;
    }


    /**
     * @param     $name
     * @param     $value
     * @param int $time
     */
    public static function setCookieVar($name, $value, $time = 0)
    {
        if ($time == 0) {
            $time = time() + 3600 * 24 * 365;
            //$time = '';
        }
        setcookie($name, $value, $time, '/');
    }

    /**
     * @param        $name
     * @param string $default
     *
     * @return string
     */
    public static function getCookieVar($name, $default = '')
    {
        if (isset($_COOKIE[$name]) && ($_COOKIE[$name] > '')) {
            return $_COOKIE[$name];
        } else {
            return $default;
        }
    }

    /**
     * @return array
     */
    public static function getCurrentUrls()
    {
        $http        = (strpos(XOOPS_URL, 'https://') === false) ? 'http://' : 'https://';
        $phpSelf     = $_SERVER['PHP_SELF'];
        $httpHost    = $_SERVER['HTTP_HOST'];
        $queryString = $_SERVER['QUERY_STRING'];

        if ($queryString != '') {
            $queryString = '?' . $queryString;
        }

        $currentURL = $http . $httpHost . $phpSelf . $queryString;

        $urls                = array();
        $urls['http']        = $http;
        $urls['httphost']    = $httpHost;
        $urls['phpself']     = $phpSelf;
        $urls['querystring'] = $queryString;
        $urls['full']        = $currentURL;

        return $urls;
    }

    /**
     * @return mixed
     */
    public static function getCurrentPage()
    {
        $urls = static::getCurrentUrls();

        return $urls['full'];
    }

    /**
     * @param array $errors
     *
     * @return string
     */
    public static function formatErrors($errors = array())
    {
        $ret = '';
        foreach ($errors as $key => $value) {
            $ret .= "<br> - {$value}";
        }

        return $ret;
    }

}
