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


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Shoutbox
	'Shoutbox'         => 'system/modules/shoutbox/Shoutbox.php',
	'ShoutboxCallback' => 'system/modules/shoutbox/ShoutboxCallback.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'com_shoutbox' => 'system/modules/shoutbox/templates',
	'mod_shoutbox' => 'system/modules/shoutbox/templates',
));
