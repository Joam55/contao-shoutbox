<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 *
 * PHP version 5
 * @copyright  2011-2012, Martin Kozianka <http://kozianka-online.de/>
 * @author     Martin Kozianka <http://kozianka-online.de/>
 * @package    shoutbox
 * @license    LGPL
 */


/**
 * Class ShoutboxRunonce
 */
class ShoutboxRunonce extends Frontend {

	public function __construct() {
		parent::__construct();
		$this->import('Automator');
	}

	public function run() {
		$this->Automator->purgeScriptsFolder();
	}
}

// Soll nur ab Contao 2.10 ausgeführt werden
if (version_compare(VERSION . '.' . BUILD, '2.10', '>')) {

	$objShoutboxRunonce = new ShoutboxRunonce();
	$objShoutboxRunonce->run();
}

?>