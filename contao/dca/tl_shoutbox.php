<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2015 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2011-2015 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    shoutbox
 * @license    LGPL
 * @filesource
 */


$GLOBALS['TL_DCA']['tl_shoutbox'] = [

    // Config
    'config' => [
        'dataContainer'     => 'Table',
        'closed'            => false,
        'ctable'            => ['tl_shoutbox_entries'],
        'notEditable'       => false,
        'sql'               => ['keys' => ['id' => 'primary']]
    ],


    // List
    'list' => [
        'sorting' => [
            'mode'                    => 2,
            'fields'                  => ['title ASC'],
            'flag'                    => 1,
            'panelLayout'             => 'filter, search, limit'
        ],
        'label' => [
            'fields'                  => ['title', 'email'],
            'showColumns'             => true,
        ],

        'operations' => [
            'entries' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox']['entries'],
                'href'                => 'table=tl_shoutbox_entries',
                'icon'                => 'tablewizard.gif'
            ],
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
            ],
            'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_shoutbox']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ]
        ]

    ],


    // Palettes
    'palettes' => [
        'default'                     => '{title_legend}, title, email'
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ],
        'title' => [
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox']['title'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => ['mandatory'=>true, 'maxlength'=>255],
            'sql'                     => "varchar(255) NOT NULL default ''",
        ],
        'email' => [
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox']['email'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => false,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => ['rgxp'=> email, 'mandatory'=>false, 'maxlength'=>255, 'tl_class' => 'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''",
        ],

    ] //fields

];
