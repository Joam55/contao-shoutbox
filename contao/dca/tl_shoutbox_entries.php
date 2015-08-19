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

$GLOBALS['TL_DCA']['tl_shoutbox_entries'] = [

    // Config
    'config' => [
        'dataContainer'      => 'Table',
        'closed'             => true,
        'notEditable'        => false,
        'ptable'             => 'tl_shoutbox',
        'sql'                => ['keys' => ['id'  => 'primary', 'pid' => 'index']]
    ],


    // List
    'list' => [
        'sorting' => [
            'mode'                    => 2,
            'fields'                  => ['datim DESC'],
            'flag'                    => 1,
            'panelLayout'             => 'filter, search, limit',
        ],
        'label' => [
            'fields'                  => ['datim', 'member', 'entry'],
            'showColumns'             => true,
            'label_callback'          => ['tl_shoutbox_entries', 'labelCallback']
        ],
        'global_operations' => [
            'all' => [
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations' => [
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox_entries']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif',
                'attributes'          => 'class="contextmenu"'
            ],
            'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_shoutbox_entries']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_shoutbox_entries']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ]
        ]

    ],

    // Palettes
    'palettes' => ['default' => '{title_legend}, datim, member, entry'],

    // Fields
    'fields' => [
        'id' => [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid' => [
            'foreignKey'              => 'tl_shoutbox.title',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => ['type'=>'belongsTo', 'load'=>'eager']
        ],
        'tstamp' => [
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ],
        'member' => [
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox_entries']['member'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'foreignKey'              => 'tl_member.username',
            'inputType'               => 'select',
            'eval'                    => ['mandatory'=>true, 'tl_class'=>'w50'],
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ],
        'datim' => [
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox_entries']['datim'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 8,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'datepicker'=>true, 'rgxp'=>'datim', 'tl_class'=>'w50 wizard'],
            'sql'                     => "int(10) unsigned NULL"
        ],
        'entry' => [
            'label'                   => $GLOBALS['TL_LANG']['tl_shoutbox_entries']['entry'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'textarea',
            'eval'                    => ['mandatory'=>true, 'style'=> 'height:80px;', 'allowHtml' => true],
            'sql'                     => "text NULL",
        ],

    ] //fields

];

class tl_shoutbox_entries extends Backend
{
    private $memberArray     = [];

    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function labelCallback($row, $label, DataContainer $dc, $args = null)
    {
        $this->fillMemberCache($row['pid']);
        $memberID = $row['member'];
        $args[1]  = $this->memberArray[$memberID];
        return $args;
    }

    private function fillMemberCache($sbID)
    {
        $result = $this->Database->prepare('SELECT id, username FROM tl_member'
                .' WHERE id IN (SELECT member FROM tl_shoutbox_entries WHERE pid = ?)'
        )->execute($sbID);

        while($result->next())
        {
            $row                    = $result->row();
            $id                     = $row['id'];
            $this->memberArray[$id] = $row['username'];
        }
        return true;
    }
}
