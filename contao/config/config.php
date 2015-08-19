<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2015 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2015 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    Shoutbox 
 * @license    LGPL 
 * @filesource
 */

$GLOBALS['FE_MOD']['miscellaneous']['shoutbox'] = 'ContaoShoutbox\ModuleShoutbox';
$GLOBALS['BE_MOD']['content']['shoutbox']       = [
    'tables'     => ['tl_shoutbox', 'tl_shoutbox_entries'],
    'icon'       => 'system/modules/shoutbox/assets/megaphone.png',
];
