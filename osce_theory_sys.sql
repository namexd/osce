/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : osce_theory_sys

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2018-03-12 16:02:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('2014_04_24_110151_create_oauth_scopes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_110304_create_oauth_grants_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_110403_create_oauth_grant_scopes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_110459_create_oauth_clients_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_110557_create_oauth_client_endpoints_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_110705_create_oauth_client_scopes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_110817_create_oauth_client_grants_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111002_create_oauth_sessions_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111109_create_oauth_session_scopes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111254_create_oauth_auth_codes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111403_create_oauth_auth_code_scopes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111518_create_oauth_access_tokens_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111657_create_oauth_access_token_scopes_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_24_111810_create_oauth_refresh_tokens_table', '1');
INSERT INTO `migrations` VALUES ('2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2014_10_12_100000_create_password_resets_table', '1');
INSERT INTO `migrations` VALUES ('2014_11_02_051938_create_roles_table', '1');
INSERT INTO `migrations` VALUES ('2014_11_02_052125_create_permissions_table', '1');
INSERT INTO `migrations` VALUES ('2014_11_02_052410_create_role_user_table', '1');
INSERT INTO `migrations` VALUES ('2014_11_02_092851_create_permission_role_table', '1');
INSERT INTO `migrations` VALUES ('2015_10_28_073625_create_jobs_table', '2');
INSERT INTO `migrations` VALUES ('2015_10_28_090253_create_failed_jobs_table', '3');
INSERT INTO `migrations` VALUES ('2014_04_24_111518_create_sss_table', '4');

