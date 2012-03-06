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
 * Class ShoutboxCallback 
 *
 * @copyright  Martin Kozianka 2011-2012 <http://kozianka-online.de/>
 * @author     Martin Kozianka <http://kozianka-online.de/> 
 * @package    Controller
 */
class ShoutboxCallback extends Frontend {

	public function __construct() {
		parent::__construct();
		$this->import('String');
	}
	
	
	public function hookAddComment($insertId, $arrSet) {
		$json = new stdClass();
		$json->token = REQUEST_TOKEN;
		$json->result = 'ready';
		
		$this->notifiy($insertId, $arrSet);
		echo json_encode($json);
		exit;
	}

	private function notifiy($insertId, $arrSet) {
		
		$strComment = $arrSet['comment'];

		$objEmail = new Email();
		$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['com_subject'], $this->Environment->host);
		
		// Convert the comment to plain text
		$strComment = strip_tags($strComment);
		$strComment = $this->String->decodeEntities($strComment);
		$strComment = str_replace(array('[&]', '[lt]', '[gt]'), array('&', '<', '>'), $strComment);
		
		// Add comment details
		$objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['com_message'],
				$arrSet['name'] . ' (' . $arrSet['email'] . ')',
				$strComment,
				$this->Environment->base . $this->Environment->request,
				$this->Environment->base . 'contao/main.php?do=comments&act=edit&id=' . $insertId);
		

		// TODO Könnte man noch konfigurieren
		$objEmail->sendTo($GLOBALS['TL_ADMIN_EMAIL']);
	}
	
}

?>