<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2014 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    shoutbox
 * @license    LGPL
 * @filesource
 */


$GLOBALS['TL_DCA']['tl_shoutbox'] = array(

// Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'closed'                      => false,
        'ctable'                      => array('tl_shoutbox_entries'),
        'notEditable'                 => false,
        'sql' => array(
            'keys' => array('id' => 'primary')
        )
    ),


// List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('title ASC'),
            'flag'                    => 1,
            'panelLayout'             => 'filter, search, limit'
        ),
        'label' => array
        (
            'fields'                  => array('title', 'email'),
            'showColumns'             => true,
        ),


        'operations' => array
        (
            'entries' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox']['entries'],
                'href'                => 'table=tl_shoutbox_entries',
                'icon'                => 'tablewizard.gif'
            ),
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_shoutbox']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            )
        )

    ),


// Palettes
    'palettes' => array
    (
        'default'                     => '{title_legend}, title, email'
    ),

// Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ),
        'title' => array
        (
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox']['title'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
            'sql'                     => "varchar(255) NOT NULL default ''",
        ),

        'email' => array
        (
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox']['email'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => false,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=> email, 'mandatory'=>false, 'maxlength'=>255, 'tl_class' => 'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''",
        ),

    ) //fields

);

class tl_shoutbox extends Backend {

    public function __construct() {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function labelCallback($row, $label, DataContainer $dc, $args = null) {
        if ($args === null) {
            return $label;
        }
        return $args;
    }
}





