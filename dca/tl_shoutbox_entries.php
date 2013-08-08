<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    shoutbox
 * @license    LGPL
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_shoutbox_entries'] = array(

// Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'closed'                      => true,
        'notEditable'                 => false,
        'ptable'                      => 'tl_shoutbox',
        'sql' => array(
            'keys' => array
            (
                'id'  => 'primary',
                'pid' => 'index'
            )
        )
    ),


// List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('datim DESC'),
            'flag'                    => 1,
            'panelLayout'             => 'filter, search, limit',
        ),
        'label' => array
        (
            'fields'                  => array('datim', 'member', 'entry'),
            'showColumns'             => true,
            'label_callback'          => array('tl_shoutbox_entries', 'labelCallback')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox_entries']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif',
                'attributes'          => 'class="contextmenu"'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox_entries']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_shoutbox_entries']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            )
        )

    ),


// Palettes
    'palettes' => array
    (
        'default'                     => '{title_legend}, datim, member, entry'
    ),

// Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'foreignKey'              => 'tl_shoutbox.title',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ),
        'member' => array
        (
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox_entries']['member'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'foreignKey'              => 'tl_member.username',
            'inputType'               => 'select',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ),
        'datim' => array
        (
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox_entries']['datim'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 8,
            'inputType'               => 'text',
            'eval'                    => array('mandatory' => true, 'datepicker'=>true, 'rgxp'=>'datim', 'tl_class'=>'w50 wizard'),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'entry' => array
        (
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox_entries']['entry'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'textarea',
            'eval'                    => array('mandatory'=>true, 'style'=> 'height:80px;', 'allowHtml' => true),
            'sql'                     => "text NULL",
        ),


    ) //fields

);

class tl_shoutbox_entries extends Backend {
    private $memberArray     = array();

    public function __construct() {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function labelCallback($row, $label, DataContainer $dc, $args = null) {
        $this->fillMemberCache($row['pid']);
        $memberID = $row['member'];
        $args[1]  = $this->memberArray[$memberID];
        return $args;
    }

    private function fillMemberCache($sbID) {
        $result = $this->Database->prepare('SELECT id, username FROM tl_member'
                .' WHERE id IN (SELECT member FROM tl_shoutbox_entries WHERE pid = ?)'
        )->execute($sbID);

        while($result->next()) {
            $row                    = $result->row();
            $id                     = $row['id'];
            $this->memberArray[$id] = $row['username'];
        }
        return true;
    }
}