-- ----------------------------
-- Table structure for oauth_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `expire_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_access_tokens_id_session_id_unique` (`id`,`session_id`) USING BTREE,
  KEY `oauth_access_tokens_session_id_index` (`session_id`) USING BTREE,
  CONSTRAINT `oauth_access_tokens_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_access_tokens
-- ----------------------------
INSERT INTO `oauth_access_tokens` VALUES ('0CWOfVdxjaYXdoIYMlHgYOmivoTGnp35x7Dof4e6', '78', '1495783483', '2017-05-26 14:24:43', '2017-05-26 14:24:43');
INSERT INTO `oauth_access_tokens` VALUES ('0DLSnQqHGagNfLXGsrhw1txUw1pW3FTv4kEedLTk', '17', '1495514737', '2017-05-23 11:45:37', '2017-05-23 11:45:37');
INSERT INTO `oauth_access_tokens` VALUES ('0G66rH9UA8ZbOqcPbOWOwVrqOAwVCvKph7Gsfg3l', '355', '1505802843', '2017-09-19 13:34:03', '2017-09-19 13:34:03');
INSERT INTO `oauth_access_tokens` VALUES ('0HKgtZTix2X4xKqTF00zm7XPl72O4gFZcpgyzjsi', '396', '1505893453', '2017-09-20 14:44:13', '2017-09-20 14:44:13');
INSERT INTO `oauth_access_tokens` VALUES ('0IKVCR8BLg0ueWpYcemkivBmPm2mIOBbnbSW5cHj', '366', '1505808679', '2017-09-19 15:11:19', '2017-09-19 15:11:19');
INSERT INTO `oauth_access_tokens` VALUES ('0jJaucisj21jHVUW7seb4u6GAN6VouRfD7qiFYP1', '372', '1505809211', '2017-09-19 15:20:11', '2017-09-19 15:20:11');
INSERT INTO `oauth_access_tokens` VALUES ('183MF2kPT3jgios0qhpRvNIHKqWqwhpbv2sa0lMt', '43', '1495783096', '2017-05-26 14:18:16', '2017-05-26 14:18:16');
INSERT INTO `oauth_access_tokens` VALUES ('1dD5AoIjv6t28BxFBrxSleka9llHcoNkcmcf3EST', '202', '1504752263', '2017-09-07 09:44:23', '2017-09-07 09:44:23');
INSERT INTO `oauth_access_tokens` VALUES ('1JWgGog3IQV2TiYbuNSsgT5DJX0C5ybW13ZVJppy', '20', '1495516875', '2017-05-23 12:21:15', '2017-05-23 12:21:15');
INSERT INTO `oauth_access_tokens` VALUES ('1ukRRgdasVjS8qpQ9ILSW59H0hVK2iuGYi3C9xGC', '93', '1495784935', '2017-05-26 14:48:55', '2017-05-26 14:48:55');
INSERT INTO `oauth_access_tokens` VALUES ('1YHSwciPgFB8jygLlP7zdLf5LDcY03x7JXsVXocj', '58', '1495783243', '2017-05-26 14:20:43', '2017-05-26 14:20:43');
INSERT INTO `oauth_access_tokens` VALUES ('1Zazy5HqVfJoLOq0865PHkB7GSULiAbWV6Z9HVez', '305', '1505376589', '2017-09-14 15:09:49', '2017-09-14 15:09:49');
INSERT INTO `oauth_access_tokens` VALUES ('218qh1yDGzWRdcbhTbDjPjJbkcP40isxC5or1MFy', '54', '1495783164', '2017-05-26 14:19:24', '2017-05-26 14:19:24');
INSERT INTO `oauth_access_tokens` VALUES ('2FdQoweCrXOZTRd3g4xrbpa3V9S6ScNmMh5kUsWz', '10', '1495347722', '2017-05-21 13:22:02', '2017-05-21 13:22:02');
INSERT INTO `oauth_access_tokens` VALUES ('2kBgN5FGEWjsnFZk4MSqedoJqFEzi3UwikU6WjOi', '412', '1505982158', '2017-09-21 15:22:38', '2017-09-21 15:22:38');
INSERT INTO `oauth_access_tokens` VALUES ('2KcyK3R0WIarodtvUdnDTYuGXZYn8g9fJdtM3mKn', '420', '1505985311', '2017-09-21 16:15:11', '2017-09-21 16:15:11');
INSERT INTO `oauth_access_tokens` VALUES ('2SfP96j4dLLab0CJN9PUK1GQPlHFCja9bv8uMiZ4', '129', '1496071156', '2017-05-29 22:19:16', '2017-05-29 22:19:16');
INSERT INTO `oauth_access_tokens` VALUES ('2X2t23ETDgQ8xL996BeSAPWsRdhEm8tH2Vg6qa9I', '52', '1495783151', '2017-05-26 14:19:11', '2017-05-26 14:19:11');
INSERT INTO `oauth_access_tokens` VALUES ('30eAEKLHexHbpSDwPZRBoMdgAzv10409PAW4aE00', '44', '1495783100', '2017-05-26 14:18:20', '2017-05-26 14:18:20');
INSERT INTO `oauth_access_tokens` VALUES ('34X510CEmwcKCvKJliH0gJ3be7xqJhK9TUY3TVn0', '199', '1504693520', '2017-09-06 17:25:20', '2017-09-06 17:25:20');
INSERT INTO `oauth_access_tokens` VALUES ('3ciplrPQpvm3M8i0BqGsgnUSsn0NPFQmXTGMXZcW', '286', '1505371492', '2017-09-14 13:44:53', '2017-09-14 13:44:53');
INSERT INTO `oauth_access_tokens` VALUES ('3dbaU8M0sBD2SCBTl3nTEPEBPV7ncBbILOOsxv2x', '125', '1496071004', '2017-05-29 22:16:44', '2017-05-29 22:16:44');
INSERT INTO `oauth_access_tokens` VALUES ('3HtaC2lDOLaFT6wLMXK2lxCanzbblhlb9ljBbT8k', '127', '1496071069', '2017-05-29 22:17:49', '2017-05-29 22:17:49');
INSERT INTO `oauth_access_tokens` VALUES ('3irkk1h9Fzlx7hSGgGGglZe0r1UjwYHy5w6saEYi', '388', '1505819772', '2017-09-19 18:16:12', '2017-09-19 18:16:12');
INSERT INTO `oauth_access_tokens` VALUES ('3lCptBorhdwVQtmuu7D2tmsRvFTIDovi9fBf5D34', '118', '1496036346', '2017-05-29 12:39:06', '2017-05-29 12:39:06');
INSERT INTO `oauth_access_tokens` VALUES ('3qDu4XOvOEiuxLhVWI02mZ32978QiSAqhH5TrrIn', '319', '1505378978', '2017-09-14 15:49:38', '2017-09-14 15:49:38');
INSERT INTO `oauth_access_tokens` VALUES ('3wf5fei88bgCx4u0jYwPYSCLko4YhwGUky2RWwJC', '102', '1495787418', '2017-05-26 15:30:18', '2017-05-26 15:30:18');
INSERT INTO `oauth_access_tokens` VALUES ('43PRxOcjfopzoSMslZVXhBXI3PsSpZr7lnzyabsd', '85', '1495783660', '2017-05-26 14:27:40', '2017-05-26 14:27:40');
INSERT INTO `oauth_access_tokens` VALUES ('4BRf9m7FMu2OKVZoVpcNjGBh5XWS792gqahySPV8', '152', '1496224684', '2017-05-31 16:58:04', '2017-05-31 16:58:04');
INSERT INTO `oauth_access_tokens` VALUES ('4ECsVTQqjUc5jqvjR0ocORhatpX3QpH09hgpusfC', '352', '1505734145', '2017-09-18 18:29:05', '2017-09-18 18:29:05');
INSERT INTO `oauth_access_tokens` VALUES ('4JYJuWdU0TQE526iIFvJJB5lfKcmc0lgoyPhOvOK', '8', '1495345483', '2017-05-21 12:44:43', '2017-05-21 12:44:43');
INSERT INTO `oauth_access_tokens` VALUES ('4OrWrWWYUm2ut5vAlHa8Ydrkqq2ZA9oQuRW869DW', '53', '1495783154', '2017-05-26 14:19:14', '2017-05-26 14:19:14');
INSERT INTO `oauth_access_tokens` VALUES ('4QZ81ZqV1ZA5G81Exbhs5b2B1rVfryyZuCSwiXmd', '71', '1495783350', '2017-05-26 14:22:30', '2017-05-26 14:22:30');
INSERT INTO `oauth_access_tokens` VALUES ('581vthsJpeg3FK21RjCeFQwj2KuCPGkj1soUDQxc', '325', '1505706419', '2017-09-18 10:46:59', '2017-09-18 10:46:59');
INSERT INTO `oauth_access_tokens` VALUES ('5CAwHhEojMelinkKeT7RvXK45D0LmyiHITfNZCyS', '34', '1495782967', '2017-05-26 14:16:07', '2017-05-26 14:16:07');
INSERT INTO `oauth_access_tokens` VALUES ('5cb8gCvfH34sMLsFmrpWvE405ZXp0cD1rbTwaYLt', '77', '1495783436', '2017-05-26 14:23:56', '2017-05-26 14:23:56');
INSERT INTO `oauth_access_tokens` VALUES ('5GQ1uVqykiBiZaYbVC5NDXv8xXAPj7xvzepaqGL1', '114', '1495856497', '2017-05-27 10:41:37', '2017-05-27 10:41:37');
INSERT INTO `oauth_access_tokens` VALUES ('5iV3seT1Iz3dpbRr8ItVmSbKdZ2kpaeAi3Akygb3', '298', '1505373763', '2017-09-14 14:22:43', '2017-09-14 14:22:43');
INSERT INTO `oauth_access_tokens` VALUES ('5IxdsGUpbd6N98bUnycKhcFWlcNNdfDhNeUEtn0z', '3', '1495283573', '2017-05-20 19:32:53', '2017-05-20 19:32:53');
INSERT INTO `oauth_access_tokens` VALUES ('5t8BUcBxSmngsWeXnSqZbrGY54M8YJAaaWLcRHJl', '431', '1514969626', '2018-01-03 15:53:46', '2018-01-03 15:53:46');
INSERT INTO `oauth_access_tokens` VALUES ('6FG06y2RwKUzgX2n1DX5yfrzIWUSUZW0UT8xi2ap', '433', '1514970801', '2018-01-03 16:13:21', '2018-01-03 16:13:21');
INSERT INTO `oauth_access_tokens` VALUES ('6j7TF8zxDpVYBggGeD9JrHkkTLi0kyirX8Cgz2sU', '267', '1505199396', '2017-09-12 13:56:36', '2017-09-12 13:56:36');
INSERT INTO `oauth_access_tokens` VALUES ('6m0Mj3oNhTLFYgW0y6faYcgipCjbRF3AE1hxGNMe', '163', '1496314958', '2017-06-01 18:02:38', '2017-06-01 18:02:38');
INSERT INTO `oauth_access_tokens` VALUES ('6N6ly0wDCpFXYI017bmYow6SOOgbWLLS1naz8fRT', '260', '1505189072', '2017-09-12 11:04:32', '2017-09-12 11:04:32');
INSERT INTO `oauth_access_tokens` VALUES ('6niE1MxHE0WXRWJzRAJ0B5MzAvUNAGjJescLRFWL', '348', '1505733779', '2017-09-18 18:22:59', '2017-09-18 18:22:59');
INSERT INTO `oauth_access_tokens` VALUES ('71XEs7QbmU2nPjSh5AF4jTf0ALoKT63NM90LdVku', '375', '1505811184', '2017-09-19 15:53:04', '2017-09-19 15:53:04');
INSERT INTO `oauth_access_tokens` VALUES ('72617YQ32rVAcmQmccyIQYGOaHOIueqU34FI4p2U', '379', '1505812132', '2017-09-19 16:08:52', '2017-09-19 16:08:52');
INSERT INTO `oauth_access_tokens` VALUES ('7hXpbGIep6kuBcJE9Gh1mfbNTViBxHZxPzSJfD1U', '258', '1505187129', '2017-09-12 10:32:09', '2017-09-12 10:32:09');
INSERT INTO `oauth_access_tokens` VALUES ('7KUuJH1hHxefXrm4xN3vn45vpAGZGI77m0XUtFeZ', '279', '1505369860', '2017-09-14 13:17:40', '2017-09-14 13:17:40');
INSERT INTO `oauth_access_tokens` VALUES ('7m3D4ICyk6JN5yPXVRvWra0dwvSiWdKMZ7rZ3FNE', '308', '1505376697', '2017-09-14 15:11:37', '2017-09-14 15:11:37');
INSERT INTO `oauth_access_tokens` VALUES ('7N3okSIUrD6xYEbnKv7XJOkttYFlPALAHyMDtyYv', '153', '1496291468', '2017-06-01 11:31:08', '2017-06-01 11:31:08');
INSERT INTO `oauth_access_tokens` VALUES ('7TpWWSJ3eX18tnyqXdbyfJ0p700F9PcYuL6YsgE9', '262', '1505191122', '2017-09-12 11:38:42', '2017-09-12 11:38:42');
INSERT INTO `oauth_access_tokens` VALUES ('8agQi041T8mK5jwDvZUYM8RSrapXTHrxdpZxQfB2', '32', '1495767231', '2017-05-26 09:53:51', '2017-05-26 09:53:51');
INSERT INTO `oauth_access_tokens` VALUES ('8f7jp9RIN3D4O5ub0Lxh7r38qd0t3H9oIITceKqR', '131', '1496115108', '2017-05-30 10:31:48', '2017-05-30 10:31:48');
INSERT INTO `oauth_access_tokens` VALUES ('8pRhL4BbFSw94RUgs4daXHfsf9vmBZJfQbXxWXti', '413', '1505985046', '2017-09-21 16:10:46', '2017-09-21 16:10:46');
INSERT INTO `oauth_access_tokens` VALUES ('91epAW4iNuvFNpjWJBHMHne3376EojxDtyGmgTvz', '274', '1505357855', '2017-09-14 09:57:35', '2017-09-14 09:57:35');
INSERT INTO `oauth_access_tokens` VALUES ('92dwxEZWXU9VmCy2MCCh5l8r0uaCq87LH3u7qvlM', '381', '1505812580', '2017-09-19 16:16:20', '2017-09-19 16:16:20');
INSERT INTO `oauth_access_tokens` VALUES ('967qXgH2LjBxCfDfNntIcByeqqhesrfX6J9Sc0kF', '411', '1505980322', '2017-09-21 14:52:02', '2017-09-21 14:52:02');
INSERT INTO `oauth_access_tokens` VALUES ('9FGbvcHQBjdorbc6qMkRw9gyV2NzKGp4V9aiPpBV', '221', '1504867161', '2017-09-08 17:39:22', '2017-09-08 17:39:22');
INSERT INTO `oauth_access_tokens` VALUES ('9qudnBEWe60iMJUCcABsBDZruVIV2zLttexlC82o', '351', '1505734039', '2017-09-18 18:27:19', '2017-09-18 18:27:19');
INSERT INTO `oauth_access_tokens` VALUES ('A0TrGBoRRxN9eENiPhmUb8V3dMTlyR89tY9zMVaW', '329', '1505706809', '2017-09-18 10:53:29', '2017-09-18 10:53:29');
INSERT INTO `oauth_access_tokens` VALUES ('a36cwKYSXgE3Erwzgl0Vsv163LTkQ5MWr9MaggLG', '269', '1505356806', '2017-09-14 09:40:06', '2017-09-14 09:40:06');
INSERT INTO `oauth_access_tokens` VALUES ('A87Yh78VLFvGpNoAt71xOTRR9fa93oftGh8sYwUI', '184', '1504687685', '2017-09-06 15:48:05', '2017-09-06 15:48:05');
INSERT INTO `oauth_access_tokens` VALUES ('A8CeupqNVfMPLvxnuk35Q48M2Xp1uMriNId5Gqn2', '180', '1504687496', '2017-09-06 15:44:56', '2017-09-06 15:44:56');
INSERT INTO `oauth_access_tokens` VALUES ('aEwuBbUdjd1k0NTSrFTUwbSFRwtQWlZw5hxhE907', '249', '1505130257', '2017-09-11 18:44:17', '2017-09-11 18:44:17');
INSERT INTO `oauth_access_tokens` VALUES ('aI30Xw4rfoBIIz9p2qEnpe1zzvFLknBpitb9QAFk', '324', '1505706347', '2017-09-18 10:45:47', '2017-09-18 10:45:47');
INSERT INTO `oauth_access_tokens` VALUES ('ALqoNNKRCx5EB3pz16SulwSTOuyzQv2xxtfGNFmR', '115', '1495858329', '2017-05-27 11:12:09', '2017-05-27 11:12:09');
INSERT INTO `oauth_access_tokens` VALUES ('ALs9EjY7vmj0eqBUM2gIaiFQ1JcVLXjCgFEcc61H', '91', '1495784396', '2017-05-26 14:39:56', '2017-05-26 14:39:56');
INSERT INTO `oauth_access_tokens` VALUES ('anStwdwSk0txSDFOLbqLcfwrSM6KnBppDPZNdwN7', '89', '1495783953', '2017-05-26 14:32:34', '2017-05-26 14:32:34');
INSERT INTO `oauth_access_tokens` VALUES ('AOxci4gLcivSIWCYcWzOg9DIOFUjtVIKybpItSYt', '374', '1505810938', '2017-09-19 15:48:58', '2017-09-19 15:48:58');
INSERT INTO `oauth_access_tokens` VALUES ('ap2Tl2CftVpzASEmKd9BbRad8M4XAOR5RJvIKEay', '92', '1495784506', '2017-05-26 14:41:46', '2017-05-26 14:41:46');
INSERT INTO `oauth_access_tokens` VALUES ('aSbOF47qXyT0IntWw7Aq9fazNnRTPqV9GnZdHBC5', '368', '1505809177', '2017-09-19 15:19:37', '2017-09-19 15:19:37');
INSERT INTO `oauth_access_tokens` VALUES ('auAKDh4g64CrSREgfVfV5yNfnvn2xbdZJZF2CWP3', '362', '1505808291', '2017-09-19 15:04:51', '2017-09-19 15:04:51');
INSERT INTO `oauth_access_tokens` VALUES ('AuD4mnUq4eefZOHSzIOqC71FRJ2bfGf3VozhsYQc', '306', '1505376639', '2017-09-14 15:10:39', '2017-09-14 15:10:39');
INSERT INTO `oauth_access_tokens` VALUES ('AuEJDhuOqHyLOZX6rcY0EBMelf9FYAMrFfazEjdE', '226', '1504869317', '2017-09-08 18:15:17', '2017-09-08 18:15:17');
INSERT INTO `oauth_access_tokens` VALUES ('AxsiaKZZx3wFCy7IZCPpUV7sct7LYakotqky4gQu', '160', '1496308120', '2017-06-01 16:08:41', '2017-06-01 16:08:41');
INSERT INTO `oauth_access_tokens` VALUES ('aZdpx7NnNlZZqjt7FRL8f38qwU5vXkfznAkSvncx', '47', '1495783110', '2017-05-26 14:18:30', '2017-05-26 14:18:30');
INSERT INTO `oauth_access_tokens` VALUES ('aZkJAKxEblhwhEHiME7WQR8CCdYwHQr9VCYZdU1I', '80', '1495783548', '2017-05-26 14:25:48', '2017-05-26 14:25:48');
INSERT INTO `oauth_access_tokens` VALUES ('b1EeE3YNUn0XjJASQVVtGLWpfo8cA9qIVj0KF6w6', '204', '1504757783', '2017-09-07 11:16:23', '2017-09-07 11:16:23');
INSERT INTO `oauth_access_tokens` VALUES ('b7JhRrAB6YeqZd3O1V9v4oGY7yZ4cqNX3VxvwFF8', '210', '1504840925', '2017-09-08 10:22:05', '2017-09-08 10:22:05');
INSERT INTO `oauth_access_tokens` VALUES ('BFFtgmDcHe4AXpJlkfG2IPFW3kmlcWBxcCMJownL', '380', '1505812260', '2017-09-19 16:11:00', '2017-09-19 16:11:00');
INSERT INTO `oauth_access_tokens` VALUES ('BFTWuF4ig3W3AHqbKWC0BnjzCawN0POIRjpJo9t2', '81', '1495783564', '2017-05-26 14:26:04', '2017-05-26 14:26:04');
INSERT INTO `oauth_access_tokens` VALUES ('bGV1z3VklSTSCC7qFTS7sbU5be7xDiN8kWpyipza', '430', '1514969570', '2018-01-03 15:52:50', '2018-01-03 15:52:50');
INSERT INTO `oauth_access_tokens` VALUES ('Bmu9tVTrzudbjoXtlLDLLJSpDuywtrUnZNAYitH2', '2', '1495276601', '2017-05-20 17:36:41', '2017-05-20 17:36:41');
INSERT INTO `oauth_access_tokens` VALUES ('bNLpG56WbkelPDnemLS20wv0jm0rdcpoDkiFo7uz', '292', '1505373583', '2017-09-14 14:19:43', '2017-09-14 14:19:43');
INSERT INTO `oauth_access_tokens` VALUES ('bPpZs3J7q4sxtHq00J3EOvTdeu8Mz7WaoxAUmEEq', '271', '1505356922', '2017-09-14 09:42:02', '2017-09-14 09:42:02');
INSERT INTO `oauth_access_tokens` VALUES ('BpVDcIJwqb72Iq2CZrj07JEgfgQlLK6qwFfSnTxj', '386', '1505813959', '2017-09-19 16:39:20', '2017-09-19 16:39:20');
INSERT INTO `oauth_access_tokens` VALUES ('bQv1fmE3sUr1lVzRLqRxJ6aDBHvtWDdUVKJSTfIM', '220', '1504866903', '2017-09-08 17:35:03', '2017-09-08 17:35:03');
INSERT INTO `oauth_access_tokens` VALUES ('brhHMPQp5Qk5D8bhboGHkdrA4Mm9ASopfcTYbyz6', '24', '1495518495', '2017-05-23 12:48:15', '2017-05-23 12:48:15');
INSERT INTO `oauth_access_tokens` VALUES ('btXrXkCIsJ0C2bazT37okzbbGicoi90rIMSkFgD7', '194', '1504693266', '2017-09-06 17:21:06', '2017-09-06 17:21:06');
INSERT INTO `oauth_access_tokens` VALUES ('Bu9ukIzM6e8m6tTaQdvF6xHq3HwoqFadUe7OSFSU', '261', '1505190242', '2017-09-12 11:24:02', '2017-09-12 11:24:02');
INSERT INTO `oauth_access_tokens` VALUES ('BZgHjNTB0RX9BkDADdu4xq5a8e4STdPSgwecR112', '326', '1505706600', '2017-09-18 10:50:00', '2017-09-18 10:50:00');
INSERT INTO `oauth_access_tokens` VALUES ('c4ikiIKrfahmkCcMo80lHtfdv1DzT9rFHvNsx2LO', '395', '1505892738', '2017-09-20 14:32:18', '2017-09-20 14:32:18');
INSERT INTO `oauth_access_tokens` VALUES ('C4X1bCZsJAf0cteAcd03sOIxzdgHUGyi7o9iwmDC', '61', '1495783281', '2017-05-26 14:21:21', '2017-05-26 14:21:21');
INSERT INTO `oauth_access_tokens` VALUES ('CcbYc29imVPoY6scSUqEVNdQjqPTjBpPYUO2CVqT', '429', '1514969517', '2018-01-03 15:51:57', '2018-01-03 15:51:57');
INSERT INTO `oauth_access_tokens` VALUES ('CF2aqg8VJ3wdZdDdHfQiRagnKaIiS611eXhjEUax', '389', '1505876282', '2017-09-20 09:58:02', '2017-09-20 09:58:02');
INSERT INTO `oauth_access_tokens` VALUES ('CgCn7Exy3pVIHzzbebgqRqY5wFxLaWZIwl8rORQf', '320', '1505379323', '2017-09-14 15:55:23', '2017-09-14 15:55:23');
INSERT INTO `oauth_access_tokens` VALUES ('CiP2U8WK9btJmyZ2EZAnGILmmBjTIcAmx76TD2Qj', '49', '1495783131', '2017-05-26 14:18:51', '2017-05-26 14:18:51');
INSERT INTO `oauth_access_tokens` VALUES ('cm4sC0YDAMBujfk9XzkP1TIcgTUqsJ5u4iHCTEJ4', '57', '1495783200', '2017-05-26 14:20:00', '2017-05-26 14:20:00');
INSERT INTO `oauth_access_tokens` VALUES ('cNipwknkitiAcgCqEOH178QtxLxsF0X6KK8I131f', '316', '1505377957', '2017-09-14 15:32:37', '2017-09-14 15:32:37');
INSERT INTO `oauth_access_tokens` VALUES ('CNmANjudhX4yY2eS2oKudxWcyKh4xYBV8ReYsJy1', '321', '1505381057', '2017-09-14 16:24:17', '2017-09-14 16:24:17');
INSERT INTO `oauth_access_tokens` VALUES ('cOtTE4Vk0Yjrh4PP5GnqzDvKySMKqSXdexpdJjIF', '7', '1495345470', '2017-05-21 12:44:30', '2017-05-21 12:44:30');
INSERT INTO `oauth_access_tokens` VALUES ('CRmgZj54PYauQ4USkP4pUVotsmcaVXxz4rADOXdm', '175', '1504687346', '2017-09-06 15:42:26', '2017-09-06 15:42:26');
INSERT INTO `oauth_access_tokens` VALUES ('cuRxkUAf5d7JA6yXPuSSePJv2dNDzW0nF9rRD89J', '124', '1496039718', '2017-05-29 13:35:18', '2017-05-29 13:35:18');
INSERT INTO `oauth_access_tokens` VALUES ('D0gNsFOhT3n6o5zWV98vtn9L6Pu0QPhL5oYRV4Vz', '331', '1505707056', '2017-09-18 10:57:36', '2017-09-18 10:57:36');
INSERT INTO `oauth_access_tokens` VALUES ('d0tco5LXTkRSTgltcANxSwrGCW5RRvKp7m4ObUo4', '137', '1496201789', '2017-05-31 10:36:29', '2017-05-31 10:36:29');
INSERT INTO `oauth_access_tokens` VALUES ('D0Whe8CthsHZO25YOPyZcWKah0vZGek468mQ7aPI', '370', '1505809202', '2017-09-19 15:20:03', '2017-09-19 15:20:03');
INSERT INTO `oauth_access_tokens` VALUES ('d1aqHf8QnXfVVj2rnoMgvXIpYo0OWVytFG0uuoZE', '357', '1505803673', '2017-09-19 13:47:54', '2017-09-19 13:47:54');
INSERT INTO `oauth_access_tokens` VALUES ('d4rrhuvMsKUTzM6EYEIjKyOQdqv1SDQNJD4LT4i6', '41', '1495783082', '2017-05-26 14:18:02', '2017-05-26 14:18:02');
INSERT INTO `oauth_access_tokens` VALUES ('D5ofHVPun19A7LRF0hbMbcLak3UU8HFossDpEZzI', '176', '1504687384', '2017-09-06 15:43:04', '2017-09-06 15:43:04');
INSERT INTO `oauth_access_tokens` VALUES ('DbjRabWBnGh89XMJk0MsfAx4CSU5GpKeXzULkCbP', '181', '1504687597', '2017-09-06 15:46:37', '2017-09-06 15:46:37');
INSERT INTO `oauth_access_tokens` VALUES ('dctT3ek1gouM6TJXCYTexecMJwhBwj94bGfm70HX', '189', '1504690371', '2017-09-06 16:32:51', '2017-09-06 16:32:51');
INSERT INTO `oauth_access_tokens` VALUES ('ddMrqpDVlpdjHq5ZpUb9K74wnGFXt8MFS3rTQauQ', '350', '1505734011', '2017-09-18 18:26:51', '2017-09-18 18:26:51');
INSERT INTO `oauth_access_tokens` VALUES ('DEbohoptX4JezyMN6CncebeaYYexNQA357HMDrkH', '146', '1496219995', '2017-05-31 15:39:55', '2017-05-31 15:39:55');
INSERT INTO `oauth_access_tokens` VALUES ('dGeLixR229aleXQPFt9fgR6GOzFCLpf2BpRL0J7P', '238', '1505128425', '2017-09-11 18:13:45', '2017-09-11 18:13:45');
INSERT INTO `oauth_access_tokens` VALUES ('DMQ5TaTvDUoEIOQ82NWhI5gL3QpWAT513P3DrfnP', '59', '1495783244', '2017-05-26 14:20:44', '2017-05-26 14:20:44');
INSERT INTO `oauth_access_tokens` VALUES ('dnIks2ocuAkPEnggxeMqyeTJvQdjT204UW2NeVdc', '300', '1505374056', '2017-09-14 14:27:36', '2017-09-14 14:27:36');
INSERT INTO `oauth_access_tokens` VALUES ('e92VpGrlLzqGQuRf4hXYlD1lXE8DopzHSoujxcZp', '346', '1505733723', '2017-09-18 18:22:03', '2017-09-18 18:22:03');
INSERT INTO `oauth_access_tokens` VALUES ('eaprjzgdg6U4IJqO2YUcBcgWrbWum08Escn6p1ZM', '5', '1495345261', '2017-05-21 12:41:01', '2017-05-21 12:41:01');
INSERT INTO `oauth_access_tokens` VALUES ('ECNwhKtGIjaFSdGCfMS5YKZjGGx0aXqkxz08uL8f', '200', '1504695096', '2017-09-06 17:51:36', '2017-09-06 17:51:36');
INSERT INTO `oauth_access_tokens` VALUES ('EeE2Hogx1DQCRaYi9jcMdRD1NwWac1iAIdHIKjDC', '56', '1495783193', '2017-05-26 14:19:53', '2017-05-26 14:19:53');
INSERT INTO `oauth_access_tokens` VALUES ('ehPyvhL4T4yV42SIDr4PnqCQiZXjf5KW06HMCcMl', '251', '1505130323', '2017-09-11 18:45:23', '2017-09-11 18:45:23');
INSERT INTO `oauth_access_tokens` VALUES ('eHSb2G0l6T74wXjmjldEYJDcrU06A2u8WzQKh72X', '111', '1495853200', '2017-05-27 09:46:40', '2017-05-27 09:46:40');
INSERT INTO `oauth_access_tokens` VALUES ('eiWiVJNr3zDsPB6MgBdPqJ6JctA9YbwxuPjEbFPq', '365', '1505808601', '2017-09-19 15:10:01', '2017-09-19 15:10:01');
INSERT INTO `oauth_access_tokens` VALUES ('ektrXMks6tTJE413CHFUJSDkeK2tyJF1tLu0ZRt6', '168', '1504671785', '2017-09-06 11:23:05', '2017-09-06 11:23:05');
INSERT INTO `oauth_access_tokens` VALUES ('EQ3nBaGMCPcxXnydqVae4gVBdoH6kABUgPlTuLGF', '301', '1505374079', '2017-09-14 14:27:59', '2017-09-14 14:27:59');
INSERT INTO `oauth_access_tokens` VALUES ('eRZ4e2RrdLmfZ0FWIgylQPKxYN2OSEj26zCHcUT7', '289', '1505371921', '2017-09-14 13:52:01', '2017-09-14 13:52:01');
INSERT INTO `oauth_access_tokens` VALUES ('ErzbbpXVcfN60umYs41m6Pat06sMOQC9TZ4dsipY', '307', '1505376657', '2017-09-14 15:10:57', '2017-09-14 15:10:57');
INSERT INTO `oauth_access_tokens` VALUES ('ErZll0HD7xgbLr8wAkwaRgFMbMJHAhrmFXJZlwk0', '215', '1504852972', '2017-09-08 13:42:52', '2017-09-08 13:42:52');
INSERT INTO `oauth_access_tokens` VALUES ('eSLUGwAbD5Qk8ERrIkRAw37hGWF8aZvW9o5UVvrk', '272', '1505357012', '2017-09-14 09:43:32', '2017-09-14 09:43:32');
INSERT INTO `oauth_access_tokens` VALUES ('EUPXOqv3lKcKZJOPufjVWJ0b7iq5XJY83SPkJu5V', '234', '1505124336', '2017-09-11 17:05:36', '2017-09-11 17:05:36');
INSERT INTO `oauth_access_tokens` VALUES ('EVRFX3WgPnesDqgiBsm3gWuwcbO15wr6bnS9mCeN', '97', '1495785414', '2017-05-26 14:56:54', '2017-05-26 14:56:54');
INSERT INTO `oauth_access_tokens` VALUES ('evsdAGrvrFO5CIOrRy6WlJHFKpBmzmyal5p5aJ7n', '263', '1505191413', '2017-09-12 11:43:33', '2017-09-12 11:43:33');
INSERT INTO `oauth_access_tokens` VALUES ('ExDgvFtt6hxWXEkKqQ0XDU3G0mZpK95tH1VwvOWX', '295', '1505373714', '2017-09-14 14:21:54', '2017-09-14 14:21:54');
INSERT INTO `oauth_access_tokens` VALUES ('F6DDUeQkdCit8VeQNjgoX81CR643dVfhqjRJkPfs', '232', '1505122662', '2017-09-11 16:37:42', '2017-09-11 16:37:42');
INSERT INTO `oauth_access_tokens` VALUES ('FbNmSbSW8rFCK6HRcbnJR8M7dB9DeFxIeAv7jj1m', '392', '1505892386', '2017-09-20 14:26:26', '2017-09-20 14:26:26');
INSERT INTO `oauth_access_tokens` VALUES ('FcWkaUYFPXUk4Jkfsjhr2Bz3fBbYrxFvG2nItJs7', '248', '1505130242', '2017-09-11 18:44:02', '2017-09-11 18:44:02');
INSERT INTO `oauth_access_tokens` VALUES ('FFXtchQXzZ2kgWFQOosS1CmvSYuY4s7Ek18MzR1U', '134', '1496115190', '2017-05-30 10:33:10', '2017-05-30 10:33:10');
INSERT INTO `oauth_access_tokens` VALUES ('FhXP7qHdUIgOKwTOyN8owg9kCS5Vi8UknP10zDUG', '293', '1505373583', '2017-09-14 14:19:43', '2017-09-14 14:19:43');
INSERT INTO `oauth_access_tokens` VALUES ('FiedbriKzDcGJONMW6VbQYpKaL6m8oJa3bptkLkV', '172', '1504683049', '2017-09-06 14:30:49', '2017-09-06 14:30:49');
INSERT INTO `oauth_access_tokens` VALUES ('fkPGdviygQPeko5rISYmVZXdAFCxS8n0LKTRuXWh', '259', '1505188634', '2017-09-12 10:57:14', '2017-09-12 10:57:14');
INSERT INTO `oauth_access_tokens` VALUES ('fl1MO2bngPpYAizQISLqhMOmGaKEPp8kj7oM3IAQ', '150', '1496221135', '2017-05-31 15:58:55', '2017-05-31 15:58:55');
INSERT INTO `oauth_access_tokens` VALUES ('FO26LvYY34NwbFKdKxuOJfLWq5RTaiiTQS2pblon', '257', '1505187100', '2017-09-12 10:31:40', '2017-09-12 10:31:40');
INSERT INTO `oauth_access_tokens` VALUES ('Fo85a5WtrLf9eNKKKINuufkRLxVY3ixG7wnFz74N', '394', '1505892669', '2017-09-20 14:31:09', '2017-09-20 14:31:09');
INSERT INTO `oauth_access_tokens` VALUES ('Fp36KZ3reqQtmsJRiITl03ogaCacqInLnKdA0vu6', '193', '1504693190', '2017-09-06 17:19:50', '2017-09-06 17:19:50');
INSERT INTO `oauth_access_tokens` VALUES ('FPLmQsB5rLNRKxA5kyJMKPFtxTB6B7POKYhNCjnj', '313', '1505377865', '2017-09-14 15:31:05', '2017-09-14 15:31:05');
INSERT INTO `oauth_access_tokens` VALUES ('Fq0zSmpHcW8XAkDcaksyVylQOipzaDyudQlYfAQd', '90', '1495784184', '2017-05-26 14:36:24', '2017-05-26 14:36:24');
INSERT INTO `oauth_access_tokens` VALUES ('fqccIEGhf5wUtZJu4hHS77tdnc5dglevLKuB0Ids', '414', '1505985047', '2017-09-21 16:10:47', '2017-09-21 16:10:47');
INSERT INTO `oauth_access_tokens` VALUES ('fv6qbpMRzLIkUWxZYGKnQ7LvDnABMhh2WHrwcAr3', '333', '1505707153', '2017-09-18 10:59:13', '2017-09-18 10:59:13');
INSERT INTO `oauth_access_tokens` VALUES ('fW7FAIvnArdIcUOMjqjmkNqpBrSwrXzxG89uyLDX', '4', '1495283634', '2017-05-20 19:33:54', '2017-05-20 19:33:54');
INSERT INTO `oauth_access_tokens` VALUES ('FZ3GzGAEYOoxfSi8LrFv9Qjkbah5Dn80kf1bIv1m', '382', '1505812714', '2017-09-19 16:18:34', '2017-09-19 16:18:34');
INSERT INTO `oauth_access_tokens` VALUES ('G4DLU4q5tKfqO69gneDPRen3dnpWBGu8271NKinY', '340', '1505731374', '2017-09-18 17:42:54', '2017-09-18 17:42:54');
INSERT INTO `oauth_access_tokens` VALUES ('g8DavJlBxmOP4XNn1QUhMuIgXXZZWqLtEoiMpg1l', '337', '1505708555', '2017-09-18 11:22:35', '2017-09-18 11:22:35');
INSERT INTO `oauth_access_tokens` VALUES ('g9hmI19wD38au1PNKNCIi6HIR8Mye3MV4ZxLOQk8', '28', '1495763574', '2017-05-26 08:52:54', '2017-05-26 08:52:54');
INSERT INTO `oauth_access_tokens` VALUES ('GdvO3q4QjZHoFnJk2RG9kwgewKj7wxVFRFmImexO', '216', '1504853187', '2017-09-08 13:46:27', '2017-09-08 13:46:27');
INSERT INTO `oauth_access_tokens` VALUES ('gfoZpJEM7A88ZkyvQEuZzIJYPnl3dGcAb6W1SODj', '377', '1505811402', '2017-09-19 15:56:42', '2017-09-19 15:56:42');
INSERT INTO `oauth_access_tokens` VALUES ('Ghd9k0Pm4E2ADbSkavDYU1lagXAbiRde6GHgoQrx', '29', '1495763798', '2017-05-26 08:56:38', '2017-05-26 08:56:38');
INSERT INTO `oauth_access_tokens` VALUES ('gIDJitoXBhoUXSExuLkThQ4FEXPZBrZLm12OoF5i', '334', '1505707306', '2017-09-18 11:01:46', '2017-09-18 11:01:46');
INSERT INTO `oauth_access_tokens` VALUES ('GJbAZeys0NVDHdbLU7Y1VQLiX5PhKSb23YDWAkhk', '426', '1505986962', '2017-09-21 16:42:42', '2017-09-21 16:42:42');
INSERT INTO `oauth_access_tokens` VALUES ('GLPMHqRpGUZharet0RmKmmJlmT7vmw3a4tDNjT6w', '178', '1504687446', '2017-09-06 15:44:06', '2017-09-06 15:44:06');
INSERT INTO `oauth_access_tokens` VALUES ('Gmfn2JEcGthDLyATvG1E3mKfptKsJoVL5S2oieL6', '207', '1504758462', '2017-09-07 11:27:43', '2017-09-07 11:27:43');
INSERT INTO `oauth_access_tokens` VALUES ('gnS5hNgWn0CvWL438vPGQhuJJ9pfyBFCJhJBqRxV', '323', '1505705860', '2017-09-18 10:37:40', '2017-09-18 10:37:40');
INSERT INTO `oauth_access_tokens` VALUES ('gSuuKi2WxcbIzAfePZp5mLrbETRkDVpooNxkjlT5', '353', '1505802767', '2017-09-19 13:32:48', '2017-09-19 13:32:48');
INSERT INTO `oauth_access_tokens` VALUES ('gUNRIXwiNc9kjOHK0y2mZ7o1FduJQZxkzO8FMmTk', '406', '1505980076', '2017-09-21 14:47:56', '2017-09-21 14:47:56');
INSERT INTO `oauth_access_tokens` VALUES ('gVONQ0ZsZp4HKWjXlWBhem3JwH2dbhw2dcFzkNeF', '26', '1495523955', '2017-05-23 14:19:15', '2017-05-23 14:19:15');
INSERT INTO `oauth_access_tokens` VALUES ('gWwGdDMi98wQulnCruWjib9yv0dJVPnEBbllL25C', '376', '1505811235', '2017-09-19 15:53:55', '2017-09-19 15:53:55');
INSERT INTO `oauth_access_tokens` VALUES ('GxMQaQ636W1mg361vZtOz51hqiLLleKL7Mg8XVEc', '161', '1496314948', '2017-06-01 18:02:28', '2017-06-01 18:02:28');
INSERT INTO `oauth_access_tokens` VALUES ('h2lpWGQlxvMc6p5B8szAyzAhA3rBhovzXHONuAvg', '84', '1495783650', '2017-05-26 14:27:30', '2017-05-26 14:27:30');
INSERT INTO `oauth_access_tokens` VALUES ('hDI33pCOmQKi1cND6yWV8XpDE1EZUc71tydGLIrw', '222', '1504867717', '2017-09-08 17:48:38', '2017-09-08 17:48:38');
INSERT INTO `oauth_access_tokens` VALUES ('HEKF8ETUOIYkHGuNiV6orhdsZprHdR9ZUwlanMIS', '299', '1505373771', '2017-09-14 14:22:51', '2017-09-14 14:22:51');
INSERT INTO `oauth_access_tokens` VALUES ('hl2eJLhjPvkazAczmVDI2peTX10Q2w8sJb4RzdqU', '213', '1504852627', '2017-09-08 13:37:07', '2017-09-08 13:37:07');
INSERT INTO `oauth_access_tokens` VALUES ('HlawYJ5YyVtcglkCg4DWC9YdCd8B5Loibepk5Kg2', '432', '1514969657', '2018-01-03 15:54:17', '2018-01-03 15:54:17');
INSERT INTO `oauth_access_tokens` VALUES ('HltMZvoSaANHga6tID3gZKybSDwCbpOqlnwmgsHE', '136', '1496201358', '2017-05-31 10:29:18', '2017-05-31 10:29:18');
INSERT INTO `oauth_access_tokens` VALUES ('hoS6Hi9dgnBTHlZ1vm864dEmIEo3oo2wn6HhugRK', '253', '1505184216', '2017-09-12 09:43:36', '2017-09-12 09:43:36');
INSERT INTO `oauth_access_tokens` VALUES ('hRvNhdRILu6CjTtVCO77kG6jFqUfjfQ6Y7hxlkGF', '399', '1505894757', '2017-09-20 15:05:57', '2017-09-20 15:05:57');
INSERT INTO `oauth_access_tokens` VALUES ('HslhC9VH1VN5jMdUreCjj40n9pLFfmOyBi9xYIvh', '135', '1496201352', '2017-05-31 10:29:12', '2017-05-31 10:29:12');
INSERT INTO `oauth_access_tokens` VALUES ('hURuZ58chxYyx1WT7dWGHXsQ8SnF6OpHcdhF18xp', '116', '1495858430', '2017-05-27 11:13:50', '2017-05-27 11:13:50');
INSERT INTO `oauth_access_tokens` VALUES ('HZMD87ESAg2O2mYVRjGA6qALmm7UHbayajXugs0s', '70', '1495783330', '2017-05-26 14:22:10', '2017-05-26 14:22:10');
INSERT INTO `oauth_access_tokens` VALUES ('HzUhpiKA017jmitHeSJd8qOmNJSOqVuUZtgaS3l2', '48', '1495783127', '2017-05-26 14:18:47', '2017-05-26 14:18:47');
INSERT INTO `oauth_access_tokens` VALUES ('I4e0jliUoWECXSe1PA75ayxIgcaKiIMhe1KpVL1l', '165', '1496315010', '2017-06-01 18:03:30', '2017-06-01 18:03:30');
INSERT INTO `oauth_access_tokens` VALUES ('i6MKPLaBHyKbU8h7oA7q7sSK1YjDqpHwtMTRurxG', '87', '1495783776', '2017-05-26 14:29:36', '2017-05-26 14:29:36');
INSERT INTO `oauth_access_tokens` VALUES ('i76TNFdF4dK1iRZUZVODd8CrVjoyFusGoqyDPbpt', '110', '1495852221', '2017-05-27 09:30:21', '2017-05-27 09:30:21');
INSERT INTO `oauth_access_tokens` VALUES ('I7K1uaefUIKb5CWzf9YHjBxmIsBRuI951EaOEIMW', '95', '1495785259', '2017-05-26 14:54:19', '2017-05-26 14:54:19');
INSERT INTO `oauth_access_tokens` VALUES ('I8G25u2XKP6fw8Hy7Lub0TXWRPqP0YhX6GtCcRYY', '425', '1505986898', '2017-09-21 16:41:38', '2017-09-21 16:41:38');
INSERT INTO `oauth_access_tokens` VALUES ('iAXYYX6k6A93kvMsN4ltIew75vJ6tF7ArTppH9Hf', '133', '1496115184', '2017-05-30 10:33:04', '2017-05-30 10:33:04');
INSERT INTO `oauth_access_tokens` VALUES ('IaYLoXp5UHGNHuF9HheWXwHIsDgByCYBslyg8T5p', '88', '1495783851', '2017-05-26 14:30:51', '2017-05-26 14:30:51');
INSERT INTO `oauth_access_tokens` VALUES ('IBv3K0m4c0hxaliIX25KkkLJgQwPmlbioEZkF2zy', '424', '1505986875', '2017-09-21 16:41:15', '2017-09-21 16:41:15');
INSERT INTO `oauth_access_tokens` VALUES ('IChO5DeHYmJAs1xsjTQRiF350ydhu4ooVU2eHrBj', '1', '1495276041', '2017-05-20 17:27:21', '2017-05-20 17:27:21');
INSERT INTO `oauth_access_tokens` VALUES ('iEgKpX3K6zS4g1LnICP6YJtSHcRxGsN1cBqexq0h', '287', '1505371542', '2017-09-14 13:45:42', '2017-09-14 13:45:42');
INSERT INTO `oauth_access_tokens` VALUES ('IEO5MIUWEGBACLtM9vIYfVOKtT6w0LZ6MIXfIVfG', '387', '1505819083', '2017-09-19 18:04:43', '2017-09-19 18:04:43');
INSERT INTO `oauth_access_tokens` VALUES ('iEwvqnBXEeemp0up4qm18s5lBVesOSbR4mxMiIx6', '364', '1505808477', '2017-09-19 15:07:57', '2017-09-19 15:07:57');
INSERT INTO `oauth_access_tokens` VALUES ('IeyH8d52l6yVA4HM2rVRys03vMvece73JLPmSY5K', '303', '1505376498', '2017-09-14 15:08:18', '2017-09-14 15:08:18');
INSERT INTO `oauth_access_tokens` VALUES ('iHzw2cDDlAKNp4jD5FS7H1aIz7gOD6ilxPBPsPEl', '183', '1504687660', '2017-09-06 15:47:40', '2017-09-06 15:47:40');
INSERT INTO `oauth_access_tokens` VALUES ('iLexIbeZKy96Qn2QVvc0Cp3AqC4klkSIe4jkwUIK', '314', '1505377883', '2017-09-14 15:31:23', '2017-09-14 15:31:23');
INSERT INTO `oauth_access_tokens` VALUES ('ILtJqmUYtN8NCMw6GnOGJkpPPQHiSqBS0mFFMYhT', '236', '1505127101', '2017-09-11 17:51:41', '2017-09-11 17:51:41');
INSERT INTO `oauth_access_tokens` VALUES ('iS8bq7NfWMYEe3cs3vc3P8jAGvSBcHLspNGjdPvM', '139', '1496219545', '2017-05-31 15:32:25', '2017-05-31 15:32:25');
INSERT INTO `oauth_access_tokens` VALUES ('IsdynAxbXKHxNDtwilydWfwfkSONnjkKDpxVflJ5', '76', '1495783435', '2017-05-26 14:23:55', '2017-05-26 14:23:55');
INSERT INTO `oauth_access_tokens` VALUES ('IXM90iGy4Dvb9zqIOy5OBktyWn8AQZDtZCjd9A9t', '166', '1496315016', '2017-06-01 18:03:36', '2017-06-01 18:03:36');
INSERT INTO `oauth_access_tokens` VALUES ('j213nVQXo42LEWtysD4wkfnltOrdxvqeoy15qmDi', '157', '1496299863', '2017-06-01 13:51:03', '2017-06-01 13:51:03');
INSERT INTO `oauth_access_tokens` VALUES ('j4adSnzKm3cRzVGV654i4IApTv6QFDO1w3J6ItZe', '427', '1505987077', '2017-09-21 16:44:37', '2017-09-21 16:44:37');
INSERT INTO `oauth_access_tokens` VALUES ('jBas6UoeMxNrfOo8XmGm9Bzhusfc91mscStvNAFE', '144', '1496219987', '2017-05-31 15:39:47', '2017-05-31 15:39:47');
INSERT INTO `oauth_access_tokens` VALUES ('JdmK6yLoaBnBoPanZn0RwqnGOd60JRzShzM1eXHc', '270', '1505356872', '2017-09-14 09:41:12', '2017-09-14 09:41:12');
INSERT INTO `oauth_access_tokens` VALUES ('jdyXHCFqzfQheAiXMIAvk3zGlowYget75j9kPaWV', '228', '1504869682', '2017-09-08 18:21:22', '2017-09-08 18:21:22');
INSERT INTO `oauth_access_tokens` VALUES ('JFCBPTC31jbVjIfQgABHFstdTk7inEeD0D2lIcqD', '243', '1505129625', '2017-09-11 18:33:46', '2017-09-11 18:33:46');
INSERT INTO `oauth_access_tokens` VALUES ('JGKMnDmYruQm64nx8pNvX6WQnnxJ8LqKQflj4Hl6', '405', '1505895396', '2017-09-20 15:16:36', '2017-09-20 15:16:36');
INSERT INTO `oauth_access_tokens` VALUES ('JgrRBq4C4qHiBRRDG1JXg0iTlRefOSEoiDIhfsmO', '225', '1504868757', '2017-09-08 18:05:57', '2017-09-08 18:05:57');
INSERT INTO `oauth_access_tokens` VALUES ('jgVMIJR6GM8VGtZU5sccJdIJOwCSwOKmtZIOxumg', '190', '1504691954', '2017-09-06 16:59:14', '2017-09-06 16:59:14');
INSERT INTO `oauth_access_tokens` VALUES ('jISM1ZhhZSeNLszkbIrIQPPNBgDGak9vQp3L1hnZ', '112', '1495854041', '2017-05-27 10:00:41', '2017-05-27 10:00:41');
INSERT INTO `oauth_access_tokens` VALUES ('JKDICaunUu59lpSyW745IFB0dUcqfaCGL86QOLQH', '223', '1504868485', '2017-09-08 18:01:25', '2017-09-08 18:01:25');
INSERT INTO `oauth_access_tokens` VALUES ('jM8VFkm4FnS9BmsxmkfteF5lY6FyNuzzlcBpbFbM', '104', '1495787700', '2017-05-26 15:35:00', '2017-05-26 15:35:00');
INSERT INTO `oauth_access_tokens` VALUES ('JMSQyzwNWhposS5Si7OKXyD32WeNT8LubhmZuEmE', '159', '1496308115', '2017-06-01 16:08:35', '2017-06-01 16:08:35');
INSERT INTO `oauth_access_tokens` VALUES ('jNjlGez6ZUE7O3l8w2hGypUyMsK1Xkz2TiRIJnpI', '401', '1505894935', '2017-09-20 15:08:55', '2017-09-20 15:08:55');
INSERT INTO `oauth_access_tokens` VALUES ('jpFSvNPPjWgrvjwykjygnN8Z9khfqeZIK3JQLsUT', '74', '1495783420', '2017-05-26 14:23:40', '2017-05-26 14:23:40');
INSERT INTO `oauth_access_tokens` VALUES ('JTVF1ZQUSx248WRb78pIjAzwkcTLIoeor1TD44Uz', '99', '1495785821', '2017-05-26 15:03:41', '2017-05-26 15:03:41');
INSERT INTO `oauth_access_tokens` VALUES ('jUeETm0TleMMfsdW5SWYk91c8AQ6CKLoQKuUD7Lc', '416', '1505985066', '2017-09-21 16:11:06', '2017-09-21 16:11:06');
INSERT INTO `oauth_access_tokens` VALUES ('JUjVX9ZjTrIgf9oQHrFDCWZ7gm3HxZhN4UVqcV5Q', '409', '1505980169', '2017-09-21 14:49:29', '2017-09-21 14:49:29');
INSERT INTO `oauth_access_tokens` VALUES ('jUXG6cbDI2tefJ33AIbQgguYdYJQ1H0BYyRePudH', '241', '1505128967', '2017-09-11 18:22:47', '2017-09-11 18:22:47');
INSERT INTO `oauth_access_tokens` VALUES ('K6t2bCWsTzeny8o7HCQCJwRWL81ZfLy3NGoxhak9', '68', '1495783322', '2017-05-26 14:22:02', '2017-05-26 14:22:02');
INSERT INTO `oauth_access_tokens` VALUES ('kBYw7RN96Myr1wCwddd95MR4Cx398mI0e6NcSwdx', '383', '1505813651', '2017-09-19 16:34:11', '2017-09-19 16:34:11');
INSERT INTO `oauth_access_tokens` VALUES ('kCbW5VEBRZpRVeiHcpCBQxs6hmQiyiCWDaJ6mcou', '108', '1495848740', '2017-05-27 08:32:20', '2017-05-27 08:32:20');
INSERT INTO `oauth_access_tokens` VALUES ('KCG4HKcDUwbdNAhWRJCgtMxEtDAc4Xe4V5PgBuXO', '179', '1504687475', '2017-09-06 15:44:35', '2017-09-06 15:44:35');
INSERT INTO `oauth_access_tokens` VALUES ('kEYlTUsjvElmGcwnTUxHyOqCiQbUVNwO8KWqAgs8', '79', '1495783504', '2017-05-26 14:25:04', '2017-05-26 14:25:04');
INSERT INTO `oauth_access_tokens` VALUES ('kGpjqA8Xro0fEtGfy7bRiL9eHeBCpIMJGAOocVVL', '119', '1496037217', '2017-05-29 12:53:37', '2017-05-29 12:53:37');
INSERT INTO `oauth_access_tokens` VALUES ('kIrJwyHap8PB4s5LF7YIE5uWtpxqZsfGecgjgkMc', '304', '1505376530', '2017-09-14 15:08:51', '2017-09-14 15:08:51');
INSERT INTO `oauth_access_tokens` VALUES ('kj3iXzd2vjv9sd6K92yJtpmFpmxGKkKadHSensbl', '393', '1505892573', '2017-09-20 14:29:33', '2017-09-20 14:29:33');
INSERT INTO `oauth_access_tokens` VALUES ('kjBPCczf2P11o3RUPR8iWCPabr6k1dyDFwrWmFWp', '344', '1505733435', '2017-09-18 18:17:15', '2017-09-18 18:17:15');
INSERT INTO `oauth_access_tokens` VALUES ('KJu71Ywlujj2fQEfaJfBxXSnGQ2mpX8pY14xbrbw', '242', '1505129517', '2017-09-11 18:31:57', '2017-09-11 18:31:57');
INSERT INTO `oauth_access_tokens` VALUES ('KLE6taqAVbreNiGzIvFft9R2q2hsvJ4zCiQXxHDX', '64', '1495783307', '2017-05-26 14:21:47', '2017-05-26 14:21:47');
INSERT INTO `oauth_access_tokens` VALUES ('KpDjWg5ehtvdhNevSTWV0PBEFqEhlctxb75gYTTy', '142', '1496219763', '2017-05-31 15:36:03', '2017-05-31 15:36:03');
INSERT INTO `oauth_access_tokens` VALUES ('kpKCludjlNiLzySxQbJn1AugH3NklPZKpB1Cohex', '285', '1505371461', '2017-09-14 13:44:21', '2017-09-14 13:44:21');
INSERT INTO `oauth_access_tokens` VALUES ('krxDDDtPUXLGJBmnaSlhRkgOpcnU3RSQd7nAJ5Ld', '15', '1495354450', '2017-05-21 15:14:10', '2017-05-21 15:14:10');
INSERT INTO `oauth_access_tokens` VALUES ('kS72KTurOnmHhrBzGvaYb5pCTA9wQm3nki4d8zL9', '62', '1495783284', '2017-05-26 14:21:24', '2017-05-26 14:21:24');
INSERT INTO `oauth_access_tokens` VALUES ('KSBtveXCH5uBQqua9jDZWmqHwIaHzxYcQxmp9M5E', '130', '1496071161', '2017-05-29 22:19:21', '2017-05-29 22:19:21');
INSERT INTO `oauth_access_tokens` VALUES ('ksQs6CkubbSDMp0jkrygzhQkpiIDulK7Ch1wOpig', '385', '1505813933', '2017-09-19 16:38:53', '2017-09-19 16:38:53');
INSERT INTO `oauth_access_tokens` VALUES ('KsSjQ0ckhMMGa8cbvBLIEzjv4nC2J98AdDdsxMQw', '278', '1505369820', '2017-09-14 13:17:01', '2017-09-14 13:17:01');
INSERT INTO `oauth_access_tokens` VALUES ('kt8WYY0QiLr6im5CN65m8hM6vBkgcWyGMNw3qcJx', '73', '1495783416', '2017-05-26 14:23:36', '2017-05-26 14:23:36');
INSERT INTO `oauth_access_tokens` VALUES ('KUhODT51n8ESbV9DbgTOZ5hXYhUHrJzd9fE56LSy', '283', '1505371316', '2017-09-14 13:41:56', '2017-09-14 13:41:56');
INSERT INTO `oauth_access_tokens` VALUES ('KwqJuVeRzYl8Uj5Qa6w7rxbFvjaQjmBoIDKyunw2', '230', '1505122602', '2017-09-11 16:36:42', '2017-09-11 16:36:42');
INSERT INTO `oauth_access_tokens` VALUES ('KwtzeEWl6idUC63qakL0PxOr8KFDd7s97VZs8Hpc', '284', '1505371351', '2017-09-14 13:42:31', '2017-09-14 13:42:31');
INSERT INTO `oauth_access_tokens` VALUES ('Lb0rHFzcF9NJea232suCChXBJUaM5Jd3hWaxi8LQ', '246', '1505130022', '2017-09-11 18:40:22', '2017-09-11 18:40:22');
INSERT INTO `oauth_access_tokens` VALUES ('lbFfV7qDlyzl7FjVoOkbUEz9evOib7dNdEAKqYDb', '402', '1505895097', '2017-09-20 15:11:37', '2017-09-20 15:11:37');
INSERT INTO `oauth_access_tokens` VALUES ('ldGVivLFfIsi6k1Qzb4WbzMhfR6yRTs6vi1UjSsP', '94', '1495785202', '2017-05-26 14:53:22', '2017-05-26 14:53:22');
INSERT INTO `oauth_access_tokens` VALUES ('LEdb3x02aCrqkvRRpE2p873vehVgVrUULT1fJeY0', '256', '1505187004', '2017-09-12 10:30:05', '2017-09-12 10:30:05');
INSERT INTO `oauth_access_tokens` VALUES ('LH9vEvWUaQrFMZr6luHC8jF57mi9toJGMcFlljSa', '208', '1504758718', '2017-09-07 11:31:58', '2017-09-07 11:31:58');
INSERT INTO `oauth_access_tokens` VALUES ('LHz6hA3sO3pdiSObtp13d9lhfdshGglBfFXH1obB', '422', '1505986667', '2017-09-21 16:37:47', '2017-09-21 16:37:47');
INSERT INTO `oauth_access_tokens` VALUES ('lL75st2RMjIfYnBpsqDXkHLrLLdK3xhM8quWlJKN', '27', '1495524158', '2017-05-23 14:22:38', '2017-05-23 14:22:38');
INSERT INTO `oauth_access_tokens` VALUES ('lmgJNPzfgNXYKDnO8gzlQp5xOfeLNXDSGzeqsiA0', '206', '1504758360', '2017-09-07 11:26:00', '2017-09-07 11:26:00');
INSERT INTO `oauth_access_tokens` VALUES ('lpEXEQH3ZVYRFQkZQpfgPdtR8hkb9qf3QZWQHpB6', '302', '1505374805', '2017-09-14 14:40:05', '2017-09-14 14:40:05');
INSERT INTO `oauth_access_tokens` VALUES ('lQHMaVyFt7agfTL7iNfEoOiDOVxsNlvzMaufTInE', '154', '1496291473', '2017-06-01 11:31:13', '2017-06-01 11:31:13');
INSERT INTO `oauth_access_tokens` VALUES ('lukIBHZJ4Gm6ISg3t8p65ffzskwV3CPOLJbKVGKz', '186', '1504688015', '2017-09-06 15:53:35', '2017-09-06 15:53:35');
INSERT INTO `oauth_access_tokens` VALUES ('lvqIlfFhWw4ykFXNcAM8dT60cx0xea8V6OCbHTJn', '266', '1505198807', '2017-09-12 13:46:47', '2017-09-12 13:46:47');
INSERT INTO `oauth_access_tokens` VALUES ('lVQoxwcSQRREqaTD2UW0oj3Rcy7UrA84IeGSsPvJ', '400', '1505894880', '2017-09-20 15:08:00', '2017-09-20 15:08:00');
INSERT INTO `oauth_access_tokens` VALUES ('LVYmxF6tulgry91i7QUteJACeUSJXwJr1Fpvuy35', '205', '1504757821', '2017-09-07 11:17:01', '2017-09-07 11:17:01');
INSERT INTO `oauth_access_tokens` VALUES ('lWco0PedPXONPgoxV0qmETrM9WZBhk37MwvWUysh', '69', '1495783326', '2017-05-26 14:22:06', '2017-05-26 14:22:06');
INSERT INTO `oauth_access_tokens` VALUES ('M22Blfok8RxDThtn46lrFPri97whmHMky13O0Hpj', '65', '1495783311', '2017-05-26 14:21:51', '2017-05-26 14:21:51');
INSERT INTO `oauth_access_tokens` VALUES ('m2uxQ7lrIFZsNeQXCRYAuyUil7XjNuwrSH411xOf', '126', '1496071010', '2017-05-29 22:16:50', '2017-05-29 22:16:50');
INSERT INTO `oauth_access_tokens` VALUES ('M92Gso9Cu07T20K3gPXGRKe7icRWWTrTqXE2lKsK', '338', '1505731148', '2017-09-18 17:39:08', '2017-09-18 17:39:08');
INSERT INTO `oauth_access_tokens` VALUES ('MBxDVXy4BDimWuGahqrwlzg9F0HmDuEKTaG9DsJO', '37', '1495783006', '2017-05-26 14:16:46', '2017-05-26 14:16:46');
INSERT INTO `oauth_access_tokens` VALUES ('MDJEz3tdOCwmZdvod8TZ6me5xqIErUnMdysQ2uVW', '255', '1505185610', '2017-09-12 10:06:50', '2017-09-12 10:06:50');
INSERT INTO `oauth_access_tokens` VALUES ('MEJTCXl95BBnjPusLqzRGOoWafsPtXj25Js5Qxna', '148', '1496221129', '2017-05-31 15:58:49', '2017-05-31 15:58:49');
INSERT INTO `oauth_access_tokens` VALUES ('mEu1DMQdHCGABhIyYfRjFp67hqSFhNI2K6285j1K', '143', '1496219982', '2017-05-31 15:39:42', '2017-05-31 15:39:42');
INSERT INTO `oauth_access_tokens` VALUES ('MFiljtFhVWpnAR8zg9IKYUZPnxwEpdF95T4MRMVZ', '38', '1495783056', '2017-05-26 14:17:36', '2017-05-26 14:17:36');
INSERT INTO `oauth_access_tokens` VALUES ('MGbu1D0IZj07PrHETdPs7Bp6e9tx7zu9m456aOuS', '107', '1495846524', '2017-05-27 07:55:24', '2017-05-27 07:55:24');
INSERT INTO `oauth_access_tokens` VALUES ('Mh84bv3N7zWt0dbTM6a2Y6tVbuyTt8hGJSgQ3UV9', '187', '1504688766', '2017-09-06 16:06:06', '2017-09-06 16:06:06');
INSERT INTO `oauth_access_tokens` VALUES ('mhUgY9dHS51W28zk50yrMCQF0yezbPbvha65B7WJ', '435', '1517803760', '2018-02-05 11:09:20', '2018-02-05 11:09:20');
INSERT INTO `oauth_access_tokens` VALUES ('mIgIpqO197mEATKNz1bK5Kio7P5jSudVSYJT65WM', '63', '1495783289', '2017-05-26 14:21:29', '2017-05-26 14:21:29');
INSERT INTO `oauth_access_tokens` VALUES ('mrOBWOQ07tEay5S54S02H1X7bNf5GrNw3YKITw5F', '101', '1495786585', '2017-05-26 15:16:25', '2017-05-26 15:16:25');
INSERT INTO `oauth_access_tokens` VALUES ('mTPC6ukMVdmk1xIIS8knCx1s6SYvInZGmi0zrroG', '218', '1504863987', '2017-09-08 16:46:27', '2017-09-08 16:46:27');
INSERT INTO `oauth_access_tokens` VALUES ('mU6TL8cYXhargiEv2NdoRpBsDSCnblTxeUpjhumM', '315', '1505377916', '2017-09-14 15:31:56', '2017-09-14 15:31:56');
INSERT INTO `oauth_access_tokens` VALUES ('MUbJaNoifqKLTAxhN3pmsICzM7r7nAPcYCn3BCw7', '40', '1495783078', '2017-05-26 14:17:58', '2017-05-26 14:17:58');
INSERT INTO `oauth_access_tokens` VALUES ('MvFKLL6UpKlYMVo4slNbGdEtFYVoJ2gBJm28xFx9', '46', '1495783105', '2017-05-26 14:18:25', '2017-05-26 14:18:25');
INSERT INTO `oauth_access_tokens` VALUES ('mXnMXQgB3kLaFmHbIc1WtkWgCl9xmauBsn86RBOa', '235', '1505124372', '2017-09-11 17:06:12', '2017-09-11 17:06:12');
INSERT INTO `oauth_access_tokens` VALUES ('MyuW3GFm9nt7AbHkIkHAmeuD310hjAPl567zwKs7', '11', '1495347961', '2017-05-21 13:26:01', '2017-05-21 13:26:01');
INSERT INTO `oauth_access_tokens` VALUES ('N1GEjoXAbBO2zxeG9mXd7FUWkIK31GHbmAJZkPXq', '86', '1495783715', '2017-05-26 14:28:35', '2017-05-26 14:28:35');
INSERT INTO `oauth_access_tokens` VALUES ('N1wy4BiGpm9r2MKqNbtWJRG1elucn0azhTQ9nI9m', '197', '1504693466', '2017-09-06 17:24:26', '2017-09-06 17:24:26');
INSERT INTO `oauth_access_tokens` VALUES ('n7syzf0ehApBPEeczzxdryVjN3LLdue9bY1oa5o2', '294', '1505373645', '2017-09-14 14:20:45', '2017-09-14 14:20:45');
INSERT INTO `oauth_access_tokens` VALUES ('nDQRH4IUsdKZ894ENGPuyV0Z1c46KKquSFtTQCEg', '50', '1495783135', '2017-05-26 14:18:55', '2017-05-26 14:18:55');
INSERT INTO `oauth_access_tokens` VALUES ('Noz3ls5LNER8IhZ5SCfUWGtSYFToGwfsZjDCTbnV', '51', '1495783136', '2017-05-26 14:18:56', '2017-05-26 14:18:56');
INSERT INTO `oauth_access_tokens` VALUES ('npHBNg0Mei8uOSd6gzdN1YTMD7ZmSfZKnjOSgkui', '13', '1495351181', '2017-05-21 14:19:41', '2017-05-21 14:19:41');
INSERT INTO `oauth_access_tokens` VALUES ('nXqIv6TqxkW1YFvn1WIP2M8QWVg8w6TtwpLJtvoL', '245', '1505130014', '2017-09-11 18:40:14', '2017-09-11 18:40:14');
INSERT INTO `oauth_access_tokens` VALUES ('nZ7XuVvp4ADGLx82X0N6oHR9lfEJycVkjMRRD33c', '254', '1505184613', '2017-09-12 09:50:14', '2017-09-12 09:50:14');
INSERT INTO `oauth_access_tokens` VALUES ('NZDmnfYLyQG2IR0d5k3Q0vmwzArre2sMlQLzfRJY', '169', '1504679487', '2017-09-06 13:31:28', '2017-09-06 13:31:28');
INSERT INTO `oauth_access_tokens` VALUES ('OCydtpLKLIp7JE6h7PfJYtnH3fCidK2aihDwCcCz', '371', '1505809207', '2017-09-19 15:20:07', '2017-09-19 15:20:07');
INSERT INTO `oauth_access_tokens` VALUES ('ohlv8h9fBIY85EAgeJbiFIXzRaSE5OOYsHBJPHfD', '252', '1505183685', '2017-09-12 09:34:46', '2017-09-12 09:34:46');
INSERT INTO `oauth_access_tokens` VALUES ('OIzxToPCgB04MDD6JcrdUiMks5tH5qhF6FwC5AoT', '282', '1505371312', '2017-09-14 13:41:52', '2017-09-14 13:41:52');
INSERT INTO `oauth_access_tokens` VALUES ('oloFLmvtWFP4K0NTEtOw9k01Gy8VtOVPEzxo51Kc', '408', '1505980153', '2017-09-21 14:49:13', '2017-09-21 14:49:13');
INSERT INTO `oauth_access_tokens` VALUES ('olSkAQXBxEIVgUxHzvYebhIV7NxYffCKWwZHjHSP', '233', '1505122670', '2017-09-11 16:37:50', '2017-09-11 16:37:50');
INSERT INTO `oauth_access_tokens` VALUES ('OnnIxu7tSK0rsNwxMlamrjWOqg7YoFKsAuRSbM9z', '309', '1505376720', '2017-09-14 15:12:00', '2017-09-14 15:12:00');
INSERT INTO `oauth_access_tokens` VALUES ('oogjPITMV0WYwuIYaTg7HRQJWotIbtS2t8Vppwfh', '436', '1517811454', '2018-02-05 13:17:34', '2018-02-05 13:17:34');
INSERT INTO `oauth_access_tokens` VALUES ('oUvXDAFDkv1B0wCwVqe57ULpgv22E2IbzZk4AraO', '378', '1505811845', '2017-09-19 16:04:05', '2017-09-19 16:04:05');
INSERT INTO `oauth_access_tokens` VALUES ('Oxwa9SIJIOAHjnwdwQBUpoHmxSpUoHQp3BkaEtmo', '42', '1495783090', '2017-05-26 14:18:10', '2017-05-26 14:18:10');
INSERT INTO `oauth_access_tokens` VALUES ('PbZ2WmdFa83cvk0wv7328mI6bXnWAc9Gxzt5maeU', '18', '1495515503', '2017-05-23 11:58:23', '2017-05-23 11:58:23');
INSERT INTO `oauth_access_tokens` VALUES ('petSihLn8ccIwvcaKAf6DxwKJpne7WU1sflbBCiP', '288', '1505371744', '2017-09-14 13:49:05', '2017-09-14 13:49:05');
INSERT INTO `oauth_access_tokens` VALUES ('PfbFOTVZ5hltt1TmBayN2SQdboHTBmJDZMjzYpxz', '212', '1504852444', '2017-09-08 13:34:04', '2017-09-08 13:34:04');
INSERT INTO `oauth_access_tokens` VALUES ('PGQ1YgkiGlGcAwlv4dDdCaZs9XMz6SyiBhxPSwEN', '224', '1504868694', '2017-09-08 18:04:54', '2017-09-08 18:04:54');
INSERT INTO `oauth_access_tokens` VALUES ('phwTx2rHT7RRbnpXTfB8aOxLrC3RN4R6l92xkhw4', '36', '1495782984', '2017-05-26 14:16:24', '2017-05-26 14:16:24');
INSERT INTO `oauth_access_tokens` VALUES ('PiYCu2aI13hSWo3CTSmLWKD8HahEF1CEd6b3EsRb', '201', '1504697536', '2017-09-06 18:32:16', '2017-09-06 18:32:16');
INSERT INTO `oauth_access_tokens` VALUES ('pJ0sxjRDJe0di3wAFEKIBnx2gX6aDn4BoJkRtKLi', '9', '1495347043', '2017-05-21 13:10:43', '2017-05-21 13:10:43');
INSERT INTO `oauth_access_tokens` VALUES ('Pju48j04s26oxSYOfvy0kxlp0wFEgTmTSvVGIaOL', '72', '1495783361', '2017-05-26 14:22:41', '2017-05-26 14:22:41');
INSERT INTO `oauth_access_tokens` VALUES ('PkUD1pcExP7ulyQOhsqHvKdtJjxI0w7j915DoVb6', '138', '1496201794', '2017-05-31 10:36:34', '2017-05-31 10:36:34');
INSERT INTO `oauth_access_tokens` VALUES ('PpPwnjKMz8NGnxFSZxIMLFDYdSE0JqwPaZCWH7i4', '140', '1496219551', '2017-05-31 15:32:31', '2017-05-31 15:32:31');
INSERT INTO `oauth_access_tokens` VALUES ('pPXohw1lhLAaEcb0G2qwTRAm0xosVRxUuNkW5JFN', '103', '1495787510', '2017-05-26 15:31:50', '2017-05-26 15:31:50');
INSERT INTO `oauth_access_tokens` VALUES ('PSBqUhgeNTaF5DVwldpCfAtsaJvjU26zFpwE77HL', '98', '1495785577', '2017-05-26 14:59:37', '2017-05-26 14:59:37');
INSERT INTO `oauth_access_tokens` VALUES ('pUIDhjlIxjliUZyXAwSEdsE12vjRvKNsZvGaiT3d', '122', '1496037225', '2017-05-29 12:53:45', '2017-05-29 12:53:45');
INSERT INTO `oauth_access_tokens` VALUES ('pzCGneyEXMDRvE24DQB6e8wztFhiKR85yx0Hw2i1', '123', '1496039713', '2017-05-29 13:35:13', '2017-05-29 13:35:13');
INSERT INTO `oauth_access_tokens` VALUES ('pZKxypptVM2nfJciZP0g1oAHBsCCUEsUrO9WFwOh', '237', '1505128384', '2017-09-11 18:13:04', '2017-09-11 18:13:04');
INSERT INTO `oauth_access_tokens` VALUES ('q1HCpmVRjfTYSIiPQfkGVX4niCQEqLihIwsNEk6n', '361', '1505808246', '2017-09-19 15:04:06', '2017-09-19 15:04:06');
INSERT INTO `oauth_access_tokens` VALUES ('q3X3E0E2dzu0VNH1GGfUGDRzGRKXeHp2Kv2ou7p3', '192', '1504693179', '2017-09-06 17:19:39', '2017-09-06 17:19:39');
INSERT INTO `oauth_access_tokens` VALUES ('q4SzroTNVW0SQGLfsems8LsweTpr86uDoCrxWzj8', '67', '1495783317', '2017-05-26 14:21:57', '2017-05-26 14:21:57');
INSERT INTO `oauth_access_tokens` VALUES ('Q7L3ML7sX32JhSDS3QM7Y2SNNPgKn5e6nQiC8oNn', '423', '1505986851', '2017-09-21 16:40:51', '2017-09-21 16:40:51');
INSERT INTO `oauth_access_tokens` VALUES ('Q7tw5N5KlZcEedTffkhePKbgyoqa1cxu5dkoPBuD', '373', '1505810923', '2017-09-19 15:48:43', '2017-09-19 15:48:43');
INSERT INTO `oauth_access_tokens` VALUES ('qCdgOnTEDjaKhdWmymh8rZxbV1DuN7FWJ4NaeGza', '317', '1505378024', '2017-09-14 15:33:44', '2017-09-14 15:33:44');
INSERT INTO `oauth_access_tokens` VALUES ('QKBHRnkvqLiNp6IJRNNJQMB9HtXfTrUcBINsfVdR', '141', '1496219757', '2017-05-31 15:35:57', '2017-05-31 15:35:57');
INSERT INTO `oauth_access_tokens` VALUES ('qPzWcLHYuRZj8rzz8DCzoxOpzHVClkrxJcA9LQxC', '214', '1504852861', '2017-09-08 13:41:01', '2017-09-08 13:41:01');
INSERT INTO `oauth_access_tokens` VALUES ('QUeoMXYBOmS4aMVCUXHZQeyzqVn8ylLnxIkN672Q', '120', '1496037219', '2017-05-29 12:53:40', '2017-05-29 12:53:40');
INSERT INTO `oauth_access_tokens` VALUES ('QY4enShGnQwOcmMgqAkdMhatcnlLp4Zzq0It9qbg', '250', '1505130309', '2017-09-11 18:45:09', '2017-09-11 18:45:09');
INSERT INTO `oauth_access_tokens` VALUES ('r2Aw9ru5LqBT1Icj9eybb9zUDuYPEOb1h5QSpnEf', '312', '1505377792', '2017-09-14 15:29:52', '2017-09-14 15:29:52');
INSERT INTO `oauth_access_tokens` VALUES ('rb5skCxkbabaXla1uSuuPcHulK1gbWqzcz1FwyCe', '244', '1505129988', '2017-09-11 18:39:48', '2017-09-11 18:39:48');
INSERT INTO `oauth_access_tokens` VALUES ('REUQ1qFva9I5gRhxkYUU6g95WPPcSpaxrMwnL6kK', '322', '1505381410', '2017-09-14 16:30:10', '2017-09-14 16:30:10');
INSERT INTO `oauth_access_tokens` VALUES ('RgBL13JQ9ysRDO2DNdHmg7Kykm8t5Ru7G6s6rxt5', '170', '1504679770', '2017-09-06 13:36:10', '2017-09-06 13:36:10');
INSERT INTO `oauth_access_tokens` VALUES ('RKFoMgVIUPOt1cMwCyiDHxGOE6TgJXJHcXC67pSg', '291', '1505373574', '2017-09-14 14:19:34', '2017-09-14 14:19:34');
INSERT INTO `oauth_access_tokens` VALUES ('RQ49dirXsXtzZfoDhp6CehHtOcjj8hLOYAk1gVEG', '339', '1505731212', '2017-09-18 17:40:12', '2017-09-18 17:40:12');
INSERT INTO `oauth_access_tokens` VALUES ('RQ6drBBg6jPfpwDHnEbvrBIo34tbLEUWZ2WcTlS8', '149', '1496221131', '2017-05-31 15:58:51', '2017-05-31 15:58:51');
INSERT INTO `oauth_access_tokens` VALUES ('rtZZy9GtwMYHw7LSvgByjH9xvd1LpSS8Yk5Ie9yq', '310', '1505376783', '2017-09-14 15:13:03', '2017-09-14 15:13:03');
INSERT INTO `oauth_access_tokens` VALUES ('rubVpIt74czhj6ACvdzTt6z4Hkinr55RnwynaWS9', '121', '1496037222', '2017-05-29 12:53:42', '2017-05-29 12:53:42');
INSERT INTO `oauth_access_tokens` VALUES ('rwbxhWdT8VZdfEa8bSQAJYGo1MPj4irKAKPfBGyp', '6', '1495345378', '2017-05-21 12:42:58', '2017-05-21 12:42:58');
INSERT INTO `oauth_access_tokens` VALUES ('RxLU3lqbFRItL6mLHzIChDBGoN08GvdieLZju8Ke', '384', '1505813836', '2017-09-19 16:37:16', '2017-09-19 16:37:16');
INSERT INTO `oauth_access_tokens` VALUES ('rxqC0DMzTL98Pqkdt93tWbAj81xYwGeA71yrQajg', '21', '1495517356', '2017-05-23 12:29:16', '2017-05-23 12:29:16');
INSERT INTO `oauth_access_tokens` VALUES ('s00FZ1gTVCZIFyVvQmORP4IUxnj8W1AP19siR3jP', '421', '1505986643', '2017-09-21 16:37:23', '2017-09-21 16:37:23');
INSERT INTO `oauth_access_tokens` VALUES ('SGGJzTaiq70jxdGdYr3BA8oTYek1GW6Ielk0cjDU', '318', '1505378029', '2017-09-14 15:33:49', '2017-09-14 15:33:49');
INSERT INTO `oauth_access_tokens` VALUES ('SO8lJU5Mcg9VjFGrJue6kP7PnG0UQ2l9VNW4pN82', '191', '1504691956', '2017-09-06 16:59:16', '2017-09-06 16:59:16');
INSERT INTO `oauth_access_tokens` VALUES ('soGYvxR9AyTmOgHcZZ1fEyEFChtVgBOSJ0H0zGSx', '327', '1505706744', '2017-09-18 10:52:24', '2017-09-18 10:52:24');
INSERT INTO `oauth_access_tokens` VALUES ('STvn1yrCVKtJG3OtnKQTYdNsNMQKYbQpPs1ukAka', '174', '1504687287', '2017-09-06 15:41:27', '2017-09-06 15:41:27');
INSERT INTO `oauth_access_tokens` VALUES ('svnkDvJ2wM4WMoEnjdF8HP7CBo3QF4AZqktsVJ6v', '209', '1504765546', '2017-09-07 13:25:46', '2017-09-07 13:25:46');
INSERT INTO `oauth_access_tokens` VALUES ('sWIYVwGID7E8OCfAeAxXC5lnoNQoBXVOpIe7CxBB', '182', '1504687610', '2017-09-06 15:46:51', '2017-09-06 15:46:51');
INSERT INTO `oauth_access_tokens` VALUES ('SwQ9LDQMinYblrcL1mhZqyqA0Bh4LNh80EJJhHNQ', '404', '1505895112', '2017-09-20 15:11:52', '2017-09-20 15:11:52');
INSERT INTO `oauth_access_tokens` VALUES ('SX8cWEXvrlaJpJNGTsyQbpNwnlVgbDdAu39Y0Cms', '397', '1505893463', '2017-09-20 14:44:23', '2017-09-20 14:44:23');
INSERT INTO `oauth_access_tokens` VALUES ('SYXgLcmsrEpvzTMuNLaRJkKuxzyPJ3W87vA1dDMb', '369', '1505809183', '2017-09-19 15:19:44', '2017-09-19 15:19:44');
INSERT INTO `oauth_access_tokens` VALUES ('sYZt2odT7Nu8f2JFmopiAOyKZwXFnkRwajfOW4oT', '23', '1495517786', '2017-05-23 12:36:26', '2017-05-23 12:36:26');
INSERT INTO `oauth_access_tokens` VALUES ('SZFxlL1mVH2DGlTFGOyJqet5h6rbKlwUfVn174Lz', '156', '1496297899', '2017-06-01 13:18:19', '2017-06-01 13:18:19');
INSERT INTO `oauth_access_tokens` VALUES ('taXg01u1DMyLgY6tWbO9yXuBPvrWwwg7RuGaZNn5', '345', '1505733441', '2017-09-18 18:17:21', '2017-09-18 18:17:21');
INSERT INTO `oauth_access_tokens` VALUES ('TBbK0LLGhLkMR8ohiwBWsuSqqsU8HlIVc9pxADg7', '35', '1495782971', '2017-05-26 14:16:11', '2017-05-26 14:16:11');
INSERT INTO `oauth_access_tokens` VALUES ('tbl5pssmnmjhEgRytHXUXeyIdwozHYbpSvRTQvYP', '105', '1495806930', '2017-05-26 20:55:30', '2017-05-26 20:55:30');
INSERT INTO `oauth_access_tokens` VALUES ('tCCIdBH7pnJekXTv5JahW7MJkLw6dj2INsqbpJ8z', '33', '1495767324', '2017-05-26 09:55:24', '2017-05-26 09:55:24');
INSERT INTO `oauth_access_tokens` VALUES ('TdhpgPoc96MsAaeAoA3NuxVtONEq7kozOey4fchf', '349', '1505733817', '2017-09-18 18:23:37', '2017-09-18 18:23:37');
INSERT INTO `oauth_access_tokens` VALUES ('TEN3yRvTvqPDSU2XZgszpwzyItXvUmTT5fjuDC3l', '336', '1505707656', '2017-09-18 11:07:36', '2017-09-18 11:07:36');
INSERT INTO `oauth_access_tokens` VALUES ('thpPko9bm9bDx5xAwyy29qrbXlyWgyQGSAZDmnFR', '240', '1505128897', '2017-09-11 18:21:37', '2017-09-11 18:21:37');
INSERT INTO `oauth_access_tokens` VALUES ('TI28NlF31zESBsGwOmsG1u65OvM0h9OlW7wp7SXh', '415', '1505985062', '2017-09-21 16:11:02', '2017-09-21 16:11:02');
INSERT INTO `oauth_access_tokens` VALUES ('TL1wH1BYFRgMfF816XBf0oiia6PIsNFKyw1Vveqf', '419', '1505985257', '2017-09-21 16:14:18', '2017-09-21 16:14:18');
INSERT INTO `oauth_access_tokens` VALUES ('tm6foz2XMh9dFoMohkK7WqhPd60CqLUhtORmnZBS', '219', '1504864140', '2017-09-08 16:49:00', '2017-09-08 16:49:00');
INSERT INTO `oauth_access_tokens` VALUES ('tM9VGokUXMv7WxFG6Y01yjw2d0tXnPEe11XN4M4Q', '281', '1505370704', '2017-09-14 13:31:44', '2017-09-14 13:31:44');
INSERT INTO `oauth_access_tokens` VALUES ('tnHzAfcE9saljsXQoiCutJwTfsYQR094GVhvImof', '354', '1505802789', '2017-09-19 13:33:09', '2017-09-19 13:33:09');
INSERT INTO `oauth_access_tokens` VALUES ('tnpUJEDSlcRANV3eTTA3MZsXOL0PZh5fQDzIPBi7', '398', '1505893913', '2017-09-20 14:51:53', '2017-09-20 14:51:53');
INSERT INTO `oauth_access_tokens` VALUES ('tOcyz6AeIUpbkxmXDGtcrBsfTPm0TXrGJSVpoFk4', '356', '1505802901', '2017-09-19 13:35:01', '2017-09-19 13:35:01');
INSERT INTO `oauth_access_tokens` VALUES ('tQPKQBVO95LBaG2wqiqr0qaF8wCvXJ1dmy1WjHld', '45', '1495783102', '2017-05-26 14:18:22', '2017-05-26 14:18:22');
INSERT INTO `oauth_access_tokens` VALUES ('tUKgeR8AeXyGZRBjOZlOlpl41zhcdLmCDMXeONrD', '16', '1495514391', '2017-05-23 11:39:51', '2017-05-23 11:39:51');
INSERT INTO `oauth_access_tokens` VALUES ('TvqszGUJ0kElYntJd1CgKz7KSMeLTSwAWmBZgbp8', '276', '1505358606', '2017-09-14 10:10:06', '2017-09-14 10:10:06');
INSERT INTO `oauth_access_tokens` VALUES ('Txc9RcK0b2f6TOUafA2DcLOEoG5zKicfZX8k2PQj', '117', '1496036340', '2017-05-29 12:39:00', '2017-05-29 12:39:00');
INSERT INTO `oauth_access_tokens` VALUES ('u8K2pfM5XMXvgK0kcisUcFU2LaZTBxAwsrrfBZRR', '367', '1505808916', '2017-09-19 15:15:16', '2017-09-19 15:15:16');
INSERT INTO `oauth_access_tokens` VALUES ('u8yQtiUCtRsByuhwvXZMht25B9b0JjeV2PIs3XnE', '332', '1505707131', '2017-09-18 10:58:52', '2017-09-18 10:58:52');
INSERT INTO `oauth_access_tokens` VALUES ('uajuIf5sr75zhuGNmGW27ACRDrmbHlllKWMZes4v', '363', '1505808424', '2017-09-19 15:07:04', '2017-09-19 15:07:04');
INSERT INTO `oauth_access_tokens` VALUES ('uaW7bqOKkGGEkTp43NzCACsY1dn9qEGDTsXWQeRk', '264', '1505191892', '2017-09-12 11:51:32', '2017-09-12 11:51:32');
INSERT INTO `oauth_access_tokens` VALUES ('UCQlsjReX7WKFHxr5gTehLrLuvcKZ9epH1HQyhr7', '217', '1504857151', '2017-09-08 14:52:31', '2017-09-08 14:52:31');
INSERT INTO `oauth_access_tokens` VALUES ('UDSZpK2p6cwXUIeDhZ2FitAiRsKST1KXVZtTusj6', '188', '1504688920', '2017-09-06 16:08:40', '2017-09-06 16:08:40');
INSERT INTO `oauth_access_tokens` VALUES ('ufaDteSfcFIAGQuvp2Fc7LzRCUKqBTP7IKUIbePc', '75', '1495783429', '2017-05-26 14:23:49', '2017-05-26 14:23:49');
INSERT INTO `oauth_access_tokens` VALUES ('UGW1BlFfDrllsD65OBNLkbLatPu8M0YkvwxGHqDR', '177', '1504687411', '2017-09-06 15:43:31', '2017-09-06 15:43:31');
INSERT INTO `oauth_access_tokens` VALUES ('uhz0IXmNgDeMMLTU26x0nW9djkxVu28LSK1SkOlj', '273', '1505357096', '2017-09-14 09:44:57', '2017-09-14 09:44:57');
INSERT INTO `oauth_access_tokens` VALUES ('um21FGhmk9TpxdQX0tUfxEXvK6aPgZwGopq5IAVP', '162', '1496314953', '2017-06-01 18:02:33', '2017-06-01 18:02:33');
INSERT INTO `oauth_access_tokens` VALUES ('uns7J1sThvKEiZSHvtJW6NVqAKJjf1fJvz3nfklT', '55', '1495783173', '2017-05-26 14:19:33', '2017-05-26 14:19:33');
INSERT INTO `oauth_access_tokens` VALUES ('uvErtMj4Pth1QM4RIKt34Ca2pfhMIOfOdJYsPq7Y', '198', '1504693488', '2017-09-06 17:24:48', '2017-09-06 17:24:48');
INSERT INTO `oauth_access_tokens` VALUES ('UwGwpGwgYI8hXw2EPVgLuSuCvND3I2t5eyvCRhC1', '185', '1504687963', '2017-09-06 15:52:43', '2017-09-06 15:52:43');
INSERT INTO `oauth_access_tokens` VALUES ('v1iUqmvtpnVPXKoDUM8GfvoXCpjSvJ8Ed9kzSzvW', '195', '1504693310', '2017-09-06 17:21:50', '2017-09-06 17:21:50');
INSERT INTO `oauth_access_tokens` VALUES ('V4O9lLyvFqvikmv2unHH8DenFukTgRnDM5Q4Dw7s', '239', '1505128449', '2017-09-11 18:14:09', '2017-09-11 18:14:09');
INSERT INTO `oauth_access_tokens` VALUES ('vb5nLcb2EgoaP39CCVfWyQChIm7FYTLYLuLIYMZ0', '132', '1496115113', '2017-05-30 10:31:53', '2017-05-30 10:31:53');
INSERT INTO `oauth_access_tokens` VALUES ('vdEkcCbSXluwuTqZxaz2FZROmVwxQhk9unspa6Bx', '147', '1496221126', '2017-05-31 15:58:46', '2017-05-31 15:58:46');
INSERT INTO `oauth_access_tokens` VALUES ('vLaMjvVn98eD39gFxoOfTA3OCrlkdaUldFI4SQW2', '410', '1505980303', '2017-09-21 14:51:43', '2017-09-21 14:51:43');
INSERT INTO `oauth_access_tokens` VALUES ('VLUAgmuXZaqzvR9R7Zl5qSyjD6U6mMcoW2E1rGOs', '171', '1504681192', '2017-09-06 13:59:52', '2017-09-06 13:59:52');
INSERT INTO `oauth_access_tokens` VALUES ('VP7aexOji0MWjEhN9CMprfPOyltUzTYMkRTXgSUk', '275', '1505358484', '2017-09-14 10:08:04', '2017-09-14 10:08:04');
INSERT INTO `oauth_access_tokens` VALUES ('vtSYBudSmMJL0WFDT80gBngqXRzzKmfSMMdfuIw7', '360', '1505808174', '2017-09-19 15:02:54', '2017-09-19 15:02:54');
INSERT INTO `oauth_access_tokens` VALUES ('VyIrxfQirsWjQPbQXGN7bj994ozy34nPxoH0dMqD', '341', '1505731424', '2017-09-18 17:43:44', '2017-09-18 17:43:44');
INSERT INTO `oauth_access_tokens` VALUES ('VYYqdzv3bNqQlFgyZULAYaJzfnE9Otc27yFMxaYo', '418', '1505985248', '2017-09-21 16:14:08', '2017-09-21 16:14:08');
INSERT INTO `oauth_access_tokens` VALUES ('vzbcpx1ZjS7pzH74JpjFejnqavYoURpFlW6XAjWf', '196', '1504693343', '2017-09-06 17:22:23', '2017-09-06 17:22:23');
INSERT INTO `oauth_access_tokens` VALUES ('w0A1e2IcJ6vqh4qTaHlTnT1iodGLUIUFG3tzEcrV', '12', '1495350692', '2017-05-21 14:11:32', '2017-05-21 14:11:32');
INSERT INTO `oauth_access_tokens` VALUES ('W4jsprkX9Vm5SY58xoZrC4ShvL6MRTCnVQMLQ3za', '203', '1504753461', '2017-09-07 10:04:21', '2017-09-07 10:04:21');
INSERT INTO `oauth_access_tokens` VALUES ('w6XTTNNydXT6u1J620TJpieSMHWg7XtPI6o7aUsj', '231', '1505122633', '2017-09-11 16:37:13', '2017-09-11 16:37:13');
INSERT INTO `oauth_access_tokens` VALUES ('w9QOTwBbvq2k9qmEUOAB0WJtFZtcVlLS6dDRMAXR', '417', '1505985124', '2017-09-21 16:12:04', '2017-09-21 16:12:04');
INSERT INTO `oauth_access_tokens` VALUES ('wCQll2IsRqtusUxamy05brq6Iki8fN2bnANKCvF2', '167', '1504670472', '2017-09-06 11:01:12', '2017-09-06 11:01:12');
INSERT INTO `oauth_access_tokens` VALUES ('WdMlYQ0HTE7B4SnKfPndff6HZlaITpJQN2iuP1AV', '428', '1514969444', '2018-01-03 15:50:44', '2018-01-03 15:50:44');
INSERT INTO `oauth_access_tokens` VALUES ('wdOIFkeOv1gkXvd4XjbI4yJYzEyBdI4b3lZAoczE', '25', '1495518635', '2017-05-23 12:50:35', '2017-05-23 12:50:35');
INSERT INTO `oauth_access_tokens` VALUES ('WKGcjhXtsLl25uU1LU1CJnUhigXwiKL50reYxXa8', '280', '1505370255', '2017-09-14 13:24:16', '2017-09-14 13:24:16');
INSERT INTO `oauth_access_tokens` VALUES ('WKKFwmgvbK995ZnPqCziCzZRS65PBmNZEHps0g2c', '19', '1495516017', '2017-05-23 12:06:57', '2017-05-23 12:06:57');
INSERT INTO `oauth_access_tokens` VALUES ('wLnXt2xSfE6jJX7MlKT4NDctTFl7gspuBECbFitD', '164', '1496314963', '2017-06-01 18:02:43', '2017-06-01 18:02:43');
INSERT INTO `oauth_access_tokens` VALUES ('wn0TdaMfrfu73a5bexVbdmjtZpzEgAhlITE8xkL9', '109', '1495850452', '2017-05-27 09:00:52', '2017-05-27 09:00:52');
INSERT INTO `oauth_access_tokens` VALUES ('wnHWFlNoWxVVzDZ6fzqAMY9BUUhutaw2TuCS8SMK', '296', '1505373736', '2017-09-14 14:22:16', '2017-09-14 14:22:16');
INSERT INTO `oauth_access_tokens` VALUES ('WQ4q51NEgUzvAT8XN6f333c1SRVDG2a7UPQrlyFI', '290', '1505372450', '2017-09-14 14:00:50', '2017-09-14 14:00:50');
INSERT INTO `oauth_access_tokens` VALUES ('wqQZiIZueATA2uCqSYo13VsQdSaoGiRaEAjMsZlY', '330', '1505707019', '2017-09-18 10:56:59', '2017-09-18 10:56:59');
INSERT INTO `oauth_access_tokens` VALUES ('wqzicJkdjDraNgdtKEBOOmh2x4z5wjx7NrzysCkH', '158', '1496299868', '2017-06-01 13:51:08', '2017-06-01 13:51:08');
INSERT INTO `oauth_access_tokens` VALUES ('wRos4gATmiKxPrFf4WDRyeeTuNDl8TYwsnd3CdpD', '151', '1496224679', '2017-05-31 16:57:59', '2017-05-31 16:57:59');
INSERT INTO `oauth_access_tokens` VALUES ('x7erTrTvGMe29QA26gzpwoudFzzVXABLS2GQed8T', '342', '1505731447', '2017-09-18 17:44:07', '2017-09-18 17:44:07');
INSERT INTO `oauth_access_tokens` VALUES ('xAYQqfmh1pqyIqJyLA9qUQguCpldppmrwvpB7jlk', '82', '1495783579', '2017-05-26 14:26:19', '2017-05-26 14:26:19');
INSERT INTO `oauth_access_tokens` VALUES ('XBT3A7ynMtCY6A0tgq8TXhJzDBMvl9gaRpseGp3O', '14', '1495354078', '2017-05-21 15:07:58', '2017-05-21 15:07:58');
INSERT INTO `oauth_access_tokens` VALUES ('XcYvXsWBdRGXwqtFDQgSFXvmcmVplCka8uBxMu84', '277', '1505358678', '2017-09-14 10:11:19', '2017-09-14 10:11:19');
INSERT INTO `oauth_access_tokens` VALUES ('XdYsiqcKa91YxrdcsSIVGbhRvyicCDRj3VRivAcn', '247', '1505130205', '2017-09-11 18:43:25', '2017-09-11 18:43:25');
INSERT INTO `oauth_access_tokens` VALUES ('XejOZGwKWkdjLvRRMArzS0YvZESbruJcNQI6S2Rt', '390', '1505876616', '2017-09-20 10:03:36', '2017-09-20 10:03:36');
INSERT INTO `oauth_access_tokens` VALUES ('XEqZM0DG2iK5peDCpVAh0DPDOVHeL1G3cqOhaeFF', '227', '1504869590', '2017-09-08 18:19:50', '2017-09-08 18:19:50');
INSERT INTO `oauth_access_tokens` VALUES ('xeuBQIBIfS6DQeYPtAIFMkaCrFxnZhiuJlrCIfgi', '100', '1495786193', '2017-05-26 15:09:53', '2017-05-26 15:09:53');
INSERT INTO `oauth_access_tokens` VALUES ('xiprPVIbg5Wr9ZAi8OMDwHRLi5uV3UuEzAGOiBlg', '265', '1505198214', '2017-09-12 13:36:54', '2017-09-12 13:36:54');
INSERT INTO `oauth_access_tokens` VALUES ('XoUsQuLJ8SWaA6QelMuIHIAggfj1LY2aqvCr2KNb', '66', '1495783312', '2017-05-26 14:21:52', '2017-05-26 14:21:52');
INSERT INTO `oauth_access_tokens` VALUES ('xTCHg7tYKpP6ZDJ5oubHeKekI9uAoucZXJDsabjZ', '145', '1496219989', '2017-05-31 15:39:50', '2017-05-31 15:39:50');
INSERT INTO `oauth_access_tokens` VALUES ('xUoLhsTcafj2wIhBQ2xRsbU5VriDCiO8EqKAQXnp', '31', '1495764478', '2017-05-26 09:07:58', '2017-05-26 09:07:58');
INSERT INTO `oauth_access_tokens` VALUES ('XUWmJWKbQ47nYcTbkDfNuLy2uAovZX7qYebciPUn', '155', '1496297894', '2017-06-01 13:18:14', '2017-06-01 13:18:14');
INSERT INTO `oauth_access_tokens` VALUES ('y9ymk9Go3F5mV8PjzBOfm6VH9opEAiCOONh2GOpR', '297', '1505373759', '2017-09-14 14:22:39', '2017-09-14 14:22:39');
INSERT INTO `oauth_access_tokens` VALUES ('YAWSmiL70OrnsyY1bzj4AiUFeC9D2ZdTcLhmI9Yj', '22', '1495517646', '2017-05-23 12:34:06', '2017-05-23 12:34:06');
INSERT INTO `oauth_access_tokens` VALUES ('yAZzqCO7irjTfnmk5wrxlNRYsp1vGibnSyYPelfj', '407', '1505980084', '2017-09-21 14:48:04', '2017-09-21 14:48:04');
INSERT INTO `oauth_access_tokens` VALUES ('ycTqbD5dSBJUPTXs1vfstZdIKwEc5kpqut7QrXds', '96', '1495785370', '2017-05-26 14:56:10', '2017-05-26 14:56:10');
INSERT INTO `oauth_access_tokens` VALUES ('YFeJJ5HwDS15TPHxr3pqpKr8bB5tuqVktCmtMXyi', '229', '1504869919', '2017-09-08 18:25:19', '2017-09-08 18:25:19');
INSERT INTO `oauth_access_tokens` VALUES ('YGzXwKZgE1kIJ0ct4Wrn3T0HZAhhIu6jaWqzAqJk', '60', '1495783265', '2017-05-26 14:21:05', '2017-05-26 14:21:05');
INSERT INTO `oauth_access_tokens` VALUES ('yiHvtNVIe6KTnN7yfuwFbBsakrxKrxshsgiGGayp', '83', '1495783608', '2017-05-26 14:26:48', '2017-05-26 14:26:48');
INSERT INTO `oauth_access_tokens` VALUES ('yLbpGECHc58tV1bnhKohgxqy6VqH2Lt558B3agWp', '311', '1505377758', '2017-09-14 15:29:18', '2017-09-14 15:29:18');
INSERT INTO `oauth_access_tokens` VALUES ('YRQPRyA8FWH2fLOxQlvI0nZXVcfOzKcLeGuWqisv', '128', '1496071074', '2017-05-29 22:17:54', '2017-05-29 22:17:54');
INSERT INTO `oauth_access_tokens` VALUES ('Z0nqBpXIClsRhuhzmKfc9PITe2L5HNKBfKOGLWzK', '347', '1505733774', '2017-09-18 18:22:54', '2017-09-18 18:22:54');
INSERT INTO `oauth_access_tokens` VALUES ('Z3sIksaB4MdFTVMWeMJAbe7VpVVD3aEcl4ARyN5o', '328', '1505706796', '2017-09-18 10:53:16', '2017-09-18 10:53:16');
INSERT INTO `oauth_access_tokens` VALUES ('z4ftwlJAh8Ijh2QTTgfY2YY8Qg4MkcUAXhyjwgu3', '359', '1505806650', '2017-09-19 14:37:30', '2017-09-19 14:37:30');
INSERT INTO `oauth_access_tokens` VALUES ('Z5zLhHXxZSHf0mCiZWIhicTW6Rtutui9jV603O8Y', '173', '1504683795', '2017-09-06 14:43:15', '2017-09-06 14:43:15');
INSERT INTO `oauth_access_tokens` VALUES ('z7YQpcA6VDME15YFDkn5CVMkrZKNdcIqs4BfalDq', '39', '1495783062', '2017-05-26 14:17:42', '2017-05-26 14:17:42');
INSERT INTO `oauth_access_tokens` VALUES ('zePEyuVD2Lw0hs3PojI4MN9AiLPsBbsutMp7S2Ms', '211', '1504852414', '2017-09-08 13:33:34', '2017-09-08 13:33:34');
INSERT INTO `oauth_access_tokens` VALUES ('Zfbhw5lHX7VpMibgYXsMcsiSU3CTvnEfkbTOLuAq', '113', '1495855269', '2017-05-27 10:21:09', '2017-05-27 10:21:09');
INSERT INTO `oauth_access_tokens` VALUES ('zfpgEXu6vcOZ3CoBTUzYTFP6irETJghn2zITwZdx', '106', '1495809733', '2017-05-26 21:42:13', '2017-05-26 21:42:13');
INSERT INTO `oauth_access_tokens` VALUES ('ZgjjoVnZdLiaBETin8QByUVoC75dpjawPF1l72Cm', '343', '1505731447', '2017-09-18 17:44:07', '2017-09-18 17:44:07');
INSERT INTO `oauth_access_tokens` VALUES ('zgksxrLbeUCs0kLITHXqyvZux3UaWV9trLaHL7pz', '391', '1505892293', '2017-09-20 14:24:54', '2017-09-20 14:24:54');
INSERT INTO `oauth_access_tokens` VALUES ('Zk5Oa0V86ExOyKZ5QA9V9liL6Gb8v6mwL9bswAIc', '30', '1495764164', '2017-05-26 09:02:44', '2017-05-26 09:02:44');
INSERT INTO `oauth_access_tokens` VALUES ('zQWGmh1Gd1GUAdgM6GLk2tBBNnQMnnrN3qQj01xQ', '403', '1505895104', '2017-09-20 15:11:44', '2017-09-20 15:11:44');
INSERT INTO `oauth_access_tokens` VALUES ('ZwSb9D6xcGMYqqWa7JAc2iJG1daLTe6tSkAPej4g', '268', '1505204862', '2017-09-12 15:27:48', '2017-09-12 15:27:48');
INSERT INTO `oauth_access_tokens` VALUES ('ZXMNUqrn8JTkWpetClU69WeloFr312u97QW5unyJ', '358', '1505806612', '2017-09-19 14:36:52', '2017-09-19 14:36:52');
INSERT INTO `oauth_access_tokens` VALUES ('Zyth9FBfLIEAI7sXm4Fci1NRMeZQarCwWMrw7DmH', '434', '1517803289', '2018-02-05 11:01:29', '2018-02-05 11:01:29');
INSERT INTO `oauth_access_tokens` VALUES ('zZwgTtzgL0LFDPO0ufYBElhzzDljXaSv1bPYOyOI', '335', '1505707580', '2017-09-18 11:06:20', '2017-09-18 11:06:20');

-- ----------------------------
-- Table structure for oauth_access_token_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access_token_scopes`;
CREATE TABLE `oauth_access_token_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_access_token_scopes_access_token_id_index` (`access_token_id`) USING BTREE,
  KEY `oauth_access_token_scopes_scope_id_index` (`scope_id`) USING BTREE,
  CONSTRAINT `oauth_access_token_scopes_ibfk_1` FOREIGN KEY (`access_token_id`) REFERENCES `oauth_access_tokens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_access_token_scopes_ibfk_2` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_access_token_scopes
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_auth_codes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expire_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_session_id_index` (`session_id`) USING BTREE,
  CONSTRAINT `oauth_auth_codes_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_auth_codes
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_auth_code_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_auth_code_scopes`;
CREATE TABLE `oauth_auth_code_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_auth_code_scopes_auth_code_id_index` (`auth_code_id`) USING BTREE,
  KEY `oauth_auth_code_scopes_scope_id_index` (`scope_id`) USING BTREE,
  CONSTRAINT `oauth_auth_code_scopes_ibfk_1` FOREIGN KEY (`auth_code_id`) REFERENCES `oauth_auth_codes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_auth_code_scopes_ibfk_2` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_auth_code_scopes
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_clients_id_secret_unique` (`id`,`secret`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_clients
-- ----------------------------
INSERT INTO `oauth_clients` VALUES ('ios', '111', 'ios', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for oauth_client_endpoints
-- ----------------------------
DROP TABLE IF EXISTS `oauth_client_endpoints`;
CREATE TABLE `oauth_client_endpoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_client_endpoints_client_id_redirect_uri_unique` (`client_id`,`redirect_uri`) USING BTREE,
  CONSTRAINT `oauth_client_endpoints_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_client_endpoints
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_client_grants
-- ----------------------------
DROP TABLE IF EXISTS `oauth_client_grants`;
CREATE TABLE `oauth_client_grants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `grant_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_client_grants_client_id_index` (`client_id`) USING BTREE,
  KEY `oauth_client_grants_grant_id_index` (`grant_id`) USING BTREE,
  CONSTRAINT `oauth_client_grants_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `oauth_client_grants_ibfk_2` FOREIGN KEY (`grant_id`) REFERENCES `oauth_grants` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_client_grants
-- ----------------------------
INSERT INTO `oauth_client_grants` VALUES ('1', 'ios', 'password', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for oauth_client_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_client_scopes`;
CREATE TABLE `oauth_client_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_client_scopes_client_id_index` (`client_id`) USING BTREE,
  KEY `oauth_client_scopes_scope_id_index` (`scope_id`) USING BTREE,
  CONSTRAINT `oauth_client_scopes_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_client_scopes_ibfk_2` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_client_scopes
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_grants
-- ----------------------------
DROP TABLE IF EXISTS `oauth_grants`;
CREATE TABLE `oauth_grants` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_grants
-- ----------------------------
INSERT INTO `oauth_grants` VALUES ('client_credentials', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `oauth_grants` VALUES ('password', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for oauth_grant_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_grant_scopes`;
CREATE TABLE `oauth_grant_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_grant_scopes_grant_id_index` (`grant_id`) USING BTREE,
  KEY `oauth_grant_scopes_scope_id_index` (`scope_id`) USING BTREE,
  CONSTRAINT `oauth_grant_scopes_ibfk_1` FOREIGN KEY (`grant_id`) REFERENCES `oauth_grants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_grant_scopes_ibfk_2` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_grant_scopes
-- ----------------------------
INSERT INTO `oauth_grant_scopes` VALUES ('1', 'client_credentials', 'system_private', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `oauth_grant_scopes` VALUES ('2', 'password', 'system_public', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `oauth_grant_scopes` VALUES ('3', 'client_credentials', 'system_public', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for oauth_refresh_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `access_token_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `expire_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`access_token_id`),
  UNIQUE KEY `oauth_refresh_tokens_id_unique` (`id`) USING BTREE,
  CONSTRAINT `oauth_refresh_tokens_ibfk_1` FOREIGN KEY (`access_token_id`) REFERENCES `oauth_access_tokens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_refresh_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_scopes`;
CREATE TABLE `oauth_scopes` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_scopes
-- ----------------------------
INSERT INTO `oauth_scopes` VALUES ('system_private', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `oauth_scopes` VALUES ('system_public', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for oauth_sessions
-- ----------------------------
DROP TABLE IF EXISTS `oauth_sessions`;
CREATE TABLE `oauth_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `owner_type` enum('client','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `owner_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_redirect_uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_sessions_client_id_owner_type_owner_id_index` (`client_id`,`owner_type`,`owner_id`) USING BTREE,
  CONSTRAINT `oauth_sessions_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=437 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_sessions
-- ----------------------------
INSERT INTO `oauth_sessions` VALUES ('1', 'ios', 'user', '1', null, '2017-05-20 17:27:21', '2017-05-20 17:27:21');
INSERT INTO `oauth_sessions` VALUES ('2', 'ios', 'user', '1', null, '2017-05-20 17:36:41', '2017-05-20 17:36:41');
INSERT INTO `oauth_sessions` VALUES ('3', 'ios', 'user', '1', null, '2017-05-20 19:32:53', '2017-05-20 19:32:53');
INSERT INTO `oauth_sessions` VALUES ('4', 'ios', 'user', '4', null, '2017-05-20 19:33:54', '2017-05-20 19:33:54');
INSERT INTO `oauth_sessions` VALUES ('5', 'ios', 'user', '4', null, '2017-05-21 12:41:01', '2017-05-21 12:41:01');
INSERT INTO `oauth_sessions` VALUES ('6', 'ios', 'user', '4', null, '2017-05-21 12:42:58', '2017-05-21 12:42:58');
INSERT INTO `oauth_sessions` VALUES ('7', 'ios', 'user', '1', null, '2017-05-21 12:44:30', '2017-05-21 12:44:30');
INSERT INTO `oauth_sessions` VALUES ('8', 'ios', 'user', '4', null, '2017-05-21 12:44:43', '2017-05-21 12:44:43');
INSERT INTO `oauth_sessions` VALUES ('9', 'ios', 'user', '1', null, '2017-05-21 13:10:43', '2017-05-21 13:10:43');
INSERT INTO `oauth_sessions` VALUES ('10', 'ios', 'user', '4', null, '2017-05-21 13:22:02', '2017-05-21 13:22:02');
INSERT INTO `oauth_sessions` VALUES ('11', 'ios', 'user', '4', null, '2017-05-21 13:26:01', '2017-05-21 13:26:01');
INSERT INTO `oauth_sessions` VALUES ('12', 'ios', 'user', '1', null, '2017-05-21 14:11:32', '2017-05-21 14:11:32');
INSERT INTO `oauth_sessions` VALUES ('13', 'ios', 'user', '4', null, '2017-05-21 14:19:41', '2017-05-21 14:19:41');
INSERT INTO `oauth_sessions` VALUES ('14', 'ios', 'user', '1', null, '2017-05-21 15:07:58', '2017-05-21 15:07:58');
INSERT INTO `oauth_sessions` VALUES ('15', 'ios', 'user', '4', null, '2017-05-21 15:14:10', '2017-05-21 15:14:10');
INSERT INTO `oauth_sessions` VALUES ('16', 'ios', 'user', '1', null, '2017-05-23 11:39:51', '2017-05-23 11:39:51');
INSERT INTO `oauth_sessions` VALUES ('17', 'ios', 'user', '4', null, '2017-05-23 11:45:37', '2017-05-23 11:45:37');
INSERT INTO `oauth_sessions` VALUES ('18', 'ios', 'user', '1', null, '2017-05-23 11:58:23', '2017-05-23 11:58:23');
INSERT INTO `oauth_sessions` VALUES ('19', 'ios', 'user', '1', null, '2017-05-23 12:06:57', '2017-05-23 12:06:57');
INSERT INTO `oauth_sessions` VALUES ('20', 'ios', 'user', '1', null, '2017-05-23 12:21:15', '2017-05-23 12:21:15');
INSERT INTO `oauth_sessions` VALUES ('21', 'ios', 'user', '4', null, '2017-05-23 12:29:16', '2017-05-23 12:29:16');
INSERT INTO `oauth_sessions` VALUES ('22', 'ios', 'user', '1', null, '2017-05-23 12:34:06', '2017-05-23 12:34:06');
INSERT INTO `oauth_sessions` VALUES ('23', 'ios', 'user', '4', null, '2017-05-23 12:36:26', '2017-05-23 12:36:26');
INSERT INTO `oauth_sessions` VALUES ('24', 'ios', 'user', '1', null, '2017-05-23 12:48:15', '2017-05-23 12:48:15');
INSERT INTO `oauth_sessions` VALUES ('25', 'ios', 'user', '4', null, '2017-05-23 12:50:35', '2017-05-23 12:50:35');
INSERT INTO `oauth_sessions` VALUES ('26', 'ios', 'user', '1', null, '2017-05-23 14:19:15', '2017-05-23 14:19:15');
INSERT INTO `oauth_sessions` VALUES ('27', 'ios', 'user', '4', null, '2017-05-23 14:22:38', '2017-05-23 14:22:38');
INSERT INTO `oauth_sessions` VALUES ('28', 'ios', 'user', '1', null, '2017-05-26 08:52:54', '2017-05-26 08:52:54');
INSERT INTO `oauth_sessions` VALUES ('29', 'ios', 'user', '4', null, '2017-05-26 08:56:38', '2017-05-26 08:56:38');
INSERT INTO `oauth_sessions` VALUES ('30', 'ios', 'user', '1', null, '2017-05-26 09:02:44', '2017-05-26 09:02:44');
INSERT INTO `oauth_sessions` VALUES ('31', 'ios', 'user', '4', null, '2017-05-26 09:07:58', '2017-05-26 09:07:58');
INSERT INTO `oauth_sessions` VALUES ('32', 'ios', 'user', '1', null, '2017-05-26 09:53:51', '2017-05-26 09:53:51');
INSERT INTO `oauth_sessions` VALUES ('33', 'ios', 'user', '4', null, '2017-05-26 09:55:24', '2017-05-26 09:55:24');
INSERT INTO `oauth_sessions` VALUES ('34', 'ios', 'user', '154', null, '2017-05-26 14:16:07', '2017-05-26 14:16:07');
INSERT INTO `oauth_sessions` VALUES ('35', 'ios', 'user', '155', null, '2017-05-26 14:16:11', '2017-05-26 14:16:11');
INSERT INTO `oauth_sessions` VALUES ('36', 'ios', 'user', '154', null, '2017-05-26 14:16:24', '2017-05-26 14:16:24');
INSERT INTO `oauth_sessions` VALUES ('37', 'ios', 'user', '154', null, '2017-05-26 14:16:46', '2017-05-26 14:16:46');
INSERT INTO `oauth_sessions` VALUES ('38', 'ios', 'user', '155', null, '2017-05-26 14:17:36', '2017-05-26 14:17:36');
INSERT INTO `oauth_sessions` VALUES ('39', 'ios', 'user', '154', null, '2017-05-26 14:17:42', '2017-05-26 14:17:42');
INSERT INTO `oauth_sessions` VALUES ('40', 'ios', 'user', '151', null, '2017-05-26 14:17:58', '2017-05-26 14:17:58');
INSERT INTO `oauth_sessions` VALUES ('41', 'ios', 'user', '154', null, '2017-05-26 14:18:02', '2017-05-26 14:18:02');
INSERT INTO `oauth_sessions` VALUES ('42', 'ios', 'user', '151', null, '2017-05-26 14:18:10', '2017-05-26 14:18:10');
INSERT INTO `oauth_sessions` VALUES ('43', 'ios', 'user', '154', null, '2017-05-26 14:18:16', '2017-05-26 14:18:16');
INSERT INTO `oauth_sessions` VALUES ('44', 'ios', 'user', '154', null, '2017-05-26 14:18:20', '2017-05-26 14:18:20');
INSERT INTO `oauth_sessions` VALUES ('45', 'ios', 'user', '151', null, '2017-05-26 14:18:22', '2017-05-26 14:18:22');
INSERT INTO `oauth_sessions` VALUES ('46', 'ios', 'user', '154', null, '2017-05-26 14:18:25', '2017-05-26 14:18:25');
INSERT INTO `oauth_sessions` VALUES ('47', 'ios', 'user', '154', null, '2017-05-26 14:18:30', '2017-05-26 14:18:30');
INSERT INTO `oauth_sessions` VALUES ('48', 'ios', 'user', '151', null, '2017-05-26 14:18:47', '2017-05-26 14:18:47');
INSERT INTO `oauth_sessions` VALUES ('49', 'ios', 'user', '149', null, '2017-05-26 14:18:51', '2017-05-26 14:18:51');
INSERT INTO `oauth_sessions` VALUES ('50', 'ios', 'user', '149', null, '2017-05-26 14:18:55', '2017-05-26 14:18:55');
INSERT INTO `oauth_sessions` VALUES ('51', 'ios', 'user', '151', null, '2017-05-26 14:18:56', '2017-05-26 14:18:56');
INSERT INTO `oauth_sessions` VALUES ('52', 'ios', 'user', '152', null, '2017-05-26 14:19:11', '2017-05-26 14:19:11');
INSERT INTO `oauth_sessions` VALUES ('53', 'ios', 'user', '154', null, '2017-05-26 14:19:14', '2017-05-26 14:19:14');
INSERT INTO `oauth_sessions` VALUES ('54', 'ios', 'user', '152', null, '2017-05-26 14:19:24', '2017-05-26 14:19:24');
INSERT INTO `oauth_sessions` VALUES ('55', 'ios', 'user', '151', null, '2017-05-26 14:19:33', '2017-05-26 14:19:33');
INSERT INTO `oauth_sessions` VALUES ('56', 'ios', 'user', '153', null, '2017-05-26 14:19:53', '2017-05-26 14:19:53');
INSERT INTO `oauth_sessions` VALUES ('57', 'ios', 'user', '153', null, '2017-05-26 14:20:00', '2017-05-26 14:20:00');
INSERT INTO `oauth_sessions` VALUES ('58', 'ios', 'user', '150', null, '2017-05-26 14:20:43', '2017-05-26 14:20:43');
INSERT INTO `oauth_sessions` VALUES ('59', 'ios', 'user', '152', null, '2017-05-26 14:20:44', '2017-05-26 14:20:44');
INSERT INTO `oauth_sessions` VALUES ('60', 'ios', 'user', '153', null, '2017-05-26 14:21:05', '2017-05-26 14:21:05');
INSERT INTO `oauth_sessions` VALUES ('61', 'ios', 'user', '150', null, '2017-05-26 14:21:21', '2017-05-26 14:21:21');
INSERT INTO `oauth_sessions` VALUES ('62', 'ios', 'user', '152', null, '2017-05-26 14:21:24', '2017-05-26 14:21:24');
INSERT INTO `oauth_sessions` VALUES ('63', 'ios', 'user', '154', null, '2017-05-26 14:21:29', '2017-05-26 14:21:29');
INSERT INTO `oauth_sessions` VALUES ('64', 'ios', 'user', '150', null, '2017-05-26 14:21:47', '2017-05-26 14:21:47');
INSERT INTO `oauth_sessions` VALUES ('65', 'ios', 'user', '153', null, '2017-05-26 14:21:51', '2017-05-26 14:21:51');
INSERT INTO `oauth_sessions` VALUES ('66', 'ios', 'user', '150', null, '2017-05-26 14:21:52', '2017-05-26 14:21:52');
INSERT INTO `oauth_sessions` VALUES ('67', 'ios', 'user', '150', null, '2017-05-26 14:21:57', '2017-05-26 14:21:57');
INSERT INTO `oauth_sessions` VALUES ('68', 'ios', 'user', '150', null, '2017-05-26 14:22:02', '2017-05-26 14:22:02');
INSERT INTO `oauth_sessions` VALUES ('69', 'ios', 'user', '149', null, '2017-05-26 14:22:06', '2017-05-26 14:22:06');
INSERT INTO `oauth_sessions` VALUES ('70', 'ios', 'user', '150', null, '2017-05-26 14:22:10', '2017-05-26 14:22:10');
INSERT INTO `oauth_sessions` VALUES ('71', 'ios', 'user', '151', null, '2017-05-26 14:22:30', '2017-05-26 14:22:30');
INSERT INTO `oauth_sessions` VALUES ('72', 'ios', 'user', '150', null, '2017-05-26 14:22:41', '2017-05-26 14:22:41');
INSERT INTO `oauth_sessions` VALUES ('73', 'ios', 'user', '154', null, '2017-05-26 14:23:36', '2017-05-26 14:23:36');
INSERT INTO `oauth_sessions` VALUES ('74', 'ios', 'user', '154', null, '2017-05-26 14:23:40', '2017-05-26 14:23:40');
INSERT INTO `oauth_sessions` VALUES ('75', 'ios', 'user', '154', null, '2017-05-26 14:23:49', '2017-05-26 14:23:49');
INSERT INTO `oauth_sessions` VALUES ('76', 'ios', 'user', '153', null, '2017-05-26 14:23:55', '2017-05-26 14:23:55');
INSERT INTO `oauth_sessions` VALUES ('77', 'ios', 'user', '154', null, '2017-05-26 14:23:56', '2017-05-26 14:23:56');
INSERT INTO `oauth_sessions` VALUES ('78', 'ios', 'user', '153', null, '2017-05-26 14:24:43', '2017-05-26 14:24:43');
INSERT INTO `oauth_sessions` VALUES ('79', 'ios', 'user', '152', null, '2017-05-26 14:25:04', '2017-05-26 14:25:04');
INSERT INTO `oauth_sessions` VALUES ('80', 'ios', 'user', '150', null, '2017-05-26 14:25:48', '2017-05-26 14:25:48');
INSERT INTO `oauth_sessions` VALUES ('81', 'ios', 'user', '154', null, '2017-05-26 14:26:04', '2017-05-26 14:26:04');
INSERT INTO `oauth_sessions` VALUES ('82', 'ios', 'user', '149', null, '2017-05-26 14:26:19', '2017-05-26 14:26:19');
INSERT INTO `oauth_sessions` VALUES ('83', 'ios', 'user', '153', null, '2017-05-26 14:26:48', '2017-05-26 14:26:48');
INSERT INTO `oauth_sessions` VALUES ('84', 'ios', 'user', '151', null, '2017-05-26 14:27:30', '2017-05-26 14:27:30');
INSERT INTO `oauth_sessions` VALUES ('85', 'ios', 'user', '152', null, '2017-05-26 14:27:40', '2017-05-26 14:27:40');
INSERT INTO `oauth_sessions` VALUES ('86', 'ios', 'user', '1', null, '2017-05-26 14:28:35', '2017-05-26 14:28:35');
INSERT INTO `oauth_sessions` VALUES ('87', 'ios', 'user', '148', null, '2017-05-26 14:29:36', '2017-05-26 14:29:36');
INSERT INTO `oauth_sessions` VALUES ('88', 'ios', 'user', '156', null, '2017-05-26 14:30:51', '2017-05-26 14:30:51');
INSERT INTO `oauth_sessions` VALUES ('89', 'ios', 'user', '150', null, '2017-05-26 14:32:33', '2017-05-26 14:32:33');
INSERT INTO `oauth_sessions` VALUES ('90', 'ios', 'user', '155', null, '2017-05-26 14:36:24', '2017-05-26 14:36:24');
INSERT INTO `oauth_sessions` VALUES ('91', 'ios', 'user', '155', null, '2017-05-26 14:39:56', '2017-05-26 14:39:56');
INSERT INTO `oauth_sessions` VALUES ('92', 'ios', 'user', '148', null, '2017-05-26 14:41:46', '2017-05-26 14:41:46');
INSERT INTO `oauth_sessions` VALUES ('93', 'ios', 'user', '154', null, '2017-05-26 14:48:55', '2017-05-26 14:48:55');
INSERT INTO `oauth_sessions` VALUES ('94', 'ios', 'user', '150', null, '2017-05-26 14:53:22', '2017-05-26 14:53:22');
INSERT INTO `oauth_sessions` VALUES ('95', 'ios', 'user', '153', null, '2017-05-26 14:54:19', '2017-05-26 14:54:19');
INSERT INTO `oauth_sessions` VALUES ('96', 'ios', 'user', '148', null, '2017-05-26 14:56:10', '2017-05-26 14:56:10');
INSERT INTO `oauth_sessions` VALUES ('97', 'ios', 'user', '153', null, '2017-05-26 14:56:54', '2017-05-26 14:56:54');
INSERT INTO `oauth_sessions` VALUES ('98', 'ios', 'user', '151', null, '2017-05-26 14:59:37', '2017-05-26 14:59:37');
INSERT INTO `oauth_sessions` VALUES ('99', 'ios', 'user', '153', null, '2017-05-26 15:03:41', '2017-05-26 15:03:41');
INSERT INTO `oauth_sessions` VALUES ('100', 'ios', 'user', '156', null, '2017-05-26 15:09:53', '2017-05-26 15:09:53');
INSERT INTO `oauth_sessions` VALUES ('101', 'ios', 'user', '153', null, '2017-05-26 15:16:25', '2017-05-26 15:16:25');
INSERT INTO `oauth_sessions` VALUES ('102', 'ios', 'user', '1', null, '2017-05-26 15:30:18', '2017-05-26 15:30:18');
INSERT INTO `oauth_sessions` VALUES ('103', 'ios', 'user', '1', null, '2017-05-26 15:31:50', '2017-05-26 15:31:50');
INSERT INTO `oauth_sessions` VALUES ('104', 'ios', 'user', '1', null, '2017-05-26 15:35:00', '2017-05-26 15:35:00');
INSERT INTO `oauth_sessions` VALUES ('105', 'ios', 'user', '1', null, '2017-05-26 20:55:30', '2017-05-26 20:55:30');
INSERT INTO `oauth_sessions` VALUES ('106', 'ios', 'user', '1', null, '2017-05-26 21:42:13', '2017-05-26 21:42:13');
INSERT INTO `oauth_sessions` VALUES ('107', 'ios', 'user', '1', null, '2017-05-27 07:55:24', '2017-05-27 07:55:24');
INSERT INTO `oauth_sessions` VALUES ('108', 'ios', 'user', '1', null, '2017-05-27 08:32:20', '2017-05-27 08:32:20');
INSERT INTO `oauth_sessions` VALUES ('109', 'ios', 'user', '1', null, '2017-05-27 09:00:52', '2017-05-27 09:00:52');
INSERT INTO `oauth_sessions` VALUES ('110', 'ios', 'user', '1', null, '2017-05-27 09:30:21', '2017-05-27 09:30:21');
INSERT INTO `oauth_sessions` VALUES ('111', 'ios', 'user', '1', null, '2017-05-27 09:46:40', '2017-05-27 09:46:40');
INSERT INTO `oauth_sessions` VALUES ('112', 'ios', 'user', '148', null, '2017-05-27 10:00:41', '2017-05-27 10:00:41');
INSERT INTO `oauth_sessions` VALUES ('113', 'ios', 'user', '151', null, '2017-05-27 10:21:09', '2017-05-27 10:21:09');
INSERT INTO `oauth_sessions` VALUES ('114', 'ios', 'user', '149', null, '2017-05-27 10:41:37', '2017-05-27 10:41:37');
INSERT INTO `oauth_sessions` VALUES ('115', 'ios', 'user', '1', null, '2017-05-27 11:12:09', '2017-05-27 11:12:09');
INSERT INTO `oauth_sessions` VALUES ('116', 'ios', 'user', '1', null, '2017-05-27 11:13:50', '2017-05-27 11:13:50');
INSERT INTO `oauth_sessions` VALUES ('117', 'ios', 'user', '1', null, '2017-05-29 12:39:00', '2017-05-29 12:39:00');
INSERT INTO `oauth_sessions` VALUES ('118', 'ios', 'user', '1', null, '2017-05-29 12:39:06', '2017-05-29 12:39:06');
INSERT INTO `oauth_sessions` VALUES ('119', 'ios', 'user', '1', null, '2017-05-29 12:53:37', '2017-05-29 12:53:37');
INSERT INTO `oauth_sessions` VALUES ('120', 'ios', 'user', '1', null, '2017-05-29 12:53:39', '2017-05-29 12:53:39');
INSERT INTO `oauth_sessions` VALUES ('121', 'ios', 'user', '1', null, '2017-05-29 12:53:42', '2017-05-29 12:53:42');
INSERT INTO `oauth_sessions` VALUES ('122', 'ios', 'user', '1', null, '2017-05-29 12:53:45', '2017-05-29 12:53:45');
INSERT INTO `oauth_sessions` VALUES ('123', 'ios', 'user', '1', null, '2017-05-29 13:35:13', '2017-05-29 13:35:13');
INSERT INTO `oauth_sessions` VALUES ('124', 'ios', 'user', '1', null, '2017-05-29 13:35:18', '2017-05-29 13:35:18');
INSERT INTO `oauth_sessions` VALUES ('125', 'ios', 'user', '1', null, '2017-05-29 22:16:44', '2017-05-29 22:16:44');
INSERT INTO `oauth_sessions` VALUES ('126', 'ios', 'user', '1', null, '2017-05-29 22:16:50', '2017-05-29 22:16:50');
INSERT INTO `oauth_sessions` VALUES ('127', 'ios', 'user', '1', null, '2017-05-29 22:17:49', '2017-05-29 22:17:49');
INSERT INTO `oauth_sessions` VALUES ('128', 'ios', 'user', '1', null, '2017-05-29 22:17:54', '2017-05-29 22:17:54');
INSERT INTO `oauth_sessions` VALUES ('129', 'ios', 'user', '1', null, '2017-05-29 22:19:16', '2017-05-29 22:19:16');
INSERT INTO `oauth_sessions` VALUES ('130', 'ios', 'user', '1', null, '2017-05-29 22:19:21', '2017-05-29 22:19:21');
INSERT INTO `oauth_sessions` VALUES ('131', 'ios', 'user', '1', null, '2017-05-30 10:31:48', '2017-05-30 10:31:48');
INSERT INTO `oauth_sessions` VALUES ('132', 'ios', 'user', '1', null, '2017-05-30 10:31:53', '2017-05-30 10:31:53');
INSERT INTO `oauth_sessions` VALUES ('133', 'ios', 'user', '1', null, '2017-05-30 10:33:04', '2017-05-30 10:33:04');
INSERT INTO `oauth_sessions` VALUES ('134', 'ios', 'user', '1', null, '2017-05-30 10:33:10', '2017-05-30 10:33:10');
INSERT INTO `oauth_sessions` VALUES ('135', 'ios', 'user', '1', null, '2017-05-31 10:29:12', '2017-05-31 10:29:12');
INSERT INTO `oauth_sessions` VALUES ('136', 'ios', 'user', '1', null, '2017-05-31 10:29:18', '2017-05-31 10:29:18');
INSERT INTO `oauth_sessions` VALUES ('137', 'ios', 'user', '1', null, '2017-05-31 10:36:29', '2017-05-31 10:36:29');
INSERT INTO `oauth_sessions` VALUES ('138', 'ios', 'user', '1', null, '2017-05-31 10:36:34', '2017-05-31 10:36:34');
INSERT INTO `oauth_sessions` VALUES ('139', 'ios', 'user', '1', null, '2017-05-31 15:32:25', '2017-05-31 15:32:25');
INSERT INTO `oauth_sessions` VALUES ('140', 'ios', 'user', '1', null, '2017-05-31 15:32:31', '2017-05-31 15:32:31');
INSERT INTO `oauth_sessions` VALUES ('141', 'ios', 'user', '1', null, '2017-05-31 15:35:57', '2017-05-31 15:35:57');
INSERT INTO `oauth_sessions` VALUES ('142', 'ios', 'user', '1', null, '2017-05-31 15:36:03', '2017-05-31 15:36:03');
INSERT INTO `oauth_sessions` VALUES ('143', 'ios', 'user', '1', null, '2017-05-31 15:39:42', '2017-05-31 15:39:42');
INSERT INTO `oauth_sessions` VALUES ('144', 'ios', 'user', '1', null, '2017-05-31 15:39:47', '2017-05-31 15:39:47');
INSERT INTO `oauth_sessions` VALUES ('145', 'ios', 'user', '1', null, '2017-05-31 15:39:50', '2017-05-31 15:39:50');
INSERT INTO `oauth_sessions` VALUES ('146', 'ios', 'user', '1', null, '2017-05-31 15:39:55', '2017-05-31 15:39:55');
INSERT INTO `oauth_sessions` VALUES ('147', 'ios', 'user', '1', null, '2017-05-31 15:58:46', '2017-05-31 15:58:46');
INSERT INTO `oauth_sessions` VALUES ('148', 'ios', 'user', '1', null, '2017-05-31 15:58:49', '2017-05-31 15:58:49');
INSERT INTO `oauth_sessions` VALUES ('149', 'ios', 'user', '1', null, '2017-05-31 15:58:51', '2017-05-31 15:58:51');
INSERT INTO `oauth_sessions` VALUES ('150', 'ios', 'user', '1', null, '2017-05-31 15:58:55', '2017-05-31 15:58:55');
INSERT INTO `oauth_sessions` VALUES ('151', 'ios', 'user', '1', null, '2017-05-31 16:57:59', '2017-05-31 16:57:59');
INSERT INTO `oauth_sessions` VALUES ('152', 'ios', 'user', '1', null, '2017-05-31 16:58:04', '2017-05-31 16:58:04');
INSERT INTO `oauth_sessions` VALUES ('153', 'ios', 'user', '1', null, '2017-06-01 11:31:08', '2017-06-01 11:31:08');
INSERT INTO `oauth_sessions` VALUES ('154', 'ios', 'user', '1', null, '2017-06-01 11:31:13', '2017-06-01 11:31:13');
INSERT INTO `oauth_sessions` VALUES ('155', 'ios', 'user', '1', null, '2017-06-01 13:18:14', '2017-06-01 13:18:14');
INSERT INTO `oauth_sessions` VALUES ('156', 'ios', 'user', '1', null, '2017-06-01 13:18:19', '2017-06-01 13:18:19');
INSERT INTO `oauth_sessions` VALUES ('157', 'ios', 'user', '1', null, '2017-06-01 13:51:03', '2017-06-01 13:51:03');
INSERT INTO `oauth_sessions` VALUES ('158', 'ios', 'user', '1', null, '2017-06-01 13:51:08', '2017-06-01 13:51:08');
INSERT INTO `oauth_sessions` VALUES ('159', 'ios', 'user', '1', null, '2017-06-01 16:08:35', '2017-06-01 16:08:35');
INSERT INTO `oauth_sessions` VALUES ('160', 'ios', 'user', '1', null, '2017-06-01 16:08:40', '2017-06-01 16:08:40');
INSERT INTO `oauth_sessions` VALUES ('161', 'ios', 'user', '1', null, '2017-06-01 18:02:28', '2017-06-01 18:02:28');
INSERT INTO `oauth_sessions` VALUES ('162', 'ios', 'user', '1', null, '2017-06-01 18:02:33', '2017-06-01 18:02:33');
INSERT INTO `oauth_sessions` VALUES ('163', 'ios', 'user', '1', null, '2017-06-01 18:02:38', '2017-06-01 18:02:38');
INSERT INTO `oauth_sessions` VALUES ('164', 'ios', 'user', '1', null, '2017-06-01 18:02:43', '2017-06-01 18:02:43');
INSERT INTO `oauth_sessions` VALUES ('165', 'ios', 'user', '1', null, '2017-06-01 18:03:30', '2017-06-01 18:03:30');
INSERT INTO `oauth_sessions` VALUES ('166', 'ios', 'user', '1', null, '2017-06-01 18:03:36', '2017-06-01 18:03:36');
INSERT INTO `oauth_sessions` VALUES ('167', 'ios', 'user', '148', null, '2017-09-06 11:01:12', '2017-09-06 11:01:12');
INSERT INTO `oauth_sessions` VALUES ('168', 'ios', 'user', '148', null, '2017-09-06 11:23:05', '2017-09-06 11:23:05');
INSERT INTO `oauth_sessions` VALUES ('169', 'ios', 'user', '148', null, '2017-09-06 13:31:27', '2017-09-06 13:31:27');
INSERT INTO `oauth_sessions` VALUES ('170', 'ios', 'user', '148', null, '2017-09-06 13:36:10', '2017-09-06 13:36:10');
INSERT INTO `oauth_sessions` VALUES ('171', 'ios', 'user', '148', null, '2017-09-06 13:59:52', '2017-09-06 13:59:52');
INSERT INTO `oauth_sessions` VALUES ('172', 'ios', 'user', '148', null, '2017-09-06 14:30:49', '2017-09-06 14:30:49');
INSERT INTO `oauth_sessions` VALUES ('173', 'ios', 'user', '148', null, '2017-09-06 14:43:15', '2017-09-06 14:43:15');
INSERT INTO `oauth_sessions` VALUES ('174', 'ios', 'user', '148', null, '2017-09-06 15:41:27', '2017-09-06 15:41:27');
INSERT INTO `oauth_sessions` VALUES ('175', 'ios', 'user', '148', null, '2017-09-06 15:42:26', '2017-09-06 15:42:26');
INSERT INTO `oauth_sessions` VALUES ('176', 'ios', 'user', '148', null, '2017-09-06 15:43:04', '2017-09-06 15:43:04');
INSERT INTO `oauth_sessions` VALUES ('177', 'ios', 'user', '148', null, '2017-09-06 15:43:31', '2017-09-06 15:43:31');
INSERT INTO `oauth_sessions` VALUES ('178', 'ios', 'user', '148', null, '2017-09-06 15:44:06', '2017-09-06 15:44:06');
INSERT INTO `oauth_sessions` VALUES ('179', 'ios', 'user', '148', null, '2017-09-06 15:44:35', '2017-09-06 15:44:35');
INSERT INTO `oauth_sessions` VALUES ('180', 'ios', 'user', '148', null, '2017-09-06 15:44:56', '2017-09-06 15:44:56');
INSERT INTO `oauth_sessions` VALUES ('181', 'ios', 'user', '148', null, '2017-09-06 15:46:37', '2017-09-06 15:46:37');
INSERT INTO `oauth_sessions` VALUES ('182', 'ios', 'user', '148', null, '2017-09-06 15:46:50', '2017-09-06 15:46:50');
INSERT INTO `oauth_sessions` VALUES ('183', 'ios', 'user', '148', null, '2017-09-06 15:47:40', '2017-09-06 15:47:40');
INSERT INTO `oauth_sessions` VALUES ('184', 'ios', 'user', '148', null, '2017-09-06 15:48:05', '2017-09-06 15:48:05');
INSERT INTO `oauth_sessions` VALUES ('185', 'ios', 'user', '148', null, '2017-09-06 15:52:43', '2017-09-06 15:52:43');
INSERT INTO `oauth_sessions` VALUES ('186', 'ios', 'user', '148', null, '2017-09-06 15:53:35', '2017-09-06 15:53:35');
INSERT INTO `oauth_sessions` VALUES ('187', 'ios', 'user', '148', null, '2017-09-06 16:06:06', '2017-09-06 16:06:06');
INSERT INTO `oauth_sessions` VALUES ('188', 'ios', 'user', '148', null, '2017-09-06 16:08:40', '2017-09-06 16:08:40');
INSERT INTO `oauth_sessions` VALUES ('189', 'ios', 'user', '148', null, '2017-09-06 16:32:51', '2017-09-06 16:32:51');
INSERT INTO `oauth_sessions` VALUES ('190', 'ios', 'user', '148', null, '2017-09-06 16:59:14', '2017-09-06 16:59:14');
INSERT INTO `oauth_sessions` VALUES ('191', 'ios', 'user', '148', null, '2017-09-06 16:59:16', '2017-09-06 16:59:16');
INSERT INTO `oauth_sessions` VALUES ('192', 'ios', 'user', '148', null, '2017-09-06 17:19:39', '2017-09-06 17:19:39');
INSERT INTO `oauth_sessions` VALUES ('193', 'ios', 'user', '148', null, '2017-09-06 17:19:50', '2017-09-06 17:19:50');
INSERT INTO `oauth_sessions` VALUES ('194', 'ios', 'user', '148', null, '2017-09-06 17:21:06', '2017-09-06 17:21:06');
INSERT INTO `oauth_sessions` VALUES ('195', 'ios', 'user', '148', null, '2017-09-06 17:21:50', '2017-09-06 17:21:50');
INSERT INTO `oauth_sessions` VALUES ('196', 'ios', 'user', '148', null, '2017-09-06 17:22:23', '2017-09-06 17:22:23');
INSERT INTO `oauth_sessions` VALUES ('197', 'ios', 'user', '148', null, '2017-09-06 17:24:26', '2017-09-06 17:24:26');
INSERT INTO `oauth_sessions` VALUES ('198', 'ios', 'user', '148', null, '2017-09-06 17:24:48', '2017-09-06 17:24:48');
INSERT INTO `oauth_sessions` VALUES ('199', 'ios', 'user', '148', null, '2017-09-06 17:25:20', '2017-09-06 17:25:20');
INSERT INTO `oauth_sessions` VALUES ('200', 'ios', 'user', '148', null, '2017-09-06 17:51:36', '2017-09-06 17:51:36');
INSERT INTO `oauth_sessions` VALUES ('201', 'ios', 'user', '148', null, '2017-09-06 18:32:16', '2017-09-06 18:32:16');
INSERT INTO `oauth_sessions` VALUES ('202', 'ios', 'user', '148', null, '2017-09-07 09:44:23', '2017-09-07 09:44:23');
INSERT INTO `oauth_sessions` VALUES ('203', 'ios', 'user', '148', null, '2017-09-07 10:04:21', '2017-09-07 10:04:21');
INSERT INTO `oauth_sessions` VALUES ('204', 'ios', 'user', '148', null, '2017-09-07 11:16:23', '2017-09-07 11:16:23');
INSERT INTO `oauth_sessions` VALUES ('205', 'ios', 'user', '148', null, '2017-09-07 11:17:01', '2017-09-07 11:17:01');
INSERT INTO `oauth_sessions` VALUES ('206', 'ios', 'user', '148', null, '2017-09-07 11:26:00', '2017-09-07 11:26:00');
INSERT INTO `oauth_sessions` VALUES ('207', 'ios', 'user', '148', null, '2017-09-07 11:27:42', '2017-09-07 11:27:42');
INSERT INTO `oauth_sessions` VALUES ('208', 'ios', 'user', '148', null, '2017-09-07 11:31:58', '2017-09-07 11:31:58');
INSERT INTO `oauth_sessions` VALUES ('209', 'ios', 'user', '148', null, '2017-09-07 13:25:46', '2017-09-07 13:25:46');
INSERT INTO `oauth_sessions` VALUES ('210', 'ios', 'user', '148', null, '2017-09-08 10:22:05', '2017-09-08 10:22:05');
INSERT INTO `oauth_sessions` VALUES ('211', 'ios', 'user', '148', null, '2017-09-08 13:33:34', '2017-09-08 13:33:34');
INSERT INTO `oauth_sessions` VALUES ('212', 'ios', 'user', '148', null, '2017-09-08 13:34:04', '2017-09-08 13:34:04');
INSERT INTO `oauth_sessions` VALUES ('213', 'ios', 'user', '148', null, '2017-09-08 13:37:07', '2017-09-08 13:37:07');
INSERT INTO `oauth_sessions` VALUES ('214', 'ios', 'user', '148', null, '2017-09-08 13:41:01', '2017-09-08 13:41:01');
INSERT INTO `oauth_sessions` VALUES ('215', 'ios', 'user', '148', null, '2017-09-08 13:42:52', '2017-09-08 13:42:52');
INSERT INTO `oauth_sessions` VALUES ('216', 'ios', 'user', '148', null, '2017-09-08 13:46:27', '2017-09-08 13:46:27');
INSERT INTO `oauth_sessions` VALUES ('217', 'ios', 'user', '148', null, '2017-09-08 14:52:31', '2017-09-08 14:52:31');
INSERT INTO `oauth_sessions` VALUES ('218', 'ios', 'user', '148', null, '2017-09-08 16:46:27', '2017-09-08 16:46:27');
INSERT INTO `oauth_sessions` VALUES ('219', 'ios', 'user', '148', null, '2017-09-08 16:49:00', '2017-09-08 16:49:00');
INSERT INTO `oauth_sessions` VALUES ('220', 'ios', 'user', '148', null, '2017-09-08 17:35:03', '2017-09-08 17:35:03');
INSERT INTO `oauth_sessions` VALUES ('221', 'ios', 'user', '148', null, '2017-09-08 17:39:21', '2017-09-08 17:39:21');
INSERT INTO `oauth_sessions` VALUES ('222', 'ios', 'user', '151', null, '2017-09-08 17:48:37', '2017-09-08 17:48:37');
INSERT INTO `oauth_sessions` VALUES ('223', 'ios', 'user', '152', null, '2017-09-08 18:01:25', '2017-09-08 18:01:25');
INSERT INTO `oauth_sessions` VALUES ('224', 'ios', 'user', '151', null, '2017-09-08 18:04:54', '2017-09-08 18:04:54');
INSERT INTO `oauth_sessions` VALUES ('225', 'ios', 'user', '152', null, '2017-09-08 18:05:57', '2017-09-08 18:05:57');
INSERT INTO `oauth_sessions` VALUES ('226', 'ios', 'user', '152', null, '2017-09-08 18:15:17', '2017-09-08 18:15:17');
INSERT INTO `oauth_sessions` VALUES ('227', 'ios', 'user', '152', null, '2017-09-08 18:19:50', '2017-09-08 18:19:50');
INSERT INTO `oauth_sessions` VALUES ('228', 'ios', 'user', '152', null, '2017-09-08 18:21:22', '2017-09-08 18:21:22');
INSERT INTO `oauth_sessions` VALUES ('229', 'ios', 'user', '152', null, '2017-09-08 18:25:19', '2017-09-08 18:25:19');
INSERT INTO `oauth_sessions` VALUES ('230', 'ios', 'user', '152', null, '2017-09-11 16:36:42', '2017-09-11 16:36:42');
INSERT INTO `oauth_sessions` VALUES ('231', 'ios', 'user', '152', null, '2017-09-11 16:37:13', '2017-09-11 16:37:13');
INSERT INTO `oauth_sessions` VALUES ('232', 'ios', 'user', '152', null, '2017-09-11 16:37:42', '2017-09-11 16:37:42');
INSERT INTO `oauth_sessions` VALUES ('233', 'ios', 'user', '152', null, '2017-09-11 16:37:50', '2017-09-11 16:37:50');
INSERT INTO `oauth_sessions` VALUES ('234', 'ios', 'user', '152', null, '2017-09-11 17:05:36', '2017-09-11 17:05:36');
INSERT INTO `oauth_sessions` VALUES ('235', 'ios', 'user', '148', null, '2017-09-11 17:06:12', '2017-09-11 17:06:12');
INSERT INTO `oauth_sessions` VALUES ('236', 'ios', 'user', '148', null, '2017-09-11 17:51:41', '2017-09-11 17:51:41');
INSERT INTO `oauth_sessions` VALUES ('237', 'ios', 'user', '148', null, '2017-09-11 18:13:04', '2017-09-11 18:13:04');
INSERT INTO `oauth_sessions` VALUES ('238', 'ios', 'user', '148', null, '2017-09-11 18:13:45', '2017-09-11 18:13:45');
INSERT INTO `oauth_sessions` VALUES ('239', 'ios', 'user', '148', null, '2017-09-11 18:14:09', '2017-09-11 18:14:09');
INSERT INTO `oauth_sessions` VALUES ('240', 'ios', 'user', '148', null, '2017-09-11 18:21:37', '2017-09-11 18:21:37');
INSERT INTO `oauth_sessions` VALUES ('241', 'ios', 'user', '148', null, '2017-09-11 18:22:47', '2017-09-11 18:22:47');
INSERT INTO `oauth_sessions` VALUES ('242', 'ios', 'user', '148', null, '2017-09-11 18:31:57', '2017-09-11 18:31:57');
INSERT INTO `oauth_sessions` VALUES ('243', 'ios', 'user', '148', null, '2017-09-11 18:33:45', '2017-09-11 18:33:45');
INSERT INTO `oauth_sessions` VALUES ('244', 'ios', 'user', '148', null, '2017-09-11 18:39:48', '2017-09-11 18:39:48');
INSERT INTO `oauth_sessions` VALUES ('245', 'ios', 'user', '148', null, '2017-09-11 18:40:14', '2017-09-11 18:40:14');
INSERT INTO `oauth_sessions` VALUES ('246', 'ios', 'user', '148', null, '2017-09-11 18:40:22', '2017-09-11 18:40:22');
INSERT INTO `oauth_sessions` VALUES ('247', 'ios', 'user', '148', null, '2017-09-11 18:43:25', '2017-09-11 18:43:25');
INSERT INTO `oauth_sessions` VALUES ('248', 'ios', 'user', '148', null, '2017-09-11 18:44:02', '2017-09-11 18:44:02');
INSERT INTO `oauth_sessions` VALUES ('249', 'ios', 'user', '148', null, '2017-09-11 18:44:17', '2017-09-11 18:44:17');
INSERT INTO `oauth_sessions` VALUES ('250', 'ios', 'user', '148', null, '2017-09-11 18:45:09', '2017-09-11 18:45:09');
INSERT INTO `oauth_sessions` VALUES ('251', 'ios', 'user', '148', null, '2017-09-11 18:45:23', '2017-09-11 18:45:23');
INSERT INTO `oauth_sessions` VALUES ('252', 'ios', 'user', '148', null, '2017-09-12 09:34:46', '2017-09-12 09:34:46');
INSERT INTO `oauth_sessions` VALUES ('253', 'ios', 'user', '148', null, '2017-09-12 09:43:36', '2017-09-12 09:43:36');
INSERT INTO `oauth_sessions` VALUES ('254', 'ios', 'user', '148', null, '2017-09-12 09:50:13', '2017-09-12 09:50:13');
INSERT INTO `oauth_sessions` VALUES ('255', 'ios', 'user', '148', null, '2017-09-12 10:06:50', '2017-09-12 10:06:50');
INSERT INTO `oauth_sessions` VALUES ('256', 'ios', 'user', '148', null, '2017-09-12 10:30:04', '2017-09-12 10:30:04');
INSERT INTO `oauth_sessions` VALUES ('257', 'ios', 'user', '148', null, '2017-09-12 10:31:40', '2017-09-12 10:31:40');
INSERT INTO `oauth_sessions` VALUES ('258', 'ios', 'user', '148', null, '2017-09-12 10:32:09', '2017-09-12 10:32:09');
INSERT INTO `oauth_sessions` VALUES ('259', 'ios', 'user', '148', null, '2017-09-12 10:57:14', '2017-09-12 10:57:14');
INSERT INTO `oauth_sessions` VALUES ('260', 'ios', 'user', '148', null, '2017-09-12 11:04:32', '2017-09-12 11:04:32');
INSERT INTO `oauth_sessions` VALUES ('261', 'ios', 'user', '148', null, '2017-09-12 11:24:02', '2017-09-12 11:24:02');
INSERT INTO `oauth_sessions` VALUES ('262', 'ios', 'user', '148', null, '2017-09-12 11:38:42', '2017-09-12 11:38:42');
INSERT INTO `oauth_sessions` VALUES ('263', 'ios', 'user', '148', null, '2017-09-12 11:43:33', '2017-09-12 11:43:33');
INSERT INTO `oauth_sessions` VALUES ('264', 'ios', 'user', '148', null, '2017-09-12 11:51:32', '2017-09-12 11:51:32');
INSERT INTO `oauth_sessions` VALUES ('265', 'ios', 'user', '151', null, '2017-09-12 13:36:54', '2017-09-12 13:36:54');
INSERT INTO `oauth_sessions` VALUES ('266', 'ios', 'user', '151', null, '2017-09-12 13:46:47', '2017-09-12 13:46:47');
INSERT INTO `oauth_sessions` VALUES ('267', 'ios', 'user', '151', null, '2017-09-12 13:56:36', '2017-09-12 13:56:36');
INSERT INTO `oauth_sessions` VALUES ('268', 'ios', 'user', '151', null, '2017-09-12 15:27:42', '2017-09-12 15:27:42');
INSERT INTO `oauth_sessions` VALUES ('269', 'ios', 'user', '148', null, '2017-09-14 09:40:06', '2017-09-14 09:40:06');
INSERT INTO `oauth_sessions` VALUES ('270', 'ios', 'user', '148', null, '2017-09-14 09:41:12', '2017-09-14 09:41:12');
INSERT INTO `oauth_sessions` VALUES ('271', 'ios', 'user', '149', null, '2017-09-14 09:42:02', '2017-09-14 09:42:02');
INSERT INTO `oauth_sessions` VALUES ('272', 'ios', 'user', '4', null, '2017-09-14 09:43:32', '2017-09-14 09:43:32');
INSERT INTO `oauth_sessions` VALUES ('273', 'ios', 'user', '150', null, '2017-09-14 09:44:57', '2017-09-14 09:44:57');
INSERT INTO `oauth_sessions` VALUES ('274', 'ios', 'user', '4', null, '2017-09-14 09:57:35', '2017-09-14 09:57:35');
INSERT INTO `oauth_sessions` VALUES ('275', 'ios', 'user', '4', null, '2017-09-14 10:08:04', '2017-09-14 10:08:04');
INSERT INTO `oauth_sessions` VALUES ('276', 'ios', 'user', '4', null, '2017-09-14 10:10:06', '2017-09-14 10:10:06');
INSERT INTO `oauth_sessions` VALUES ('277', 'ios', 'user', '148', null, '2017-09-14 10:11:18', '2017-09-14 10:11:18');
INSERT INTO `oauth_sessions` VALUES ('278', 'ios', 'user', '148', null, '2017-09-14 13:17:00', '2017-09-14 13:17:00');
INSERT INTO `oauth_sessions` VALUES ('279', 'ios', 'user', '148', null, '2017-09-14 13:17:40', '2017-09-14 13:17:40');
INSERT INTO `oauth_sessions` VALUES ('280', 'ios', 'user', '4', null, '2017-09-14 13:24:15', '2017-09-14 13:24:15');
INSERT INTO `oauth_sessions` VALUES ('281', 'ios', 'user', '4', null, '2017-09-14 13:31:44', '2017-09-14 13:31:44');
INSERT INTO `oauth_sessions` VALUES ('282', 'ios', 'user', '148', null, '2017-09-14 13:41:52', '2017-09-14 13:41:52');
INSERT INTO `oauth_sessions` VALUES ('283', 'ios', 'user', '150', null, '2017-09-14 13:41:56', '2017-09-14 13:41:56');
INSERT INTO `oauth_sessions` VALUES ('284', 'ios', 'user', '149', null, '2017-09-14 13:42:31', '2017-09-14 13:42:31');
INSERT INTO `oauth_sessions` VALUES ('285', 'ios', 'user', '149', null, '2017-09-14 13:44:21', '2017-09-14 13:44:21');
INSERT INTO `oauth_sessions` VALUES ('286', 'ios', 'user', '148', null, '2017-09-14 13:44:52', '2017-09-14 13:44:52');
INSERT INTO `oauth_sessions` VALUES ('287', 'ios', 'user', '148', null, '2017-09-14 13:45:42', '2017-09-14 13:45:42');
INSERT INTO `oauth_sessions` VALUES ('288', 'ios', 'user', '148', null, '2017-09-14 13:49:04', '2017-09-14 13:49:04');
INSERT INTO `oauth_sessions` VALUES ('289', 'ios', 'user', '149', null, '2017-09-14 13:52:01', '2017-09-14 13:52:01');
INSERT INTO `oauth_sessions` VALUES ('290', 'ios', 'user', '149', null, '2017-09-14 14:00:50', '2017-09-14 14:00:50');
INSERT INTO `oauth_sessions` VALUES ('291', 'ios', 'user', '151', null, '2017-09-14 14:19:34', '2017-09-14 14:19:34');
INSERT INTO `oauth_sessions` VALUES ('292', 'ios', 'user', '148', null, '2017-09-14 14:19:43', '2017-09-14 14:19:43');
INSERT INTO `oauth_sessions` VALUES ('293', 'ios', 'user', '149', null, '2017-09-14 14:19:43', '2017-09-14 14:19:43');
INSERT INTO `oauth_sessions` VALUES ('294', 'ios', 'user', '151', null, '2017-09-14 14:20:45', '2017-09-14 14:20:45');
INSERT INTO `oauth_sessions` VALUES ('295', 'ios', 'user', '154', null, '2017-09-14 14:21:54', '2017-09-14 14:21:54');
INSERT INTO `oauth_sessions` VALUES ('296', 'ios', 'user', '148', null, '2017-09-14 14:22:16', '2017-09-14 14:22:16');
INSERT INTO `oauth_sessions` VALUES ('297', 'ios', 'user', '148', null, '2017-09-14 14:22:39', '2017-09-14 14:22:39');
INSERT INTO `oauth_sessions` VALUES ('298', 'ios', 'user', '151', null, '2017-09-14 14:22:43', '2017-09-14 14:22:43');
INSERT INTO `oauth_sessions` VALUES ('299', 'ios', 'user', '149', null, '2017-09-14 14:22:51', '2017-09-14 14:22:51');
INSERT INTO `oauth_sessions` VALUES ('300', 'ios', 'user', '148', null, '2017-09-14 14:27:36', '2017-09-14 14:27:36');
INSERT INTO `oauth_sessions` VALUES ('301', 'ios', 'user', '154', null, '2017-09-14 14:27:59', '2017-09-14 14:27:59');
INSERT INTO `oauth_sessions` VALUES ('302', 'ios', 'user', '148', null, '2017-09-14 14:40:05', '2017-09-14 14:40:05');
INSERT INTO `oauth_sessions` VALUES ('303', 'ios', 'user', '148', null, '2017-09-14 15:08:18', '2017-09-14 15:08:18');
INSERT INTO `oauth_sessions` VALUES ('304', 'ios', 'user', '148', null, '2017-09-14 15:08:50', '2017-09-14 15:08:50');
INSERT INTO `oauth_sessions` VALUES ('305', 'ios', 'user', '148', null, '2017-09-14 15:09:49', '2017-09-14 15:09:49');
INSERT INTO `oauth_sessions` VALUES ('306', 'ios', 'user', '148', null, '2017-09-14 15:10:39', '2017-09-14 15:10:39');
INSERT INTO `oauth_sessions` VALUES ('307', 'ios', 'user', '148', null, '2017-09-14 15:10:57', '2017-09-14 15:10:57');
INSERT INTO `oauth_sessions` VALUES ('308', 'ios', 'user', '148', null, '2017-09-14 15:11:37', '2017-09-14 15:11:37');
INSERT INTO `oauth_sessions` VALUES ('309', 'ios', 'user', '148', null, '2017-09-14 15:12:00', '2017-09-14 15:12:00');
INSERT INTO `oauth_sessions` VALUES ('310', 'ios', 'user', '148', null, '2017-09-14 15:13:03', '2017-09-14 15:13:03');
INSERT INTO `oauth_sessions` VALUES ('311', 'ios', 'user', '151', null, '2017-09-14 15:29:18', '2017-09-14 15:29:18');
INSERT INTO `oauth_sessions` VALUES ('312', 'ios', 'user', '4', null, '2017-09-14 15:29:52', '2017-09-14 15:29:52');
INSERT INTO `oauth_sessions` VALUES ('313', 'ios', 'user', '151', null, '2017-09-14 15:31:05', '2017-09-14 15:31:05');
INSERT INTO `oauth_sessions` VALUES ('314', 'ios', 'user', '152', null, '2017-09-14 15:31:23', '2017-09-14 15:31:23');
INSERT INTO `oauth_sessions` VALUES ('315', 'ios', 'user', '151', null, '2017-09-14 15:31:56', '2017-09-14 15:31:56');
INSERT INTO `oauth_sessions` VALUES ('316', 'ios', 'user', '151', null, '2017-09-14 15:32:37', '2017-09-14 15:32:37');
INSERT INTO `oauth_sessions` VALUES ('317', 'ios', 'user', '151', null, '2017-09-14 15:33:44', '2017-09-14 15:33:44');
INSERT INTO `oauth_sessions` VALUES ('318', 'ios', 'user', '149', null, '2017-09-14 15:33:49', '2017-09-14 15:33:49');
INSERT INTO `oauth_sessions` VALUES ('319', 'ios', 'user', '152', null, '2017-09-14 15:49:38', '2017-09-14 15:49:38');
INSERT INTO `oauth_sessions` VALUES ('320', 'ios', 'user', '152', null, '2017-09-14 15:55:23', '2017-09-14 15:55:23');
INSERT INTO `oauth_sessions` VALUES ('321', 'ios', 'user', '148', null, '2017-09-14 16:24:17', '2017-09-14 16:24:17');
INSERT INTO `oauth_sessions` VALUES ('322', 'ios', 'user', '148', null, '2017-09-14 16:30:10', '2017-09-14 16:30:10');
INSERT INTO `oauth_sessions` VALUES ('323', 'ios', 'user', '154', null, '2017-09-18 10:37:40', '2017-09-18 10:37:40');
INSERT INTO `oauth_sessions` VALUES ('324', 'ios', 'user', '154', null, '2017-09-18 10:45:47', '2017-09-18 10:45:47');
INSERT INTO `oauth_sessions` VALUES ('325', 'ios', 'user', '148', null, '2017-09-18 10:46:59', '2017-09-18 10:46:59');
INSERT INTO `oauth_sessions` VALUES ('326', 'ios', 'user', '156', null, '2017-09-18 10:50:00', '2017-09-18 10:50:00');
INSERT INTO `oauth_sessions` VALUES ('327', 'ios', 'user', '156', null, '2017-09-18 10:52:24', '2017-09-18 10:52:24');
INSERT INTO `oauth_sessions` VALUES ('328', 'ios', 'user', '149', null, '2017-09-18 10:53:16', '2017-09-18 10:53:16');
INSERT INTO `oauth_sessions` VALUES ('329', 'ios', 'user', '156', null, '2017-09-18 10:53:29', '2017-09-18 10:53:29');
INSERT INTO `oauth_sessions` VALUES ('330', 'ios', 'user', '154', null, '2017-09-18 10:56:59', '2017-09-18 10:56:59');
INSERT INTO `oauth_sessions` VALUES ('331', 'ios', 'user', '156', null, '2017-09-18 10:57:36', '2017-09-18 10:57:36');
INSERT INTO `oauth_sessions` VALUES ('332', 'ios', 'user', '149', null, '2017-09-18 10:58:51', '2017-09-18 10:58:51');
INSERT INTO `oauth_sessions` VALUES ('333', 'ios', 'user', '154', null, '2017-09-18 10:59:13', '2017-09-18 10:59:13');
INSERT INTO `oauth_sessions` VALUES ('334', 'ios', 'user', '149', null, '2017-09-18 11:01:46', '2017-09-18 11:01:46');
INSERT INTO `oauth_sessions` VALUES ('335', 'ios', 'user', '149', null, '2017-09-18 11:06:20', '2017-09-18 11:06:20');
INSERT INTO `oauth_sessions` VALUES ('336', 'ios', 'user', '156', null, '2017-09-18 11:07:36', '2017-09-18 11:07:36');
INSERT INTO `oauth_sessions` VALUES ('337', 'ios', 'user', '148', null, '2017-09-18 11:22:35', '2017-09-18 11:22:35');
INSERT INTO `oauth_sessions` VALUES ('338', 'ios', 'user', '148', null, '2017-09-18 17:39:08', '2017-09-18 17:39:08');
INSERT INTO `oauth_sessions` VALUES ('339', 'ios', 'user', '149', null, '2017-09-18 17:40:12', '2017-09-18 17:40:12');
INSERT INTO `oauth_sessions` VALUES ('340', 'ios', 'user', '151', null, '2017-09-18 17:42:54', '2017-09-18 17:42:54');
INSERT INTO `oauth_sessions` VALUES ('341', 'ios', 'user', '152', null, '2017-09-18 17:43:44', '2017-09-18 17:43:44');
INSERT INTO `oauth_sessions` VALUES ('342', 'ios', 'user', '151', null, '2017-09-18 17:44:07', '2017-09-18 17:44:07');
INSERT INTO `oauth_sessions` VALUES ('343', 'ios', 'user', '156', null, '2017-09-18 17:44:07', '2017-09-18 17:44:07');
INSERT INTO `oauth_sessions` VALUES ('344', 'ios', 'user', '154', null, '2017-09-18 18:17:15', '2017-09-18 18:17:15');
INSERT INTO `oauth_sessions` VALUES ('345', 'ios', 'user', '148', null, '2017-09-18 18:17:21', '2017-09-18 18:17:21');
INSERT INTO `oauth_sessions` VALUES ('346', 'ios', 'user', '152', null, '2017-09-18 18:22:03', '2017-09-18 18:22:03');
INSERT INTO `oauth_sessions` VALUES ('347', 'ios', 'user', '151', null, '2017-09-18 18:22:54', '2017-09-18 18:22:54');
INSERT INTO `oauth_sessions` VALUES ('348', 'ios', 'user', '152', null, '2017-09-18 18:22:59', '2017-09-18 18:22:59');
INSERT INTO `oauth_sessions` VALUES ('349', 'ios', 'user', '152', null, '2017-09-18 18:23:37', '2017-09-18 18:23:37');
INSERT INTO `oauth_sessions` VALUES ('350', 'ios', 'user', '149', null, '2017-09-18 18:26:51', '2017-09-18 18:26:51');
INSERT INTO `oauth_sessions` VALUES ('351', 'ios', 'user', '156', null, '2017-09-18 18:27:19', '2017-09-18 18:27:19');
INSERT INTO `oauth_sessions` VALUES ('352', 'ios', 'user', '148', null, '2017-09-18 18:29:05', '2017-09-18 18:29:05');
INSERT INTO `oauth_sessions` VALUES ('353', 'ios', 'user', '149', null, '2017-09-19 13:32:47', '2017-09-19 13:32:47');
INSERT INTO `oauth_sessions` VALUES ('354', 'ios', 'user', '156', null, '2017-09-19 13:33:09', '2017-09-19 13:33:09');
INSERT INTO `oauth_sessions` VALUES ('355', 'ios', 'user', '148', null, '2017-09-19 13:34:03', '2017-09-19 13:34:03');
INSERT INTO `oauth_sessions` VALUES ('356', 'ios', 'user', '149', null, '2017-09-19 13:35:01', '2017-09-19 13:35:01');
INSERT INTO `oauth_sessions` VALUES ('357', 'ios', 'user', '156', null, '2017-09-19 13:47:53', '2017-09-19 13:47:53');
INSERT INTO `oauth_sessions` VALUES ('358', 'ios', 'user', '149', null, '2017-09-19 14:36:52', '2017-09-19 14:36:52');
INSERT INTO `oauth_sessions` VALUES ('359', 'ios', 'user', '149', null, '2017-09-19 14:37:30', '2017-09-19 14:37:30');
INSERT INTO `oauth_sessions` VALUES ('360', 'ios', 'user', '148', null, '2017-09-19 15:02:54', '2017-09-19 15:02:54');
INSERT INTO `oauth_sessions` VALUES ('361', 'ios', 'user', '151', null, '2017-09-19 15:04:06', '2017-09-19 15:04:06');
INSERT INTO `oauth_sessions` VALUES ('362', 'ios', 'user', '149', null, '2017-09-19 15:04:51', '2017-09-19 15:04:51');
INSERT INTO `oauth_sessions` VALUES ('363', 'ios', 'user', '151', null, '2017-09-19 15:07:04', '2017-09-19 15:07:04');
INSERT INTO `oauth_sessions` VALUES ('364', 'ios', 'user', '150', null, '2017-09-19 15:07:57', '2017-09-19 15:07:57');
INSERT INTO `oauth_sessions` VALUES ('365', 'ios', 'user', '150', null, '2017-09-19 15:10:01', '2017-09-19 15:10:01');
INSERT INTO `oauth_sessions` VALUES ('366', 'ios', 'user', '148', null, '2017-09-19 15:11:19', '2017-09-19 15:11:19');
INSERT INTO `oauth_sessions` VALUES ('367', 'ios', 'user', '148', null, '2017-09-19 15:15:16', '2017-09-19 15:15:16');
INSERT INTO `oauth_sessions` VALUES ('368', 'ios', 'user', '149', null, '2017-09-19 15:19:37', '2017-09-19 15:19:37');
INSERT INTO `oauth_sessions` VALUES ('369', 'ios', 'user', '151', null, '2017-09-19 15:19:43', '2017-09-19 15:19:43');
INSERT INTO `oauth_sessions` VALUES ('370', 'ios', 'user', '148', null, '2017-09-19 15:20:02', '2017-09-19 15:20:02');
INSERT INTO `oauth_sessions` VALUES ('371', 'ios', 'user', '151', null, '2017-09-19 15:20:07', '2017-09-19 15:20:07');
INSERT INTO `oauth_sessions` VALUES ('372', 'ios', 'user', '150', null, '2017-09-19 15:20:11', '2017-09-19 15:20:11');
INSERT INTO `oauth_sessions` VALUES ('373', 'ios', 'user', '151', null, '2017-09-19 15:48:43', '2017-09-19 15:48:43');
INSERT INTO `oauth_sessions` VALUES ('374', 'ios', 'user', '151', null, '2017-09-19 15:48:58', '2017-09-19 15:48:58');
INSERT INTO `oauth_sessions` VALUES ('375', 'ios', 'user', '151', null, '2017-09-19 15:53:04', '2017-09-19 15:53:04');
INSERT INTO `oauth_sessions` VALUES ('376', 'ios', 'user', '150', null, '2017-09-19 15:53:55', '2017-09-19 15:53:55');
INSERT INTO `oauth_sessions` VALUES ('377', 'ios', 'user', '148', null, '2017-09-19 15:56:42', '2017-09-19 15:56:42');
INSERT INTO `oauth_sessions` VALUES ('378', 'ios', 'user', '149', null, '2017-09-19 16:04:05', '2017-09-19 16:04:05');
INSERT INTO `oauth_sessions` VALUES ('379', 'ios', 'user', '149', null, '2017-09-19 16:08:52', '2017-09-19 16:08:52');
INSERT INTO `oauth_sessions` VALUES ('380', 'ios', 'user', '149', null, '2017-09-19 16:11:00', '2017-09-19 16:11:00');
INSERT INTO `oauth_sessions` VALUES ('381', 'ios', 'user', '149', null, '2017-09-19 16:16:20', '2017-09-19 16:16:20');
INSERT INTO `oauth_sessions` VALUES ('382', 'ios', 'user', '150', null, '2017-09-19 16:18:34', '2017-09-19 16:18:34');
INSERT INTO `oauth_sessions` VALUES ('383', 'ios', 'user', '148', null, '2017-09-19 16:34:11', '2017-09-19 16:34:11');
INSERT INTO `oauth_sessions` VALUES ('384', 'ios', 'user', '150', null, '2017-09-19 16:37:16', '2017-09-19 16:37:16');
INSERT INTO `oauth_sessions` VALUES ('385', 'ios', 'user', '149', null, '2017-09-19 16:38:53', '2017-09-19 16:38:53');
INSERT INTO `oauth_sessions` VALUES ('386', 'ios', 'user', '148', null, '2017-09-19 16:39:19', '2017-09-19 16:39:19');
INSERT INTO `oauth_sessions` VALUES ('387', 'ios', 'user', '150', null, '2017-09-19 18:04:43', '2017-09-19 18:04:43');
INSERT INTO `oauth_sessions` VALUES ('388', 'ios', 'user', '150', null, '2017-09-19 18:16:12', '2017-09-19 18:16:12');
INSERT INTO `oauth_sessions` VALUES ('389', 'ios', 'user', '148', null, '2017-09-20 09:58:02', '2017-09-20 09:58:02');
INSERT INTO `oauth_sessions` VALUES ('390', 'ios', 'user', '148', null, '2017-09-20 10:03:36', '2017-09-20 10:03:36');
INSERT INTO `oauth_sessions` VALUES ('391', 'ios', 'user', '148', null, '2017-09-20 14:24:54', '2017-09-20 14:24:54');
INSERT INTO `oauth_sessions` VALUES ('392', 'ios', 'user', '149', null, '2017-09-20 14:26:26', '2017-09-20 14:26:26');
INSERT INTO `oauth_sessions` VALUES ('393', 'ios', 'user', '149', null, '2017-09-20 14:29:33', '2017-09-20 14:29:33');
INSERT INTO `oauth_sessions` VALUES ('394', 'ios', 'user', '156', null, '2017-09-20 14:31:09', '2017-09-20 14:31:09');
INSERT INTO `oauth_sessions` VALUES ('395', 'ios', 'user', '149', null, '2017-09-20 14:32:18', '2017-09-20 14:32:18');
INSERT INTO `oauth_sessions` VALUES ('396', 'ios', 'user', '150', null, '2017-09-20 14:44:13', '2017-09-20 14:44:13');
INSERT INTO `oauth_sessions` VALUES ('397', 'ios', 'user', '148', null, '2017-09-20 14:44:23', '2017-09-20 14:44:23');
INSERT INTO `oauth_sessions` VALUES ('398', 'ios', 'user', '148', null, '2017-09-20 14:51:53', '2017-09-20 14:51:53');
INSERT INTO `oauth_sessions` VALUES ('399', 'ios', 'user', '150', null, '2017-09-20 15:05:57', '2017-09-20 15:05:57');
INSERT INTO `oauth_sessions` VALUES ('400', 'ios', 'user', '150', null, '2017-09-20 15:08:00', '2017-09-20 15:08:00');
INSERT INTO `oauth_sessions` VALUES ('401', 'ios', 'user', '148', null, '2017-09-20 15:08:55', '2017-09-20 15:08:55');
INSERT INTO `oauth_sessions` VALUES ('402', 'ios', 'user', '148', null, '2017-09-20 15:11:37', '2017-09-20 15:11:37');
INSERT INTO `oauth_sessions` VALUES ('403', 'ios', 'user', '148', null, '2017-09-20 15:11:44', '2017-09-20 15:11:44');
INSERT INTO `oauth_sessions` VALUES ('404', 'ios', 'user', '148', null, '2017-09-20 15:11:52', '2017-09-20 15:11:52');
INSERT INTO `oauth_sessions` VALUES ('405', 'ios', 'user', '148', null, '2017-09-20 15:16:36', '2017-09-20 15:16:36');
INSERT INTO `oauth_sessions` VALUES ('406', 'ios', 'user', '151', null, '2017-09-21 14:47:56', '2017-09-21 14:47:56');
INSERT INTO `oauth_sessions` VALUES ('407', 'ios', 'user', '148', null, '2017-09-21 14:48:04', '2017-09-21 14:48:04');
INSERT INTO `oauth_sessions` VALUES ('408', 'ios', 'user', '152', null, '2017-09-21 14:49:13', '2017-09-21 14:49:13');
INSERT INTO `oauth_sessions` VALUES ('409', 'ios', 'user', '150', null, '2017-09-21 14:49:29', '2017-09-21 14:49:29');
INSERT INTO `oauth_sessions` VALUES ('410', 'ios', 'user', '151', null, '2017-09-21 14:51:43', '2017-09-21 14:51:43');
INSERT INTO `oauth_sessions` VALUES ('411', 'ios', 'user', '148', null, '2017-09-21 14:52:02', '2017-09-21 14:52:02');
INSERT INTO `oauth_sessions` VALUES ('412', 'ios', 'user', '148', null, '2017-09-21 15:22:38', '2017-09-21 15:22:38');
INSERT INTO `oauth_sessions` VALUES ('413', 'ios', 'user', '151', null, '2017-09-21 16:10:46', '2017-09-21 16:10:46');
INSERT INTO `oauth_sessions` VALUES ('414', 'ios', 'user', '150', null, '2017-09-21 16:10:47', '2017-09-21 16:10:47');
INSERT INTO `oauth_sessions` VALUES ('415', 'ios', 'user', '152', null, '2017-09-21 16:11:02', '2017-09-21 16:11:02');
INSERT INTO `oauth_sessions` VALUES ('416', 'ios', 'user', '148', null, '2017-09-21 16:11:06', '2017-09-21 16:11:06');
INSERT INTO `oauth_sessions` VALUES ('417', 'ios', 'user', '150', null, '2017-09-21 16:12:04', '2017-09-21 16:12:04');
INSERT INTO `oauth_sessions` VALUES ('418', 'ios', 'user', '148', null, '2017-09-21 16:14:08', '2017-09-21 16:14:08');
INSERT INTO `oauth_sessions` VALUES ('419', 'ios', 'user', '151', null, '2017-09-21 16:14:17', '2017-09-21 16:14:17');
INSERT INTO `oauth_sessions` VALUES ('420', 'ios', 'user', '150', null, '2017-09-21 16:15:11', '2017-09-21 16:15:11');
INSERT INTO `oauth_sessions` VALUES ('421', 'ios', 'user', '149', null, '2017-09-21 16:37:23', '2017-09-21 16:37:23');
INSERT INTO `oauth_sessions` VALUES ('422', 'ios', 'user', '151', null, '2017-09-21 16:37:47', '2017-09-21 16:37:47');
INSERT INTO `oauth_sessions` VALUES ('423', 'ios', 'user', '151', null, '2017-09-21 16:40:51', '2017-09-21 16:40:51');
INSERT INTO `oauth_sessions` VALUES ('424', 'ios', 'user', '148', null, '2017-09-21 16:41:15', '2017-09-21 16:41:15');
INSERT INTO `oauth_sessions` VALUES ('425', 'ios', 'user', '150', null, '2017-09-21 16:41:38', '2017-09-21 16:41:38');
INSERT INTO `oauth_sessions` VALUES ('426', 'ios', 'user', '150', null, '2017-09-21 16:42:42', '2017-09-21 16:42:42');
INSERT INTO `oauth_sessions` VALUES ('427', 'ios', 'user', '148', null, '2017-09-21 16:44:37', '2017-09-21 16:44:37');
INSERT INTO `oauth_sessions` VALUES ('428', 'ios', 'user', '149', null, '2018-01-03 15:50:44', '2018-01-03 15:50:44');
INSERT INTO `oauth_sessions` VALUES ('429', 'ios', 'user', '149', null, '2018-01-03 15:51:57', '2018-01-03 15:51:57');
INSERT INTO `oauth_sessions` VALUES ('430', 'ios', 'user', '149', null, '2018-01-03 15:52:50', '2018-01-03 15:52:50');
INSERT INTO `oauth_sessions` VALUES ('431', 'ios', 'user', '149', null, '2018-01-03 15:53:46', '2018-01-03 15:53:46');
INSERT INTO `oauth_sessions` VALUES ('432', 'ios', 'user', '149', null, '2018-01-03 15:54:17', '2018-01-03 15:54:17');
INSERT INTO `oauth_sessions` VALUES ('433', 'ios', 'user', '151', null, '2018-01-03 16:13:21', '2018-01-03 16:13:21');
INSERT INTO `oauth_sessions` VALUES ('434', 'ios', 'user', '4', null, '2018-02-05 11:01:29', '2018-02-05 11:01:29');
INSERT INTO `oauth_sessions` VALUES ('435', 'ios', 'user', '4', null, '2018-02-05 11:09:20', '2018-02-05 11:09:20');
INSERT INTO `oauth_sessions` VALUES ('436', 'ios', 'user', '4', null, '2018-02-05 13:17:34', '2018-02-05 13:17:34');

-- ----------------------------
-- Table structure for oauth_session_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_session_scopes`;
CREATE TABLE `oauth_session_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_session_scopes_scope_id_index` (`scope_id`) USING BTREE,
  KEY `oauth_session_scopes_session_id_index` (`session_id`) USING BTREE,
  CONSTRAINT `oauth_session_scopes_ibfk_1` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_session_scopes_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of oauth_session_scopes
-- ----------------------------

-- ----------------------------
-- Table structure for sys_config
-- ----------------------------
DROP TABLE IF EXISTS `sys_config`;
CREATE TABLE `sys_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `cate` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of sys_config
-- ----------------------------

-- ----------------------------
-- Table structure for sys_element
-- ----------------------------
DROP TABLE IF EXISTS `sys_element`;
CREATE TABLE `sys_element` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) NOT NULL,
  `page` varchar(50) DEFAULT '',
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='页面权限点';

-- ----------------------------
-- Records of sys_element
-- ----------------------------

-- ----------------------------
-- Table structure for sys_failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `sys_failed_jobs`;
CREATE TABLE `sys_failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for sys_functions
-- ----------------------------
DROP TABLE IF EXISTS `sys_functions`;
CREATE TABLE `sys_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统功能表';

-- ----------------------------
-- Records of sys_functions
-- ----------------------------

-- ----------------------------
-- Table structure for sys_groups
-- ----------------------------
DROP TABLE IF EXISTS `sys_groups`;
CREATE TABLE `sys_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组';

-- ----------------------------
-- Records of sys_groups
-- ----------------------------

-- ----------------------------
-- Table structure for sys_group_role
-- ----------------------------
DROP TABLE IF EXISTS `sys_group_role`;
CREATE TABLE `sys_group_role` (
  `group_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`group_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组角色表';

-- ----------------------------
-- Records of sys_group_role
-- ----------------------------

-- ----------------------------
-- Table structure for sys_menus
-- ----------------------------
DROP TABLE IF EXISTS `sys_menus`;
CREATE TABLE `sys_menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `ico` varchar(255) DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '0',
  `descrition` varchar(255) DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='系统菜单表';

-- ----------------------------
-- Records of sys_menus
-- ----------------------------
INSERT INTO `sys_menus` VALUES ('1', 'osce', '资源管理', '', '0', 'fa-list-alt', '1', '', '2016-01-23 15:40:42', '2016-01-23 15:40:42');
INSERT INTO `sys_menus` VALUES ('2', 'osce', '考试管理', '', '0', 'fa-th-large', '2', '', '2016-01-23 15:40:42', '2016-01-23 15:40:42');
INSERT INTO `sys_menus` VALUES ('3', 'osce', '统计分析', '', '0', 'fa-bar-chart-o', '3', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('4', 'osce', '系统管理', '', '0', 'fa-gear', '4', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('5', 'osce', '考试内容', 'osce.admin.topic.getList', '11', 'J_menuItem', '1', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('6', 'osce', '设备管理', 'osce.admin.machine.getMachineList', '11', 'J_menuItem', '3', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('7', 'osce', '场所管理', 'osce.admin.room.getRoomList', '11', 'J_menuItem', '4', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('8', 'osce', '考站管理', 'osce.admin.Station.getStationList', '11', 'J_menuItem', '6', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('9', 'osce', '人员管理', 'osce.admin.invigilator.getInvigilatorList', '11', 'J_menuItem', '2', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('12', 'osce', '考试安排', 'osce.admin.exam.getExamList', '211', 'J_menuItem', '1', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('13', 'osce', '考生管理', 'osce.admin.exam.getStudentQuery', '211', 'J_menuItem', '2', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('14', 'osce', '成绩查询', 'osce.admin.geExamResultList', '211', 'J_menuItem', '3', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('15', 'osce', '资讯&amp;通知', 'osce.admin.notice.getList', '211', 'J_menuItem', '4', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('16', 'osce', '考前培训', 'osce.admin.getTrainList', '211', 'J_menuItem', '5', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_menus` VALUES ('17', 'osce', '用户管理', 'osce.admin.user.getStaffList', '4', 'J_menuItem', '0', '', '2016-01-23 15:40:44', '2016-01-23 15:40:44');
INSERT INTO `sys_menus` VALUES ('18', 'osce', '权限管理', 'auth.AuthManage', '4', 'J_menuItem', '0', '', '2016-01-23 15:40:44', '2016-01-23 15:40:44');
INSERT INTO `sys_menus` VALUES ('19', 'osce', '系统设置', 'osce.admin.config.getIndex', '411', 'J_menuItem', '0', '', '2016-01-23 15:40:44', '2016-01-23 15:40:44');
INSERT INTO `sys_menus` VALUES ('20', 'osce', '项目成绩统计', 'osce.admin.course.getIndex', '311', 'J_menuItem', '0', '', '2016-01-31 12:44:56', '2016-01-31 12:44:59');
INSERT INTO `sys_menus` VALUES ('21', 'osce', '考生成绩统计', 'osce.admin.course.getStudentScore', '311', 'J_menuItem', '0', '', '2016-01-31 12:45:52', '2016-01-31 12:45:55');
INSERT INTO `sys_menus` VALUES ('22', 'osce', '项目成绩分析', 'osce.admin.SubjectStatisticsController.SubjectGradeList', '311', 'J_menuItem', '0', '', '2016-03-14 14:56:32', '2016-03-14 14:56:32');
INSERT INTO `sys_menus` VALUES ('23', 'osce', '考生成绩分析', 'osce.admin.TestScoresController.TestScoreList', '311', 'J_menuItem', '0', '', '2016-03-14 14:56:32', '2016-03-14 14:56:32');
INSERT INTO `sys_menus` VALUES ('24', 'osce', '教学成绩分析', 'osce.admin.TestScoresController.testScoresCount', '311', 'J_menuItem', '0', '', '2016-03-14 14:56:32', '2016-03-14 14:56:32');
INSERT INTO `sys_menus` VALUES ('25', 'osce', '考生总成绩统计', 'osce.admin.course.getStudentAllScore', '311', 'J_menuItem', '6', '', null, null);
INSERT INTO `sys_menus` VALUES ('28', 'osce', '试卷管理', 'osce.admin.ExamLabelController.getExamLabel', '111', 'J_menuItem', '5', '', '2016-03-07 15:00:21', '2016-03-07 15:00:21');
INSERT INTO `sys_menus` VALUES ('29', 'osce', '考试监控', 'osce.admin.ExamControlController.getExamlist', '211', 'J_menuItem', '6', '', '2016-04-21 09:45:53', '2016-04-21 09:45:53');
INSERT INTO `sys_menus` VALUES ('30', 'osce', '理论考试', '', '10', 'fa-file-text-o', '5', '', '2017-08-01 13:39:53', null);
INSERT INTO `sys_menus` VALUES ('31', 'osce', '考试管理', 'osce.theory.index', '2', 'J_menuItem', '1', '', '2017-08-01 13:39:56', null);
INSERT INTO `sys_menus` VALUES ('32', 'osce', '试卷管理', 'osce.theory.examquestion', '1', 'J_menuItem', '3', '', '2017-08-01 13:39:57', null);
INSERT INTO `sys_menus` VALUES ('33', 'osce', '成绩管理', 'osce.theory.examscore', '3', 'J_menuItem', '4', '', '2017-08-01 13:39:59', null);
INSERT INTO `sys_menus` VALUES ('34', 'osce', '在线批卷', 'osce.theory.examcheck', '2', 'J_menuItem', '5', '', '2017-08-01 15:41:25', null);
INSERT INTO `sys_menus` VALUES ('35', 'osce', '题库管理', 'osce.theory.getQuestionList', '1', 'J_menuItem', '2', '', '2018-01-26 11:30:00', null);

-- ----------------------------
-- Table structure for sys_permissions
-- ----------------------------
DROP TABLE IF EXISTS `sys_permissions`;
CREATE TABLE `sys_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'MENU,OPERATION,ELEMENT',
  `itemid` int(11) NOT NULL COMMENT '根据type字段确定表，不同类型表的主键',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限表';

-- ----------------------------
-- Records of sys_permissions
-- ----------------------------
INSERT INTO `sys_permissions` VALUES ('1', 'osce', 'MENU', '1', '资源管理', '', '2016-01-23 15:40:42', '2016-01-23 15:40:42');
INSERT INTO `sys_permissions` VALUES ('2', 'osce', 'MENU', '2', '考试管理', '', '2016-01-23 15:40:42', '2016-01-23 15:40:42');
INSERT INTO `sys_permissions` VALUES ('3', 'osce', 'MENU', '3', '统计分析', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('4', 'osce', 'MENU', '4', '系统管理', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('5', 'osce', 'MENU', '5', '科目管理', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('6', 'osce', 'MENU', '6', '设备管理', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('7', 'osce', 'MENU', '7', '场所管理', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('8', 'osce', 'MENU', '8', '考站管理', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('9', 'osce', 'MENU', '9', '人员管理', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('12', 'osce', 'MENU', '12', '考试安排', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('13', 'osce', 'MENU', '13', '考生查询', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('14', 'osce', 'MENU', '14', '成绩查询', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('15', 'osce', 'MENU', '15', '资讯&amp;通知', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('16', 'osce', 'MENU', '16', '考前培训', '', '2016-01-23 15:40:43', '2016-01-23 15:40:43');
INSERT INTO `sys_permissions` VALUES ('17', 'osce', 'MENU', '17', '用户管理', '', '2016-01-23 15:40:44', '2016-01-23 15:40:44');
INSERT INTO `sys_permissions` VALUES ('18', 'osce', 'MENU', '18', '权限管理', '', '2016-01-23 15:40:44', '2016-01-23 15:40:44');
INSERT INTO `sys_permissions` VALUES ('19', 'osce', 'MENU', '19', '系统设置', '', '2016-01-23 15:40:44', '2016-01-23 15:40:44');
INSERT INTO `sys_permissions` VALUES ('20', 'osce', 'MENU', '20', '项目成绩统计', null, '2016-01-31 13:07:41', '2016-01-31 13:07:44');
INSERT INTO `sys_permissions` VALUES ('21', 'osce', 'MENU', '21', '考生成绩统计', null, '2016-01-31 13:07:47', '2016-01-31 13:07:50');
INSERT INTO `sys_permissions` VALUES ('22', 'osce', 'MENU', '22', '项目成绩分析', '', '2016-03-14 14:56:32', '2016-03-14 14:56:32');
INSERT INTO `sys_permissions` VALUES ('23', 'osce', 'MENU', '23', '考生成绩分析', '', '2016-03-14 14:56:32', '2016-03-14 14:56:32');
INSERT INTO `sys_permissions` VALUES ('24', 'osce', 'MENU', '24', '教学成绩分析', '', '2016-03-14 14:56:32', '2016-03-14 14:56:32');
INSERT INTO `sys_permissions` VALUES ('25', 'osce', 'MENU', '25', '考生总成绩统计', null, null, null);
INSERT INTO `sys_permissions` VALUES ('28', 'osce', 'MENU', '28', '试卷管理', null, '2017-08-01 13:39:35', null);
INSERT INTO `sys_permissions` VALUES ('29', 'osce', 'MENU', '29', '考试监控', null, '2017-08-01 13:39:37', null);
INSERT INTO `sys_permissions` VALUES ('30', 'osce', 'MENU', '30', '理论管理', null, '2017-08-01 13:39:39', null);
INSERT INTO `sys_permissions` VALUES ('31', 'osce', 'MENU', '31', '考试管理', null, '2017-08-01 13:39:42', null);
INSERT INTO `sys_permissions` VALUES ('32', 'osce', 'MENU', '32', '试卷管理', null, '2017-08-01 15:40:45', null);
INSERT INTO `sys_permissions` VALUES ('33', 'osce', 'MENU', '33', '成绩管理', null, null, null);
INSERT INTO `sys_permissions` VALUES ('34', 'osce', 'MENU', '34', '在线批卷', null, null, null);
INSERT INTO `sys_permissions` VALUES ('35', 'osce', 'MENU', '35', '题库管理', null, null, null);

-- ----------------------------
-- Table structure for sys_permission_element
-- ----------------------------
DROP TABLE IF EXISTS `sys_permission_element`;
CREATE TABLE `sys_permission_element` (
  `element_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`element_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限-页面权限点关联表';

-- ----------------------------
-- Records of sys_permission_element
-- ----------------------------

-- ----------------------------
-- Table structure for sys_permission_function
-- ----------------------------
DROP TABLE IF EXISTS `sys_permission_function`;
CREATE TABLE `sys_permission_function` (
  `function_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`function_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限-功能关联表';

-- ----------------------------
-- Records of sys_permission_function
-- ----------------------------

-- ----------------------------
-- Table structure for sys_permission_menu
-- ----------------------------
DROP TABLE IF EXISTS `sys_permission_menu`;
CREATE TABLE `sys_permission_menu` (
  `permission_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='权限菜单关联表';

-- ----------------------------
-- Records of sys_permission_menu
-- ----------------------------
INSERT INTO `sys_permission_menu` VALUES ('1', '1', '2016-01-23 15:40:42', '2016-01-23 15:40:42', '1');
INSERT INTO `sys_permission_menu` VALUES ('2', '2', '2016-01-23 15:40:42', '2016-01-23 15:40:42', '2');
INSERT INTO `sys_permission_menu` VALUES ('3', '3', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '3');
INSERT INTO `sys_permission_menu` VALUES ('4', '4', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '4');
INSERT INTO `sys_permission_menu` VALUES ('5', '5', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '5');
INSERT INTO `sys_permission_menu` VALUES ('6', '6', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '6');
INSERT INTO `sys_permission_menu` VALUES ('7', '7', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '7');
INSERT INTO `sys_permission_menu` VALUES ('8', '8', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '8');
INSERT INTO `sys_permission_menu` VALUES ('9', '9', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '9');
INSERT INTO `sys_permission_menu` VALUES ('10', '10', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '10');
INSERT INTO `sys_permission_menu` VALUES ('11', '11', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '11');
INSERT INTO `sys_permission_menu` VALUES ('12', '12', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '12');
INSERT INTO `sys_permission_menu` VALUES ('13', '13', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '13');
INSERT INTO `sys_permission_menu` VALUES ('14', '14', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '14');
INSERT INTO `sys_permission_menu` VALUES ('15', '15', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '15');
INSERT INTO `sys_permission_menu` VALUES ('16', '16', '2016-01-23 15:40:43', '2016-01-23 15:40:43', '16');
INSERT INTO `sys_permission_menu` VALUES ('17', '17', '2016-01-23 15:40:44', '2016-01-23 15:40:44', '17');
INSERT INTO `sys_permission_menu` VALUES ('18', '18', '2016-01-23 15:40:44', '2016-01-23 15:40:44', '18');
INSERT INTO `sys_permission_menu` VALUES ('19', '19', '2016-01-23 15:40:44', '2016-01-23 15:40:44', '19');
INSERT INTO `sys_permission_menu` VALUES ('20', '20', '2016-01-31 12:57:12', '2016-01-31 12:57:14', '20');
INSERT INTO `sys_permission_menu` VALUES ('21', '21', '2016-01-31 12:57:22', '2016-01-31 12:57:24', '21');
INSERT INTO `sys_permission_menu` VALUES ('22', '22', '2016-04-15 07:17:19', '2016-04-15 11:00:37', '22');
INSERT INTO `sys_permission_menu` VALUES ('23', '23', '2016-03-14 14:56:32', '2016-03-14 14:56:32', '23');
INSERT INTO `sys_permission_menu` VALUES ('24', '24', '2016-03-14 14:56:32', '2016-03-14 14:56:32', '24');
INSERT INTO `sys_permission_menu` VALUES ('26', '28', '2016-03-07 15:00:21', '2016-03-07 15:00:21', '25');
INSERT INTO `sys_permission_menu` VALUES ('27', '29', '2016-04-21 09:47:23', '2016-04-21 09:47:23', '26');
INSERT INTO `sys_permission_menu` VALUES ('28', '30', '2017-08-01 13:39:10', '2017-08-01 13:39:12', '27');
INSERT INTO `sys_permission_menu` VALUES ('29', '31', '2017-08-01 13:39:14', '2017-08-01 13:39:17', '28');
INSERT INTO `sys_permission_menu` VALUES ('30', '32', '2017-08-01 13:39:19', '2017-08-01 13:39:21', '29');
INSERT INTO `sys_permission_menu` VALUES ('31', '33', '2017-08-01 13:39:23', '2017-08-01 13:39:25', '30');
INSERT INTO `sys_permission_menu` VALUES ('32', '34', '2017-08-01 15:42:01', '2017-08-01 15:42:03', '31');
INSERT INTO `sys_permission_menu` VALUES ('33', '25', '2017-09-13 13:22:32', '2017-09-13 13:22:38', '32');
INSERT INTO `sys_permission_menu` VALUES ('35', '35', '2018-01-29 11:36:06', '2018-01-29 11:36:09', '33');

-- ----------------------------
-- Table structure for sys_roles
-- ----------------------------
DROP TABLE IF EXISTS `sys_roles`;
CREATE TABLE `sys_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_roles
-- ----------------------------
INSERT INTO `sys_roles` VALUES ('1', '监考老师', '监考老师', '监考老师', '0000-00-00 00:00:00', '2017-03-09 14:30:07');
INSERT INTO `sys_roles` VALUES ('2', '考生', '考生', null, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `sys_roles` VALUES ('3', '系统管理员', 'osce', null, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `sys_roles` VALUES ('4', 'sp病人', 'sp病人', null, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `sys_roles` VALUES ('5', '超级管理员', '超级', '独立于角色存在的任务，且不能修改权限', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `sys_roles` VALUES ('6', '巡考老师', '900044', '巡考老师', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for sys_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `sys_role_permission`;
CREATE TABLE `sys_role_permission` (
  `permission_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_roles_id_foreign` (`role_id`) USING BTREE,
  CONSTRAINT `sys_role_permission_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `sys_permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sys_role_permission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `sys_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_role_permission
-- ----------------------------
INSERT INTO `sys_role_permission` VALUES ('1', '3', '2017-08-01 15:43:42', '2017-08-01 15:43:42');
INSERT INTO `sys_role_permission` VALUES ('1', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('1', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('2', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('2', '2', '2018-02-02 15:16:23', '2018-02-02 15:16:23');
INSERT INTO `sys_role_permission` VALUES ('2', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('2', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('2', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('3', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('3', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('3', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('3', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('4', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('4', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('4', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('5', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('5', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('5', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('6', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('6', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('6', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('7', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('7', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('7', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('8', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('8', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('8', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('9', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('9', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('9', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('12', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('12', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('12', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('12', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('13', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('13', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('13', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('13', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('14', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('14', '2', '2018-02-02 15:16:23', '2018-02-02 15:16:23');
INSERT INTO `sys_role_permission` VALUES ('14', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('14', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('14', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('15', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('15', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('15', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('15', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('16', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('16', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('16', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('16', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('17', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('17', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('17', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('18', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('18', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('18', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('19', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('19', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('19', '5', '2016-01-23 16:19:01', '2016-01-23 16:19:01');
INSERT INTO `sys_role_permission` VALUES ('20', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('20', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('20', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('21', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('21', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('21', '4', '2016-02-03 15:12:41', '2016-02-03 15:12:41');
INSERT INTO `sys_role_permission` VALUES ('22', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('23', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('24', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('28', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');
INSERT INTO `sys_role_permission` VALUES ('28', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('29', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('30', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('31', '3', '2017-08-01 15:43:43', '2017-08-01 15:43:43');
INSERT INTO `sys_role_permission` VALUES ('32', '1', '2017-08-01 15:42:49', '2017-08-01 15:42:49');

-- ----------------------------
-- Table structure for sys_user_group
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_group`;
CREATE TABLE `sys_user_group` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of sys_user_group
-- ----------------------------

-- ----------------------------
-- Table structure for sys_user_role
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_role`;
CREATE TABLE `sys_user_role` (
  `role_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`role_id`,`user_id`),
  KEY `role_user_role_id_index` (`role_id`) USING BTREE,
  KEY `role_user_user_id_index` (`user_id`) USING BTREE,
  CONSTRAINT `sys_user_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `sys_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sys_user_role_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_user_role
-- ----------------------------
INSERT INTO `sys_user_role` VALUES ('1', '4', '2017-05-20 19:27:26', '2017-05-20 19:27:26');
INSERT INTO `sys_user_role` VALUES ('1', '147', '2017-05-26 11:07:35', '2017-05-26 11:07:35');
INSERT INTO `sys_user_role` VALUES ('1', '148', '2017-05-26 11:07:36', '2017-05-26 11:07:36');
INSERT INTO `sys_user_role` VALUES ('1', '149', '2017-05-26 11:07:36', '2017-05-26 11:07:36');
INSERT INTO `sys_user_role` VALUES ('1', '150', '2017-05-26 11:07:36', '2017-05-26 11:07:36');
INSERT INTO `sys_user_role` VALUES ('1', '151', '2017-05-26 11:07:37', '2017-05-26 11:07:37');
INSERT INTO `sys_user_role` VALUES ('1', '152', '2017-05-26 11:07:37', '2017-05-26 11:07:37');
INSERT INTO `sys_user_role` VALUES ('1', '153', '2017-05-26 11:07:37', '2017-05-26 11:07:37');
INSERT INTO `sys_user_role` VALUES ('1', '154', '2017-05-26 11:07:38', '2017-05-26 11:07:38');
INSERT INTO `sys_user_role` VALUES ('1', '155', '2017-05-26 11:07:38', '2017-05-26 11:07:38');
INSERT INTO `sys_user_role` VALUES ('1', '156', '2017-05-26 11:07:40', '2017-05-26 11:07:40');
INSERT INTO `sys_user_role` VALUES ('1', '238', '2017-06-01 11:17:54', '2017-06-01 11:17:54');
INSERT INTO `sys_user_role` VALUES ('1', '239', '2017-06-01 11:22:17', '2017-06-01 11:22:17');
INSERT INTO `sys_user_role` VALUES ('1', '249', '2017-12-05 01:04:14', '2017-12-05 01:04:14');
INSERT INTO `sys_user_role` VALUES ('1', '251', '2017-12-05 01:04:18', '2017-12-05 01:04:18');
INSERT INTO `sys_user_role` VALUES ('2', '6', '2017-05-20 19:31:06', '2017-05-20 19:31:06');
INSERT INTO `sys_user_role` VALUES ('2', '7', '2017-05-20 19:31:06', '2017-05-20 19:31:06');
INSERT INTO `sys_user_role` VALUES ('2', '8', '2017-05-20 19:31:06', '2017-05-20 19:31:06');
INSERT INTO `sys_user_role` VALUES ('2', '9', '2017-05-20 19:31:07', '2017-05-20 19:31:07');
INSERT INTO `sys_user_role` VALUES ('2', '10', '2017-05-20 19:31:07', '2017-05-20 19:31:07');
INSERT INTO `sys_user_role` VALUES ('2', '11', '2017-05-20 19:31:08', '2017-05-20 19:31:08');
INSERT INTO `sys_user_role` VALUES ('2', '12', '2017-05-20 19:31:08', '2017-05-20 19:31:08');
INSERT INTO `sys_user_role` VALUES ('2', '13', '2017-05-20 19:31:08', '2017-05-20 19:31:08');
INSERT INTO `sys_user_role` VALUES ('2', '14', '2017-05-20 19:31:08', '2017-05-20 19:31:08');
INSERT INTO `sys_user_role` VALUES ('2', '15', '2017-05-20 19:31:09', '2017-05-20 19:31:09');
INSERT INTO `sys_user_role` VALUES ('2', '16', '2017-05-20 19:31:09', '2017-05-20 19:31:09');
INSERT INTO `sys_user_role` VALUES ('2', '17', '2017-05-20 19:31:09', '2017-05-20 19:31:09');
INSERT INTO `sys_user_role` VALUES ('2', '18', '2017-05-20 19:31:10', '2017-05-20 19:31:10');
INSERT INTO `sys_user_role` VALUES ('2', '19', '2017-05-20 19:31:10', '2017-05-20 19:31:10');
INSERT INTO `sys_user_role` VALUES ('2', '20', '2017-05-20 19:31:10', '2017-05-20 19:31:10');
INSERT INTO `sys_user_role` VALUES ('2', '21', '2017-05-20 19:31:11', '2017-05-20 19:31:11');
INSERT INTO `sys_user_role` VALUES ('2', '22', '2017-05-20 19:31:11', '2017-05-20 19:31:11');
INSERT INTO `sys_user_role` VALUES ('2', '23', '2017-05-20 19:31:11', '2017-05-20 19:31:11');
INSERT INTO `sys_user_role` VALUES ('2', '24', '2017-05-20 19:31:12', '2017-05-20 19:31:12');
INSERT INTO `sys_user_role` VALUES ('2', '25', '2017-05-20 19:31:12', '2017-05-20 19:31:12');
INSERT INTO `sys_user_role` VALUES ('2', '26', '2017-05-20 19:31:13', '2017-05-20 19:31:13');
INSERT INTO `sys_user_role` VALUES ('2', '27', '2017-05-20 19:31:13', '2017-05-20 19:31:13');
INSERT INTO `sys_user_role` VALUES ('2', '28', '2017-05-20 19:31:13', '2017-05-20 19:31:13');
INSERT INTO `sys_user_role` VALUES ('2', '29', '2017-05-20 19:31:14', '2017-05-20 19:31:14');
INSERT INTO `sys_user_role` VALUES ('2', '30', '2017-05-20 19:31:14', '2017-05-20 19:31:14');
INSERT INTO `sys_user_role` VALUES ('2', '31', '2017-05-20 19:31:14', '2017-05-20 19:31:14');
INSERT INTO `sys_user_role` VALUES ('2', '32', '2017-05-20 19:31:14', '2017-05-20 19:31:14');
INSERT INTO `sys_user_role` VALUES ('2', '33', '2017-05-20 19:31:15', '2017-05-20 19:31:15');
INSERT INTO `sys_user_role` VALUES ('2', '34', '2017-05-20 19:31:15', '2017-05-20 19:31:15');
INSERT INTO `sys_user_role` VALUES ('2', '35', '2017-05-20 19:31:15', '2017-05-20 19:31:15');
INSERT INTO `sys_user_role` VALUES ('2', '36', '2017-05-20 19:31:16', '2017-05-20 19:31:16');
INSERT INTO `sys_user_role` VALUES ('2', '37', '2017-05-20 19:31:16', '2017-05-20 19:31:16');
INSERT INTO `sys_user_role` VALUES ('2', '38', '2017-05-20 19:31:16', '2017-05-20 19:31:16');
INSERT INTO `sys_user_role` VALUES ('2', '39', '2017-05-20 19:31:17', '2017-05-20 19:31:17');
INSERT INTO `sys_user_role` VALUES ('2', '40', '2017-05-20 19:31:17', '2017-05-20 19:31:17');
INSERT INTO `sys_user_role` VALUES ('2', '41', '2017-05-20 19:31:17', '2017-05-20 19:31:17');
INSERT INTO `sys_user_role` VALUES ('2', '42', '2017-05-20 19:31:17', '2017-05-20 19:31:17');
INSERT INTO `sys_user_role` VALUES ('2', '43', '2017-05-20 19:31:18', '2017-05-20 19:31:18');
INSERT INTO `sys_user_role` VALUES ('2', '44', '2017-05-20 19:31:18', '2017-05-20 19:31:18');
INSERT INTO `sys_user_role` VALUES ('2', '45', '2017-05-20 19:31:18', '2017-05-20 19:31:18');
INSERT INTO `sys_user_role` VALUES ('2', '46', '2017-05-20 19:31:19', '2017-05-20 19:31:19');
INSERT INTO `sys_user_role` VALUES ('2', '47', '2017-05-20 19:31:19', '2017-05-20 19:31:19');
INSERT INTO `sys_user_role` VALUES ('2', '48', '2017-05-20 19:31:19', '2017-05-20 19:31:19');
INSERT INTO `sys_user_role` VALUES ('2', '49', '2017-05-20 19:31:20', '2017-05-20 19:31:20');
INSERT INTO `sys_user_role` VALUES ('2', '50', '2017-05-20 19:31:20', '2017-05-20 19:31:20');
INSERT INTO `sys_user_role` VALUES ('2', '51', '2017-05-20 19:31:20', '2017-05-20 19:31:20');
INSERT INTO `sys_user_role` VALUES ('2', '52', '2017-05-20 19:31:21', '2017-05-20 19:31:21');
INSERT INTO `sys_user_role` VALUES ('2', '53', '2017-05-20 19:31:21', '2017-05-20 19:31:21');
INSERT INTO `sys_user_role` VALUES ('2', '54', '2017-05-20 19:31:21', '2017-05-20 19:31:21');
INSERT INTO `sys_user_role` VALUES ('2', '55', '2017-05-20 19:31:21', '2017-05-20 19:31:21');
INSERT INTO `sys_user_role` VALUES ('2', '56', '2017-05-20 19:31:22', '2017-05-20 19:31:22');
INSERT INTO `sys_user_role` VALUES ('2', '57', '2017-05-20 19:31:22', '2017-05-20 19:31:22');
INSERT INTO `sys_user_role` VALUES ('2', '58', '2017-05-20 19:31:22', '2017-05-20 19:31:22');
INSERT INTO `sys_user_role` VALUES ('2', '59', '2017-05-20 19:31:23', '2017-05-20 19:31:23');
INSERT INTO `sys_user_role` VALUES ('2', '60', '2017-05-20 19:31:23', '2017-05-20 19:31:23');
INSERT INTO `sys_user_role` VALUES ('2', '61', '2017-05-20 19:31:23', '2017-05-20 19:31:23');
INSERT INTO `sys_user_role` VALUES ('2', '62', '2017-05-20 19:31:24', '2017-05-20 19:31:24');
INSERT INTO `sys_user_role` VALUES ('2', '63', '2017-05-20 19:31:24', '2017-05-20 19:31:24');
INSERT INTO `sys_user_role` VALUES ('2', '64', '2017-05-20 19:31:24', '2017-05-20 19:31:24');
INSERT INTO `sys_user_role` VALUES ('2', '65', '2017-05-20 19:31:24', '2017-05-20 19:31:24');
INSERT INTO `sys_user_role` VALUES ('2', '66', '2017-05-20 19:31:25', '2017-05-20 19:31:25');
INSERT INTO `sys_user_role` VALUES ('2', '67', '2017-05-20 19:31:25', '2017-05-20 19:31:25');
INSERT INTO `sys_user_role` VALUES ('2', '68', '2017-05-20 19:31:25', '2017-05-20 19:31:25');
INSERT INTO `sys_user_role` VALUES ('2', '69', '2017-05-20 19:31:26', '2017-05-20 19:31:26');
INSERT INTO `sys_user_role` VALUES ('2', '70', '2017-05-20 19:31:26', '2017-05-20 19:31:26');
INSERT INTO `sys_user_role` VALUES ('2', '71', '2017-05-20 19:31:26', '2017-05-20 19:31:26');
INSERT INTO `sys_user_role` VALUES ('2', '72', '2017-05-20 19:31:27', '2017-05-20 19:31:27');
INSERT INTO `sys_user_role` VALUES ('2', '73', '2017-05-20 19:31:27', '2017-05-20 19:31:27');
INSERT INTO `sys_user_role` VALUES ('2', '74', '2017-05-20 19:31:27', '2017-05-20 19:31:27');
INSERT INTO `sys_user_role` VALUES ('2', '75', '2017-05-20 19:31:27', '2017-05-20 19:31:27');
INSERT INTO `sys_user_role` VALUES ('2', '76', '2017-05-20 19:31:28', '2017-05-20 19:31:28');
INSERT INTO `sys_user_role` VALUES ('2', '77', '2017-05-20 19:31:28', '2017-05-20 19:31:28');
INSERT INTO `sys_user_role` VALUES ('2', '78', '2017-05-20 19:31:28', '2017-05-20 19:31:28');
INSERT INTO `sys_user_role` VALUES ('2', '79', '2017-05-20 19:31:29', '2017-05-20 19:31:29');
INSERT INTO `sys_user_role` VALUES ('2', '80', '2017-05-20 19:31:29', '2017-05-20 19:31:29');
INSERT INTO `sys_user_role` VALUES ('2', '81', '2017-05-20 19:31:29', '2017-05-20 19:31:29');
INSERT INTO `sys_user_role` VALUES ('2', '82', '2017-05-20 19:31:29', '2017-05-20 19:31:29');
INSERT INTO `sys_user_role` VALUES ('2', '83', '2017-05-20 19:31:30', '2017-05-20 19:31:30');
INSERT INTO `sys_user_role` VALUES ('2', '84', '2017-05-20 19:31:30', '2017-05-20 19:31:30');
INSERT INTO `sys_user_role` VALUES ('2', '85', '2017-05-20 19:31:30', '2017-05-20 19:31:30');
INSERT INTO `sys_user_role` VALUES ('2', '86', '2017-05-20 19:31:31', '2017-05-20 19:31:31');
INSERT INTO `sys_user_role` VALUES ('2', '87', '2017-05-20 19:31:31', '2017-05-20 19:31:31');
INSERT INTO `sys_user_role` VALUES ('2', '88', '2017-05-20 19:31:31', '2017-05-20 19:31:31');
INSERT INTO `sys_user_role` VALUES ('2', '89', '2017-05-20 19:31:32', '2017-05-20 19:31:32');
INSERT INTO `sys_user_role` VALUES ('2', '90', '2017-05-20 19:31:32', '2017-05-20 19:31:32');
INSERT INTO `sys_user_role` VALUES ('2', '91', '2017-05-20 19:31:32', '2017-05-20 19:31:32');
INSERT INTO `sys_user_role` VALUES ('2', '92', '2017-05-20 19:31:32', '2017-05-20 19:31:32');
INSERT INTO `sys_user_role` VALUES ('2', '93', '2017-05-20 19:31:33', '2017-05-20 19:31:33');
INSERT INTO `sys_user_role` VALUES ('2', '94', '2017-05-20 19:31:33', '2017-05-20 19:31:33');
INSERT INTO `sys_user_role` VALUES ('2', '95', '2017-05-20 19:31:33', '2017-05-20 19:31:33');
INSERT INTO `sys_user_role` VALUES ('2', '96', '2017-05-20 19:31:34', '2017-05-20 19:31:34');
INSERT INTO `sys_user_role` VALUES ('2', '97', '2017-05-20 19:31:34', '2017-05-20 19:31:34');
INSERT INTO `sys_user_role` VALUES ('2', '98', '2017-05-20 19:31:34', '2017-05-20 19:31:34');
INSERT INTO `sys_user_role` VALUES ('2', '99', '2017-05-20 19:31:34', '2017-05-20 19:31:34');
INSERT INTO `sys_user_role` VALUES ('2', '100', '2017-05-20 19:31:35', '2017-05-20 19:31:35');
INSERT INTO `sys_user_role` VALUES ('2', '101', '2017-05-20 19:31:35', '2017-05-20 19:31:35');
INSERT INTO `sys_user_role` VALUES ('2', '102', '2017-05-20 19:31:35', '2017-05-20 19:31:35');
INSERT INTO `sys_user_role` VALUES ('2', '103', '2017-05-20 19:31:36', '2017-05-20 19:31:36');
INSERT INTO `sys_user_role` VALUES ('2', '104', '2017-05-20 19:31:36', '2017-05-20 19:31:36');
INSERT INTO `sys_user_role` VALUES ('2', '105', '2017-05-20 19:31:36', '2017-05-20 19:31:36');
INSERT INTO `sys_user_role` VALUES ('2', '106', '2017-05-20 19:31:37', '2017-05-20 19:31:37');
INSERT INTO `sys_user_role` VALUES ('2', '107', '2017-05-20 19:31:37', '2017-05-20 19:31:37');
INSERT INTO `sys_user_role` VALUES ('2', '108', '2017-05-20 19:31:37', '2017-05-20 19:31:37');
INSERT INTO `sys_user_role` VALUES ('2', '109', '2017-05-20 19:31:37', '2017-05-20 19:31:37');
INSERT INTO `sys_user_role` VALUES ('2', '110', '2017-05-20 19:31:38', '2017-05-20 19:31:38');
INSERT INTO `sys_user_role` VALUES ('2', '111', '2017-05-20 19:31:38', '2017-05-20 19:31:38');
INSERT INTO `sys_user_role` VALUES ('2', '112', '2017-05-20 19:31:38', '2017-05-20 19:31:38');
INSERT INTO `sys_user_role` VALUES ('2', '113', '2017-05-20 19:31:39', '2017-05-20 19:31:39');
INSERT INTO `sys_user_role` VALUES ('2', '114', '2017-05-20 19:31:39', '2017-05-20 19:31:39');
INSERT INTO `sys_user_role` VALUES ('2', '115', '2017-05-20 19:31:39', '2017-05-20 19:31:39');
INSERT INTO `sys_user_role` VALUES ('2', '116', '2017-05-20 19:31:40', '2017-05-20 19:31:40');
INSERT INTO `sys_user_role` VALUES ('2', '117', '2017-05-20 19:31:40', '2017-05-20 19:31:40');
INSERT INTO `sys_user_role` VALUES ('2', '118', '2017-05-20 19:31:40', '2017-05-20 19:31:40');
INSERT INTO `sys_user_role` VALUES ('2', '119', '2017-05-20 19:31:40', '2017-05-20 19:31:40');
INSERT INTO `sys_user_role` VALUES ('2', '120', '2017-05-20 19:31:41', '2017-05-20 19:31:41');
INSERT INTO `sys_user_role` VALUES ('2', '121', '2017-05-20 19:31:41', '2017-05-20 19:31:41');
INSERT INTO `sys_user_role` VALUES ('2', '122', '2017-05-20 19:31:41', '2017-05-20 19:31:41');
INSERT INTO `sys_user_role` VALUES ('2', '123', '2017-05-20 19:31:42', '2017-05-20 19:31:42');
INSERT INTO `sys_user_role` VALUES ('2', '124', '2017-05-20 19:31:42', '2017-05-20 19:31:42');
INSERT INTO `sys_user_role` VALUES ('2', '125', '2017-05-20 19:31:42', '2017-05-20 19:31:42');
INSERT INTO `sys_user_role` VALUES ('2', '126', '2017-05-20 19:31:43', '2017-05-20 19:31:43');
INSERT INTO `sys_user_role` VALUES ('2', '127', '2017-05-20 19:31:43', '2017-05-20 19:31:43');
INSERT INTO `sys_user_role` VALUES ('2', '128', '2017-05-20 19:31:43', '2017-05-20 19:31:43');
INSERT INTO `sys_user_role` VALUES ('2', '129', '2017-05-20 19:31:43', '2017-05-20 19:31:43');
INSERT INTO `sys_user_role` VALUES ('2', '130', '2017-05-20 19:31:44', '2017-05-20 19:31:44');
INSERT INTO `sys_user_role` VALUES ('2', '131', '2017-05-20 19:31:44', '2017-05-20 19:31:44');
INSERT INTO `sys_user_role` VALUES ('2', '132', '2017-05-20 19:31:44', '2017-05-20 19:31:44');
INSERT INTO `sys_user_role` VALUES ('2', '133', '2017-05-20 19:31:45', '2017-05-20 19:31:45');
INSERT INTO `sys_user_role` VALUES ('2', '134', '2017-05-20 19:31:45', '2017-05-20 19:31:45');
INSERT INTO `sys_user_role` VALUES ('2', '135', '2017-05-20 19:31:45', '2017-05-20 19:31:45');
INSERT INTO `sys_user_role` VALUES ('2', '136', '2017-05-20 19:31:46', '2017-05-20 19:31:46');
INSERT INTO `sys_user_role` VALUES ('2', '137', '2017-05-20 19:31:46', '2017-05-20 19:31:46');
INSERT INTO `sys_user_role` VALUES ('2', '138', '2017-05-20 19:31:46', '2017-05-20 19:31:46');
INSERT INTO `sys_user_role` VALUES ('2', '139', '2017-05-20 19:31:46', '2017-05-20 19:31:46');
INSERT INTO `sys_user_role` VALUES ('2', '140', '2017-05-20 19:31:47', '2017-05-20 19:31:47');
INSERT INTO `sys_user_role` VALUES ('2', '141', '2017-05-20 19:31:47', '2017-05-20 19:31:47');
INSERT INTO `sys_user_role` VALUES ('2', '142', '2017-05-20 19:31:47', '2017-05-20 19:31:47');
INSERT INTO `sys_user_role` VALUES ('2', '143', '2017-05-20 19:31:48', '2017-05-20 19:31:48');
INSERT INTO `sys_user_role` VALUES ('2', '144', '2017-05-20 19:31:48', '2017-05-20 19:31:48');
INSERT INTO `sys_user_role` VALUES ('2', '145', '2017-05-20 19:31:48', '2017-05-20 19:31:48');
INSERT INTO `sys_user_role` VALUES ('2', '146', '2017-05-20 19:31:49', '2017-05-20 19:31:49');
INSERT INTO `sys_user_role` VALUES ('2', '157', '2017-05-26 11:56:11', '2017-05-26 11:56:11');
INSERT INTO `sys_user_role` VALUES ('2', '158', '2017-05-26 11:56:11', '2017-05-26 11:56:11');
INSERT INTO `sys_user_role` VALUES ('2', '159', '2017-05-26 11:56:12', '2017-05-26 11:56:12');
INSERT INTO `sys_user_role` VALUES ('2', '160', '2017-05-26 11:56:12', '2017-05-26 11:56:12');
INSERT INTO `sys_user_role` VALUES ('2', '161', '2017-05-26 11:56:12', '2017-05-26 11:56:12');
INSERT INTO `sys_user_role` VALUES ('2', '162', '2017-05-26 11:56:13', '2017-05-26 11:56:13');
INSERT INTO `sys_user_role` VALUES ('2', '163', '2017-05-26 11:56:13', '2017-05-26 11:56:13');
INSERT INTO `sys_user_role` VALUES ('2', '164', '2017-05-26 14:01:07', '2017-05-26 14:01:07');
INSERT INTO `sys_user_role` VALUES ('2', '165', '2017-05-26 14:01:07', '2017-05-26 14:01:07');
INSERT INTO `sys_user_role` VALUES ('2', '166', '2017-05-26 14:01:07', '2017-05-26 14:01:07');
INSERT INTO `sys_user_role` VALUES ('2', '167', '2017-05-26 14:01:08', '2017-05-26 14:01:08');
INSERT INTO `sys_user_role` VALUES ('2', '168', '2017-05-26 14:01:08', '2017-05-26 14:01:08');
INSERT INTO `sys_user_role` VALUES ('2', '169', '2017-05-26 14:01:08', '2017-05-26 14:01:08');
INSERT INTO `sys_user_role` VALUES ('2', '170', '2017-05-26 14:01:09', '2017-05-26 14:01:09');
INSERT INTO `sys_user_role` VALUES ('2', '171', '2017-05-26 14:01:09', '2017-05-26 14:01:09');
INSERT INTO `sys_user_role` VALUES ('2', '172', '2017-05-26 14:01:09', '2017-05-26 14:01:09');
INSERT INTO `sys_user_role` VALUES ('2', '173', '2017-05-26 14:01:10', '2017-05-26 14:01:10');
INSERT INTO `sys_user_role` VALUES ('2', '174', '2017-05-26 14:01:10', '2017-05-26 14:01:10');
INSERT INTO `sys_user_role` VALUES ('2', '175', '2017-05-26 14:02:11', '2017-05-26 14:02:11');
INSERT INTO `sys_user_role` VALUES ('2', '176', '2017-05-26 14:02:11', '2017-05-26 14:02:11');
INSERT INTO `sys_user_role` VALUES ('2', '177', '2017-05-26 14:02:12', '2017-05-26 14:02:12');
INSERT INTO `sys_user_role` VALUES ('2', '178', '2017-05-26 14:02:12', '2017-05-26 14:02:12');
INSERT INTO `sys_user_role` VALUES ('2', '179', '2017-05-26 14:02:12', '2017-05-26 14:02:12');
INSERT INTO `sys_user_role` VALUES ('2', '180', '2017-05-26 14:02:13', '2017-05-26 14:02:13');
INSERT INTO `sys_user_role` VALUES ('2', '181', '2017-05-26 14:02:13', '2017-05-26 14:02:13');
INSERT INTO `sys_user_role` VALUES ('2', '182', '2017-05-26 14:02:13', '2017-05-26 14:02:13');
INSERT INTO `sys_user_role` VALUES ('2', '183', '2017-05-26 14:02:14', '2017-05-26 14:02:14');
INSERT INTO `sys_user_role` VALUES ('2', '184', '2017-05-26 14:02:14', '2017-05-26 14:02:14');
INSERT INTO `sys_user_role` VALUES ('2', '185', '2017-05-26 14:02:14', '2017-05-26 14:02:14');
INSERT INTO `sys_user_role` VALUES ('2', '186', '2017-05-26 14:02:15', '2017-05-26 14:02:15');
INSERT INTO `sys_user_role` VALUES ('2', '187', '2017-05-26 14:02:15', '2017-05-26 14:02:15');
INSERT INTO `sys_user_role` VALUES ('2', '188', '2017-05-26 14:02:15', '2017-05-26 14:02:15');
INSERT INTO `sys_user_role` VALUES ('2', '189', '2017-05-26 14:02:16', '2017-05-26 14:02:16');
INSERT INTO `sys_user_role` VALUES ('2', '190', '2017-05-26 14:02:16', '2017-05-26 14:02:16');
INSERT INTO `sys_user_role` VALUES ('2', '191', '2017-05-26 14:02:16', '2017-05-26 14:02:16');
INSERT INTO `sys_user_role` VALUES ('2', '192', '2017-05-26 14:02:17', '2017-05-26 14:02:17');
INSERT INTO `sys_user_role` VALUES ('2', '193', '2017-05-26 14:02:17', '2017-05-26 14:02:17');
INSERT INTO `sys_user_role` VALUES ('2', '194', '2017-05-26 14:02:17', '2017-05-26 14:02:17');
INSERT INTO `sys_user_role` VALUES ('2', '195', '2017-05-26 14:02:18', '2017-05-26 14:02:18');
INSERT INTO `sys_user_role` VALUES ('2', '196', '2017-05-26 14:02:18', '2017-05-26 14:02:18');
INSERT INTO `sys_user_role` VALUES ('2', '197', '2017-05-26 14:02:18', '2017-05-26 14:02:18');
INSERT INTO `sys_user_role` VALUES ('2', '198', '2017-05-26 14:02:18', '2017-05-26 14:02:18');
INSERT INTO `sys_user_role` VALUES ('2', '199', '2017-05-26 14:02:19', '2017-05-26 14:02:19');
INSERT INTO `sys_user_role` VALUES ('2', '200', '2017-05-26 14:02:19', '2017-05-26 14:02:19');
INSERT INTO `sys_user_role` VALUES ('2', '201', '2017-05-26 14:02:19', '2017-05-26 14:02:19');
INSERT INTO `sys_user_role` VALUES ('2', '202', '2017-05-26 14:02:20', '2017-05-26 14:02:20');
INSERT INTO `sys_user_role` VALUES ('2', '203', '2017-05-26 14:02:20', '2017-05-26 14:02:20');
INSERT INTO `sys_user_role` VALUES ('2', '204', '2017-05-26 14:02:20', '2017-05-26 14:02:20');
INSERT INTO `sys_user_role` VALUES ('2', '205', '2017-05-26 14:02:20', '2017-05-26 14:02:20');
INSERT INTO `sys_user_role` VALUES ('2', '206', '2017-05-26 14:02:21', '2017-05-26 14:02:21');
INSERT INTO `sys_user_role` VALUES ('2', '207', '2017-05-26 14:02:21', '2017-05-26 14:02:21');
INSERT INTO `sys_user_role` VALUES ('2', '208', '2017-05-26 14:02:21', '2017-05-26 14:02:21');
INSERT INTO `sys_user_role` VALUES ('2', '209', '2017-05-26 14:02:22', '2017-05-26 14:02:22');
INSERT INTO `sys_user_role` VALUES ('2', '210', '2017-05-26 14:02:22', '2017-05-26 14:02:22');
INSERT INTO `sys_user_role` VALUES ('2', '211', '2017-05-26 14:02:22', '2017-05-26 14:02:22');
INSERT INTO `sys_user_role` VALUES ('2', '212', '2017-05-26 14:02:23', '2017-05-26 14:02:23');
INSERT INTO `sys_user_role` VALUES ('2', '213', '2017-05-26 14:02:24', '2017-05-26 14:02:24');
INSERT INTO `sys_user_role` VALUES ('2', '214', '2017-05-26 14:02:24', '2017-05-26 14:02:24');
INSERT INTO `sys_user_role` VALUES ('2', '215', '2017-05-26 14:02:24', '2017-05-26 14:02:24');
INSERT INTO `sys_user_role` VALUES ('2', '216', '2017-05-26 14:02:24', '2017-05-26 14:02:24');
INSERT INTO `sys_user_role` VALUES ('2', '217', '2017-05-26 14:02:25', '2017-05-26 14:02:25');
INSERT INTO `sys_user_role` VALUES ('2', '225', '2017-05-31 10:24:30', '2017-05-31 10:24:30');
INSERT INTO `sys_user_role` VALUES ('2', '226', '2017-05-31 10:24:31', '2017-05-31 10:24:31');
INSERT INTO `sys_user_role` VALUES ('2', '227', '2017-05-31 10:24:32', '2017-05-31 10:24:32');
INSERT INTO `sys_user_role` VALUES ('2', '228', '2017-05-31 10:24:33', '2017-05-31 10:24:33');
INSERT INTO `sys_user_role` VALUES ('2', '230', '2017-05-31 10:25:20', '2017-05-31 10:25:20');
INSERT INTO `sys_user_role` VALUES ('2', '231', '2017-05-31 10:25:21', '2017-05-31 10:25:21');
INSERT INTO `sys_user_role` VALUES ('2', '232', '2017-05-31 10:25:21', '2017-05-31 10:25:21');
INSERT INTO `sys_user_role` VALUES ('2', '233', '2017-05-31 10:25:22', '2017-05-31 10:25:22');
INSERT INTO `sys_user_role` VALUES ('2', '234', '2017-05-31 10:25:25', '2017-05-31 10:25:25');
INSERT INTO `sys_user_role` VALUES ('2', '240', '2017-06-01 13:47:38', '2017-06-01 13:47:38');
INSERT INTO `sys_user_role` VALUES ('2', '241', '2017-06-01 13:47:39', '2017-06-01 13:47:39');
INSERT INTO `sys_user_role` VALUES ('2', '246', '2017-06-01 18:00:41', '2017-06-01 18:00:41');
INSERT INTO `sys_user_role` VALUES ('2', '247', '2017-07-17 15:24:50', '2017-07-17 15:24:50');
INSERT INTO `sys_user_role` VALUES ('2', '248', '2017-07-17 15:24:50', '2017-07-17 15:24:50');
INSERT INTO `sys_user_role` VALUES ('2', '252', '2017-12-28 09:37:19', '2017-12-28 09:37:19');
INSERT INTO `sys_user_role` VALUES ('2', '253', '2018-01-08 14:15:51', '2018-01-08 14:15:51');
INSERT INTO `sys_user_role` VALUES ('2', '254', '2018-01-08 14:15:52', '2018-01-08 14:15:52');
INSERT INTO `sys_user_role` VALUES ('2', '255', '2018-01-08 14:15:52', '2018-01-08 14:15:52');
INSERT INTO `sys_user_role` VALUES ('2', '256', '2018-01-08 14:15:52', '2018-01-08 14:15:52');
INSERT INTO `sys_user_role` VALUES ('2', '257', '2018-01-08 14:15:53', '2018-01-08 14:15:53');
INSERT INTO `sys_user_role` VALUES ('2', '258', '2018-01-08 14:15:53', '2018-01-08 14:15:53');
INSERT INTO `sys_user_role` VALUES ('2', '259', '2018-01-08 14:15:54', '2018-01-08 14:15:54');
INSERT INTO `sys_user_role` VALUES ('2', '260', '2018-01-08 14:15:54', '2018-01-08 14:15:54');
INSERT INTO `sys_user_role` VALUES ('2', '261', '2018-01-23 15:08:52', '2018-01-23 15:08:52');
INSERT INTO `sys_user_role` VALUES ('2', '262', '2018-02-05 10:58:02', '2018-02-05 10:58:02');
INSERT INTO `sys_user_role` VALUES ('5', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for sys_validatecode
-- ----------------------------
DROP TABLE IF EXISTS `sys_validatecode`;
CREATE TABLE `sys_validatecode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `expiretime` int(10) unsigned NOT NULL COMMENT '过期时间',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型 1=验证码',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '对比代码',
  `email` varchar(50) DEFAULT NULL COMMENT '邮件地址',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='验证码表';

-- ----------------------------
-- Records of sys_validatecode
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `qq` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `openid` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `weixinnickname` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `province` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `adress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastlogindate` datetime DEFAULT NULL,
  `idcard_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '证件类型【1:中国居民身份证，2:学生证，3:港澳通行证，4:驾照，5:军官证，6:护照，7:其他】',
  `idcard` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_mobile_unique` (`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', '超级管理老师', '13999999999', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '超级管理员', '1', '', 'otTKFv2Uff-QP3FZwRRieEZ83Yq0', '', '', '', '', '', '/images/20160420174241_jh9tLk.jpg', '21323123@1.3', '2016-04-21 15:52:29', '1', '333333333333333333', '1', '2016-03-04 10:29:13', '2018-02-08 15:52:18', 'utspd0BspAlZym0Q1TS7AQbQL1AxeaL1uK9YNsJG3bLcK0RHdlR60LMlB8bJ', '2323');
INSERT INTO `users` VALUES ('4', '13218192769', '江老师', '13218192769', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/20170520192618_YLEuHo.jpg', 'andy@china.com', null, '1', '343823198807031323', '1', '2017-05-20 19:27:24', '2017-06-01 12:37:27', 'CeFFc2CJZ9pIZLCfwCH8r2ZgiKwbvLgxtgpwxZk4InQGJ2zki6jR8NCUyL6Y', '100');
INSERT INTO `users` VALUES ('6', '18055699256', '郑一峰', '18055699256', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '332522198705040011', '1', '2017-05-20 19:31:06', '2017-05-20 19:31:06', null, '9000');
INSERT INTO `users` VALUES ('7', '18055699257', '许家圣', '18055699257', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '35042619790906301X', '1', '2017-05-20 19:31:06', '2017-05-20 19:31:06', null, '9001');
INSERT INTO `users` VALUES ('8', '18055699258', '李靖男', '18055699258', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '210602198711260513', '1', '2017-05-20 19:31:06', '2017-05-20 19:31:06', null, '9002');
INSERT INTO `users` VALUES ('9', '18055699259', '陈侃', '18055699259', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '340103198303072000', '1', '2017-05-20 19:31:07', '2017-05-20 19:31:07', null, '9003');
INSERT INTO `users` VALUES ('10', '18055699260', '康焕卉', '18055699260', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '410183199307210000', '1', '2017-05-20 19:31:07', '2017-05-20 19:31:07', null, '9004');
INSERT INTO `users` VALUES ('11', '18055699261', '池鹏', '18055699261', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '362326198306270000', '1', '2017-05-20 19:31:07', '2017-05-20 19:31:07', null, '9005');
INSERT INTO `users` VALUES ('12', '18055699262', '刘大奇', '18055699262', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '430503198706130000', '1', '2017-05-20 19:31:08', '2017-05-20 19:31:08', null, '9006');
INSERT INTO `users` VALUES ('13', '18055699263', '许子豪', '18055699263', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '331081198601210000', '1', '2017-05-20 19:31:08', '2017-05-20 19:31:08', null, '9007');
INSERT INTO `users` VALUES ('14', '18055699264', '唐爽', '18055699264', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '230103198509121000', '1', '2017-05-20 19:31:08', '2017-05-20 19:31:08', null, '9008');
INSERT INTO `users` VALUES ('15', '18055699265', '沈楠', '18055699265', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '120106199501191000', '1', '2017-05-20 19:31:09', '2017-05-20 19:31:09', null, '9009');
INSERT INTO `users` VALUES ('16', '18055699266', '杨豪', '18055699266', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130825199408161000', '1', '2017-05-20 19:31:09', '2017-05-20 19:31:09', null, '9010');
INSERT INTO `users` VALUES ('17', '18055699267', '谭宏旺', '18055699267', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '342623198109140000', '1', '2017-05-20 19:31:09', '2017-05-20 19:31:09', null, '9011');
INSERT INTO `users` VALUES ('18', '18055699268', '王雷', '18055699268', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '522125198511033000', '1', '2017-05-20 19:31:10', '2017-05-20 19:31:10', null, '9012');
INSERT INTO `users` VALUES ('19', '18055699269', '张庆', '18055699269', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '422823198511163000', '1', '2017-05-20 19:31:10', '2017-05-20 19:31:10', null, '9013');
INSERT INTO `users` VALUES ('20', '18055699270', '于峰', '18055699270', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130181198808086000', '1', '2017-05-20 19:31:10', '2017-05-20 19:31:10', null, '9014');
INSERT INTO `users` VALUES ('21', '18055699271', '陈明', '18055699271', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '31010919850214201X', '1', '2017-05-20 19:31:11', '2017-05-20 19:31:11', null, '9015');
INSERT INTO `users` VALUES ('22', '18055699272', '严益春', '18055699272', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '37060219830224041X', '1', '2017-05-20 19:31:11', '2017-05-20 19:31:11', null, '9016');
INSERT INTO `users` VALUES ('23', '18055699273', '刘洋', '18055699273', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '431223199110305000', '1', '2017-05-20 19:31:11', '2017-05-20 19:31:11', null, '9017');
INSERT INTO `users` VALUES ('24', '18055699274', '南存赞', '18055699274', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '422201199001160000', '1', '2017-05-20 19:31:11', '2017-05-20 19:31:11', null, '9018');
INSERT INTO `users` VALUES ('25', '18055699275', '刘康', '18055699275', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '431002198211301000', '1', '2017-05-20 19:31:12', '2017-05-20 19:31:12', null, '9019');
INSERT INTO `users` VALUES ('26', '18055699276', '于谨', '18055699276', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '330382198909171000', '1', '2017-05-20 19:31:12', '2017-05-20 19:31:12', null, '9020');
INSERT INTO `users` VALUES ('27', '18055699277', '陈瑶', '18055699277', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130982198610307000', '1', '2017-05-20 19:31:13', '2017-05-20 19:31:13', null, '9021');
INSERT INTO `users` VALUES ('28', '18055699278', '白旭明', '18055699278', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '220622198404090000', '1', '2017-05-20 19:31:13', '2017-05-20 19:31:13', null, '9022');
INSERT INTO `users` VALUES ('29', '18055699279', '张楠', '18055699279', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '410481199610310000', '1', '2017-05-20 19:31:13', '2017-05-20 19:31:13', null, '9023');
INSERT INTO `users` VALUES ('30', '18055699280', '蔡启忠', '18055699280', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '142326198401021000', '1', '2017-05-20 19:31:14', '2017-05-20 19:31:14', null, '9024');
INSERT INTO `users` VALUES ('31', '18055699281', '薛洋', '18055699281', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '120105198103305000', '1', '2017-05-20 19:31:14', '2017-05-20 19:31:14', null, '9025');
INSERT INTO `users` VALUES ('32', '18055699282', '李帅', '18055699282', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '441522199611208000', '1', '2017-05-20 19:31:14', '2017-05-20 19:31:14', null, '9026');
INSERT INTO `users` VALUES ('33', '18055699283', '侯祥红', '18055699283', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '340323198812203000', '1', '2017-05-20 19:31:15', '2017-05-20 19:31:15', null, '9027');
INSERT INTO `users` VALUES ('34', '18055699284', '李来超', '18055699284', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '350182198801011000', '1', '2017-05-20 19:31:15', '2017-05-20 19:31:15', null, '9028');
INSERT INTO `users` VALUES ('35', '18055699285', '王荣斌', '18055699285', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '511502198507054000', '1', '2017-05-20 19:31:15', '2017-05-20 19:31:15', null, '9029');
INSERT INTO `users` VALUES ('36', '18055699286', '任和', '18055699286', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '370481198801201000', '1', '2017-05-20 19:31:16', '2017-05-20 19:31:16', null, '9030');
INSERT INTO `users` VALUES ('37', '18055699287', '王勇', '18055699287', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '510823198603117000', '1', '2017-05-20 19:31:16', '2017-05-20 19:31:16', null, '9031');
INSERT INTO `users` VALUES ('38', '18055699288', '李超', '18055699288', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '620523198810120000', '1', '2017-05-20 19:31:16', '2017-05-20 19:31:16', null, '9032');
INSERT INTO `users` VALUES ('39', '18055699289', '陈凌枫', '18055699289', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '310107197909294000', '1', '2017-05-20 19:31:16', '2017-05-20 19:31:16', null, '9033');
INSERT INTO `users` VALUES ('40', '18055699290', '张仟', '18055699290', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '320103198609050000', '1', '2017-05-20 19:31:17', '2017-05-20 19:31:17', null, '9034');
INSERT INTO `users` VALUES ('41', '18055699291', '马凤兰', '18055699291', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '330324198904300000', '1', '2017-05-20 19:31:17', '2017-05-20 19:31:17', null, '9035');
INSERT INTO `users` VALUES ('42', '18055699292', '韩龙', '18055699292', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '360723199207010000', '1', '2017-05-20 19:31:17', '2017-05-20 19:31:17', null, '9036');
INSERT INTO `users` VALUES ('43', '18055699293', '张旭', '18055699293', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '230204195606030000', '1', '2017-05-20 19:31:18', '2017-05-20 19:31:18', null, '9037');
INSERT INTO `users` VALUES ('44', '18055699294', '周辰', '18055699294', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '120101198810070000', '1', '2017-05-20 19:31:18', '2017-05-20 19:31:18', null, '9038');
INSERT INTO `users` VALUES ('45', '18055699295', '陈冬', '18055699295', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '110104198704261000', '1', '2017-05-20 19:31:18', '2017-05-20 19:31:18', null, '9039');
INSERT INTO `users` VALUES ('46', '18055699296', '杨振琪', '18055699296', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '420302198811211000', '1', '2017-05-20 19:31:19', '2017-05-20 19:31:19', null, '9040');
INSERT INTO `users` VALUES ('47', '18055699297', '陈玉花', '18055699297', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '420702198910107000', '1', '2017-05-20 19:31:19', '2017-05-20 19:31:19', null, '9041');
INSERT INTO `users` VALUES ('48', '18055699298', '赵宇涵', '18055699298', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130304198812131000', '1', '2017-05-20 19:31:19', '2017-05-20 19:31:19', null, '9042');
INSERT INTO `users` VALUES ('49', '18055699299', '徐芝鑫', '18055699299', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '37092319861210434X', '1', '2017-05-20 19:31:19', '2017-05-20 19:31:19', null, '9043');
INSERT INTO `users` VALUES ('50', '18055699300', '徐建超', '18055699300', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '370725199508020000', '1', '2017-05-20 19:31:20', '2017-05-20 19:31:20', null, '9044');
INSERT INTO `users` VALUES ('51', '18055699301', '高智', '18055699301', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '370212198801241000', '1', '2017-05-20 19:31:20', '2017-05-20 19:31:20', null, '9045');
INSERT INTO `users` VALUES ('52', '18055699302', '饶桑林', '18055699302', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '13063819890227253X', '1', '2017-05-20 19:31:20', '2017-05-20 19:31:20', null, '9046');
INSERT INTO `users` VALUES ('53', '18055699303', '吴杨文', '18055699303', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '522501199008282000', '1', '2017-05-20 19:31:21', '2017-05-20 19:31:21', null, '9047');
INSERT INTO `users` VALUES ('54', '18055699304', '黄斯敏', '18055699304', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '421181198901101000', '1', '2017-05-20 19:31:21', '2017-05-20 19:31:21', null, '9048');
INSERT INTO `users` VALUES ('55', '18055699305', '李豹', '18055699305', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '431124198701024000', '1', '2017-05-20 19:31:21', '2017-05-20 19:31:21', null, '9049');
INSERT INTO `users` VALUES ('56', '18055699306', '李冬冬', '18055699306', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '310112199408103000', '1', '2017-05-20 19:31:21', '2017-05-20 19:31:21', null, '9050');
INSERT INTO `users` VALUES ('57', '18055699307', '石海波', '18055699307', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '432503198701235000', '1', '2017-05-20 19:31:22', '2017-05-20 19:31:22', null, '9051');
INSERT INTO `users` VALUES ('58', '18055699308', '田大伟', '18055699308', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '513723198608095000', '1', '2017-05-20 19:31:22', '2017-05-20 19:31:22', null, '9052');
INSERT INTO `users` VALUES ('59', '18055699309', '辜彬礼', '18055699309', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '430724198612182000', '1', '2017-05-20 19:31:22', '2017-05-20 19:31:22', null, '9053');
INSERT INTO `users` VALUES ('60', '18055699310', '叶建成', '18055699310', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '320202198801281000', '1', '2017-05-20 19:31:23', '2017-05-20 19:31:23', null, '9054');
INSERT INTO `users` VALUES ('61', '18055699311', '林建斌', '18055699311', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '445121199301274000', '1', '2017-05-20 19:31:23', '2017-05-20 19:31:23', null, '9055');
INSERT INTO `users` VALUES ('62', '18055699312', '王光虎', '18055699312', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '500226199107221000', '1', '2017-05-20 19:31:23', '2017-05-20 19:31:23', null, '9056');
INSERT INTO `users` VALUES ('63', '18055699313', '邱君', '18055699313', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '352228198611053000', '1', '2017-05-20 19:31:24', '2017-05-20 19:31:24', null, '9057');
INSERT INTO `users` VALUES ('64', '18055699314', '吴伟', '18055699314', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '320381198410214000', '1', '2017-05-20 19:31:24', '2017-05-20 19:31:24', null, '9058');
INSERT INTO `users` VALUES ('65', '18055699315', '黄锦声', '18055699315', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '42102319940912635X', '1', '2017-05-20 19:31:24', '2017-05-20 19:31:24', null, '9059');
INSERT INTO `users` VALUES ('66', '18055699316', '刘丹', '18055699316', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '350502199306140000', '1', '2017-05-20 19:31:25', '2017-05-20 19:31:25', null, '9060');
INSERT INTO `users` VALUES ('67', '18055699317', '陈蒙', '18055699317', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '440682198206162000', '1', '2017-05-20 19:31:25', '2017-05-20 19:31:25', null, '9061');
INSERT INTO `users` VALUES ('68', '18055699318', '王彭全', '18055699318', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '430722199307198000', '1', '2017-05-20 19:31:25', '2017-05-20 19:31:25', null, '9062');
INSERT INTO `users` VALUES ('69', '18055699319', '王玉龙', '18055699319', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '653126198911080000', '1', '2017-05-20 19:31:25', '2017-05-20 19:31:25', null, '9063');
INSERT INTO `users` VALUES ('70', '18055699320', '姜龙', '18055699320', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '420102198710273000', '1', '2017-05-20 19:31:26', '2017-05-20 19:31:26', null, '9064');
INSERT INTO `users` VALUES ('71', '18055699321', '冀磊', '18055699321', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '13042519880910001X', '1', '2017-05-20 19:31:26', '2017-05-20 19:31:26', null, '9065');
INSERT INTO `users` VALUES ('72', '18055699322', '刘东川', '18055699322', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '320981198809080000', '1', '2017-05-20 19:31:26', '2017-05-20 19:31:26', null, '9066');
INSERT INTO `users` VALUES ('73', '18055699323', '王程龙', '18055699323', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '370982198609101000', '1', '2017-05-20 19:31:27', '2017-05-20 19:31:27', null, '9067');
INSERT INTO `users` VALUES ('74', '18055699324', '高科', '18055699324', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '513001199103228000', '1', '2017-05-20 19:31:27', '2017-05-20 19:31:27', null, '9068');
INSERT INTO `users` VALUES ('75', '18055699325', '叶菲', '18055699325', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '231083198502191000', '1', '2017-05-20 19:31:27', '2017-05-20 19:31:27', null, '9069');
INSERT INTO `users` VALUES ('76', '18055699326', '谢波', '18055699326', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '370303198601032000', '1', '2017-05-20 19:31:28', '2017-05-20 19:31:28', null, '9070');
INSERT INTO `users` VALUES ('77', '18055699327', '李冰凌', '18055699327', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130404198205013000', '1', '2017-05-20 19:31:28', '2017-05-20 19:31:28', null, '9071');
INSERT INTO `users` VALUES ('78', '18055699328', '宋震林', '18055699328', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '510781198111010000', '1', '2017-05-20 19:31:28', '2017-05-20 19:31:28', null, '9072');
INSERT INTO `users` VALUES ('79', '18055699329', '方存磊', '18055699329', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '620302199212210000', '1', '2017-05-20 19:31:28', '2017-05-20 19:31:28', null, '9073');
INSERT INTO `users` VALUES ('80', '18055699330', '余昌隆', '18055699330', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '622201197909230000', '1', '2017-05-20 19:31:29', '2017-05-20 19:31:29', null, '9074');
INSERT INTO `users` VALUES ('81', '18055699331', '吕权富', '18055699331', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '342422198503180000', '1', '2017-05-20 19:31:29', '2017-05-20 19:31:29', null, '9075');
INSERT INTO `users` VALUES ('82', '18055699332', '崔默', '18055699332', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '412826198802168000', '1', '2017-05-20 19:31:29', '2017-05-20 19:31:29', null, '9076');
INSERT INTO `users` VALUES ('83', '18055699333', '王琎', '18055699333', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '452527198309202000', '1', '2017-05-20 19:31:30', '2017-05-20 19:31:30', null, '9077');
INSERT INTO `users` VALUES ('84', '18055699334', '叶桂梅', '18055699334', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '321088199011118000', '1', '2017-05-20 19:31:30', '2017-05-20 19:31:30', null, '9078');
INSERT INTO `users` VALUES ('85', '18055699335', '刘良鹏', '18055699335', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '410105198306172000', '1', '2017-05-20 19:31:30', '2017-05-20 19:31:30', null, '9079');
INSERT INTO `users` VALUES ('86', '18055699336', '惠亮亮', '18055699336', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '421123196512041000', '1', '2017-05-20 19:31:30', '2017-05-20 19:31:30', null, '9080');
INSERT INTO `users` VALUES ('87', '18055699337', '吴志远', '18055699337', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '520112199108100000', '1', '2017-05-20 19:31:31', '2017-05-20 19:31:31', null, '9081');
INSERT INTO `users` VALUES ('88', '18055699338', '荆林枭', '18055699338', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '61273119920113323X', '1', '2017-05-20 19:31:31', '2017-05-20 19:31:31', null, '9082');
INSERT INTO `users` VALUES ('89', '18055699339', '俞晓敏', '18055699339', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '342501199506180000', '1', '2017-05-20 19:31:31', '2017-05-20 19:31:31', null, '9083');
INSERT INTO `users` VALUES ('90', '18055699340', '李森森', '18055699340', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '410181199208265000', '1', '2017-05-20 19:31:32', '2017-05-20 19:31:32', null, '9084');
INSERT INTO `users` VALUES ('91', '18055699341', '赵巍', '18055699341', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '320684198809076000', '1', '2017-05-20 19:31:32', '2017-05-20 19:31:32', null, '9085');
INSERT INTO `users` VALUES ('92', '18055699342', '孙琦', '18055699342', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '350822198602034000', '1', '2017-05-20 19:31:32', '2017-05-20 19:31:32', null, '9086');
INSERT INTO `users` VALUES ('93', '18055699343', '朱一', '18055699343', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '430102198410253000', '1', '2017-05-20 19:31:33', '2017-05-20 19:31:33', null, '9087');
INSERT INTO `users` VALUES ('94', '18055699344', '朱二', '18055699344', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '511502198708152000', '1', '2017-05-20 19:31:33', '2017-05-20 19:31:33', null, '9088');
INSERT INTO `users` VALUES ('95', '18055699345', '朱三', '18055699345', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '120106198908311000', '1', '2017-05-20 19:31:33', '2017-05-20 19:31:33', null, '9089');
INSERT INTO `users` VALUES ('96', '18055699346', '朱四', '18055699346', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '231121198903085000', '1', '2017-05-20 19:31:33', '2017-05-20 19:31:33', null, '9090');
INSERT INTO `users` VALUES ('97', '18055699347', '朱五', '18055699347', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130182199105016000', '1', '2017-05-20 19:31:34', '2017-05-20 19:31:34', null, '9091');
INSERT INTO `users` VALUES ('98', '18055699348', '朱六', '18055699348', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '612401197910240000', '1', '2017-05-20 19:31:34', '2017-05-20 19:31:34', null, '9092');
INSERT INTO `users` VALUES ('99', '18055699349', '朱七', '18055699349', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '433022198209155000', '1', '2017-05-20 19:31:34', '2017-05-20 19:31:34', null, '9093');
INSERT INTO `users` VALUES ('100', '18055699350', '朱八', '18055699350', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '130823198811251000', '1', '2017-05-20 19:31:35', '2017-05-20 19:31:35', null, '9094');
INSERT INTO `users` VALUES ('101', '18055699351', '朱九', '18055699351', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '36230219890515053X', '1', '2017-05-20 19:31:35', '2017-05-20 19:31:35', null, '9095');
INSERT INTO `users` VALUES ('102', '18055699352', '朱十', '18055699352', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '37082719900610327X', '1', '2017-05-20 19:31:35', '2017-05-20 19:31:35', null, '9096');
INSERT INTO `users` VALUES ('103', '18055699353', '朱十一', '18055699353', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '420821198912134000', '1', '2017-05-20 19:31:35', '2017-05-20 19:31:35', null, '9097');
INSERT INTO `users` VALUES ('104', '18055699354', '朱十二', '18055699354', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '211223197611260000', '1', '2017-05-20 19:31:36', '2017-05-20 19:31:36', null, '9098');
INSERT INTO `users` VALUES ('105', '18055699355', '朱十三', '18055699355', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head1.png', 'phpinfo@foxmail.com', null, '1', '460200199406185000', '1', '2017-05-20 19:31:36', '2017-05-20 19:31:36', null, '9099');
INSERT INTO `users` VALUES ('106', '18055699356', '程娟娟', '18055699356', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head2.png', 'phpinfo@foxmail.com', null, '1', '320830197803310000', '1', '2017-05-20 19:31:36', '2017-05-20 19:31:36', null, '9100');
INSERT INTO `users` VALUES ('107', '18055699357', '于菁', '18055699357', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head3.png', 'phpinfo@foxmail.com', null, '1', '310110199012091000', '1', '2017-05-20 19:31:37', '2017-05-20 19:31:37', null, '9101');
INSERT INTO `users` VALUES ('108', '18055699358', '王怡', '18055699358', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head4.png', 'phpinfo@foxmail.com', null, '1', '310109198212231000', '1', '2017-05-20 19:31:37', '2017-05-20 19:31:37', null, '9102');
INSERT INTO `users` VALUES ('109', '18055699359', '梁守明', '18055699359', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head5.png', 'phpinfo@foxmail.com', null, '1', '35210119640811031X', '1', '2017-05-20 19:31:37', '2017-05-20 19:31:37', null, '9103');
INSERT INTO `users` VALUES ('110', '18055699360', '晏芳', '18055699360', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head6.png', 'phpinfo@foxmail.com', null, '1', '310109198209262000', '1', '2017-05-20 19:31:38', '2017-05-20 19:31:38', null, '9104');
INSERT INTO `users` VALUES ('111', '18055699361', '陈粉娣', '18055699361', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head7.png', 'phpinfo@foxmail.com', null, '1', '321025194107058000', '1', '2017-05-20 19:31:38', '2017-05-20 19:31:38', null, '9105');
INSERT INTO `users` VALUES ('112', '18055699362', '徐倩影', '18055699362', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head8.png', 'phpinfo@foxmail.com', null, '1', '321201198902100000', '1', '2017-05-20 19:31:38', '2017-05-20 19:31:38', null, '9106');
INSERT INTO `users` VALUES ('113', '18055699363', '陈健-', '18055699363', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head9.png', 'phpinfo@foxmail.com', null, '1', '420523197908170000', '1', '2017-05-20 19:31:38', '2017-05-20 19:31:38', null, '9107');
INSERT INTO `users` VALUES ('114', '18055699364', '徐佳琴', '18055699364', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head10.png', 'phpinfo@foxmail.com', null, '1', '320829197309121000', '1', '2017-05-20 19:31:39', '2017-05-20 19:31:39', null, '9108');
INSERT INTO `users` VALUES ('115', '18055699365', '洪世远', '18055699365', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head11.png', 'phpinfo@foxmail.com', null, '1', '420500195405131000', '1', '2017-05-20 19:31:39', '2017-05-20 19:31:39', null, '9109');
INSERT INTO `users` VALUES ('116', '18055699366', '洪毅-', '18055699366', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head12.png', 'phpinfo@foxmail.com', null, '1', '420502198607284000', '1', '2017-05-20 19:31:39', '2017-05-20 19:31:39', null, '9110');
INSERT INTO `users` VALUES ('117', '18055699367', '顾亦媛', '18055699367', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head13.png', 'phpinfo@foxmail.com', null, '1', '310109199010052000', '1', '2017-05-20 19:31:40', '2017-05-20 19:31:40', null, '9111');
INSERT INTO `users` VALUES ('118', '18055699368', '徐挺宙', '18055699368', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head14.png', 'phpinfo@foxmail.com', null, '1', '310103197504080000', '1', '2017-05-20 19:31:40', '2017-05-20 19:31:40', null, '9112');
INSERT INTO `users` VALUES ('119', '18055699369', '郑科', '18055699369', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head15.png', 'phpinfo@foxmail.com', null, '1', '320421196910100000', '1', '2017-05-20 19:31:40', '2017-05-20 19:31:40', null, '9113');
INSERT INTO `users` VALUES ('120', '18055699370', '杭大飞', '18055699370', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head16.png', 'phpinfo@foxmail.com', null, '1', '320325198102271000', '1', '2017-05-20 19:31:41', '2017-05-20 19:31:41', null, '9114');
INSERT INTO `users` VALUES ('121', '18055699371', '胡一平', '18055699371', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head17.png', 'phpinfo@foxmail.com', null, '1', '320525198108145000', '1', '2017-05-20 19:31:41', '2017-05-20 19:31:41', null, '9115');
INSERT INTO `users` VALUES ('122', '18055699372', '张雷-', '18055699372', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head18.png', 'phpinfo@foxmail.com', null, '1', '320382198309261000', '1', '2017-05-20 19:31:41', '2017-05-20 19:31:41', null, '9116');
INSERT INTO `users` VALUES ('123', '18055699373', '刘立飞', '18055699373', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head19.png', 'phpinfo@foxmail.com', null, '1', '320721198206272000', '1', '2017-05-20 19:31:41', '2017-05-20 19:31:41', null, '9117');
INSERT INTO `users` VALUES ('124', '18055699374', '叶大鹏', '18055699374', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head20.png', 'phpinfo@foxmail.com', null, '1', '330302197906041000', '1', '2017-05-20 19:31:42', '2017-05-20 19:31:42', null, '9118');
INSERT INTO `users` VALUES ('125', '18055699375', '吴昊', '18055699375', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head21.png', 'phpinfo@foxmail.com', null, '1', '320325198109291000', '1', '2017-05-20 19:31:42', '2017-05-20 19:31:42', null, '9119');
INSERT INTO `users` VALUES ('126', '18055699376', '商贵洋', '18055699376', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head22.png', 'phpinfo@foxmail.com', null, '1', '320925196910181000', '1', '2017-05-20 19:31:42', '2017-05-20 19:31:42', null, '9120');
INSERT INTO `users` VALUES ('127', '18055699377', '骆鑫锋', '18055699377', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head23.png', 'phpinfo@foxmail.com', null, '1', '330183198903232000', '1', '2017-05-20 19:31:43', '2017-05-20 19:31:43', null, '9121');
INSERT INTO `users` VALUES ('128', '18055699378', '沈琪-', '18055699378', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head24.png', 'phpinfo@foxmail.com', null, '1', '32050219790129201X', '1', '2017-05-20 19:31:43', '2017-05-20 19:31:43', null, '9122');
INSERT INTO `users` VALUES ('129', '18055699379', '骆东祥', '18055699379', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head25.png', 'phpinfo@foxmail.com', null, '1', '330123196509022000', '1', '2017-05-20 19:31:43', '2017-05-20 19:31:43', null, '9123');
INSERT INTO `users` VALUES ('130', '18055699380', '周秀干', '18055699380', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head26.png', 'phpinfo@foxmail.com', null, '1', '100316193811018000', '1', '2017-05-20 19:31:44', '2017-05-20 19:31:44', null, '9124');
INSERT INTO `users` VALUES ('131', '18055699381', '策军锋', '18055699381', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head27.png', 'phpinfo@foxmail.com', null, '1', '61040319731028005X', '1', '2017-05-20 19:31:44', '2017-05-20 19:31:44', null, '9125');
INSERT INTO `users` VALUES ('132', '18055699382', '孙丹丹', '18055699382', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head28.png', 'phpinfo@foxmail.com', null, '1', '321284199109271000', '1', '2017-05-20 19:31:44', '2017-05-20 19:31:44', null, '9126');
INSERT INTO `users` VALUES ('133', '18055699383', '华伟-', '18055699383', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head29.png', 'phpinfo@foxmail.com', null, '1', '321284198907053000', '1', '2017-05-20 19:31:44', '2017-05-20 19:31:44', null, '9127');
INSERT INTO `users` VALUES ('134', '18055699384', '陈曦-', '18055699384', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head30.png', 'phpinfo@foxmail.com', null, '1', '320504198511301000', '1', '2017-05-20 19:31:45', '2017-05-20 19:31:45', null, '9128');
INSERT INTO `users` VALUES ('135', '18055699385', '李红梅', '18055699385', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head31.png', 'phpinfo@foxmail.com', null, '1', '43038119860405364X', '1', '2017-05-20 19:31:45', '2017-05-20 19:31:45', null, '9129');
INSERT INTO `users` VALUES ('136', '18055699386', '陈良东', '18055699386', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head32.png', 'phpinfo@foxmail.com', null, '1', '452133198211212000', '1', '2017-05-20 19:31:45', '2017-05-20 19:31:45', null, '9130');
INSERT INTO `users` VALUES ('137', '18055699387', '赵晓中', '18055699387', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head33.png', 'phpinfo@foxmail.com', null, '1', '412728198111150000', '1', '2017-05-20 19:31:46', '2017-05-20 19:31:46', null, '9131');
INSERT INTO `users` VALUES ('138', '18055699388', '赵军-', '18055699388', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head34.png', 'phpinfo@foxmail.com', null, '1', '412701197308052000', '1', '2017-05-20 19:31:46', '2017-05-20 19:31:46', null, '9132');
INSERT INTO `users` VALUES ('139', '18055699389', '丁娜-', '18055699389', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head35.png', 'phpinfo@foxmail.com', null, '1', '41010519770123106X', '1', '2017-05-20 19:31:46', '2017-05-20 19:31:46', null, '9133');
INSERT INTO `users` VALUES ('140', '18055699390', '李德喜', '18055699390', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head36.png', 'phpinfo@foxmail.com', null, '1', '220381197907206000', '1', '2017-05-20 19:31:47', '2017-05-20 19:31:47', null, '9134');
INSERT INTO `users` VALUES ('141', '18055699391', '刘长宽', '18055699391', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head37.png', 'phpinfo@foxmail.com', null, '1', '110108194709187000', '1', '2017-05-20 19:31:47', '2017-05-20 19:31:47', null, '9135');
INSERT INTO `users` VALUES ('142', '18055699392', '李海贤', '18055699392', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head38.png', 'phpinfo@foxmail.com', null, '1', '331081198803194000', '1', '2017-05-20 19:31:47', '2017-05-20 19:31:47', null, '9136');
INSERT INTO `users` VALUES ('143', '18055699393', '蒋盛辉', '18055699393', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head39.png', 'phpinfo@foxmail.com', null, '1', '331081198312094000', '1', '2017-05-20 19:31:48', '2017-05-20 19:31:48', null, '9137');
INSERT INTO `users` VALUES ('144', '18055699394', '张萌-', '18055699394', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head40.png', 'phpinfo@foxmail.com', null, '1', '330103197610270000', '1', '2017-05-20 19:31:48', '2017-05-20 19:31:48', null, '9138');
INSERT INTO `users` VALUES ('145', '18055699395', '沈飞-', '18055699395', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head41.png', 'phpinfo@foxmail.com', null, '1', '320481198106139000', '1', '2017-05-20 19:31:48', '2017-05-20 19:31:48', null, '9139');
INSERT INTO `users` VALUES ('146', '18055699396', '毛素琴', '18055699396', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head42.png', 'phpinfo@foxmail.com', null, '1', '330823197005116000', '1', '2017-05-20 19:31:48', '2017-05-20 19:31:48', null, '9140');
INSERT INTO `users` VALUES ('147', '13869688032', '李垂青', '13869688032', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '679@163.com', null, '1', '370723197806285331', '1', '2017-05-26 11:07:34', '2017-06-01 12:35:53', 'gBDHjUVGHDYd3Z73zhx9i5r482XZn3FRS419VXFvotdXeyMcjrjkBe12kQpe', '010');
INSERT INTO `users` VALUES ('148', '13953677518', '侯万升', '13953677518', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '55789@163.com', null, '1', '370725196302280033', '1', '2017-05-26 11:07:35', '2017-05-26 13:43:04', null, '005');
INSERT INTO `users` VALUES ('149', '15610293315', '杜汉旺', '15610293315', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '789@163.com', null, '1', '372922198008109094', '1', '2017-05-26 11:07:36', '2017-05-26 13:42:37', null, '007');
INSERT INTO `users` VALUES ('150', '13869686729', '徐有光', '13869686729', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '456789@163.com', null, '1', '370702196506280057', '1', '2017-05-26 11:07:36', '2017-05-26 13:42:09', null, '003');
INSERT INTO `users` VALUES ('151', '13563602712', '白玉', '13563602712', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '23456789@163.com', null, '1', '370724198306037658', '1', '2017-05-26 11:07:36', '2017-05-26 13:40:43', null, '008');
INSERT INTO `users` VALUES ('152', '13792635869', '郑薇薇', '13792635869', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '/images/head.png', '75789@163.com', null, '1', '370102197803084521', '1', '2017-05-26 11:07:37', '2017-05-26 13:44:53', null, '009');
INSERT INTO `users` VALUES ('153', '13953609223', '朱勤伟', '13953609223', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '6789@163.com', null, '1', '370786197712196034', '1', '2017-05-26 11:07:37', '2017-05-26 13:45:36', null, '004');
INSERT INTO `users` VALUES ('154', '13953659712', '张学正', '13953659712', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '755@163.com', null, '1', '370723197607106919', '1', '2017-05-26 11:07:37', '2017-05-26 13:46:27', null, '001');
INSERT INTO `users` VALUES ('155', '13181675100', '张全文', '13181675100', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '535789@163.com', null, '1', '370723197207085733', '1', '2017-05-26 11:07:38', '2017-05-26 13:46:57', null, '002');
INSERT INTO `users` VALUES ('156', '13589168267', '王少明', '13589168267', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '539@163.com', null, '1', '370702197312290016', '1', '2017-05-26 11:07:38', '2017-05-26 13:47:26', null, '006');
INSERT INTO `users` VALUES ('157', '15780030155', '邓超凡', '15780030155', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@174.com', null, '5', '450203197910220316', '1', '2017-05-26 11:56:09', '2018-02-01 10:37:04', 'HSJOP53DEmJQVJcwI1G05HEYB6EEYaX5ivoJeq4pjpuP9leS57Ckkekzx2eU', '6781');
INSERT INTO `users` VALUES ('158', '15770030155', '周强', '15770030155', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@163.com', null, '1', '511621198907207934', '1', '2017-05-26 11:56:11', '2018-01-31 09:58:57', 'nbegcqieJ9Hx6dUqAenZ36sX0bxrgKXnBVJayJKQYFThJ6sGIb6pxfRu6qRu', '5555560');
INSERT INTO `users` VALUES ('159', '18113144263', '刘源', '18113144263', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@163.com', null, '2', '510107199003265014', '1', '2017-05-26 11:56:11', '2018-01-25 16:53:28', 'EwoWtEapkwpVjpQYJSfek85hxccNyRw5U7iaxXwPZ8A9WX9nqyNL7PDHVzM0', '5555561');
INSERT INTO `users` VALUES ('160', '15182180398', '唐政伟', '15182180398', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@172.com', null, '3', '511023199206016953', '1', '2017-05-26 11:56:12', '2018-01-25 16:53:06', 'ZDsX4Q7H0RxR69j5u35UwohUFZQ4GvQ6DE8Uk22TGL2KxYqWFH3gBktDl6wM', '6779');
INSERT INTO `users` VALUES ('161', '15182180399', '徐敏', '15182180399', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '/images/head.png', '1216277@173.com', null, '4', '513902199205078721', '1', '2017-05-26 11:56:12', '2018-01-25 16:51:53', 'SFpuD1R5SiFfWM1sfNDAwEMcPVwV5EyJA854ZiibC571Ci0SylpDLI1ETfzp', '6780');
INSERT INTO `users` VALUES ('162', '15182180401', '向虎', '15182180401', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@175.com', null, '6', '511322199002161999', '1', '2017-05-26 11:56:12', '2018-01-25 16:48:33', 'M3Y34vnuyOLUP4nHy7tU0bhkBDVCvjtw5tA00PbP2m4NecMmN5OP8oI5nWWJ', '6782');
INSERT INTO `users` VALUES ('163', '15182180402', '温婷婷', '15182180402', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@176.com', null, '7', '452231199107030026', '1', '2017-05-26 11:56:13', '2018-01-25 16:33:47', '6phXISJRHuB5LL0zOQPf1MkSfkgXIEEJZsA5Ldm0w7aqBezGZdtbwmXbqN5O', '6783');
INSERT INTO `users` VALUES ('164', '15069383037', '仇贵蓉', '15069383037', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370303198902107225', '1', '2017-05-26 14:01:04', '2017-09-20 15:42:07', 'BmmMkxUflntXqAGdxDoc1BA9CYGdvg3U7fUGmhQbV9O623jikgaVbRvZzLsp', '201401');
INSERT INTO `users` VALUES ('165', '18678150730', '孙远洋', '18678150730', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370124198807280012', '1', '2017-05-26 14:01:07', '2017-09-20 16:22:57', 'XoVUo97p3GD8EFPfKhP6PECpDapad1RPhWaNj6w4883tkcSOKReeQqLYYFvL', '201402');
INSERT INTO `users` VALUES ('166', '18206447525', '刘旭', '18206447525', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370782198707104317', '1', '2017-05-26 14:01:07', '2017-09-20 16:51:03', 'xujcFe5PyLBZodVSVAQeYFOBn8BBfxICHlT2vWzDABJggZ5ZMaiZy67qidsb', '201403');
INSERT INTO `users` VALUES ('167', '13954654097', '王宗杰', '13954654097', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370521198612010023', '1', '2017-05-26 14:01:07', '2017-09-20 17:02:15', '2T9m4cS8iLlmLA3dYWqiuhsTgokKV3FgsbXDltjATA3ML2naF4vzNys1aL0a', '201404');
INSERT INTO `users` VALUES ('168', '13325057693', '王君', '13325057693', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370502198607140828', '1', '2017-05-26 14:01:08', '2017-09-20 17:34:27', 'UQLkmPNfL54XpIAsXCw8K1YUQVL19ifrouyLTBbPb4LMBZth2u2LKGWBJUVM', '201405');
INSERT INTO `users` VALUES ('169', '15265458922', '刘大鹏', '15265458922', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370781198803134819', '1', '2017-05-26 14:01:08', '2017-09-20 17:38:26', 'W3TYeM1MtbiKmacnzZLT2pu7qsYJP45D2dISGwyeiO5PA5NdfZKHezHGF9tL', '201406');
INSERT INTO `users` VALUES ('170', '13153679308', '秦国振', '13153679308', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '37072519871030307X', '1', '2017-05-26 14:01:08', '2017-09-20 16:40:46', 'vz2BBOGKQGUicjnVV3EOyO5cG9Hgv0qe6OeW3jQztODvD6eriitIDKDhkGI2', '201407');
INSERT INTO `users` VALUES ('171', '13053642150', '李媛媛', '13053642150', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370781198712287764', '1', '2017-05-26 14:01:09', '2017-09-20 16:43:23', 'zA0Yg511HtqCQQpUwnfNffsFWSpHexHtzh0g2VKB1x49snWt9WhUNCxy1UFA', '201408');
INSERT INTO `users` VALUES ('172', '15288813073', '孙迎', '15288813073', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370724198709217768', '1', '2017-05-26 14:01:09', '2017-09-20 16:51:28', '5u2bExNfg8ZFZHOn6JptelGGUHqQXj0JCcVmEevIsJLUtipZt1fdXXrs6tii', '201409');
INSERT INTO `users` VALUES ('173', '15169547362', '黄焱', '15169547362', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370702198801202229', '1', '2017-05-26 14:01:09', '2017-09-15 15:02:05', 'fqDWXelQ6JNUcU2xPAeFigP2FE7V0k0vfCmUjVd4rsSKiUPKLIzoux32fkUZ', '201410');
INSERT INTO `users` VALUES ('174', '18253631027', '王金源', '18253631027', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370724198805030792', '1', '2017-05-26 14:01:10', '2017-05-26 14:01:10', null, '201411');
INSERT INTO `users` VALUES ('175', '15688982855', '李杰玉', '15688982855', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370724198811096923', '1', '2017-05-26 14:02:10', '2017-05-26 14:02:10', null, '201412');
INSERT INTO `users` VALUES ('176', '13964779831', '王龙', '13964779831', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370725198802132378', '1', '2017-05-26 14:02:11', '2017-05-26 14:02:11', null, '201413');
INSERT INTO `users` VALUES ('177', '18553316852', '贾懿新', '18553316852', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '371521198901124669', '1', '2017-05-26 14:02:12', '2017-05-26 14:02:12', null, '201414');
INSERT INTO `users` VALUES ('178', '18654725209', '张录杰', '18654725209', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370784198801073353', '1', '2017-05-26 14:02:12', '2017-05-26 14:02:12', null, '201415');
INSERT INTO `users` VALUES ('179', '18654725208', '万欢', '18654725208', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '360123198704180029', '1', '2017-05-26 14:02:12', '2017-05-26 14:02:12', null, '201416');
INSERT INTO `users` VALUES ('180', '18463652512', '郑英杰', '18463652512', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370285198709120822', '1', '2017-05-26 14:02:13', '2017-05-26 14:02:13', null, '201417');
INSERT INTO `users` VALUES ('181', '13573627707', '张良', '13573627707', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370785198501140039', '1', '2017-05-26 14:02:13', '2017-05-28 09:15:30', null, '201419');
INSERT INTO `users` VALUES ('182', '15163670247', '程磊', '15163670247', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '371202198511026312', '1', '2017-05-26 14:02:13', '2017-05-26 14:02:13', null, '201420');
INSERT INTO `users` VALUES ('183', '18765763951', '苏冠凤', '18765763951', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370783198502186546', '1', '2017-05-26 14:02:13', '2017-05-26 14:02:13', null, '201421');
INSERT INTO `users` VALUES ('184', '15163607830', '于丽娜', '15163607830', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370703198509133728', '1', '2017-05-26 14:02:14', '2017-05-26 14:02:14', null, '201422');
INSERT INTO `users` VALUES ('185', '13791608217', '邓俏', '13791608217', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370704198911262428', '1', '2017-05-26 14:02:14', '2017-05-26 14:02:14', null, '201423');
INSERT INTO `users` VALUES ('186', '15953179219', '刘俊义', '15953179219', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370786198411226628', '1', '2017-05-26 14:02:14', '2017-05-26 14:02:14', null, '201424');
INSERT INTO `users` VALUES ('187', '15169690575', '孙温伟', '15169690575', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370685198706186510', '1', '2017-05-26 14:02:15', '2017-05-26 14:02:15', null, '201425');
INSERT INTO `users` VALUES ('188', '15163691186', '葛瑾', '15163691186', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370703198610020322', '1', '2017-05-26 14:02:15', '2017-05-26 14:02:15', null, '201426');
INSERT INTO `users` VALUES ('189', '15269604687', '袁辉', '15269604687', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370522198902051313', '1', '2017-05-26 14:02:15', '2017-05-26 14:02:15', null, '201427');
INSERT INTO `users` VALUES ('190', '13475363391', '许昕', '13475363391', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370702198901272224', '1', '2017-05-26 14:02:16', '2017-05-26 14:02:16', null, '201428');
INSERT INTO `users` VALUES ('191', '15165368710', '朱德友', '15165368710', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370725198802223974', '1', '2017-05-26 14:02:16', '2017-05-26 14:02:16', null, '201429');
INSERT INTO `users` VALUES ('192', '15854889283', '李方圆', '15854889283', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370503198901153511', '1', '2017-05-26 14:02:16', '2017-05-26 14:02:16', null, '201430');
INSERT INTO `users` VALUES ('193', '15169615579', '范国娟', '15169615579', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '37078419871005208X', '1', '2017-05-26 14:02:17', '2017-05-26 14:02:17', null, '201431');
INSERT INTO `users` VALUES ('194', '13006696739', '庞冲', '13006696739', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370302198712203631', '1', '2017-05-26 14:02:17', '2017-05-26 14:02:17', null, '201432');
INSERT INTO `users` VALUES ('195', '15169697600', '王海威', '15169697600', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370785198802205529', '1', '2017-05-26 14:02:17', '2017-05-26 14:02:17', null, '201433');
INSERT INTO `users` VALUES ('196', '18663615431', '张旭', '18663615431', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370126198808210422', '1', '2017-05-26 14:02:18', '2017-05-26 14:02:18', null, '201434');
INSERT INTO `users` VALUES ('197', '15854448271', '张继海', '15854448271', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370784198607232031', '1', '2017-05-26 14:02:18', '2017-05-26 14:02:18', null, '201435');
INSERT INTO `users` VALUES ('198', '18765687610', '杨昭颖', '18765687610', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370502198710301220', '1', '2017-05-26 14:02:18', '2017-05-26 14:02:18', null, '201436');
INSERT INTO `users` VALUES ('199', '13964638781', '张尊路', '13964638781', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370125198802246639', '1', '2017-05-26 14:02:19', '2017-05-26 14:02:19', null, '201437');
INSERT INTO `users` VALUES ('200', '15966086804', '王颖', '15966086804', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370702198712222228', '1', '2017-05-26 14:02:19', '2017-05-26 14:02:19', null, '201438');
INSERT INTO `users` VALUES ('201', '18363605828', '王文霞', '18363605828', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '37078419851015206x', '1', '2017-05-26 14:02:19', '2017-05-26 14:02:19', null, '201439');
INSERT INTO `users` VALUES ('202', '15069602630', '王琼', '15069602630', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370112198701014511', '1', '2017-05-26 14:02:19', '2017-05-26 14:02:19', null, '201440');
INSERT INTO `users` VALUES ('203', '15095247052', '嵇建刚', '15095247052', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370702198611105110', '1', '2017-05-26 14:02:20', '2017-05-26 14:02:20', null, '201441');
INSERT INTO `users` VALUES ('204', '18306467877', '傅凯丽', '18306467877', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370784198902164406', '1', '2017-05-26 14:02:20', '2017-05-26 14:02:20', null, '201442');
INSERT INTO `users` VALUES ('205', '13953617579', '赵欣', '13953617579', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370724198604241868', '1', '2017-05-26 14:02:20', '2017-05-26 14:02:20', null, '201443');
INSERT INTO `users` VALUES ('206', '15853621930', '宋磊', '15853621930', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370782198710125514', '1', '2017-05-26 14:02:20', '2017-05-26 14:02:20', null, '201444');
INSERT INTO `users` VALUES ('207', '18763686736', '赵婷', '18763686736', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '37072519851203004x', '1', '2017-05-26 14:02:21', '2017-05-26 14:02:21', null, '201445');
INSERT INTO `users` VALUES ('208', '15153646199', '闫振阳', '15153646199', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370784198612154330', '1', '2017-05-26 14:02:21', '2017-05-26 14:02:21', null, '201446');
INSERT INTO `users` VALUES ('209', '15966167169', '张丽萍', '15966167169', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370725198807030688', '1', '2017-05-26 14:02:21', '2017-05-26 14:02:21', null, '201447');
INSERT INTO `users` VALUES ('210', '15265444945', '周超', '15265444945', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370983198701173431', '1', '2017-05-26 14:02:22', '2017-05-26 14:02:22', null, '201448');
INSERT INTO `users` VALUES ('211', '15908017790', '姚玉强', '15908017790', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370784198710195056', '1', '2017-05-26 14:02:22', '2017-05-26 14:02:22', null, '201449');
INSERT INTO `users` VALUES ('212', '15165669356', '贾志明', '15165669356', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370725198804103618', '1', '2017-05-26 14:02:22', '2017-05-26 14:02:22', null, '201450');
INSERT INTO `users` VALUES ('213', '15306365821', '彭新宇', '15306365821', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370705198810082547', '1', '2017-05-26 14:02:23', '2017-05-26 14:02:23', null, '201451');
INSERT INTO `users` VALUES ('214', '18264483125', '张好元', '18264483125', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370725198406021473', '1', '2017-05-26 14:02:24', '2017-05-26 14:02:24', null, '201452');
INSERT INTO `users` VALUES ('215', '15963633687', '刘洪智', '15963633687', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370783198402124372', '1', '2017-05-26 14:02:24', '2017-05-26 14:02:24', null, '201453');
INSERT INTO `users` VALUES ('216', '15065622601', '曹李娟', '15065622601', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '370785198502109623', '1', '2017-05-26 14:02:24', '2017-05-26 14:02:24', null, '201454');
INSERT INTO `users` VALUES ('217', '15206364381', '刘仁昌', '15206364381', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '0.jpg', '123456789@163.com', null, '1', '37030619861124101x', '1', '2017-05-26 14:02:25', '2017-05-26 14:02:25', null, '201455');
INSERT INTO `users` VALUES ('225', '17000000000', '金威威', '17000000000', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@174.com', null, '1', '32132319860814281X', '1', '2017-05-31 10:24:30', '2017-05-31 10:24:30', null, '500');
INSERT INTO `users` VALUES ('226', '17000000001', '樊峻灏', '17000000001', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216278@163.com', null, '1', '320502199303273539', '1', '2017-05-31 10:24:31', '2017-09-20 15:41:29', 'RKhOlviHh4EDUkh7MXCzub0Db92Ahf4pTCNi1fOpXGsMziJ1XCIz4mcWeHTU', '501');
INSERT INTO `users` VALUES ('227', '17000000002', '韩怀顺', '17000000002', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@175.com', null, '1', '370832198810317654', '1', '2017-05-31 10:24:32', '2017-09-20 15:42:25', 'rAXJchcgpvGplMth4AaAjPXYglGPO3GmIyubYNBUMPa5k1N1bAnpe9Hybj5G', '502');
INSERT INTO `users` VALUES ('228', '17000000003', '周谷城', '17000000003', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216278@164.com', null, '1', '320581198810254034', '1', '2017-05-31 10:24:33', '2017-09-20 15:43:03', 'uPLInAgs6JjCGprTljfoIV84TSHup4dGHoFvZhZLXAwqtvRPcS03lUc6h8TT', '503');
INSERT INTO `users` VALUES ('230', '17000000005', '徐波', '17000000005', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216278@165.com', null, '1', '321283198809296410', '1', '2017-05-31 10:25:20', '2017-05-31 10:25:20', null, '505');
INSERT INTO `users` VALUES ('231', '17000000006', '张陈禹', '17000000006', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@177.com', null, '1', '320582199212222674', '1', '2017-05-31 10:25:20', '2017-05-31 10:25:20', null, '506');
INSERT INTO `users` VALUES ('232', '17000000007', '兰功伟', '17000000007', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@178.com', null, '1', '210404198002123913', '1', '2017-05-31 10:25:21', '2017-05-31 10:25:21', null, '507');
INSERT INTO `users` VALUES ('233', '17000000008', '王莹', '17000000008', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '/images/head.png', '1216277@179.com', null, '1', '210302198811200327', '1', '2017-05-31 10:25:22', '2017-05-31 10:25:22', null, '508');
INSERT INTO `users` VALUES ('234', '17000000009', '夏志军', '17000000009', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@179.com', null, '1', '320723197209253252', '1', '2017-05-31 10:25:23', '2017-05-31 10:25:23', null, '509');
INSERT INTO `users` VALUES ('238', '13800000001', '技能老师A', '13800000001', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/20170601112007_4BEZIy.jpg', 'sz@163.com', null, '1', '387821198606053728', '1', '2017-06-01 11:17:53', '2017-09-15 17:45:52', 'oLww9Pk5aVp931Naj5ZxMgEfMl2qdnOBjYE7mVSz6I32WB6Ws6Nvwo35Z42v', '853213');
INSERT INTO `users` VALUES ('239', '13800000002', '技能老师B', '13800000002', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/20170601112212_4zQDog.jpg', 'feisz@163.com', null, '1', '328533198807032313', '1', '2017-06-01 11:22:16', '2017-06-01 12:32:36', 'eHTG6HOAwsoPXa2mC19gcovv9bqFS90CJ4cCEoCrwuyEtXu5MOzhi7H5bit4', '636734');
INSERT INTO `users` VALUES ('240', '15000000003', '朱雷亮', '15000000003', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@178.com', null, '1', '341023199011146019', '1', '2017-06-01 13:47:37', '2017-09-20 15:42:56', 'SUveU3XnRDRjeY6cv4DlXH4htmEvtTFOigpJEfXt1tfxofqmTNeYPVtcsMcG', '507');
INSERT INTO `users` VALUES ('241', '13812652676', '孙杰', '13812652676', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/20180125144417_Btkipp.png', '13812652676@dd.com', null, '1', '320724199207041235', '1', '2017-06-01 13:47:38', '2018-01-25 15:23:08', '3bzzVlejr6bEVFcnPU2izVV5IYAYhcEWWBel7UBeWBXTgPRMn3awSwu4LZ1m', '13812652676');
INSERT INTO `users` VALUES ('246', '18055699255', '范典', '18055699255', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', 'phpinfo@foxmail.com', null, '1', '340822198406043913', '1', '2017-06-01 18:00:41', '2018-01-03 15:26:26', 'kDDctJUptpYGSySBlz7C2h8zabJjZb0QgCiPVMFKI9qkHXy81gfPCqaOyStJ', '6781');
INSERT INTO `users` VALUES ('247', '15000000002', '邹佳', '15000000002', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@177.com', null, '1', '320586199110302716', '1', '2017-07-17 15:24:50', '2017-09-15 15:00:52', 'rjDr3cC4Zigw70YYctMzmIXeqjcHDiDaZD4R8vzrnYlAJnuw2kDpd7XPRPrW', '506');
INSERT INTO `users` VALUES ('248', '15000000000', '刘飞', '15000000000', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@179.com', null, '1', '140211199404104714', '1', '2017-07-17 15:24:50', '2017-09-15 14:46:56', 'KujsVTjBK9kmfBJXGl8cGEC2P5rJtIqMDJZpeiEKB7eX72mrtxcCmMS7tWcc', '508');
INSERT INTO `users` VALUES ('249', '13699456677', '毛老师', '13699456677', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', 'maolaoshi@163.com', null, '1', '610324197905181938', '1', '2017-12-05 01:04:14', '2017-12-05 01:04:14', null, '888888');
INSERT INTO `users` VALUES ('250', '15928785618', '付老师', '15928785618', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '/images/head.png', 'fulaoshi@163.com', null, '1', '513901984011230155', '1', '2017-12-05 01:04:15', '2017-12-05 01:04:15', null, '777777');
INSERT INTO `users` VALUES ('251', '18625863256', '汪老师', '18625863256', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', 'wanglaoshi@163.com', null, '1', '411525197702172039', '1', '2017-12-05 01:04:17', '2017-12-05 01:04:17', null, '666666');
INSERT INTO `users` VALUES ('252', '13771729048', '佚名', '13771729048', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '222222@163.com', null, '1', '320586199208275217', '1', '2017-12-28 09:37:18', '2018-02-05 10:58:02', 'tujKNd6ZFyLuqCc48lX3D8EPedPkxf3hD54FweUOicltkgXTphgkfr3opsvs', '6777');
INSERT INTO `users` VALUES ('253', '13900000001', '邓超凡', '13900000001', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216271@174.com', null, '1', '400203197910220311', '1', '2018-01-08 14:15:51', '2018-01-08 17:03:17', 'EW2aUEbAvEq1hrjJXJFWeDxUhoX1IZYPpr0i6GX9KSFSkCkNBX7WA2CBNhmf', '6781');
INSERT INTO `users` VALUES ('254', '13900000002', '周强', '13900000002', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216272@175.com', null, '1', '400203197910220312', '1', '2018-01-08 14:15:52', '2018-01-08 14:15:52', null, '6782');
INSERT INTO `users` VALUES ('255', '13900000003', '刘源', '13900000003', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216273@176.com', null, '1', '400203197910220313', '1', '2018-01-08 14:15:52', '2018-01-08 14:15:52', null, '6783');
INSERT INTO `users` VALUES ('256', '13900000004', '唐政伟', '13900000004', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216274@177.com', null, '1', '400203197910220314', '1', '2018-01-08 14:15:52', '2018-01-08 14:15:52', null, '6784');
INSERT INTO `users` VALUES ('257', '13900000005', '徐敏', '13900000005', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '2', '', '', '', '', '', '', '', '/images/head.png', '1216275@178.com', null, '1', '400203197910220315', '1', '2018-01-08 14:15:53', '2018-01-08 14:15:53', null, '6785');
INSERT INTO `users` VALUES ('258', '13900000006', '邓爱莲', '13900000006', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216276@179.com', null, '1', '400203197910220316', '1', '2018-01-08 14:15:53', '2018-01-08 14:15:53', null, '6786');
INSERT INTO `users` VALUES ('259', '13900000007', '向虎', '13900000007', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216277@180.com', null, '1', '400203197910220317', '1', '2018-01-08 14:15:54', '2018-01-08 14:15:54', null, '6787');
INSERT INTO `users` VALUES ('260', '13900000008', '温婷婷', '13900000008', '$2y$10$6u5aPpJCfUEr5L7dbOd.Duh3DJhKV.qc4RWgmiHgje1fmhQQipiYO', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1216278@181.com', null, '1', '400203197910220318', '1', '2018-01-08 14:15:54', '2018-01-23 15:29:10', null, '1');
INSERT INTO `users` VALUES ('261', '15151416889', '朱伟', '15151416889', '$2y$10$sNCg2XKTNJF3UcgSm4PVHuhiI0dprqqtAxOCwXSEtSIHL0MZpUFJK', '', '1', '', '', '', '', '', '', '', '/images/head.png', '1232132@163.com', null, '1', '320586199208275218', '1', '2018-01-23 15:08:52', '2018-01-23 15:08:52', null, '6666');
INSERT INTO `users` VALUES ('262', '15151151515', '一个合', '15151151515', '$2y$10$SSYUHiqJdkemNE9XfcY0sejny5CnELTDAn9thDg26RFo9jnWdSD.u', '', '1', '', '', '', '', '', '', '', '/images/head.png', 'xssss@qq.com', null, '1', '320684199211151614', '1', '2018-02-05 10:58:02', '2018-02-05 10:58:02', null, '2222');

-- ----------------------------
-- Table structure for users_logs
-- ----------------------------
DROP TABLE IF EXISTS `users_logs`;
CREATE TABLE `users_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users_logs
-- ----------------------------

-- ----------------------------
-- Table structure for users_messages
-- ----------------------------
DROP TABLE IF EXISTS `users_messages`;
CREATE TABLE `users_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users_messages
-- ----------------------------

-- ----------------------------
-- Table structure for users_password_resets
-- ----------------------------
DROP TABLE IF EXISTS `users_password_resets`;
CREATE TABLE `users_password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `wx_openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users_password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for users_pm
-- ----------------------------
DROP TABLE IF EXISTS `users_pm`;
CREATE TABLE `users_pm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `accept_user_id` int(11) NOT NULL COMMENT '接收人id',
  `send_user_id` int(11) NOT NULL COMMENT '发出人id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未读 1=已读',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `module` varchar(32) DEFAULT NULL COMMENT '模块名称',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户站内信';

-- ----------------------------
-- Records of users_pm
-- ----------------------------
SET FOREIGN_KEY_CHECKS=1;
