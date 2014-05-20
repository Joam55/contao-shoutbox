<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    Shoutbox 
 * @license    LGPL 
 * @filesource
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'Shoutbox'         => 'system/modules/shoutbox/modules/Shoutbox.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'shoutbox_entry'   => 'system/modules/shoutbox/templates',
    'mod_shoutbox'     => 'system/modules/shoutbox/templates',
));
