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
 * Class ShoutboxCallback 
 *
 * @copyright  Martin Kozianka 2011-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/> 
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


}

