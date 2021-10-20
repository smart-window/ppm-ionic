UPDATE `plugins` SET `id`='61', `name`='live', `category`='Software', `core_file`=NULL, `css_file`='live', `js_file`='live', `image`='https://premiumdatingscript.com/plugins/icons/live.jpg', `title`='Live Streaming', `info`='Allow users to start a live broadcasting in the website to the world from their webcam at just one click', `settings`='1', `premium`='1', `orden`='8', `author`='Premium Dating Script', `custom_url`='0', `tutorial`='0', `enabled`='1', `categoryOrden`='0', `visible`='1', `custom_url_link`=NULL, `beta`='0' WHERE (`id`='61');
INSERT INTO `plugins_blocks` (`plugin`, `block`, `title`, `info`, `orden`, `type`) VALUES ('live', 'live_enabled', 'Live Streaming System', NULL, '5', '1');
INSERT INTO `plugins_blocks` (`plugin`, `block`, `title`, `info`, `orden`, `type`) VALUES ('live', 'live_general', 'Live Streaming General Settings', NULL, '3', '1');
INSERT INTO `plugins_blocks` (`plugin`, `block`, `title`, `info`, `orden`, `type`) VALUES ('live', 'live_credits', 'Live Streaming Credits Settings', NULL, '2', '1');
INSERT INTO `plugins_blocks` (`plugin`, `block`, `title`, `info`, `orden`, `type`) VALUES ('live', 'live_chat', 'Live Streaming Chat Settings', NULL, '1', '1');
INSERT INTO `plugins_blocks` (`plugin`, `block`, `title`, `info`, `orden`, `type`) VALUES ('live', 'live_settings', 'Live Streaming API Settings', NULL, '4', '1');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'enabled', 'Yes', 'Live Streaming', 'If live streaming is disabled, the link will dissapear from the menu', 'Enable Live Streaming', 'live_enabled', 'boolean', NULL, '0', '1', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'transferCredits', 'Yes', 'Transfer gift credits', 'If enabled the credits price from gifts recieved in the live broadcast will be added to the streamer balance', 'Transfer gift credits', 'live_credits', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'pub_token', '', 'PUBLISHING TOKEN', 'Add your PUBLISHING TOKEN from your millicast panel', 'PUBLISHING TOKEN', 'live_settings', 'text', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'account_id', '', 'ACCOUNT ID ', 'Add your ACCOUNT ID of your created stream token in millicast', 'ACCOUNT ID ', 'live_settings', 'text', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'showPrivateStreamModal', 'No', 'Show private stream modal', 'If enabled when the stream is private it will display a well designed modal saying that the stream is private', 'Show private stream modal', 'live_general', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'streamerCustomMessage', 'Yes', 'Custom stream message', 'If enabled when the user is previewing the camera, allows the user to add a custom message that will show to every one as first message', 'Custom Stream Message', 'live_chat', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'adminCustomMessage', '', 'Admin custom message', 'For show a custom message in all streams bellow the streamer custom message write it here, if you leave it empty it will show nothing', 'Admin Stream Message', 'live_chat', 'textarea', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'announceVisitor', 'Yes', 'Announce new visitor in chat', 'If enabled when the user start vieweing the stream it will be announced in the chat for everybody', 'Announce new visitor in chat', 'live_chat', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'privateStream', 'Yes', 'Private Stream', 'If enabled streamers are allowed to make their stream private, will be accessible only for those that pay the credits fee setted by the streamer', 'Private Stream', 'live_credits', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'disablePrintScreen', 'Yes', 'Disable Print Screen', 'If enabled the users cant take screenshots in the live section', 'Disable Print Screen', 'live_general', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'onlyForPremium', 'No', 'View stream only for premium users', 'If enabled the users that are not premium cant see live streams', 'View stream only for premium users', 'live_general', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'onlyForPremiumSeconds', '0', 'Stream sneak peek ', 'If <code>Stream only for premiums</code> is enabled but you want to allow users to see <code>\"X\"</code> amount of seconds, you can set here, in seconds how much free users can see before the stream is not visible anymore<br><br>Set it in 0 for disable sneak peek', 'Stream sneak peek ', 'live_general', 'number', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'canStreamGender', '5', 'Gender allowed to start a live stream', 'Set which gender of your site can start a live stream', 'Gender allowed to start a live stream', 'live_general', 'gender', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'onlyVerifiedStream', 'No', 'Only verified user can start a live stream', 'If enabled do not allow to stream users that are not verified', 'Only verified user can start a live stream', 'live_general', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'onlyVerifiedView', 'No', 'Only verified user can view stream', 'If enabled do not allow to view live stream if user is not verified', 'Only verified user can view stream', 'live_general', 'boolean', NULL, '0', '0', '0');
INSERT INTO `plugins_settings` (`plugin`, `setting`, `setting_val`, `title`, `info`, `label`, `block`, `setting_type`, `setting_options`, `orden`, `docs`, `premium`) VALUES ('live', 'announceVisitorLeave', 'Yes', 'Announce when a viewer leaves', 'If enabled when a user leaves the stream it will be announced in the chat for everybody', 'Announce when a viewer leaves', 'live_chat', 'boolean', NULL, '0', '0', '0');
-- ----------------------------
-- Table structure for live
-- ----------------------------
DROP TABLE IF EXISTS `live`;
CREATE TABLE `live` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `live` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `viewers` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `start_time` varchar(25) DEFAULT NULL,
  `end_time` varchar(25) DEFAULT '0',
  `custom_text` longtext,
  `is_streaming` varchar(255) DEFAULT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `lng` varchar(255) DEFAULT NULL,
  `gender` int(2) DEFAULT NULL,
  `credits` int(11) DEFAULT '0',
  `in_private` varchar(255) DEFAULT 'No',
  `private_price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ID` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for live_banned
-- ----------------------------
DROP TABLE IF EXISTS `live_banned`;
CREATE TABLE `live_banned` (
  `streamer_id` int(11) NOT NULL,
  `banned_id` int(11) NOT NULL,
  PRIMARY KEY (`streamer_id`,`banned_id`),
  UNIQUE KEY `USER` (`streamer_id`,`banned_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for live_streamer_banned
-- ----------------------------
DROP TABLE IF EXISTS `live_streamer_banned`;
CREATE TABLE `live_streamer_banned` (
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `USER` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;