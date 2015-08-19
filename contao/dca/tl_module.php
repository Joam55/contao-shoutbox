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

$GLOBALS['TL_DCA']['tl_module']['palettes']['shoutbox'] = '{title_legend},name,headline,type;'
.'{shoutbox_legend},shoutbox_id, shoutbox_entries';

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_id'] = [
	'label' 		=> &$GLOBALS['TL_LANG']['tl_module']['shoutbox_id'],
	'exclude'		=> true,
	'inputType'		=> 'select',
    'foreignKey'    => 'tl_shoutbox.title',
	'eval'			=> ['mandatory' => true, 'tl_class' => 'w50'],
	'sql'           => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_entries'] = [
	'label'         => &$GLOBALS['TL_LANG']['tl_module']['shoutbox_entries'],
	'exclude'       => true,
	'inputType'		=> 'select',
	'options'		=> [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70],
	'default'		=> 25,
	'eval'          => ['mandatory' => true, 'tl_class'=>'w50'],
	'sql'           => "smallint(5) unsigned NOT NULL default '15'"
];