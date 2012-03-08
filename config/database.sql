
CREATE TABLE `tl_module` (
  `shoutbox_id` smallint(5) unsigned NOT NULL default '1',
  `shoutbox_entries` smallint(5) unsigned NOT NULL default '15',
  `shoutbox_rows` smallint(5) unsigned NOT NULL default '3',
  `shoutbox_cols` smallint(5) unsigned NOT NULL default '25',
  `shoutbox_notification` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

