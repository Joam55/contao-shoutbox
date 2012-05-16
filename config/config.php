<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2012 <http://kozianka-online.de/>
 * @author     Martin Kozianka <http://kozianka-online.de/>
 * @package    Shoutbox 
 * @license    LGPL 
 * @filesource
 */

$GLOBALS['FE_MOD']['miscellaneous']['shoutbox'] = 'Shoutbox';

$GLOBALS['TL_HOOKS']['addComment'][] = array("ShoutboxCallback", "hookAddComment");

