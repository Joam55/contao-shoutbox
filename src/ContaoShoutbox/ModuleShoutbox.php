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
namespace ContaoShoutbox;

/**
 * Class Shoutbox 
 *
 * @copyright  Martin Kozianka 2011-2015 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/> 
 * @package    Controller
 */
class ModuleShoutbox extends \Module {
    static $emojionePath       = 'composer/vendor/emojione/emojione/';

    private $lockInSeconds     = 10;
    private $loggedIn          = false;
	private $isAjax            = null;
    private $message           = '';
	protected $strTemplate     = 'mod_shoutbox';
    protected $entryTemplate   = 'shoutbox_entry';


    private function getEntries() {

        $result = $this->Database->prepare("SELECT tl_shoutbox_entries.*, "
            ."tl_member.username AS username, CONCAT(tl_member.firstname, ' ', tl_member.lastname) AS fullname"
            ." FROM tl_shoutbox_entries, tl_member"
            ." WHERE pid = ? AND tl_shoutbox_entries.member = tl_member.id"
            ." ORDER BY datim DESC")
            ->limit($this->shoutbox_entries)->execute($this->shoutbox_id);
        $strContent = "";

        $i          = 0;
        $objPartial = new \FrontendTemplate($this->entryTemplate);
        while($result->next()) {
            $row              = $result->row();
            $format           = $GLOBALS['TL_CONFIG']['datimFormat'];
            $row['date']      = \Date::parse($format, $row['datim']);
            $row['timesince'] = $this->timesince($row['datim']);
            $row['cssClass']  = 'entry '.(($i++ % 2 == 0) ? 'even': 'odd');

            $objPartial->setData($row);
            $strContent .= $objPartial->parse();
        }
        $strContent = $this->emoticon_replacer($strContent);
        $strContent = $this->replaceInsertTags($strContent);
        return $strContent;
    }

    protected function compile() {
        global $objPage;
        $this->import('FrontendUser', 'User');
        $this->import('Comments');

        $this->isAjax   = \Environment::get('isAjaxRequest');
        $this->loggedIn = FE_USER_LOGGED_IN;

        if (\Input::get('shoutbox_action') === 'update' && $this->isAjax) {
            $this->output($this->getEntries());
        }

        // Keine Posten wenn man nicht eingeloggt ist!
        if (!$this->loggedIn && \Input::post('shoutbox_action') === 'shout' && $this->isAjax) {
            $jsonObj              = new \stdClass();
            $jsonObj->token       = REQUEST_TOKEN;
            $jsonObj->message     = $GLOBALS['TL_LANG']['FMD']['shoutbox_no_access'];
            $this->output(json_encode($jsonObj), true);
        }

        if (\Input::post('shoutbox_action') === 'shout' && $this->loggedIn) {
            $addedEntry = $this->addEntry();
            if ($this->isAjax) {
                $jsonObj              = new \stdClass();
                $jsonObj->token       = REQUEST_TOKEN;
                $jsonObj->entriesHtml = $this->getEntries();
                $jsonObj->addedEntry  = $addedEntry;
                $jsonObj->message     = $this->message;
                $this->output(json_encode($jsonObj), true);
            }
            // TODO Redirect um POST data zu entfernen
		}

        // iscroll
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/shoutbox/assets/iscroll/iscroll.js|static';

        // textcomplete
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/shoutbox/assets/textcomplete/jquery.textcomplete.js|static';
        $GLOBALS['TL_CSS'][]        = 'system/modules/shoutbox/assets/textcomplete/jquery.textcomplete.css||static';

        // dropdown menu
        $GLOBALS['TL_JAVASCRIPT'][] = $this->createEmojiStrategy().'|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/shoutbox/assets/shoutbox-dropdown.js|static';
        $GLOBALS['TL_CSS'][]        = 'system/modules/shoutbox/assets/shoutbox-dropdown.css||static';

        // shoutbox
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/shoutbox/assets/shoutbox.js|static';
        $GLOBALS['TL_CSS'][]        = 'system/modules/shoutbox/assets/shoutbox.css||static';

        $GLOBALS['TL_CSS'][]        = 'system/modules/shoutbox/assets/fontello/css/fontello.css||static';

        $this->Template->shoutboxCssId  = "shoutbox_".$this->shoutbox_id;
        $this->Template->jsObj          = "Shoutbox".$this->shoutbox_id;

        $this->Template->action         = \Environment::get('indexFreeRequest');
        $this->Template->loggedIn       = $this->loggedIn;
        $this->Template->hasJavascript  = $objPage->hasJQuery;

        $this->Template->message        = $this->message;
        $this->Template->entries        = $this->getEntries();

        $GLOBALS['TL_BODY'][] = "
            <script>
                var ".$this->Template->jsObj." = new Shoutbox('".$this->Template->shoutboxCssId."');
            </script>
        ";


	}


    private function addEntry() {
        $now    = time();
        $result = $this->Database->prepare('SELECT tstamp FROM tl_shoutbox_entries'
            .' WHERE member = ? ORDER BY tstamp DESC')->limit(1)->execute($this->User->id);

        if ($result->numRows == 1) {
            $diff = $result->tstamp + $this->lockInSeconds - $now;
            if ($diff > 0) {
                $this->message = sprintf($GLOBALS['TL_LANG']['FMD']['shoutbox_locked_message'], $diff);
                return false;
            }
        }

        $entry  = $this->parseEntry(\Input::post('shoutbox_entry'), true);
        $sql    = "INSERT INTO tl_shoutbox_entries (pid, tstamp, member, datim, entry) VALUES(?, ?, ?, ?, ?)";
        $result = $this->Database->prepare($sql)->execute($this->shoutbox_id, $now, $this->User->id, $now, $entry);

        $this->notifiy($result->insertId);
        return true;
    }

