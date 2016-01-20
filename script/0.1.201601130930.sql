-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 192.168.1.254    Database: sys_mis
-- ------------------------------------------------------
-- Server version	5.7.10

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `sys_mis`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `sys_mis` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `sys_mis`;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=468;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_access_token_scopes`
--

DROP TABLE IF EXISTS `oauth_access_token_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_access_token_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_access_token_scopes_access_token_id_index` (`access_token_id`),
  KEY `oauth_access_token_scopes_scope_id_index` (`scope_id`),
  CONSTRAINT `oauth_access_token_scopes_access_token_id_foreign` FOREIGN KEY (`access_token_id`) REFERENCES `oauth_access_tokens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_access_token_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `expire_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_access_tokens_id_session_id_unique` (`id`,`session_id`),
  KEY `oauth_access_tokens_session_id_index` (`session_id`),
  CONSTRAINT `oauth_access_tokens_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_auth_code_scopes`
--

DROP TABLE IF EXISTS `oauth_auth_code_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_auth_code_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_code_scopes_auth_code_id_index` (`auth_code_id`),
  KEY `oauth_auth_code_scopes_scope_id_index` (`scope_id`),
  CONSTRAINT `oauth_auth_code_scopes_auth_code_id_foreign` FOREIGN KEY (`auth_code_id`) REFERENCES `oauth_auth_codes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_auth_code_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expire_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_session_id_index` (`session_id`),
  CONSTRAINT `oauth_auth_codes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_client_endpoints`
--

DROP TABLE IF EXISTS `oauth_client_endpoints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_client_endpoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_client_endpoints_client_id_redirect_uri_unique` (`client_id`,`redirect_uri`),
  CONSTRAINT `oauth_client_endpoints_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_client_grants`
--

DROP TABLE IF EXISTS `oauth_client_grants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_client_grants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `grant_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_client_grants_client_id_index` (`client_id`),
  KEY `oauth_client_grants_grant_id_index` (`grant_id`),
  CONSTRAINT `oauth_client_grants_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `oauth_client_grants_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `oauth_grants` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_client_scopes`
--

DROP TABLE IF EXISTS `oauth_client_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_client_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_client_scopes_client_id_index` (`client_id`),
  KEY `oauth_client_scopes_scope_id_index` (`scope_id`),
  CONSTRAINT `oauth_client_scopes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_client_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_clients` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_clients_id_secret_unique` (`id`,`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_grant_scopes`
--

DROP TABLE IF EXISTS `oauth_grant_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_grant_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_grant_scopes_grant_id_index` (`grant_id`),
  KEY `oauth_grant_scopes_scope_id_index` (`scope_id`),
  CONSTRAINT `oauth_grant_scopes_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `oauth_grants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_grant_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_grants`
--

DROP TABLE IF EXISTS `oauth_grants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_grants` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `access_token_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `expire_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`access_token_id`),
  UNIQUE KEY `oauth_refresh_tokens_id_unique` (`id`),
  CONSTRAINT `oauth_refresh_tokens_access_token_id_foreign` FOREIGN KEY (`access_token_id`) REFERENCES `oauth_access_tokens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_scopes`
--

DROP TABLE IF EXISTS `oauth_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_scopes` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_session_scopes`
--

DROP TABLE IF EXISTS `oauth_session_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_session_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `scope_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_session_scopes_scope_id_index` (`scope_id`),
  KEY `oauth_session_scopes_session_id_index` (`session_id`),
  CONSTRAINT `oauth_session_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_session_scopes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_sessions`
--

DROP TABLE IF EXISTS `oauth_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `owner_type` enum('client','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `owner_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_redirect_uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `oauth_sessions_client_id_owner_type_owner_id_index` (`client_id`,`owner_type`,`owner_id`),
  CONSTRAINT `oauth_sessions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_config`
--

DROP TABLE IF EXISTS `sys_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_element`
--

DROP TABLE IF EXISTS `sys_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_element` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `page` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_failed_jobs`
--

DROP TABLE IF EXISTS `sys_failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_functions`
--

DROP TABLE IF EXISTS `sys_functions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_functions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pid` int(10) unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_group_role`
--

