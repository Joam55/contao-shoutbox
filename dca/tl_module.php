<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2012 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    Shoutbox 
 * @license    LGPL 
 * @filesource
 */


/**
 * Front end modules
 */

/* TODO SQL Attributes
 ----------------------------------------------------------------
`shoutbox_id` 
`shoutbox_entries` 
`shoutbox_rows` 
`shoutbox_cols` 
`shoutbox_notification` 
----------------------------------------------------------------
*/

$GLOBALS['TL_DCA']['tl_module']['palettes']['shoutbox'] = '{title_legend},name,headline,type;'
.'{shoutbox_legend},shoutbox_id,shoutbox_entries,shoutbox_rows,shoutbox_cols,shoutbox_notification';

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_id'] = array(
	'label' 		=> &$GLOBALS['TL_LANG']['tl_module']['shoutbox_id'],
	'exclude'		=> true,
	'inputType'		=> 'select',
	'options'		=> array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
	'eval'			=> array('mandatory' => true, 'tl_class' => 'w50'),
	'sql'           => "smallint(5) unsigned NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_entries'] = array(
	'label'         => &$GLOBALS['TL_LANG']['tl_module']['shoutbox_entries'],
	'exclude'       => true,
	'inputType'		=> 'select',
	'options'		=> array(10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70),
	'default'		=> 25,
	'eval'          => array('tl_class'=>'w50'),
	'sql'           => "smallint(5) unsigned NOT NULL default '15'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_rows'] = array(
		'label' 		=> &$GLOBALS['TL_LANG']['tl_module']['shoutbox_rows'],
		'exclude'		=> true,
		'inputType'		=> 'select',
		'options'		=> array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
		'default'		=> 3,
		'eval'			=> array('tl_class' => 'w50'),
		'sql'           => "smallint(5) unsigned NOT NULL default '3'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_cols'] = array(
	'label' 		=> &$GLOBALS['TL_LANG']['tl_module']['shoutbox_cols'],
	'exclude'		=> true,	
	'inputType'		=> 'select',
	'options'		=> array(10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70),
	'default'		=> 25,
	'eval'			=> array('tl_class' => 'w50'),
	'sql'           => "smallint(5) unsigned NOT NULL default '25'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_notification'] = array(
		'label' 		=> &$GLOBALS['TL_LANG']['tl_module']['shoutbox_notification'],
		'exclude'		=> true,
		'inputType'		=> 'checkbox',
		'default'		=> true,
		'eval'			=> array('tl_class' => 'w50'),
		'sql'           => "char(1) NOT NULL default ''"
);

