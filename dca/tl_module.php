<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2012 <http://kozianka-online.de/>
 * @author     Martin Kozianka <http://kozianka-online.de/> 
 * @package    Shoutbox 
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Front end modules
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['shoutbox'] = '{title_legend},name,headline,type;{shoutbox},shoutbox_id,shoutbox_entries,shoutbox_rows,shoutbox_cols';

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_id'] = array(
	'label' 		=> &$GLOBALS['TL_LANG']['FMD']['shoutbox_id'],
	'exclude'		=> true,
	'inputType'		=> 'select',
	'options'		=> array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
	'eval'			=> array('mandatory' => true, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_entries'] = array(
	'label'         => &$GLOBALS['TL_LANG']['FMD']['shoutbox_entries'],
	'exclude'       => true,
	'inputType'		=> 'select',
	'options'		=> array(10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70),
	'default'		=> 25,
	'eval'          => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_rows'] = array(
	'label' 		=> &$GLOBALS['TL_LANG']['FMD']['shoutbox_rows'],
	'exclude'		=> true,
	'inputType'		=> 'select',
	'options'		=> array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
	'default'		=> 3,
	'eval'			=> array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['shoutbox_cols'] = array(
	'label' 		=> &$GLOBALS['TL_LANG']['FMD']['shoutbox_cols'],
	'exclude'		=> true,	
	'inputType'		=> 'select',
	'options'		=> array(10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70),
	'default'		=> 25,
	'eval'			=> array('tl_class' => 'w50')
);

?>