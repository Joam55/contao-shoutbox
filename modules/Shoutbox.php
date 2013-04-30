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
 * Class Shoutbox 
 *
 * @copyright  Martin Kozianka 2011-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/> 
 * @package    Controller
 */

// TODO: Sperre für ein paar Minuten
// TODO: Realer Name oder Benutzername
// TODO: Ordner für die Link-Icons (Datei generieren wenn Sie nicht existiert.)

class Shoutbox extends Module {
	private $loggedIn        = false;
	private $objConfig       = null;
	protected $strTemplate   = 'mod_shoutbox';
	protected $entryTemplate = 'shoutbox_entry';

    private function getEntries() {
        $result = $this->Database->prepare("SELECT * FROM tl_shoutbox_entries WHERE pid = ? ORDER BY datim DESC")
            ->limit($this->shoutbox_entries)->execute($this->shoutbox_id);
        $strContent = "";
        $objPartial = new FrontendTemplate($this->entryTemplate);
        while($result->next()) {
            $row = $result->row();
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

        $action  = Input::get('shoutbox_action');

        if ($action === 'update') {
            $new_entries = $this->getNewEntries();
            $this->output($new_entries);
        }

		if ($action === 'shout' && $this->loggedIn) {
            $this->addEntry();
            // AJAX Request?
		}

        $GLOBALS['TL_CSS'][] 		 = 'system/modules/shoutbox/assets/shoutbox.css|all,screen|static';
        $GLOBALS['TL_JAVASCRIPT'][]  = 'system/modules/shoutbox/assets/shoutbox.js';

        $this->Template->action      = Environment::get('indexFreeRequest');
        $this->Template->loggedIn    = $this->loggedIn;
        $this->template->entries     = $this->getEntries();
	}

	private function output($content, $jsonHeader = false) {
		header('HTTP/1.0 200 OK');
		if ($jsonHeader) {
			header('Content-type: application/json');
		}
		echo $content;
		exit;
	}


    private function addEntry() {
        $entry = $this->parseEntry(Input::post('shoutbox_entry'));
    }


    private function parseEntry($entry) {
        $img   = '[img]'.$this->Environment->base.'/system/modules/shoutbox/assets/link.png[/img]';
        $entry = preg_replace('/(((http(s)?\:\/\/)|(www\.))([^\s]+[^\.\s]+))/', '[url=http$4://$5$6] '.$img.' [/url]', $entry);
        return $entry;
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

        // TODO Sollte die Adresse aus der Rootseite sein
        // TODO Könnte man noch konfigurieren
        $objEmail->sendTo($GLOBALS['TL_ADMIN_EMAIL']);
        return true;
    }

}