    private function parseEntry($entry) {

        // Convert links
        $entry  = preg_replace_callback('/(((http(s)?\:\/\/)|(www\.))([^\s]+[^\.\s]+))/', function ($arr) {
                $host     = parse_url($arr[0], PHP_URL_HOST);
                $host     = (strpos($host, 'www.') === 0) ? str_replace('www.', '', $host) : $host;

                return sprintf('
                            <a target="_blank" href="%s" title="%s"><img class="emojione" src="%s"></a>',
                    $arr[0], $arr[0], static::emoticonCallback(array('', '2197'))
                );
        }, $entry);

        $entry  = preg_replace('@\n\n+@', "\n\n", $entry);

        $entry  = $this->Comments->parseBbCode($entry);
        $entry  = $this->Comments->convertLineFeeds($entry);
        return $entry;
    }

    private function emoticon_replacer($input) {
        $strReplace   = '___REPLACE___';
        $arrEmoticons = array(":-&#41;", ":&#41;", ";-&#41;", ";&#41;", ":-&#40;", ":&#40;");
        $arrEmojione  = array(":smiley:",":smiley:", ":wink:", ":wink:", ":disappointed:", ":disappointed:");
        $input        = str_replace($arrEmoticons, $arrEmojione, $input);

        \Emojione\Emojione::$cacheBustParam = '';
        \Emojione\Emojione::$imagePathPNG   = $strReplace;

        $content = \Emojione\Emojione::shortnameToImage($input);
        $content = preg_replace_callback('/'.$strReplace.'(.*)'.'\.png/Si', 'static::emoticonCallback', $content);

        return $content;
    }

    static function emoticonCallback($m) {

        if((!is_array($m)) || (!isset($m[1])) || (empty($m[1]))) {
            return $m[0];
        }
        $path   = static::$emojionePath.'assets/png/'.$m[1].'.png';
        $objImg = \Image::create($path);
        return \Image::get($path, '64', '64', '', $objImg->getCacheName(), true);
    }

    private function createEmojiStrategy() {
        $strPath     = 'assets/js/shoutbox_emoji_strategy.js';
        $strSource   = static::$emojionePath.'emoji_strategy.json';

        if (!file_exists($strPath) && is_readable($strSource)) {
            $contentTmpl       = "var emojiStrategy = %s;";
            $jsonEmojiStrategy = file_get_contents($strSource);

            $file = new \File($strPath);
            $file->write(sprintf($contentTmpl, $jsonEmojiStrategy));
            $file->close();
        }
        return $strPath;
    }

    private function notifiy($insertId) {
        $result = $this->Database->prepare('SELECT
            tl_shoutbox_entries.*,
            tl_shoutbox.email AS email,
            tl_member.username AS username,
            tl_member.email AS useremail
            FROM tl_shoutbox_entries, tl_shoutbox, tl_member
            WHERE tl_shoutbox_entries.id = ?
            AND tl_shoutbox_entries.member = tl_member.id
            AND tl_shoutbox_entries.pid = tl_shoutbox.id')->execute($insertId);
        if($result->numRows != 1) {
            return false;
        }
        $data = (Object) $result->row();

        if (!\Validator::isEmail($data->email)) {
            return false;
        }

        // Convert the comment to plain text
        $strComment = strip_tags($data->entry);
        $strComment = \String::decodeEntities($strComment);
        $strComment = str_replace(array('[&]', '[lt]', '[gt]'), array('&', '<', '>'), $strComment);

        $objEmail           = new \Email();
        $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
        $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
        $objEmail->subject  = "New shoutbox entry from ".$data->username.' ('.$data->useremail.')';

        // Add comment details
        $objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['com_message'],
            $data->username . ' (' . $data->useremail . ')',
            $strComment,
            \Environment::get('base'). \Environment::get('request'),
            \Environment::get('base'). 'contao/main.php?do=shoutbox&table=tl_shoutbox_entries&id='.$data->pid
        );

        $objEmail->sendTo($data->email);
        return true;
    }


    private function output($content, $jsonHeader = false) {
        header('HTTP/1.0 200 OK');
        if ($jsonHeader) {
            header('Content-type: application/json');
        }
        echo $content;
        exit;
    }

    public function timesince($timestamp) {
        $diff       = time() - $timestamp;
        $lengths    = array("60","60","24","7","4.35","12","10");

        for($j = 0; $diff >= $lengths[$j] && $j < sizeof($lengths); $j++) {
            $diff /= $lengths[$j];
        }

        $format       = &$GLOBALS['TL_LANG']['FMD']['shoutbox_timesince_format'];
        $langSingular = &$GLOBALS['TL_LANG']['FMD']['shoutbox_timesince'];
        $langPlural   = &$GLOBALS['TL_LANG']['FMD']['shoutbox_timesince_plural'];
        $diff         = round($diff);
        $period       = ($diff == 1) ? $langSingular[$j] : $langPlural[$j];

        return sprintf($format, $diff.' '.$period);
    }

}

