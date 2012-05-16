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
 * Class Shoutbox 
 *
 * @copyright  Martin Kozianka 2011-2012 <http://kozianka-online.de/>
 * @author     Martin Kozianka <http://kozianka-online.de/> 
 * @package    Controller
 */
class Shoutbox extends Module {
	private $versionStr = '?v=0.9.5';
	private $loggedIn   = false;
	private $objConfig  = null;
	
	protected $strTemplate = 'mod_shoutbox';
	protected $com_template = 'com_shoutbox';
	
	private function parseComment($comment) {
		$img = '[img]'.$this->Environment->base.'/system/modules/shoutbox/html/link.png[/img]';
		$comment = preg_replace('/(((http(s)?\:\/\/)|(www\.))([^\s]+[^\.\s]+))/', '[url=http$4://$5$6] '.$img.' [/url]', $comment);
		return $comment;
	}

	private function emoticon_replacer($input) {
		$emoticons = array(":-)",";-(", ";-)", "]:-|", ":-(|)", ":o", ":)", ":(", ";)", "8)", "*JOKE*",
				":'(", ":|", ":-*", "*angel*");
		$emoticons_spans = array(
			'<span title=":-)" class="emoticon emoticon-1"></span>',
			'<span title=":-(" class="emoticon emoticon-2"></span>',			
			'<span title=";-)" class="emoticon emoticon-3"></span>',			
			'<span title="]:-|" class="emoticon emoticon-10"></span>',
			'<span title=":-(|)" class="emoticon emoticon-11"></span>','<span title=":o" class="emoticon emoticon-12"></span>',
			'<span title=":)" class="emoticon emoticon-1"></span>', '<span title=":(" class="emoticon emoticon-2"></span>',
			'<span title=";)" class="emoticon emoticon-3"></span>', '<span title="8)" class="emoticon emoticon-4"></span>',		
			'<span title="*JOKE*" class="emoticon emoticon-5"></span>', '<span title=":\'(" class="emoticon emoticon-6"></span>',
			'<span title=":|" class="emoticon emoticon-7"></span>', '<span title=":-*" class="emoticon emoticon-8"></span>',		
			'<span title="*angel*" class="emoticon emoticon-9"></span>', 		
		);
		return str_replace($emoticons, $emoticons_spans, $input);
	}

	private function getNewEntries($date) {
		
		$result = $this->Database->prepare("SELECT * FROM tl_comments WHERE source = ? AND parent = ? ORDER BY date DESC")
			->limit($this->shoutbox_entries)->execute("Shoutbox", $this->shoutbox_id, $date);

		$strContent = "";
		$objPartial = new FrontendTemplate($this->com_template);	
		while($result->next()) {
			$row = $result->row();
			
			$row['timestamp'] = $row['date']; 
			$objPartial->setData($row);
			$strContent .= $objPartial->parse();
		}
		$strContent = $this->replaceInsertTags($strContent);
		$strContent = $this->emoticon_replacer($strContent);
		return $strContent;
	}	
			
	protected function compile() {
		$this->import('FrontendUser', 'User');
		$this->loggedIn = FE_USER_LOGGED_IN;

		// TODO: Sperre für ein paar Minuten
		// TODO: Realer Name oder Benutzername
		
		$action  = $this->Input->get('shoutbox_action');
		if (strlen($action) === 0) {
			$action  = $this->Input->post('shoutbox_action');
		}
		
		
		
		$sb_ajax = ($this->Input->get('shoutbox_ajax') === 'true');

		if ($action === 'update') {
			$date = intval($this->Input->get('shoutbox_date'));
			$new_entries = $this->getNewEntries($date);
			$this->output($new_entries);
		}

		if ($action === 'shout' && $this->loggedIn) {
			$_POST['name']  = $this->User->username;
			$_POST['email'] = $this->User->email;
			$_POST['comment'] = $this->parseComment($_POST['comment']);
		}

		$this->import('Comments');
		
		$this->Comments->addCommentsToTemplate(
			$this->Template,
			$this->getCommentConfigObj(),
			'Shoutbox',
			$this->shoutbox_id,
			array($GLOBALS['TL_ADMIN_EMAIL'])
		);
		
		
		// Return JSON String for Contao 2.9.x
		if ($sb_ajax && $action === 'shout') {
			$json = new stdClass();
			$json->result = 'ready';
			$this->output(json_encode($json), true);	
		}
		
		
		$GLOBALS['TL_CSS'][] = 'system/modules/shoutbox/html/shoutbox.css'.$this->versionStr;
		$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/shoutbox/html/shoutbox.js'.$this->versionStr;
		
		$this->Template->action = $this->getIndexFreeRequest();
		$this->Template->loggedIn = $this->loggedIn;
		$this->Template->comments = $this->emoticon_replacer($this->Template->comments);
		
	}

	function getCommentConfigObj() {
		$objConfig = new stdClass();
		$objConfig->perPage        = $this->shoutbox_entries;
		$objConfig->template       = 'com_shoutbox';
		$objConfig->order          = 'descending';
		$objConfig->requireLogin   = true;
		$objConfig->disableCaptcha = true;
		$objConfig->bbcode         = true;
		$objConfig->moderate       = false;
		return $objConfig;
	}

	private function output($content, $isAjax = false) {
		header('HTTP/1.0 200 OK');
		if ($isAjax) {
			header('Content-type: application/json');
		}
		echo $content;
		exit;
	}
}

?>