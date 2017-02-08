
-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_acl_groups'
--

CREATE TABLE phpbb_acl_groups (
  group_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_option_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_role_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_setting tinyint(2) NOT NULL DEFAULT '0',
  KEY group_id (group_id),
  KEY auth_opt_id (auth_option_id),
  KEY auth_role_id (auth_role_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_acl_options'
--

CREATE TABLE phpbb_acl_options (
  auth_option_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  auth_option varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  is_global tinyint(1) unsigned NOT NULL DEFAULT '0',
  is_local tinyint(1) unsigned NOT NULL DEFAULT '0',
  founder_only tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (auth_option_id),
  UNIQUE KEY auth_option (auth_option)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_acl_roles'
--

CREATE TABLE phpbb_acl_roles (
  role_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  role_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  role_description text COLLATE utf8_bin NOT NULL,
  role_type varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  role_order smallint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (role_id),
  KEY role_type (role_type),
  KEY role_order (role_order)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_acl_roles_data'
--

CREATE TABLE phpbb_acl_roles_data (
  role_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_option_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_setting tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (role_id,auth_option_id),
  KEY ath_op_id (auth_option_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_acl_users'
--

CREATE TABLE phpbb_acl_users (
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_option_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_role_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  auth_setting tinyint(2) NOT NULL DEFAULT '0',
  KEY user_id (user_id),
  KEY auth_option_id (auth_option_id),
  KEY auth_role_id (auth_role_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_attachments'
--

CREATE TABLE phpbb_attachments (
  attach_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  post_msg_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  in_message tinyint(1) unsigned NOT NULL DEFAULT '0',
  poster_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  is_orphan tinyint(1) unsigned NOT NULL DEFAULT '1',
  physical_filename varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  real_filename varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  download_count mediumint(8) unsigned NOT NULL DEFAULT '0',
  attach_comment text COLLATE utf8_bin NOT NULL,
  extension varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  mimetype varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  filesize int(20) unsigned NOT NULL DEFAULT '0',
  filetime int(11) unsigned NOT NULL DEFAULT '0',
  thumbnail tinyint(1) unsigned NOT NULL DEFAULT '0',
  i_width smallint(4) unsigned NOT NULL DEFAULT '0',
  i_height smallint(4) unsigned NOT NULL DEFAULT '0',
  i_poster tinyint(1) unsigned NOT NULL DEFAULT '0',
  i_external tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (attach_id),
  KEY filetime (filetime),
  KEY post_msg_id (post_msg_id),
  KEY topic_id (topic_id),
  KEY poster_id (poster_id),
  KEY is_orphan (is_orphan),
  KEY i_poster (i_poster)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_banlist'
--

CREATE TABLE phpbb_banlist (
  ban_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  ban_userid mediumint(8) unsigned NOT NULL DEFAULT '0',
  ban_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  ban_email varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  ban_start int(11) unsigned NOT NULL DEFAULT '0',
  ban_end int(11) unsigned NOT NULL DEFAULT '0',
  ban_exclude tinyint(1) unsigned NOT NULL DEFAULT '0',
  ban_reason varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  ban_give_reason varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (ban_id),
  KEY ban_end (ban_end),
  KEY ban_user (ban_userid,ban_exclude),
  KEY ban_email (ban_email,ban_exclude),
  KEY ban_ip (ban_ip,ban_exclude)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_bbcodes'
--

CREATE TABLE phpbb_bbcodes (
  bbcode_id smallint(4) unsigned NOT NULL DEFAULT '0',
  bbcode_tag varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '',
  bbcode_helpline varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  display_on_posting tinyint(1) unsigned NOT NULL DEFAULT '0',
  bbcode_match text COLLATE utf8_bin NOT NULL,
  bbcode_tpl mediumtext COLLATE utf8_bin NOT NULL,
  first_pass_match mediumtext COLLATE utf8_bin NOT NULL,
  first_pass_replace mediumtext COLLATE utf8_bin NOT NULL,
  second_pass_match mediumtext COLLATE utf8_bin NOT NULL,
  second_pass_replace mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (bbcode_id),
  KEY display_on_post (display_on_posting)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_bookmarks'
--

CREATE TABLE phpbb_bookmarks (
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (topic_id,user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_bots'
--

CREATE TABLE phpbb_bots (
  bot_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  bot_active tinyint(1) unsigned NOT NULL DEFAULT '1',
  bot_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  bot_agent varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  bot_ip varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (bot_id),
  KEY bot_active (bot_active)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_config'
--

CREATE TABLE phpbb_config (
  config_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  config_value varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  is_dynamic tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (config_name),
  KEY is_dynamic (is_dynamic)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_confirm'
--

CREATE TABLE phpbb_confirm (
  confirm_id char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  session_id char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  confirm_type tinyint(3) NOT NULL DEFAULT '0',
  `code` varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  seed int(10) unsigned NOT NULL DEFAULT '0',
  attempts mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (session_id,confirm_id),
  KEY confirm_type (confirm_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_disallow'
--

CREATE TABLE phpbb_disallow (
  disallow_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  disallow_username varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (disallow_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_drafts'
--

CREATE TABLE phpbb_drafts (
  draft_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  save_time int(11) unsigned NOT NULL DEFAULT '0',
  draft_subject varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  draft_message mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (draft_id),
  KEY save_time (save_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_extensions'
--

CREATE TABLE phpbb_extensions (
  extension_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  group_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  extension varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (extension_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_extension_groups'
--

CREATE TABLE phpbb_extension_groups (
  group_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  group_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  cat_id tinyint(2) NOT NULL DEFAULT '0',
  allow_group tinyint(1) unsigned NOT NULL DEFAULT '0',
  download_mode tinyint(1) unsigned NOT NULL DEFAULT '1',
  upload_icon varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  max_filesize int(20) unsigned NOT NULL DEFAULT '0',
  allowed_forums text COLLATE utf8_bin NOT NULL,
  allow_in_pm tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (group_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_forums'
--

CREATE TABLE phpbb_forums (
  forum_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  parent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  left_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  right_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_parents mediumtext COLLATE utf8_bin NOT NULL,
  forum_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_desc text COLLATE utf8_bin NOT NULL,
  forum_desc_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_desc_options int(11) unsigned NOT NULL DEFAULT '7',
  forum_desc_uid varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_link varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_password varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_style mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_image varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_rules text COLLATE utf8_bin NOT NULL,
  forum_rules_link varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_rules_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_rules_options int(11) unsigned NOT NULL DEFAULT '7',
  forum_rules_uid varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_topics_per_page tinyint(4) NOT NULL DEFAULT '0',
  forum_type tinyint(4) NOT NULL DEFAULT '0',
  forum_status tinyint(4) NOT NULL DEFAULT '0',
  forum_posts mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_topics mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_topics_real mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_last_post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_last_poster_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_last_post_subject varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_last_post_time int(11) unsigned NOT NULL DEFAULT '0',
  forum_last_poster_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_last_poster_colour varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_flags tinyint(4) NOT NULL DEFAULT '32',
  forum_options int(20) unsigned NOT NULL DEFAULT '0',
  display_subforum_list tinyint(1) unsigned NOT NULL DEFAULT '1',
  display_on_index tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_indexing tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_icons tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_prune tinyint(1) unsigned NOT NULL DEFAULT '0',
  prune_next int(11) unsigned NOT NULL DEFAULT '0',
  prune_days mediumint(8) unsigned NOT NULL DEFAULT '0',
  prune_viewed mediumint(8) unsigned NOT NULL DEFAULT '0',
  prune_freq mediumint(8) unsigned NOT NULL DEFAULT '0',
  forumas tinyint(1) unsigned NOT NULL DEFAULT '0',
  forum_torrents mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_comments mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_subforumslist_type tinyint(4) DEFAULT '0',
  forum_first_post_show tinyint(1) unsigned NOT NULL DEFAULT '0',
  similar_topic_forums varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forum_addfields smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (forum_id),
  KEY left_right_id (left_id,right_id),
  KEY forum_lastpost_id (forum_last_post_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_forums_access'
--

CREATE TABLE phpbb_forums_access (
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  session_id char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (forum_id,user_id,session_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_forums_track'
--

CREATE TABLE phpbb_forums_track (
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  mark_time int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id,forum_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_forums_watch'
--

CREATE TABLE phpbb_forums_watch (
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  notify_status tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY forum_id (forum_id),
  KEY user_id (user_id),
  KEY notify_stat (notify_status)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_groups'
--

CREATE TABLE phpbb_groups (
  group_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  group_type tinyint(4) NOT NULL DEFAULT '1',
  group_founder_manage tinyint(1) unsigned NOT NULL DEFAULT '0',
  group_skip_auth tinyint(1) unsigned NOT NULL DEFAULT '0',
  group_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  group_desc text COLLATE utf8_bin NOT NULL,
  group_desc_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  group_desc_options int(11) unsigned NOT NULL DEFAULT '7',
  group_desc_uid varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  group_display tinyint(1) unsigned NOT NULL DEFAULT '0',
  group_avatar varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  group_avatar_type tinyint(2) NOT NULL DEFAULT '0',
  group_avatar_width smallint(4) unsigned NOT NULL DEFAULT '0',
  group_avatar_height smallint(4) unsigned NOT NULL DEFAULT '0',
  group_rank mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_colour varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  group_sig_chars mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_receive_pm tinyint(1) unsigned NOT NULL DEFAULT '0',
  group_message_limit mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_max_recipients mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_legend tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (group_id),
  KEY group_legend_name (group_legend,group_name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_icons'
--

CREATE TABLE phpbb_icons (
  icons_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  icons_url varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  icons_width tinyint(4) NOT NULL DEFAULT '0',
  icons_height tinyint(4) NOT NULL DEFAULT '0',
  icons_order mediumint(8) unsigned NOT NULL DEFAULT '0',
  display_on_posting tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (icons_id),
  KEY display_on_posting (display_on_posting)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_lang'
--

CREATE TABLE phpbb_lang (
  lang_id tinyint(4) NOT NULL AUTO_INCREMENT,
  lang_iso varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  lang_dir varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  lang_english_name varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  lang_local_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  lang_author varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (lang_id),
  KEY lang_iso (lang_iso)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_log'
--

CREATE TABLE phpbb_log (
  log_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  log_type tinyint(4) NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  reportee_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  log_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  log_time int(11) unsigned NOT NULL DEFAULT '0',
  log_operation text COLLATE utf8_bin NOT NULL,
  log_data mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (log_id),
  KEY log_type (log_type),
  KEY forum_id (forum_id),
  KEY topic_id (topic_id),
  KEY reportee_id (reportee_id),
  KEY user_id (user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_login_attempts'
--

CREATE TABLE phpbb_login_attempts (
  attempt_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  attempt_browser varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  attempt_forwarded_for varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  attempt_time int(11) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  username_clean varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  KEY att_ip (attempt_ip,attempt_time),
  KEY att_for (attempt_forwarded_for,attempt_time),
  KEY att_time (attempt_time),
  KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_moderator_cache'
--

CREATE TABLE phpbb_moderator_cache (
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  group_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  display_on_index tinyint(1) unsigned NOT NULL DEFAULT '1',
  KEY disp_idx (display_on_index),
  KEY forum_id (forum_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_modules'
--

CREATE TABLE phpbb_modules (
  module_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  module_enabled tinyint(1) unsigned NOT NULL DEFAULT '1',
  module_display tinyint(1) unsigned NOT NULL DEFAULT '1',
  module_basename varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_class varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  parent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  left_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  right_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  module_langname varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_mode varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_auth varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (module_id),
  KEY left_right_id (left_id,right_id),
  KEY module_enabled (module_enabled),
  KEY class_left_id (module_class,left_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_poll_options'
--

CREATE TABLE phpbb_poll_options (
  poll_option_id tinyint(4) NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  poll_option_text text COLLATE utf8_bin NOT NULL,
  poll_option_total mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY poll_opt_id (poll_option_id),
  KEY topic_id (topic_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_poll_votes'
--

CREATE TABLE phpbb_poll_votes (
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  poll_option_id tinyint(4) NOT NULL DEFAULT '0',
  vote_user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  vote_user_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  KEY topic_id (topic_id),
  KEY vote_user_id (vote_user_id),
  KEY vote_user_ip (vote_user_ip)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_portal_config'
--

CREATE TABLE phpbb_portal_config (
  config_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  config_value mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (config_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_portal_modules'
--

CREATE TABLE phpbb_portal_modules (
  module_id int(3) unsigned NOT NULL AUTO_INCREMENT,
  module_classname varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_column tinyint(3) NOT NULL DEFAULT '0',
  module_order tinyint(3) NOT NULL DEFAULT '0',
  module_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_image_src varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_image_width int(3) NOT NULL DEFAULT '0',
  module_image_height int(3) NOT NULL DEFAULT '0',
  module_group_ids varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  module_status tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (module_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_posts'
--

CREATE TABLE phpbb_posts (
  post_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  poster_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  icon_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  poster_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  post_time int(11) unsigned NOT NULL DEFAULT '0',
  post_approved tinyint(1) unsigned NOT NULL DEFAULT '1',
  post_reported tinyint(1) unsigned NOT NULL DEFAULT '0',
  enable_bbcode tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_smilies tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_magic_url tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_sig tinyint(1) unsigned NOT NULL DEFAULT '1',
  post_username varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  post_subject varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  post_text mediumtext COLLATE utf8_bin NOT NULL,
  post_checksum varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  post_attachment tinyint(1) unsigned NOT NULL DEFAULT '0',
  bbcode_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  bbcode_uid varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  post_postcount tinyint(1) unsigned NOT NULL DEFAULT '1',
  post_edit_time int(11) unsigned NOT NULL DEFAULT '0',
  post_edit_reason varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  post_edit_user mediumint(8) unsigned NOT NULL DEFAULT '0',
  post_edit_count smallint(4) unsigned NOT NULL DEFAULT '0',
  post_edit_locked tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (post_id),
  KEY forum_id (forum_id),
  KEY topic_id (topic_id),
  KEY poster_ip (poster_ip),
  KEY poster_id (poster_id),
  KEY post_approved (post_approved),
  KEY post_username (post_username),
  KEY tid_post_time (topic_id,post_time)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_ppkchat_messages'
--

CREATE TABLE phpbb_ppkchat_messages (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  message varchar(255) COLLATE utf8_bin NOT NULL,
  user_id bigint(10) NOT NULL DEFAULT '0',
  to_user bigint(10) NOT NULL DEFAULT '0',
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  rights varchar(255) COLLATE utf8_bin NOT NULL,
  room smallint(5) unsigned NOT NULL DEFAULT '0',
  username varchar(255) COLLATE utf8_bin NOT NULL,
  user_color varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_ip int(11) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  edited_by mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY room (room),
  KEY to_user (to_user),
  KEY forum_id (forum_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_ppkchat_users'
--

CREATE TABLE phpbb_ppkchat_users (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  username varchar(255) COLLATE utf8_bin NOT NULL,
  rights varchar(255) COLLATE utf8_bin NOT NULL,
  lastpost int(11) unsigned NOT NULL DEFAULT '0',
  room smallint(5) unsigned NOT NULL DEFAULT '0',
  lastaccess int(11) unsigned NOT NULL DEFAULT '0',
  user_id bigint(10) NOT NULL DEFAULT '0',
  chatkey varchar(32) COLLATE utf8_bin NOT NULL,
  user_color varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_hidden tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_ip int(11) unsigned NOT NULL DEFAULT '0',
  user_lang varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_timezone decimal(5,2) NOT NULL DEFAULT '0.00',
  user_dst tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_dateformat varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'd M Y H:i',
  session_id varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_avatar varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY room (room),
  KEY user_id (user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_privmsgs'
--

CREATE TABLE phpbb_privmsgs (
  msg_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  root_level mediumint(8) unsigned NOT NULL DEFAULT '0',
  author_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  icon_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  author_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  message_time int(11) unsigned NOT NULL DEFAULT '0',
  enable_bbcode tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_smilies tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_magic_url tinyint(1) unsigned NOT NULL DEFAULT '1',
  enable_sig tinyint(1) unsigned NOT NULL DEFAULT '1',
  message_subject varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `message_text` mediumtext COLLATE utf8_bin NOT NULL,
  message_edit_reason varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  message_edit_user mediumint(8) unsigned NOT NULL DEFAULT '0',
  message_attachment tinyint(1) unsigned NOT NULL DEFAULT '0',
  bbcode_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  bbcode_uid varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  message_edit_time int(11) unsigned NOT NULL DEFAULT '0',
  message_edit_count smallint(4) unsigned NOT NULL DEFAULT '0',
  to_address text COLLATE utf8_bin NOT NULL,
  bcc_address text COLLATE utf8_bin NOT NULL,
  message_reported tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (msg_id),
  KEY author_ip (author_ip),
  KEY message_time (message_time),
  KEY author_id (author_id),
  KEY root_level (root_level)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_privmsgs_folder'
--

CREATE TABLE phpbb_privmsgs_folder (
  folder_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  folder_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  pm_count mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (folder_id),
  KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_privmsgs_rules'
--

CREATE TABLE phpbb_privmsgs_rules (
  rule_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  rule_check mediumint(8) unsigned NOT NULL DEFAULT '0',
  rule_connection mediumint(8) unsigned NOT NULL DEFAULT '0',
  rule_string varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  rule_user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  rule_group_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  rule_action mediumint(8) unsigned NOT NULL DEFAULT '0',
  rule_folder_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (rule_id),
  KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_privmsgs_to'
--

CREATE TABLE phpbb_privmsgs_to (
  msg_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  author_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  pm_deleted tinyint(1) unsigned NOT NULL DEFAULT '0',
  pm_new tinyint(1) unsigned NOT NULL DEFAULT '1',
  pm_unread tinyint(1) unsigned NOT NULL DEFAULT '1',
  pm_replied tinyint(1) unsigned NOT NULL DEFAULT '0',
  pm_marked tinyint(1) unsigned NOT NULL DEFAULT '0',
  pm_forwarded tinyint(1) unsigned NOT NULL DEFAULT '0',
  folder_id int(11) NOT NULL DEFAULT '0',
  KEY msg_id (msg_id),
  KEY author_id (author_id),
  KEY usr_flder_id (user_id,folder_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_profile_fields'
--

CREATE TABLE phpbb_profile_fields (
  field_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  field_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_type tinyint(4) NOT NULL DEFAULT '0',
  field_ident varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_length varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_minlen varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_maxlen varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_novalue varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_default_value varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_validation varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  field_required tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_show_novalue tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_show_on_reg tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_show_on_vt tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_show_profile tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_hide tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_no_view tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_active tinyint(1) unsigned NOT NULL DEFAULT '0',
  field_order mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (field_id),
  KEY fld_type (field_type),
  KEY fld_ordr (field_order)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_profile_fields_data'
--

CREATE TABLE phpbb_profile_fields_data (
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_profile_fields_lang'
--

CREATE TABLE phpbb_profile_fields_lang (
  field_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  lang_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  option_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  field_type tinyint(4) NOT NULL DEFAULT '0',
  lang_value varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (field_id,lang_id,option_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_profile_lang'
--

CREATE TABLE phpbb_profile_lang (
  field_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  lang_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  lang_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  lang_explain text COLLATE utf8_bin NOT NULL,
  lang_default_value varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (field_id,lang_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_ranks'
--

CREATE TABLE phpbb_ranks (
  rank_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  rank_title varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  rank_min mediumint(8) unsigned NOT NULL DEFAULT '0',
  rank_special tinyint(1) unsigned NOT NULL DEFAULT '0',
  rank_image varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (rank_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_reports'
--

CREATE TABLE phpbb_reports (
  report_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  reason_id smallint(4) unsigned NOT NULL DEFAULT '0',
  post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  pm_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_notify tinyint(1) unsigned NOT NULL DEFAULT '0',
  report_closed tinyint(1) unsigned NOT NULL DEFAULT '0',
  report_time int(11) unsigned NOT NULL DEFAULT '0',
  report_text mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (report_id),
  KEY post_id (post_id),
  KEY pm_id (pm_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_reports_reasons'
--

CREATE TABLE phpbb_reports_reasons (
  reason_id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  reason_title varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  reason_description mediumtext COLLATE utf8_bin NOT NULL,
  reason_order smallint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (reason_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_search_results'
--

CREATE TABLE phpbb_search_results (
  search_key varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  search_time int(11) unsigned NOT NULL DEFAULT '0',
  search_keywords mediumtext COLLATE utf8_bin NOT NULL,
  search_authors mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (search_key)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_search_wordlist'
--

CREATE TABLE phpbb_search_wordlist (
  word_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  word_text varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  word_common tinyint(1) unsigned NOT NULL DEFAULT '0',
  word_count mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (word_id),
  UNIQUE KEY wrd_txt (word_text),
  KEY wrd_cnt (word_count)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_search_wordmatch'
--

CREATE TABLE phpbb_search_wordmatch (
  post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  word_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  title_match tinyint(1) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY unq_mtch (word_id,post_id,title_match),
  KEY word_id (word_id),
  KEY post_id (post_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_sessions'
--

CREATE TABLE phpbb_sessions (
  session_id char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  session_user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  session_forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  session_topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  session_last_visit int(11) unsigned NOT NULL DEFAULT '0',
  session_start int(11) unsigned NOT NULL DEFAULT '0',
  session_time int(11) unsigned NOT NULL DEFAULT '0',
  session_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  session_browser varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  session_forwarded_for varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  session_page varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  session_viewonline tinyint(1) unsigned NOT NULL DEFAULT '1',
  session_autologin tinyint(1) unsigned NOT NULL DEFAULT '0',
  session_admin tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (session_id),
  KEY session_time (session_time),
  KEY session_user_id (session_user_id),
  KEY session_fid (session_forum_id),
  KEY session_topic_id (session_topic_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_sessions_keys'
--

CREATE TABLE phpbb_sessions_keys (
  key_id char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  last_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  last_login int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (key_id,user_id),
  KEY last_login (last_login)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_sitelist'
--

CREATE TABLE phpbb_sitelist (
  site_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  site_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  site_hostname varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  ip_exclude tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (site_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_smilies'
--

CREATE TABLE phpbb_smilies (
  smiley_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  emotion varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  smiley_url varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  smiley_width smallint(4) unsigned NOT NULL DEFAULT '0',
  smiley_height smallint(4) unsigned NOT NULL DEFAULT '0',
  smiley_order mediumint(8) unsigned NOT NULL DEFAULT '0',
  display_on_posting tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (smiley_id),
  KEY display_on_post (display_on_posting)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_styles'
--

CREATE TABLE phpbb_styles (
  style_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  style_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  style_copyright varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  style_active tinyint(1) unsigned NOT NULL DEFAULT '1',
  template_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  theme_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  imageset_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (style_id),
  UNIQUE KEY style_name (style_name),
  KEY template_id (template_id),
  KEY theme_id (theme_id),
  KEY imageset_id (imageset_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_styles_imageset'
--

CREATE TABLE phpbb_styles_imageset (
  imageset_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  imageset_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  imageset_copyright varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  imageset_path varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (imageset_id),
  UNIQUE KEY imgset_nm (imageset_name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_styles_imageset_data'
--

CREATE TABLE phpbb_styles_imageset_data (
  image_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  image_name varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  image_filename varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  image_lang varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  image_height smallint(4) unsigned NOT NULL DEFAULT '0',
  image_width smallint(4) unsigned NOT NULL DEFAULT '0',
  imageset_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (image_id),
  KEY i_d (imageset_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_styles_template'
--

CREATE TABLE phpbb_styles_template (
  template_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  template_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  template_copyright varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  template_path varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  bbcode_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'kNg=',
  template_storedb tinyint(1) unsigned NOT NULL DEFAULT '0',
  template_inherits_id int(4) unsigned NOT NULL DEFAULT '0',
  template_inherit_path varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (template_id),
  UNIQUE KEY tmplte_nm (template_name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_styles_template_data'
--

CREATE TABLE phpbb_styles_template_data (
  template_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  template_filename varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  template_included text COLLATE utf8_bin NOT NULL,
  template_mtime int(11) unsigned NOT NULL DEFAULT '0',
  template_data mediumtext COLLATE utf8_bin NOT NULL,
  KEY tid (template_id),
  KEY tfn (template_filename)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_styles_theme'
--

CREATE TABLE phpbb_styles_theme (
  theme_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  theme_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  theme_copyright varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  theme_path varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  theme_storedb tinyint(1) unsigned NOT NULL DEFAULT '0',
  theme_mtime int(11) unsigned NOT NULL DEFAULT '0',
  theme_data mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (theme_id),
  UNIQUE KEY theme_name (theme_name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_topics'
--

CREATE TABLE phpbb_topics (
  topic_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  icon_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_attachment tinyint(1) unsigned NOT NULL DEFAULT '0',
  topic_approved tinyint(1) unsigned NOT NULL DEFAULT '1',
  topic_reported tinyint(1) unsigned NOT NULL DEFAULT '0',
  topic_title varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  topic_poster mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_time int(11) unsigned NOT NULL DEFAULT '0',
  topic_time_limit int(11) unsigned NOT NULL DEFAULT '0',
  topic_views mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_replies mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_replies_real mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_status tinyint(3) NOT NULL DEFAULT '0',
  topic_type tinyint(3) NOT NULL DEFAULT '0',
  topic_first_post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_first_poster_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  topic_first_poster_colour varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  topic_last_post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_last_poster_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_last_poster_name varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  topic_last_poster_colour varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  topic_last_post_subject varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  topic_last_post_time int(11) unsigned NOT NULL DEFAULT '0',
  topic_last_view_time int(11) unsigned NOT NULL DEFAULT '0',
  topic_moved_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_bumped tinyint(1) unsigned NOT NULL DEFAULT '0',
  topic_bumper mediumint(8) unsigned NOT NULL DEFAULT '0',
  poll_title varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  poll_start int(11) unsigned NOT NULL DEFAULT '0',
  poll_length int(11) unsigned NOT NULL DEFAULT '0',
  poll_max_options tinyint(4) NOT NULL DEFAULT '1',
  poll_last_vote int(11) unsigned NOT NULL DEFAULT '0',
  poll_vote_change tinyint(1) unsigned NOT NULL DEFAULT '0',
  topic_first_post_show tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (topic_id),
  KEY forum_id (forum_id),
  KEY forum_id_type (forum_id,topic_type),
  KEY last_post_time (topic_last_post_time),
  KEY topic_approved (topic_approved),
  KEY forum_appr_last (forum_id,topic_approved,topic_last_post_id),
  KEY fid_time_moved (forum_id,topic_last_post_time,topic_moved_id),
  KEY topic_first_post_id (topic_first_post_id),
  KEY topic_last_post_id (topic_last_post_id),
  FULLTEXT KEY topic_title (topic_title)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_topics_posted'
--

CREATE TABLE phpbb_topics_posted (
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_posted tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id,topic_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_topics_track'
--

CREATE TABLE phpbb_topics_track (
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  mark_time int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id,topic_id),
  KEY topic_id (topic_id),
  KEY forum_id (forum_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_topics_watch'
--

CREATE TABLE phpbb_topics_watch (
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  notify_status tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY topic_id (topic_id),
  KEY user_id (user_id),
  KEY notify_stat (notify_status)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_afftpl'
--

CREATE TABLE phpbb_tracker_afftpl (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  addfields varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_hlp mediumtext COLLATE utf8_bin NOT NULL,
  addfields_br varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_br2 varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_inall tinyint(1) unsigned NOT NULL DEFAULT '0',
  addfields_ta tinyint(1) unsigned NOT NULL DEFAULT '0',
  addfields_multi tinyint(1) NOT NULL DEFAULT '0',
  addfields_bbcode varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_bbcodes varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_descr varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_enable tinyint(1) unsigned NOT NULL DEFAULT '0',
  addfields_split varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  af_id smallint(5) unsigned NOT NULL DEFAULT '0',
  addfields_def varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_checkas varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_title varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  addfields_alias mediumtext COLLATE utf8_bin NOT NULL,
  addfields_skip tinyint(1) unsigned NOT NULL DEFAULT '0',
  addfields_exists tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY addfields (addfields),
  KEY addfields_enable (addfields_enable)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_afsets'
--

CREATE TABLE phpbb_tracker_afsets (
  id mediumint(8) NOT NULL AUTO_INCREMENT,
  af_id smallint(5) unsigned NOT NULL DEFAULT '0',
  af_name varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  af_count tinyint(1) unsigned NOT NULL DEFAULT '0',
  af_order smallint(3) unsigned NOT NULL DEFAULT '0',
  af_required tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY af_id (af_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_aftpl'
--

CREATE TABLE phpbb_tracker_aftpl (
  id smallint(5) NOT NULL AUTO_INCREMENT,
  af_name varchar(64) COLLATE utf8_bin NOT NULL,
  af_descr text COLLATE utf8_bin NOT NULL,
  af_subject varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  af_data text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_config'
--

CREATE TABLE phpbb_tracker_config (
  config_name varchar(255) COLLATE utf8_bin NOT NULL,
  config_value text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (config_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_cron'
--

CREATE TABLE phpbb_tracker_cron (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `data` text COLLATE utf8_bin NOT NULL,
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  added int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_downloads'
--

CREATE TABLE phpbb_tracker_downloads (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  downloader_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  dl_time int(11) unsigned NOT NULL DEFAULT '0',
  dl_ip varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '0',
  attach_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  post_msg_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  guests tinyint(1) unsigned NOT NULL DEFAULT '0',
  dl_date int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY post_msg_id (post_msg_id),
  KEY downloader_id (downloader_id),
  KEY attach_id (attach_id),
  KEY dl_date (dl_date)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_files'
--

CREATE TABLE phpbb_tracker_files (
  id int(10) unsigned NOT NULL DEFAULT '0',
  filename text COLLATE utf8_bin NOT NULL,
  size bigint(20) unsigned NOT NULL DEFAULT '0',
  KEY id (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_guests'
--

CREATE TABLE phpbb_tracker_guests (
  user_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_passkey varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_ip int(11) unsigned NOT NULL DEFAULT '0',
  user_time int(11) unsigned NOT NULL DEFAULT '0',
  user_last_time int(11) unsigned NOT NULL DEFAULT '0',
  session_id varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  unreg tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id),
  KEY user_passkey (user_passkey),
  KEY session_id (session_id),
  KEY unreg (unreg)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_peers'
--

CREATE TABLE phpbb_tracker_peers (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  torrent int(10) unsigned NOT NULL DEFAULT '0',
  peer_id binary(20) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  ip int(11) unsigned NOT NULL DEFAULT '0',
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  uploaded bigint(20) unsigned NOT NULL DEFAULT '0',
  downloaded bigint(20) unsigned NOT NULL DEFAULT '0',
  to_go bigint(20) unsigned NOT NULL DEFAULT '0',
  startdat int(11) unsigned NOT NULL DEFAULT '0',
  last_action int(11) unsigned NOT NULL DEFAULT '0',
  userid int(10) unsigned NOT NULL DEFAULT '0',
  rights varchar(64) COLLATE utf8_bin NOT NULL,
  guests tinyint(1) unsigned NOT NULL DEFAULT '0',
  seeder tinyint(1) unsigned NOT NULL DEFAULT '0',
  connectable tinyint(1) unsigned NOT NULL DEFAULT '0',
  agent varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  KEY torrent (torrent),
  KEY userid (userid),
  KEY ip (ip),
  KEY seeder (seeder)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_rannounces'
--

CREATE TABLE phpbb_tracker_rannounces (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  torrent int(10) unsigned NOT NULL DEFAULT '0',
  tracker int(11) unsigned NOT NULL DEFAULT '0',
  next_announce int(11) unsigned NOT NULL DEFAULT '0',
  next_scrape int(11) unsigned NOT NULL DEFAULT '0',
  a_message varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  s_message varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  err_count smallint(5) unsigned NOT NULL DEFAULT '0',
  a_interval mediumint(6) unsigned NOT NULL DEFAULT '0',
  seeders int(10) unsigned NOT NULL DEFAULT '0',
  leechers int(10) unsigned NOT NULL DEFAULT '0',
  times_completed int(10) unsigned NOT NULL DEFAULT '0',
  locked tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY tracker (tracker,torrent),
  KEY torrent (torrent)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_rtrack'
--

CREATE TABLE phpbb_tracker_rtrack (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  zone_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  rtrack_url text COLLATE utf8_bin NOT NULL,
  rtrack_user tinyint(1) unsigned NOT NULL DEFAULT '0',
  rtrack_remote tinyint(1) NOT NULL DEFAULT '0',
  torrent int(10) unsigned NOT NULL DEFAULT '0',
  rtrack_forb tinyint(1) unsigned NOT NULL DEFAULT '0',
  rtrack_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
  forb_type enum('r','s','i') COLLATE utf8_bin NOT NULL DEFAULT 's',
  PRIMARY KEY (id),
  KEY zone_id (zone_id),
  KEY rtrack_user (rtrack_user),
  KEY torrent (torrent),
  KEY rtrack_enabled (rtrack_enabled)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_snatched'
--

CREATE TABLE phpbb_tracker_snatched (
  id int(11) NOT NULL AUTO_INCREMENT,
  userid int(10) unsigned NOT NULL DEFAULT '0',
  torrent int(10) unsigned NOT NULL DEFAULT '0',
  uploaded bigint(20) unsigned NOT NULL DEFAULT '0',
  downloaded bigint(20) unsigned NOT NULL DEFAULT '0',
  to_go bigint(20) unsigned NOT NULL DEFAULT '0',
  last_action int(11) unsigned NOT NULL DEFAULT '0',
  startdat int(11) unsigned NOT NULL DEFAULT '0',
  completedat int(11) unsigned NOT NULL DEFAULT '0',
  finished smallint(5) unsigned NOT NULL DEFAULT '0',
  bonus_count bigint(20) unsigned NOT NULL DEFAULT '0',
  guests tinyint(1) unsigned NOT NULL DEFAULT '0',
  uploadoffset bigint(20) unsigned NOT NULL DEFAULT '0',
  downloadoffset bigint(20) unsigned NOT NULL DEFAULT '0',
  prev_action int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY torrent (torrent),
  KEY userid (userid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_statuses'
--

CREATE TABLE phpbb_tracker_statuses (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  status_id tinyint(2) NOT NULL,
  status_reason varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  status_mark varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  def_forb tinyint(1) unsigned NOT NULL DEFAULT '0',
  def_notforb tinyint(1) unsigned NOT NULL DEFAULT '0',
  author_candown tinyint(1) unsigned NOT NULL DEFAULT '0',
  status_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
  guest_cantdown tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (status_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_thanks'
--

CREATE TABLE phpbb_tracker_thanks (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  torrent_id int(10) unsigned NOT NULL DEFAULT '0',
  to_user mediumint(8) unsigned NOT NULL DEFAULT '0',
  tadded int(11) unsigned NOT NULL DEFAULT '0',
  post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY user_id (user_id,torrent_id),
  KEY to_user (to_user),
  KEY post_id (post_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_tracker_torrents'
--

CREATE TABLE phpbb_tracker_torrents (
  id int(10) unsigned NOT NULL DEFAULT '0',
  post_msg_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  topic_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  poster_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  info_hash binary(20) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  numfiles int(10) unsigned NOT NULL DEFAULT '0',
  times_completed int(10) unsigned NOT NULL DEFAULT '0',
  leechers int(10) unsigned NOT NULL DEFAULT '0',
  seeders int(10) unsigned NOT NULL DEFAULT '0',
  size bigint(20) unsigned NOT NULL DEFAULT '0',
  free tinyint(3) unsigned NOT NULL DEFAULT '0',
  upload tinyint(3) unsigned NOT NULL DEFAULT '0',
  forb tinyint(2) NOT NULL DEFAULT '0',
  forb_user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  forb_reason varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  forb_date int(11) unsigned NOT NULL DEFAULT '0',
  added int(11) unsigned NOT NULL DEFAULT '0',
  req_upload bigint(20) unsigned NOT NULL DEFAULT '0',
  req_ratio decimal(6,3) unsigned NOT NULL DEFAULT '0.000',
  private tinyint(1) unsigned NOT NULL DEFAULT '0',
  tsl_speed varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  ip varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  unreg tinyint(1) unsigned NOT NULL DEFAULT '0',
  lastseed int(11) unsigned NOT NULL DEFAULT '0',
  lastleech int(11) unsigned NOT NULL DEFAULT '0',
  forum_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  lastcleanup int(11) unsigned NOT NULL DEFAULT '0',
  rem_seeders int(11) unsigned NOT NULL DEFAULT '0',
  rem_leechers int(11) unsigned NOT NULL DEFAULT '0',
  rem_times_completed int(11) unsigned NOT NULL DEFAULT '0',
  lastremote int(11) unsigned NOT NULL DEFAULT '0',
  thanks mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY info_hash (info_hash),
  KEY topic_id (topic_id),
  KEY poster_id (poster_id),
  KEY post_msg_id (post_msg_id),
  KEY forb (forb),
  KEY forum_id (forum_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_users'
--

CREATE TABLE phpbb_users (
  user_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  user_type tinyint(2) NOT NULL DEFAULT '0',
  group_id mediumint(8) unsigned NOT NULL DEFAULT '3',
  user_permissions mediumtext COLLATE utf8_bin NOT NULL,
  user_perm_from mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_ip varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_regdate int(11) unsigned NOT NULL DEFAULT '0',
  username varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  username_clean varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_password varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_passchg int(11) unsigned NOT NULL DEFAULT '0',
  user_pass_convert tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_email varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_email_hash bigint(20) NOT NULL DEFAULT '0',
  user_birthday varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_lastvisit int(11) unsigned NOT NULL DEFAULT '0',
  user_lastmark int(11) unsigned NOT NULL DEFAULT '0',
  user_lastpost_time int(11) unsigned NOT NULL DEFAULT '0',
  user_lastpage varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_last_confirm_key varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_last_search int(11) unsigned NOT NULL DEFAULT '0',
  user_warnings tinyint(4) NOT NULL DEFAULT '0',
  user_last_warning int(11) unsigned NOT NULL DEFAULT '0',
  user_login_attempts tinyint(4) NOT NULL DEFAULT '0',
  user_inactive_reason tinyint(2) NOT NULL DEFAULT '0',
  user_inactive_time int(11) unsigned NOT NULL DEFAULT '0',
  user_posts mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_lang varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_timezone decimal(5,2) NOT NULL DEFAULT '0.00',
  user_dst tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_dateformat varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'd M Y H:i',
  user_style mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_rank mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_colour varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_new_privmsg int(4) NOT NULL DEFAULT '0',
  user_unread_privmsg int(4) NOT NULL DEFAULT '0',
  user_last_privmsg int(11) unsigned NOT NULL DEFAULT '0',
  user_message_rules tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_full_folder int(11) NOT NULL DEFAULT '-3',
  user_emailtime int(11) unsigned NOT NULL DEFAULT '0',
  user_topic_show_days smallint(4) unsigned NOT NULL DEFAULT '0',
  user_topic_sortby_type varchar(1) COLLATE utf8_bin NOT NULL DEFAULT 't',
  user_topic_sortby_dir varchar(1) COLLATE utf8_bin NOT NULL DEFAULT 'd',
  user_post_show_days smallint(4) unsigned NOT NULL DEFAULT '0',
  user_post_sortby_type varchar(1) COLLATE utf8_bin NOT NULL DEFAULT 't',
  user_post_sortby_dir varchar(1) COLLATE utf8_bin NOT NULL DEFAULT 'a',
  user_notify tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_notify_pm tinyint(1) unsigned NOT NULL DEFAULT '1',
  user_notify_type tinyint(4) NOT NULL DEFAULT '0',
  user_allow_pm tinyint(1) unsigned NOT NULL DEFAULT '1',
  user_allow_viewonline tinyint(1) unsigned NOT NULL DEFAULT '1',
  user_allow_viewemail tinyint(1) unsigned NOT NULL DEFAULT '1',
  user_allow_massemail tinyint(1) unsigned NOT NULL DEFAULT '1',
  user_options int(11) unsigned NOT NULL DEFAULT '230271',
  user_avatar varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_avatar_type tinyint(2) NOT NULL DEFAULT '0',
  user_avatar_width smallint(4) unsigned NOT NULL DEFAULT '0',
  user_avatar_height smallint(4) unsigned NOT NULL DEFAULT '0',
  user_sig mediumtext COLLATE utf8_bin NOT NULL,
  user_sig_bbcode_uid varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_sig_bbcode_bitfield varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_from varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_icq varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_aim varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_yim varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_msnm varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_jabber varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_website varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_occ text COLLATE utf8_bin NOT NULL,
  user_interests text COLLATE utf8_bin NOT NULL,
  user_actkey varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_newpasswd varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_form_salt varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_passkey varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_uploaded bigint(20) unsigned NOT NULL DEFAULT '0',
  user_downloaded bigint(20) unsigned NOT NULL DEFAULT '0',
  user_comments mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_torrents mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_bonus decimal(6,3) unsigned NOT NULL DEFAULT '0.000',
  user_shadow_downloaded bigint(20) unsigned NOT NULL DEFAULT '0',
  user_chatkey varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_new tinyint(1) unsigned NOT NULL DEFAULT '1',
  user_reminded tinyint(4) NOT NULL DEFAULT '0',
  user_reminded_time int(11) unsigned NOT NULL DEFAULT '0',
  user_tracker_data varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_tracker_options varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  user_fromthanks_count smallint(5) unsigned NOT NULL DEFAULT '0',
  user_tothanks_count smallint(5) unsigned NOT NULL DEFAULT '0',
  user_uploaded_self bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id),
  UNIQUE KEY username_clean (username_clean),
  KEY user_birthday (user_birthday),
  KEY user_email_hash (user_email_hash),
  KEY user_type (user_type),
  KEY user_passkey (user_passkey),
  KEY user_chatkey (user_chatkey)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_user_group'
--

CREATE TABLE phpbb_user_group (
  group_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_leader tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_pending tinyint(1) unsigned NOT NULL DEFAULT '1',
  KEY group_id (group_id),
  KEY user_id (user_id),
  KEY group_leader (group_leader)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_warnings'
--

CREATE TABLE phpbb_warnings (
  warning_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  post_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  log_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  warning_time int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (warning_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_words'
--

CREATE TABLE phpbb_words (
  word_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  word varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  replacement varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (word_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'phpbb_zebra'
--

CREATE TABLE phpbb_zebra (
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  zebra_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  friend tinyint(1) unsigned NOT NULL DEFAULT '0',
  foe tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id,zebra_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