DROP TABLE IF EXISTS `sys_group_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_group_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_groups`
--

DROP TABLE IF EXISTS `sys_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_menus`
--

DROP TABLE IF EXISTS `sys_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `ico` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(10) unsigned NOT NULL,
  `descrition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_permission_element`
--

DROP TABLE IF EXISTS `sys_permission_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_permission_element` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_permission_function`
--

DROP TABLE IF EXISTS `sys_permission_function`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_permission_function` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `function_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_permission_menu`
--

DROP TABLE IF EXISTS `sys_permission_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_permission_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `menu_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_permissions`
--

DROP TABLE IF EXISTS `sys_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `itemid` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_role_permission`
--

DROP TABLE IF EXISTS `sys_role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_roles`
--

DROP TABLE IF EXISTS `sys_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=5461;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_user_group`
--

DROP TABLE IF EXISTS `sys_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_user_role`
--

DROP TABLE IF EXISTS `sys_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_validatecode`
--

DROP TABLE IF EXISTS `sys_validatecode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_validatecode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiretime` int(10) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=2048;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `idcard_type` tinyint(1) NOT NULL DEFAULT '1',
  `idcard` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_mobile_unique` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=309;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_logs`
--

DROP TABLE IF EXISTS `users_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_messages`
--

DROP TABLE IF EXISTS `users_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_password_resets`
--

DROP TABLE IF EXISTS `users_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `wx_openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_pm`
--

DROP TABLE IF EXISTS `users_pm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户站内信';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Current Database: `osce_mis`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `osce_mis` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `osce_mis`;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `code` varchar(32) DEFAULT NULL COMMENT '代码',
  `cate` varchar(32) NOT NULL COMMENT '类别',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='考试区域';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `area_vcr`
--

