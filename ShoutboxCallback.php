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
		
		if ($arrSet['source'] !== 'Shoutbox') {
			return;	
		}

		$this->notifiy($insertId, $arrSet);
		
		if ($this->Input->get('shoutbox_ajax') === 'true') {
			
			header('HTTP/1.0 200 OK');
			header('Content-type: application/json');

			$json = new stdClass();
			$json->token = REQUEST_TOKEN;
			$json->result = 'ready';
			echo json_encode($json);
			exit;
		}
		
		$this->redirect($this->addToUrl(''));
	}

	private function notifiy($insertId, $arrSet) {

		// Check if notification is activated
		$result = $this->Database->prepare("SELECT shoutbox_notification"
				." FROM tl_module WHERE shoutbox_id = ? AND type = ?")
				->execute($arrSet['parent'], 'shoutbox');
		if($result->numRows) {
			if ($result->shoutbox_notification != '1') {
				return false;
			}
		}
				
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
		return true;
	}
	
}

?>