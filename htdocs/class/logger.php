<?php
/**
 * XOOPS legacy logger
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          logger
 * @since               2.0.0
 * @version             $Id: logger.php 13082 2015-06-06 21:59:41Z beckmi $
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * this file is for backward compatibility only
 * @package    kernel
 * @subpackage logger
 **/
/**
 * Load the new XoopsLogger class
 **/
require_once $GLOBALS['xoops']->path('class/logger/xoopslogger.php');
trigger_error("Instance of " . __FILE__ . " file is deprecated, check 'XoopsLogger' in class/logger/xoopslogger.php");