DROP TABLE IF EXISTS `area_vcr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_vcr` (
  `id` int(11) NOT NULL COMMENT '编号',
  `area_id` int(11) DEFAULT NULL COMMENT '区域_编号',
  `vcr_id` int(11) DEFAULT NULL COMMENT '摄像机_编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_32` (`area_id`),
  KEY `FK_Reference_33` (`vcr_id`),
  CONSTRAINT `FK_Reference_32` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`),
  CONSTRAINT `FK_Reference_33` FOREIGN KEY (`vcr_id`) REFERENCES `vcr` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试区域-摄像机关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bbs_topic`
--

DROP TABLE IF EXISTS `bbs_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `pid` int(11) DEFAULT '0' COMMENT '父id',
  `level` int(11) DEFAULT '0' COMMENT '层级',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='讨论区_主题';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) DEFAULT NULL COMMENT '名称',
  `code` varchar(32) DEFAULT NULL COMMENT '代码',
  `description` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='病例(考试内容)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '名称',
  `cate` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '类别',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '类型',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT '配置值',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '配置说明',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam`
--

DROP TABLE IF EXISTS `exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `code` varchar(32) DEFAULT NULL COMMENT '考试代码',
  `name` varchar(64) DEFAULT NULL COMMENT '考试名称',
  `begin_dt` datetime DEFAULT NULL COMMENT '开始时间',
  `end_dt` datetime DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '当前状态',
  `total` int(11) DEFAULT NULL COMMENT '总人数',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `description` text COMMENT '描述',
  `sequence_cate` tinyint(1) NOT NULL DEFAULT '1' COMMENT '排序方式',
  `sequence_mode` tinyint(2) NOT NULL DEFAULT '1' COMMENT '考试排序模式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='考试';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_flow`
--

DROP TABLE IF EXISTS `exam_flow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `flow_id` int(11) NOT NULL COMMENT '流程编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_48` (`flow_id`),
  KEY `FK_Reference_49` (`exam_id`),
  CONSTRAINT `FK_Reference_48` FOREIGN KEY (`flow_id`) REFERENCES `flows` (`id`),
  CONSTRAINT `FK_Reference_49` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试-流程关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_flow_room`
--

DROP TABLE IF EXISTS `exam_flow_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_flow_room` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `serialnumber` int(11) NOT NULL COMMENT '序号',
  `room_id` int(11) NOT NULL COMMENT '房间编号',
  `flow_id` int(11) NOT NULL COMMENT '流程编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_53` (`flow_id`),
  KEY `FK_exam_flow_room_room_id` (`room_id`),
  CONSTRAINT `FK_Reference_53` FOREIGN KEY (`flow_id`) REFERENCES `flows` (`id`),
  CONSTRAINT `FK_exam_flow_room_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试流程-房间关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_flow_station`
--

DROP TABLE IF EXISTS `exam_flow_station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_flow_station` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `serialnumber` int(11) NOT NULL COMMENT '序号',
  `station_id` int(11) NOT NULL COMMENT '考站编号',
  `flow_id` int(11) NOT NULL COMMENT '流程编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_46` (`flow_id`),
  KEY `FK_exam_flow_station_station_id` (`station_id`),
  CONSTRAINT `FK_Reference_46` FOREIGN KEY (`flow_id`) REFERENCES `flows` (`id`),
  CONSTRAINT `FK_exam_flow_station_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试流程-考站关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_plan`
--

DROP TABLE IF EXISTS `exam_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_plan` (
  `id` int(11) NOT NULL COMMENT '编号',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `exam_screening_id` int(11) NOT NULL COMMENT '场次编号',
  `student_id` int(11) NOT NULL COMMENT '考生编号',
  `station_id` int(11) NOT NULL COMMENT '考站编号',
  `room_id` int(11) NOT NULL COMMENT '房间编号',
  `begin_dt` datetime NOT NULL COMMENT '开始时间',
  `end_dt` datetime DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_exam_plan_station_id` (`station_id`),
  CONSTRAINT `FK_exam_plan_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试计划';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_queue`
--

DROP TABLE IF EXISTS `exam_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_queue` (
  `id` int(11) NOT NULL COMMENT '编号',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `exam_screening_id` int(11) NOT NULL COMMENT '场次编号',
  `student_id` int(11) NOT NULL COMMENT '考生编号',
  `station_id` int(11) NOT NULL COMMENT '考站编号',
  `room_id` int(11) NOT NULL COMMENT '房间编号',
  `begin_dt` datetime NOT NULL COMMENT '开始时间',
  `end_dt` datetime DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_exam_queue_station_id` (`station_id`),
  CONSTRAINT `FK_exam_queue_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试队列';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_room`
--

DROP TABLE IF EXISTS `exam_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_room` (
  `id` int(11) NOT NULL COMMENT '编号',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `room_id` int(11) NOT NULL COMMENT '房间编号',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_47` (`exam_id`),
  KEY `FK_exam_room_room_id` (`room_id`),
  CONSTRAINT `FK_Reference_47` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`),
  CONSTRAINT `FK_exam_room_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试房间关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_score`
--

DROP TABLE IF EXISTS `exam_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `exam_result_id` int(11) NOT NULL COMMENT '考试结果编号',
  `subject_id` int(11) NOT NULL COMMENT '考核项目编号',
  `standard_id` int(11) NOT NULL COMMENT '评分标准编号',
  `score` tinyint(4) DEFAULT NULL COMMENT '分值',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_27` (`exam_result_id`),
  KEY `FK_Reference_28` (`subject_id`),
  KEY `FK_Reference_29` (`standard_id`),
  CONSTRAINT `FK_Reference_27` FOREIGN KEY (`exam_result_id`) REFERENCES `test_result` (`id`),
  CONSTRAINT `FK_Reference_28` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`),
  CONSTRAINT `FK_Reference_29` FOREIGN KEY (`standard_id`) REFERENCES `standard` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考核评分详情';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_screening`
--

DROP TABLE IF EXISTS `exam_screening`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_screening` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `begin_dt` datetime DEFAULT NULL COMMENT '开始时间',
  `end_dt` datetime DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '当前状态',
  `sort` int(11) DEFAULT '0' COMMENT '序号',
  `total` int(11) DEFAULT '0' COMMENT '人数',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `nfc_tag` varchar(64) DEFAULT NULL COMMENT 'nfc标签',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_4` (`exam_id`),
  CONSTRAINT `FK_Reference_4` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='考试考场关系表(考试场次表)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_screening_student`
--

DROP TABLE IF EXISTS `exam_screening_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_screening_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `exam_screening_id` int(11) NOT NULL COMMENT '考试场次编号',
  `student_id` int(11) NOT NULL COMMENT '考生编号',
  `is_notity` tinyint(1) NOT NULL DEFAULT '0' COMMENT '已通知',
  `is_signin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '已签到',
  `is_end` tinyint(1) NOT NULL DEFAULT '0' COMMENT '考试场次终止',
  `signin_dt` datetime DEFAULT NULL COMMENT '签到时间',
  `watch_id` int(11) DEFAULT NULL COMMENT '腕表编号',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_20` (`student_id`),
  KEY `FK_Reference_21` (`exam_screening_id`),
  KEY `FK_Reference_22` (`watch_id`),
  CONSTRAINT `FK_Reference_20` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`),
  CONSTRAINT `FK_Reference_21` FOREIGN KEY (`exam_screening_id`) REFERENCES `exam_screening` (`id`),
  CONSTRAINT `FK_Reference_22` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试场次-学生关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exam_sp_teacher`
--

DROP TABLE IF EXISTS `exam_sp_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_sp_teacher` (
  `id` int(11) NOT NULL COMMENT '编号',
  `invite_id` int(11) DEFAULT NULL COMMENT '邀请编号',
  `exam_screening_id` int(11) NOT NULL COMMENT '考试场次编号',
  `case_id` int(11) DEFAULT NULL COMMENT '病例编号',
  `teacher_id` int(11) NOT NULL COMMENT '教师编号',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_38` (`invite_id`),
  KEY `FK_Reference_39` (`exam_screening_id`),
  KEY `FK_Reference_40` (`teacher_id`),
  CONSTRAINT `FK_Reference_38` FOREIGN KEY (`invite_id`) REFERENCES `invite` (`id`),
  CONSTRAINT `FK_Reference_39` FOREIGN KEY (`exam_screening_id`) REFERENCES `exam` (`id`),
  CONSTRAINT `FK_Reference_40` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试-sp老师关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flows`
--

DROP TABLE IF EXISTS `flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flows` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `description` text COMMENT '备注',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试流程';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inform_info`
--

DROP TABLE IF EXISTS `inform_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inform_info` (
  `id` int(11) NOT NULL COMMENT '编号',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `accept` varchar(32) NOT NULL COMMENT '接收人角色',
  `content` text COMMENT '内容',
  `attachments` text COMMENT '附件url地址数组',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_56` (`exam_id`),
  CONSTRAINT `FK_Reference_56` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资讯通知';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inform_training`
--

DROP TABLE IF EXISTS `inform_training`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inform_training` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `address` varchar(64) DEFAULT NULL COMMENT '地点',
  `begin_dt` datetime DEFAULT NULL COMMENT '开始时间',
  `end_dt` datetime DEFAULT NULL COMMENT '结束时间',
  `teacher` varchar(64) DEFAULT NULL COMMENT '讲师',
  `content` text COMMENT '内容',
  `attachments` text COMMENT '附件url地址数组',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `description` text COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='培训通知';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invite`
--

DROP TABLE IF EXISTS `invite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invite` (
  `id` int(11) NOT NULL COMMENT '编号',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `begin_dt` datetime NOT NULL COMMENT '开始时间',
  `end_dt` datetime NOT NULL COMMENT '结束时间',
  `exam_screening_id` int(11) NOT NULL COMMENT '考试场次编号',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_41` (`exam_screening_id`),
  CONSTRAINT `FK_Reference_41` FOREIGN KEY (`exam_screening_id`) REFERENCES `exam_screening` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='sp老师邀请';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pad`
--

DROP TABLE IF EXISTS `pad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pad` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `code` varchar(32) NOT NULL COMMENT '设备代码',
  `name` varchar(32) DEFAULT NULL COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PAD信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `nfc` varchar(32) DEFAULT NULL COMMENT 'NFC代码',
  `address` varchar(64) DEFAULT NULL COMMENT '位置',
  `code` varchar(32) DEFAULT NULL COMMENT '房间编码',
  `create_user_id` int(11) DEFAULT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `description` text COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='房间';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room_station`
--

DROP TABLE IF EXISTS `room_station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `room_id` int(11) NOT NULL COMMENT '房间编号',
  `station_id` int(11) NOT NULL COMMENT '考站编号',
  `create_user_id` int(11) DEFAULT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_room_station_room_id` (`room_id`),
  KEY `FK_room_station_station_id` (`station_id`),
  CONSTRAINT `FK_room_station_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`),
  CONSTRAINT `FK_room_station_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='房间-考站关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room_vcr`
--

DROP TABLE IF EXISTS `room_vcr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_vcr` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `room_id` int(11) NOT NULL COMMENT '房间_编号',
  `vcr_id` int(11) NOT NULL COMMENT '摄像机_编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_room_vcr_room_id` (`room_id`),
  KEY `FK_Reference_11` (`vcr_id`),
  CONSTRAINT `FK_Reference_11` FOREIGN KEY (`vcr_id`) REFERENCES `vcr` (`id`),
  CONSTRAINT `FK_room_vcr_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='房间-摄像机关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `standard`
--

DROP TABLE IF EXISTS `standard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `standard` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `subject_id` int(11) DEFAULT NULL COMMENT '考试项目编号',
  `content` text COMMENT '标准内容',
  `sort` tinyint(4) DEFAULT NULL COMMENT '序号',
  `score` tinyint(4) DEFAULT NULL COMMENT '分值',
  `pid` int(11) DEFAULT '0' COMMENT '父编号',
  `level` int(11) DEFAULT '0' COMMENT '层级',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `answer` text COMMENT '参考答案',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_3` (`subject_id`),
  CONSTRAINT `FK_Reference_3` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评分标准';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `station`
--

DROP TABLE IF EXISTS `station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) DEFAULT NULL COMMENT '名称',
  `code` varchar(32) DEFAULT NULL COMMENT '编码',
  `type` tinyint(4) NOT NULL COMMENT '考站类型',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `subject_id` int(11) NOT NULL COMMENT '考试项目编号',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `mins` int(11) NOT NULL DEFAULT '0' COMMENT '标准考试时间(分钟)',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_34` (`subject_id`),
  CONSTRAINT `FK_Reference_34` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='考站';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `station_case`
--

DROP TABLE IF EXISTS `station_case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `case_id` int(11) NOT NULL COMMENT '病例_编号',
  `station_id` int(11) NOT NULL COMMENT '考站_编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_7` (`case_id`),
  KEY `FK_station_case_station_id` (`station_id`),
  CONSTRAINT `FK_Reference_7` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `FK_station_case_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='考站-病例关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `station_teacher`
--

DROP TABLE IF EXISTS `station_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station_teacher` (
  `id` int(11) NOT NULL COMMENT '编号',
  `station_id` int(11) NOT NULL COMMENT '考站_编号',
  `user_id` int(11) NOT NULL COMMENT '教师_编号',
  `case_id` int(11) DEFAULT NULL COMMENT '病例_编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_35` (`case_id`),
  KEY `FK_Reference_37` (`user_id`),
  KEY `FK_station_teacher_station_id` (`station_id`),
  CONSTRAINT `FK_Reference_35` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `FK_Reference_37` FOREIGN KEY (`user_id`) REFERENCES `teacher` (`id`),
  CONSTRAINT `FK_station_teacher_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考站-老师关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `station_vcr`
--

DROP TABLE IF EXISTS `station_vcr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station_vcr` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `station_id` int(11) NOT NULL COMMENT '考站_编号',
  `vcr_id` int(11) NOT NULL COMMENT '录像机_编号',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_12` (`vcr_id`),
  KEY `FK_station_vcr_station_id` (`station_id`),
  CONSTRAINT `FK_Reference_12` FOREIGN KEY (`vcr_id`) REFERENCES `vcr` (`id`),
  CONSTRAINT `FK_station_vcr_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='考站-摄像机关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '姓名',
  `exam_id` int(11) NOT NULL COMMENT '考试编号',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户编号',
  `idcard` varchar(32) NOT NULL COMMENT '身份证号码',
  `mobile` varchar(11) NOT NULL COMMENT '手机号码',
  `code` varchar(32) DEFAULT NULL COMMENT '学号',
  `avator` varchar(128) DEFAULT NULL COMMENT '照片',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='考生';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `score` tinyint(4) NOT NULL COMMENT '满分',
  `sort` tinyint(4) DEFAULT NULL COMMENT '序号',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `description` text COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='考试项目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher` (
  `id` int(11) NOT NULL COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '姓名',
  `code` varchar(32) DEFAULT NULL COMMENT '工号',
  `type` tinyint(1) NOT NULL COMMENT '类型',
  `case_id` int(11) DEFAULT NULL COMMENT '病例编号',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_result`
--

DROP TABLE IF EXISTS `test_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_result` (
  `id` int(11) NOT NULL COMMENT '编号',
  `student_id` int(11) NOT NULL COMMENT '考生编号',
  `exam_screening_id` int(11) NOT NULL COMMENT '场次编号',
  `station_id` int(11) NOT NULL COMMENT '考站编号',
  `begin_dt` datetime NOT NULL COMMENT '考试开始时间',
  `end_dt` datetime NOT NULL COMMENT '考试结束时间',
  `time` int(11) DEFAULT NULL COMMENT '用时',
  `score` int(11) NOT NULL COMMENT '最终成绩',
  `score_dt` datetime NOT NULL COMMENT '评分时间',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `teacher_id` int(11) NOT NULL COMMENT '评分人编号',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_23` (`student_id`),
  KEY `FK_Reference_24` (`exam_screening_id`),
  KEY `FK_Reference_36` (`teacher_id`),
  KEY `FK_test_result_station_id` (`station_id`),
  CONSTRAINT `FK_Reference_23` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`),
  CONSTRAINT `FK_Reference_24` FOREIGN KEY (`exam_screening_id`) REFERENCES `exam_screening` (`id`),
  CONSTRAINT `FK_Reference_36` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`),
  CONSTRAINT `FK_test_result_station_id` FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试结果记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vcr`
--

DROP TABLE IF EXISTS `vcr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vcr` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `code` varchar(32) DEFAULT NULL COMMENT '代码',
  `ip` varchar(16) NOT NULL COMMENT 'IP地址',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `port` int(11) NOT NULL COMMENT '端口',
  `channel` varchar(32) NOT NULL COMMENT '通道',
  `description` text COMMENT '备注',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态',
  `created_user_id` int(11) NOT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='摄像机信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `watch`
--

DROP TABLE IF EXISTS `watch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `watch` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `code` varchar(32) NOT NULL COMMENT '设备代码',
  `name` varchar(32) DEFAULT NULL COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `description` varchar(255) DEFAULT NULL COMMENT '备注',
  `factory` varchar(255) DEFAULT NULL COMMENT '生产厂家',
  `sp` varchar(255) DEFAULT NULL COMMENT '规格',
  `create_user_id` int(11) DEFAULT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='腕表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `watch_log`
--

DROP TABLE IF EXISTS `watch_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `watch_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `watch_id` int(11) NOT NULL COMMENT '腕表编号',
  `action` varchar(32) NOT NULL COMMENT '动作类型',
  `context` text COMMENT '当前上下文(序列化)',
  `student_id` int(11) DEFAULT '0' COMMENT '绑定学生编号',
  `create_user_id` int(11) DEFAULT NULL COMMENT '创建人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `FK_Reference_55` (`watch_id`),
  KEY `FK_watch_log_student_id` (`student_id`),
  CONSTRAINT `FK_Reference_55` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`id`),
  CONSTRAINT `FK_watch_log_student_id` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='腕表使用历史';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-13 14:06:33
