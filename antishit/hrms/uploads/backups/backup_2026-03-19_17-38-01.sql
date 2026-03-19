-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hrms_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `applicants`
--

DROP TABLE IF EXISTS `applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('new','reviewing','interview','offered','hired','rejected') DEFAULT 'new',
  `interviewed_by` int(10) unsigned DEFAULT NULL,
  `interview_date` datetime DEFAULT NULL,
  `interview_notes` text DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `interviewed_by` (`interviewed_by`),
  KEY `idx_app_job` (`job_id`),
  KEY `idx_app_status` (`status`),
  CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`),
  CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`interviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicants`
--

LOCK TABLES `applicants` WRITE;
/*!40000 ALTER TABLE `applicants` DISABLE KEYS */;
INSERT INTO `applicants` VALUES (9,1,'Marco','Tan','marco.tan@email.com','09301234567',NULL,NULL,'interview',NULL,NULL,NULL,'LinkedIn','2026-03-18 14:33:56','2026-03-18 14:33:56'),(10,1,'Linda','Wong','linda.wong@email.com','09311234567',NULL,NULL,'reviewing',NULL,NULL,NULL,'JobStreet','2026-03-18 14:33:56','2026-03-18 14:33:56'),(11,2,'Bernard','Castro','bernard.c@email.com','09321234567',NULL,NULL,'new',NULL,NULL,NULL,'Referral','2026-03-18 14:33:56','2026-03-18 14:33:56'),(12,3,'Grace','Navarro','grace.n@email.com','09331234567',NULL,NULL,'reviewing',NULL,NULL,NULL,'Indeed','2026-03-18 14:33:56','2026-03-18 14:33:56');
/*!40000 ALTER TABLE `applicants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `clock_in` datetime DEFAULT NULL,
  `clock_out` datetime DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT 0.00,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `status` enum('present','absent','late','half_day','on_leave') DEFAULT 'present',
  `remarks` text DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_att_emp_date` (`employee_id`,`date`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_att_date` (`date`),
  KEY `idx_att_emp` (`employee_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (15,7,'2026-03-12','2026-03-12 08:02:00','2026-03-12 17:05:00',8.98,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(16,7,'2026-03-13','2026-03-13 08:15:00','2026-03-13 17:00:00',8.75,0.00,'late',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(17,7,'2026-03-14','2026-03-14 08:01:00','2026-03-14 17:05:00',9.07,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(18,8,'2026-03-12','2026-03-12 08:00:00','2026-03-12 17:00:00',9.00,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(19,8,'2026-03-13','2026-03-13 08:00:00','2026-03-13 17:00:00',9.00,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(20,9,'2026-03-12','2026-03-12 09:15:00','2026-03-12 17:00:00',7.75,0.00,'late',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(21,10,'2026-03-12','2026-03-12 08:05:00','2026-03-12 17:00:00',8.92,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(22,7,'2026-03-19','0000-00-00 00:00:00','0000-00-00 00:00:00',0.05,0.00,'late','',NULL,'2026-03-19 02:54:07','2026-03-19 04:09:47'),(23,10,'2026-03-18',NULL,NULL,0.00,0.00,'on_leave','',NULL,'2026-03-19 03:13:58','2026-03-19 03:48:59');
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `target_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_module` (`module`),
  KEY `idx_audit_date` (`created_at`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (5,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-18 14:34:08'),(6,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-18 14:34:17'),(7,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:36:13'),(8,1,'reject','leaves','Rejected leave request #11',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:46:19'),(9,1,'update','settings','Updated system settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:48:34'),(10,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:52:37'),(11,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:52:44'),(12,7,'request_leave','leaves','Submitted leave request',13,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:53:44'),(13,7,'clock_in','attendance','Employee clocked in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:54:07'),(14,7,'upload','documents','Uploaded document: zeri_1.jpg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:56:18'),(15,7,'clock_out','attendance','Employee clocked out',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:57:24'),(16,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:57:36'),(17,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:57:44'),(18,1,'update_profile','profile','User updated their profile',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:01:48'),(19,1,'create','payroll','Created payroll period: try',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:06:47'),(20,1,'create','payroll','Created payroll period: try',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:06:58'),(21,1,'reject','leaves','Rejected leave request #13',13,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:12:12'),(22,1,'approve','leaves','Approved leave request #12',12,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:13:58'),(23,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:15:35'),(24,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:15:42'),(25,7,'request_leave','leaves','Submitted leave request',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:16:24'),(26,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:17:50'),(27,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:17:58'),(28,1,'approve','leaves','Approved leave request #14',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:18:12'),(29,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:19:08'),(30,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:19:16'),(31,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:26:56'),(32,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:27:17'),(33,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:39:02'),(34,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:39:10'),(35,1,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:44:41'),(36,1,'edit','attendance','Edited attendance for emp #10 on 2026-03-18',10,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:49:00'),(37,1,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:58:39'),(38,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:07:59'),(39,1,'edit','attendance','Edited attendance for emp #7 on 2026-03-19',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:09:47'),(40,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:12'),(41,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:29'),(42,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:49'),(43,1,'update','employees','Updated employee #1',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:56'),(44,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:57'),(45,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:13:10'),(46,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:13:17'),(47,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:19:36'),(48,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:19:52'),(49,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:20:28'),(50,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:20:37'),(51,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:22:25'),(52,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:22:38'),(53,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:01'),(54,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:27'),(55,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:46'),(56,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:56'),(57,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:28:17'),(58,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:28:34'),(59,1,'update','employees','Updated employee #3',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:04'),(60,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:09'),(61,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:19'),(62,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:52'),(63,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:02'),(64,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:08'),(65,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:39'),(66,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:46'),(67,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:50'),(68,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:31:06'),(69,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:31:48'),(70,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:32:13'),(71,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:34:40'),(72,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:35:00'),(73,4,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:35:44'),(74,4,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:37:00'),(75,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:42:00'),(76,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:42:13'),(77,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:45:57'),(78,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:46:40'),(79,1,'create','payroll','Created payroll period: Verification Period',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:47:42'),(80,1,'generate','payroll','Generated payroll for period #3: 10 records',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:47:53'),(81,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:50:17'),(82,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:50:35'),(83,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:57:43'),(84,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:58:13'),(85,2,'approve','payroll','Approved payroll period #3',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:29:58'),(86,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:30:15'),(87,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:30:35'),(88,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:30:51'),(89,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:31:07'),(90,4,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:31:16'),(91,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:31:50'),(92,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:11'),(93,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:12'),(94,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:20'),(95,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:25'),(96,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:33:32'),(97,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:33:55'),(98,1,'create','employees','Added employee Rome Lorente',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:36:58'),(99,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:37:18'),(100,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:37:22'),(101,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:37:32'),(102,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:39:53'),(103,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:41:48'),(104,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:41:57'),(105,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:34'),(106,NULL,'login_fail','auth','Failed login attempt for: cruz@example.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:41'),(107,NULL,'login_fail','auth','Failed login attempt for: cruz@example.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:49'),(108,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:57'),(109,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:23'),(110,NULL,'login_fail','auth','Failed login attempt for: finance@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:26'),(111,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:34'),(112,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:46'),(113,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:49'),(114,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:45:41'),(115,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:45:43'),(116,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:47:21'),(117,NULL,'login_fail','auth','Failed login attempt for: Admin@1234superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:47:38'),(118,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:47:49'),(119,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:52:36'),(120,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:52:48'),(121,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:55:46'),(122,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:56:00'),(123,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:59:41'),(124,1,'approve','payroll','Approved payroll period #2',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:59:51'),(125,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:00:11'),(126,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:00:17'),(127,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:14:10'),(128,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:15:48'),(129,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:15:54'),(130,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:30:09'),(131,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:30:15'),(132,1,'update','settings','Updated system settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:30:39'),(133,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:31:55'),(134,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:32:09'),(135,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:35:20'),(136,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:35:30');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Human Resources','HR','People management and culture',2,1,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(2,'Finance','FIN','Financial operations',NULL,1,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(3,'Information Technology','IT','Software and infrastructure',3,1,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(4,'Operations','OPS','Day-to-day business operations',NULL,1,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(5,'Sales & Marketing','MKT','Revenue generation',NULL,1,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(6,'Administration','ADM','General administration',NULL,1,'2026-03-18 14:33:56','2026-03-18 14:33:56');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `category` enum('contract','id','certificate','policy','other') DEFAULT 'other',
  `filename` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(10) unsigned DEFAULT NULL,
  `uploaded_by` int(10) unsigned NOT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_doc_emp` (`employee_id`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,7,'iD','id','78b91622044fc8ce0d591910f15939d4.jpg','image/jpeg',239240,7,0,'2026-03-19 02:56:17');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `employee_number` varchar(30) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `position_id` int(10) unsigned NOT NULL,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','intern') DEFAULT 'full_time',
  `status` enum('active','inactive','resigned','terminated','on_leave') DEFAULT 'active',
  `date_hired` date NOT NULL,
  `date_regularized` date DEFAULT NULL,
  `date_separated` date DEFAULT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `civil_status` enum('single','married','widowed','divorced') DEFAULT NULL,
  `sss_number` varchar(30) DEFAULT NULL,
  `philhealth_number` varchar(30) DEFAULT NULL,
  `pagibig_number` varchar(30) DEFAULT NULL,
  `tin_number` varchar(30) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `employee_number` (`employee_number`),
  KEY `position_id` (`position_id`),
  KEY `manager_id` (`manager_id`),
  KEY `idx_dept` (`department_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `employees_ibfk_4` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,1,'EMP-0001',6,13,NULL,'full_time','active','2020-01-01',NULL,NULL,50000.00,'09171234568','','','1985-05-10','male','single','','','','','','','','2026-03-18 14:33:56','2026-03-19 04:12:56'),(2,2,'EMP-0002',1,1,NULL,'full_time','active','2020-01-15',NULL,NULL,95000.00,'09181234567',NULL,NULL,'1982-03-22','female',NULL,'01-3456789-0','12-456789012-3','2345-6789-0123','234-567-890-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(3,3,'EMP-0003',1,6,NULL,'full_time','active','2020-02-01',NULL,NULL,88000.00,'09191234567','','','1980-07-15','male','single','','','','','','','','2026-03-18 14:33:56','2026-03-19 04:29:04'),(4,4,'EMP-0004',2,4,NULL,'full_time','active','2020-03-01',NULL,NULL,82000.00,'09201234567',NULL,NULL,'1983-11-30','female',NULL,'01-5678901-2','12-678901234-5','4567-8901-2345','456-789-012-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(5,5,'EMP-0005',1,2,NULL,'full_time','active','2021-01-10',NULL,NULL,45000.00,'09211234567',NULL,NULL,'1990-06-18','female',NULL,'01-6789012-3','12-789012345-6','5678-9012-3456','567-890-123-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(6,6,'EMP-0006',1,3,NULL,'full_time','active','2021-06-01',NULL,NULL,38000.00,'09221234567',NULL,NULL,'1993-09-25','male',NULL,'01-7890123-4','12-890123456-7','6789-0123-4567','678-901-234-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(7,7,'EMP-0007',3,7,NULL,'full_time','active','2022-03-15',NULL,NULL,55000.00,'09231234567',NULL,NULL,'1995-01-10','male',NULL,'01-8901234-5','12-901234567-8','7890-1234-5678','789-012-345-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(8,8,'EMP-0008',5,12,NULL,'full_time','active','2022-07-01',NULL,NULL,32000.00,'09241234567',NULL,NULL,'1997-08-14','female',NULL,'01-9012345-6','12-012345678-9','8901-2345-6789','890-123-456-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(9,9,'EMP-0009',4,10,NULL,'full_time','active','2023-01-01',NULL,NULL,28000.00,'09251234567',NULL,NULL,'1999-03-05','male',NULL,'01-0123456-7','12-123456789-0','9012-3456-7890','901-234-567-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(10,10,'EMP-0010',5,12,NULL,'full_time','active','2023-06-01',NULL,NULL,30000.00,'09261234567',NULL,NULL,'1998-12-20','female',NULL,'01-1234567-8','12-234567890-1','0123-4567-8901','012-345-678-000',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(11,11,'EMP-0011',3,6,7,'full_time','active','2026-03-19',NULL,NULL,250000.00,'09124181359','test city','rwat','2004-06-20','male','single','056404056456','0456045645604','04564560450645','045645064560','Enzo Santos','04564506546045',NULL,'2026-03-19 08:36:58','2026-03-19 08:36:58');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `position_id` int(10) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `salary_min` decimal(12,2) DEFAULT NULL,
  `salary_max` decimal(12,2) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','intern') DEFAULT 'full_time',
  `vacancies` int(11) DEFAULT 1,
  `status` enum('open','closed','on_hold','filled') DEFAULT 'open',
  `posted_by` int(10) unsigned NOT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `position_id` (`position_id`),
  KEY `posted_by` (`posted_by`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'Software Developer',3,7,'Looking for a skilled PHP/Laravel developer.','3+ years PHP, MySQL, REST APIs',NULL,NULL,'full_time',2,'open',6,'2026-04-17','2026-03-18 14:33:56','2026-03-18 14:33:56'),(2,'Sales Representative',5,12,'Dynamic sales rep needed.','1+ year sales experience',NULL,NULL,'full_time',3,'open',6,'2026-05-02','2026-03-18 14:33:56','2026-03-18 14:33:56'),(3,'HR Specialist',1,2,'HR Specialist to support recruitment and engagement.','HR background, 2 years exp',NULL,NULL,'full_time',1,'open',6,'2026-04-07','2026-03-18 14:33:56','2026-03-18 14:33:56');
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kpis`
--

DROP TABLE IF EXISTS `kpis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT 1.00,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `kpis_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kpis`
--

LOCK TABLES `kpis` WRITE;
/*!40000 ALTER TABLE `kpis` DISABLE KEYS */;
INSERT INTO `kpis` VALUES (7,'Quality of Work','Accuracy and quality of output',NULL,1.50,1),(8,'Productivity','Volume and efficiency of work completed',NULL,1.50,1),(9,'Communication Skills','Clarity and effectiveness of communication',NULL,1.00,1),(10,'Teamwork','Collaboration and team support',NULL,1.00,1),(11,'Initiative','Proactively taking action beyond duties',NULL,1.00,1),(12,'Attendance & Punctuality','Consistent attendance and punctuality',NULL,1.00,1);
/*!40000 ALTER TABLE `kpis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_balances`
--

DROP TABLE IF EXISTS `leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_balances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `year` year(4) NOT NULL,
  `allocated` decimal(5,2) DEFAULT 0.00,
  `used` decimal(5,2) DEFAULT 0.00,
  `remaining` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lb` (`employee_id`,`leave_type_id`,`year`),
  KEY `leave_type_id` (`leave_type_id`),
  CONSTRAINT `leave_balances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leave_balances_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_balances`
--

LOCK TABLES `leave_balances` WRITE;
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
INSERT INTO `leave_balances` VALUES (41,1,1,2026,15.00,0.00,15.00),(42,1,2,2026,15.00,0.00,15.00),(43,1,5,2026,3.00,0.00,3.00),(44,1,6,2026,30.00,0.00,30.00),(45,2,1,2026,15.00,0.00,15.00),(46,2,2,2026,15.00,0.00,15.00),(47,2,5,2026,3.00,0.00,3.00),(48,2,6,2026,30.00,0.00,30.00),(49,3,1,2026,15.00,0.00,15.00),(50,3,2,2026,15.00,0.00,15.00),(51,3,5,2026,3.00,0.00,3.00),(52,3,6,2026,30.00,0.00,30.00),(53,4,1,2026,15.00,0.00,15.00),(54,4,2,2026,15.00,0.00,15.00),(55,4,5,2026,3.00,0.00,3.00),(56,4,6,2026,30.00,0.00,30.00),(57,5,1,2026,15.00,0.00,15.00),(58,5,2,2026,15.00,0.00,15.00),(59,5,5,2026,3.00,0.00,3.00),(60,5,6,2026,30.00,0.00,30.00),(61,6,1,2026,15.00,0.00,15.00),(62,6,2,2026,15.00,0.00,15.00),(63,6,5,2026,3.00,0.00,3.00),(64,6,6,2026,30.00,0.00,30.00),(65,7,1,2026,15.00,0.00,15.00),(66,7,2,2026,15.00,0.00,15.00),(67,7,5,2026,3.00,1.00,2.00),(68,7,6,2026,30.00,0.00,30.00),(69,8,1,2026,15.00,0.00,15.00),(70,8,2,2026,15.00,0.00,15.00),(71,8,5,2026,3.00,0.00,3.00),(72,8,6,2026,30.00,0.00,30.00),(73,9,1,2026,15.00,0.00,15.00),(74,9,2,2026,15.00,0.00,15.00),(75,9,5,2026,3.00,0.00,3.00),(76,9,6,2026,30.00,0.00,30.00),(77,10,1,2026,15.00,0.00,15.00),(78,10,2,2026,15.00,1.00,14.00),(79,10,5,2026,3.00,0.00,3.00),(80,10,6,2026,30.00,0.00,30.00),(81,11,5,2026,3.00,0.00,3.00),(82,11,3,2026,105.00,0.00,105.00),(83,11,4,2026,7.00,0.00,7.00),(84,11,2,2026,15.00,0.00,15.00),(85,11,6,2026,30.00,0.00,30.00),(86,11,1,2026,15.00,0.00,15.00);
/*!40000 ALTER TABLE `leave_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `days_allowed` int(11) DEFAULT 15,
  `is_paid` tinyint(1) DEFAULT 1,
  `carry_forward` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES (1,'Vacation Leave','VL',15,1,1,'Annual vacation leave',1),(2,'Sick Leave','SL',15,1,0,'Medical/health leave',1),(3,'Maternity Leave','ML',105,1,0,'For female employees',1),(4,'Paternity Leave','PL',7,1,0,'For male employees upon birth of child',1),(5,'Emergency Leave','EL',3,1,0,'Family emergencies',1),(6,'Unpaid Leave','UL',30,0,0,'Leave without pay',1);
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_requested` decimal(5,2) NOT NULL DEFAULT 1.00,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `reviewed_by` int(10) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_leave_emp` (`employee_id`),
  KEY `idx_leave_status` (`status`),
  CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `leaves_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  CONSTRAINT `leaves_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
INSERT INTO `leaves` VALUES (9,7,1,'2026-03-10','2026-03-12',3.00,'Family vacation trip','approved',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(10,8,2,'2026-03-05','2026-03-05',1.00,'Doctor appointment','approved',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(11,9,1,'2026-03-20','2026-03-21',2.00,'Personal matters','rejected',1,'2026-03-19 02:46:19','',NULL,'2026-03-18 14:33:56','2026-03-19 02:46:19'),(12,10,2,'2026-03-18','2026-03-18',1.00,'Not feeling well','approved',1,'2026-03-19 03:13:58','',NULL,'2026-03-18 14:33:56','2026-03-19 03:13:58'),(13,7,5,'2026-03-19','2026-03-22',2.00,'i need to go to hospital','rejected',1,'2026-03-19 03:12:12','nooo','080780f79c405c9cdda1750e231f56f7.jpg','2026-03-19 02:53:44','2026-03-19 03:12:12'),(14,7,5,'2026-03-19','2026-03-19',1.00,'tinatamad ako','approved',1,'2026-03-19 03:18:12','','39960b2fac4910a15b2b2eb32d99f64e.jpg','2026-03-19 03:16:24','2026-03-19 03:18:12');
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger') DEFAULT 'info',
  `module` varchar(100) DEFAULT NULL,
  `module_id` int(10) unsigned DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  KEY `idx_notif_unread` (`user_id`,`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (7,7,'Leave Approved','Your vacation leave (Mar 10-12) has been approved.','success','leaves',NULL,1,'2026-03-18 14:33:56'),(8,9,'Leave Pending','Your leave request is under review.','info','leaves',NULL,0,'2026-03-18 14:33:56'),(9,2,'New Leave Request','Employee Juan Dela Cruz submitted a leave request.','info','leaves',NULL,1,'2026-03-18 14:33:56'),(10,7,'Leave Request Submitted','Your leave request is pending review.','info','leaves',NULL,1,'2026-03-19 02:53:44'),(11,10,'Leave Approved','Your leave request has been approved.','success','leaves',NULL,0,'2026-03-19 03:13:58'),(12,7,'Leave Request Submitted','Your leave request is pending review.','info','leaves',NULL,1,'2026-03-19 03:16:24'),(13,7,'Leave Approved','Your leave request has been approved.','success','leaves',NULL,1,'2026-03-19 03:18:12'),(14,1,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,1,'2026-03-19 08:58:03'),(15,2,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(16,3,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(17,4,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,1,'2026-03-19 08:58:03'),(18,5,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(19,6,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(20,7,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(21,8,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(22,9,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(23,10,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(29,1,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,1,'2026-03-19 08:59:51'),(30,2,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(31,3,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(32,4,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,1,'2026-03-19 08:59:51'),(33,5,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(34,6,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(35,7,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(36,8,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(37,9,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(38,10,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(44,1,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,1,'2026-03-19 09:11:21'),(45,2,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(46,3,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(47,4,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,1,'2026-03-19 09:11:21'),(48,5,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(49,6,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(50,7,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(51,8,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(52,9,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(53,10,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll`
--

DROP TABLE IF EXISTS `payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `days_worked` decimal(5,2) NOT NULL DEFAULT 0.00,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `overtime_pay` decimal(12,2) DEFAULT 0.00,
  `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sss_deduction` decimal(12,2) DEFAULT 0.00,
  `philhealth_deduction` decimal(12,2) DEFAULT 0.00,
  `pagibig_deduction` decimal(12,2) DEFAULT 0.00,
  `tax_deduction` decimal(12,2) DEFAULT 0.00,
  `other_deductions` decimal(12,2) DEFAULT 0.00,
  `total_deductions` decimal(12,2) DEFAULT 0.00,
  `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(12,2) DEFAULT 0.00,
  `bonuses` decimal(12,2) DEFAULT 0.00,
  `status` enum('draft','approved','paid') DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payroll` (`period_id`,`employee_id`),
  KEY `idx_payroll_period` (`period_id`),
  KEY `idx_payroll_emp` (`employee_id`),
  CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `payroll_periods` (`id`),
  CONSTRAINT `payroll_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll`
--

LOCK TABLES `payroll` WRITE;
/*!40000 ALTER TABLE `payroll` DISABLE KEYS */;
INSERT INTO `payroll` VALUES (1,2,1,50000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(2,2,2,95000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(3,2,3,88000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(4,2,4,82000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(5,2,5,45000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(6,2,6,38000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(7,2,7,55000.00,4.00,0.00,0.00,15714.29,707.14,785.71,100.00,0.00,0.00,1592.86,14121.43,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(8,2,8,32000.00,2.00,0.00,0.00,4571.43,205.71,228.57,91.43,0.00,0.00,525.71,4045.71,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(9,2,9,28000.00,1.00,0.00,0.00,2000.00,90.00,100.00,40.00,0.00,0.00,230.00,1770.00,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(10,2,10,30000.00,1.00,0.00,0.00,2142.86,96.43,107.14,42.86,0.00,0.00,246.43,1896.43,0.00,0.00,'approved',NULL,'2026-03-19 03:44:41','2026-03-19 08:59:51'),(41,3,1,50000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(42,3,2,95000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(43,3,3,88000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(44,3,4,82000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(45,3,5,45000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(46,3,6,38000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(47,3,7,55000.00,3.00,0.00,0.00,16500.00,742.50,825.00,100.00,0.00,0.00,1667.50,14832.50,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(48,3,8,32000.00,2.00,0.00,0.00,6400.00,288.00,320.00,100.00,0.00,0.00,708.00,5692.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(49,3,9,28000.00,1.00,0.00,0.00,2800.00,126.00,140.00,56.00,0.00,0.00,322.00,2478.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21'),(50,3,10,30000.00,1.00,0.00,0.00,3000.00,135.00,150.00,60.00,0.00,0.00,345.00,2655.00,0.00,0.00,'approved',NULL,'2026-03-19 04:47:53','2026-03-19 09:11:21');
/*!40000 ALTER TABLE `payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_periods`
--

DROP TABLE IF EXISTS `payroll_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_periods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pay_date` date NOT NULL,
  `status` enum('draft','processing','approved','paid') DEFAULT 'draft',
  `created_by` int(10) unsigned NOT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `payroll_periods_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `payroll_periods_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_periods`
--

LOCK TABLES `payroll_periods` WRITE;
/*!40000 ALTER TABLE `payroll_periods` DISABLE KEYS */;
INSERT INTO `payroll_periods` VALUES (1,'try','2026-03-01','2026-03-19','2026-03-19','draft',1,NULL,NULL,'2026-03-19 03:06:47'),(2,'try','2026-03-01','2026-03-19','2026-03-19','approved',1,1,'2026-03-19 08:59:51','2026-03-19 03:06:58'),(3,'Verification Period','2026-03-01','2026-03-15','2026-03-20','approved',1,1,'2026-03-19 09:11:21','2026-03-19 04:47:42');
/*!40000 ALTER TABLE `payroll_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_kpi_scores`
--

DROP TABLE IF EXISTS `performance_kpi_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performance_kpi_scores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` int(10) unsigned NOT NULL,
  `kpi_id` int(10) unsigned NOT NULL,
  `score` decimal(3,1) NOT NULL,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  KEY `kpi_id` (`kpi_id`),
  CONSTRAINT `performance_kpi_scores_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `performance_reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `performance_kpi_scores_ibfk_2` FOREIGN KEY (`kpi_id`) REFERENCES `kpis` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_kpi_scores`
--

LOCK TABLES `performance_kpi_scores` WRITE;
/*!40000 ALTER TABLE `performance_kpi_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `performance_kpi_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_reviews`
--

DROP TABLE IF EXISTS `performance_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performance_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `reviewer_id` int(10) unsigned NOT NULL,
  `review_period` varchar(50) NOT NULL,
  `review_date` date NOT NULL,
  `overall_rating` decimal(3,1) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `improvements` text DEFAULT NULL,
  `goals_next_period` text DEFAULT NULL,
  `status` enum('draft','submitted','acknowledged') DEFAULT 'draft',
  `employee_ack` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `reviewer_id` (`reviewer_id`),
  CONSTRAINT `performance_reviews_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `performance_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_reviews`
--

LOCK TABLES `performance_reviews` WRITE;
/*!40000 ALTER TABLE `performance_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `performance_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_perm` (`module`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','view','View dashboard'),(2,'employees','view','View employees'),(3,'employees','create','Add employee'),(4,'employees','edit','Edit employee'),(5,'employees','delete','Delete employee'),(6,'attendance','view','View attendance'),(7,'attendance','manage','Manage attendance'),(8,'leaves','view','View leaves'),(9,'leaves','request','Request leave'),(10,'leaves','approve','Approve/reject leaves'),(11,'payroll','view','View payroll'),(12,'payroll','generate','Generate payroll'),(13,'payroll','approve','Approve payroll'),(14,'recruitment','view','View jobs & applicants'),(15,'recruitment','manage','Manage recruitment'),(16,'performance','view','View reviews'),(17,'performance','manage','Manage reviews'),(18,'training','view','View trainings'),(19,'training','manage','Manage trainings'),(20,'documents','view','View documents'),(21,'documents','upload','Upload documents'),(22,'documents','delete','Delete documents'),(23,'reports','view','View reports'),(24,'reports','export','Export reports'),(25,'notifications','view','View notifications'),(26,'audit','view','View audit logs'),(27,'settings','view','View settings'),(28,'settings','manage','Manage settings');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `salary_min` decimal(12,2) DEFAULT 0.00,
  `salary_max` decimal(12,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'HR Director',1,80000.00,120000.00,1,'2026-03-18 14:33:56'),(2,'HR Specialist',1,35000.00,55000.00,1,'2026-03-18 14:33:56'),(3,'Recruitment Officer',1,30000.00,50000.00,1,'2026-03-18 14:33:56'),(4,'Finance Manager',2,70000.00,100000.00,1,'2026-03-18 14:33:56'),(5,'Accountant',2,30000.00,50000.00,1,'2026-03-18 14:33:56'),(6,'IT Manager',3,75000.00,110000.00,1,'2026-03-18 14:33:56'),(7,'Software Developer',3,40000.00,80000.00,1,'2026-03-18 14:33:56'),(8,'System Administrator',3,35000.00,60000.00,1,'2026-03-18 14:33:56'),(9,'Operations Manager',4,60000.00,90000.00,1,'2026-03-18 14:33:56'),(10,'Operations Staff',4,20000.00,35000.00,1,'2026-03-18 14:33:56'),(11,'Sales Manager',5,55000.00,85000.00,1,'2026-03-18 14:33:56'),(12,'Sales Representative',5,25000.00,40000.00,1,'2026-03-18 14:33:56'),(13,'Admin Officer',6,20000.00,30000.00,1,'2026-03-18 14:33:56');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,27),(3,1),(3,2),(3,4),(3,6),(3,7),(3,8),(3,10),(3,16),(3,17),(3,18),(3,20),(3,23),(3,25),(4,1),(4,2),(4,11),(4,12),(4,13),(4,23),(4,24),(4,25),(5,1),(5,2),(5,3),(5,4),(5,6),(5,7),(5,8),(5,10),(5,20),(5,21),(5,22),(5,23),(5,25),(6,1),(6,2),(6,14),(6,15),(6,20),(6,21),(6,25),(7,1),(7,6),(7,8),(7,9),(7,11),(7,16),(7,18),(7,20),(7,21),(7,25);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','super_admin','Full system control','2026-03-18 14:33:56'),(2,'HR Director','hr_director','Full HR lifecycle management','2026-03-18 14:33:56'),(3,'Department Manager','department_manager','Manage department employees','2026-03-18 14:33:56'),(4,'Finance Manager','finance_manager','Payroll and financial operations','2026-03-18 14:33:56'),(5,'HR Specialist','hr_specialist','Employee records and attendance','2026-03-18 14:33:56'),(6,'Recruitment Officer','recruitment_officer','Recruitment and onboarding','2026-03-18 14:33:56'),(7,'Employee','employee','Self-service access','2026-03-18 14:33:56');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_grades`
--

DROP TABLE IF EXISTS `salary_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_grades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade` varchar(20) NOT NULL,
  `basic_min` decimal(12,2) NOT NULL,
  `basic_max` decimal(12,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_grades`
--

LOCK TABLES `salary_grades` WRITE;
/*!40000 ALTER TABLE `salary_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `salary_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key_name` varchar(150) NOT NULL,
  `value` text DEFAULT NULL,
  `label` varchar(200) DEFAULT NULL,
  `group_name` varchar(100) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (14,'company_name','HRMS Pro Corporation','Company Name','general','2026-03-18 14:33:56'),(15,'company_address','123 Business Ave, Makati City, Philippines','Company Address','general','2026-03-18 14:33:56'),(16,'company_phone','+63 2 8888 0000','Company Phone','general','2026-03-18 14:33:56'),(17,'company_email','info@hrmspro.com','Company Email','general','2026-03-18 14:33:56'),(18,'work_start_time','08:00','Work Start Time','attendance','2026-03-18 14:33:56'),(19,'work_end_time','17:00','Work End Time','attendance','2026-03-18 14:33:56'),(20,'late_threshold_min','15','Late Threshold (mins)','attendance','2026-03-18 14:33:56'),(21,'overtime_rate','1.25','Overtime Rate','payroll','2026-03-18 14:33:56'),(22,'sss_employee_rate','0.045','SSS Employee Rate','payroll','2026-03-18 14:33:56'),(23,'philhealth_rate','0.05','PhilHealth Rate','payroll','2026-03-18 14:33:56'),(24,'pagibig_rate','0.02','Pag-IBIG Rate','payroll','2026-03-18 14:33:56'),(25,'currency_symbol','₱','Currency Symbol','general','2026-03-18 14:33:56'),(26,'date_format','M d, Y','Date Format','general','2026-03-18 14:33:56'),(31,'work_hours_per_day','8',NULL,'general','2026-03-19 02:48:34'),(33,'late_deduction_rate','0',NULL,'general','2026-03-19 02:48:34'),(34,'sss_employer_rate','8',NULL,'general','2026-03-19 02:48:34'),(35,'office_start_time','08:00',NULL,'general','2026-03-19 02:48:34'),(36,'office_end_time','17:00',NULL,'general','2026-03-19 02:48:34'),(37,'late_grace_period','15',NULL,'general','2026-03-19 02:48:34');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_enrollments`
--

DROP TABLE IF EXISTS `training_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_enrollments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `status` enum('enrolled','completed','absent','cancelled') DEFAULT 'enrolled',
  `score` decimal(5,2) DEFAULT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_enroll` (`training_id`,`employee_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `training_enrollments_ibfk_1` FOREIGN KEY (`training_id`) REFERENCES `trainings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_enrollments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_enrollments`
--

LOCK TABLES `training_enrollments` WRITE;
/*!40000 ALTER TABLE `training_enrollments` DISABLE KEYS */;
INSERT INTO `training_enrollments` VALUES (7,1,3,'enrolled',NULL,NULL,'2026-03-18 14:33:56'),(8,1,4,'enrolled',NULL,NULL,'2026-03-18 14:33:56'),(9,1,5,'enrolled',NULL,NULL,'2026-03-18 14:33:56'),(10,1,6,'enrolled',NULL,NULL,'2026-03-18 14:33:56'),(11,1,7,'enrolled',NULL,NULL,'2026-03-18 14:33:56'),(12,1,8,'enrolled',NULL,NULL,'2026-03-18 14:33:56');
/*!40000 ALTER TABLE `training_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainings`
--

DROP TABLE IF EXISTS `trainings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `trainer` varchar(150) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT 0.00,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainings`
--

LOCK TABLES `trainings` WRITE;
/*!40000 ALTER TABLE `trainings` DISABLE KEYS */;
INSERT INTO `trainings` VALUES (1,'Data Privacy Act Compliance','Annual DPA training for all employees','Legal Team','2026-03-18','2026-03-19','Conference Room A',30,0.00,'scheduled',2,'2026-03-18 14:33:56'),(2,'Leadership Development Program','Management skills enhancement','HR Consulting Group','2026-03-25','2026-03-27','Training Center',15,0.00,'scheduled',2,'2026-03-18 14:33:56');
/*!40000 ALTER TABLE `trainings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expiry` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'superadmin@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','System','Administrator','e6ef74d4c0ac5e15c7c9644991b8e797.jpg',1,'2026-03-19 09:35:30',NULL,NULL,'2026-03-18 14:33:56','2026-03-19 09:35:30'),(2,2,'hrdirector@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Maria','Santos',NULL,1,'2026-03-19 04:50:35',NULL,NULL,'2026-03-18 14:33:56','2026-03-19 04:50:35'),(3,3,'manager.it@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Jose','Reyes',NULL,1,'2026-03-19 04:32:13',NULL,NULL,'2026-03-18 14:33:56','2026-03-19 04:32:13'),(4,4,'finance@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Ana','Cruz',NULL,1,'2026-03-19 08:56:00',NULL,NULL,'2026-03-18 14:33:56','2026-03-19 08:56:00'),(5,5,'hrspecialist@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Lorna','Garcia',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(6,6,'recruiter@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Ryan','Torres',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(7,7,'employee@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Juan','Dela Cruz',NULL,1,'2026-03-19 09:00:17',NULL,NULL,'2026-03-18 14:33:56','2026-03-19 09:00:17'),(8,7,'employee2@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Rosa','Mendoza',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(9,7,'employee3@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Carlo','Buenaventura',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(10,7,'employee4@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942','Patricia','Villanueva',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(11,2,'lorenteromejoseph@gmail.com','$2y$12$tgmTNZzpKs0hvfpab3lzpuSkT/1T7PswDJ2.KV8wyHxfB.6FA7BtW','Rome','Lorente',NULL,1,'2026-03-19 08:37:22',NULL,NULL,'2026-03-19 08:36:58','2026-03-19 08:37:22');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_attendance_summary`
--

DROP TABLE IF EXISTS `v_attendance_summary`;
/*!50001 DROP VIEW IF EXISTS `v_attendance_summary`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_attendance_summary` AS SELECT
 1 AS `employee_id`,
  1 AS `full_name`,
  1 AS `department_name`,
  1 AS `yr`,
  1 AS `mth`,
  1 AS `total_days`,
  1 AS `present`,
  1 AS `absent`,
  1 AS `late`,
  1 AS `on_leave`,
  1 AS `total_hours` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_employees`
--

DROP TABLE IF EXISTS `v_employees`;
/*!50001 DROP VIEW IF EXISTS `v_employees`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_employees` AS SELECT
 1 AS `id`,
  1 AS `user_id`,
  1 AS `employee_number`,
  1 AS `employment_type`,
  1 AS `status`,
  1 AS `date_hired`,
  1 AS `basic_salary`,
  1 AS `phone`,
  1 AS `birth_date`,
  1 AS `gender`,
  1 AS `department_id`,
  1 AS `position_id`,
  1 AS `manager_id`,
  1 AS `first_name`,
  1 AS `last_name`,
  1 AS `full_name`,
  1 AS `email`,
  1 AS `avatar`,
  1 AS `user_active`,
  1 AS `department_name`,
  1 AS `department_code`,
  1 AS `position_title`,
  1 AS `role_name`,
  1 AS `role_slug`,
  1 AS `role_id` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_attendance_summary`
--

/*!50001 DROP VIEW IF EXISTS `v_attendance_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_attendance_summary` AS select `a`.`employee_id` AS `employee_id`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `full_name`,`d`.`name` AS `department_name`,year(`a`.`date`) AS `yr`,month(`a`.`date`) AS `mth`,count(0) AS `total_days`,sum(`a`.`status` = 'present') AS `present`,sum(`a`.`status` = 'absent') AS `absent`,sum(`a`.`status` = 'late') AS `late`,sum(`a`.`status` = 'on_leave') AS `on_leave`,sum(`a`.`hours_worked`) AS `total_hours` from (((`attendance` `a` join `employees` `e` on(`a`.`employee_id` = `e`.`id`)) join `users` `u` on(`e`.`user_id` = `u`.`id`)) join `departments` `d` on(`e`.`department_id` = `d`.`id`)) group by `a`.`employee_id`,year(`a`.`date`),month(`a`.`date`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_employees`
--

/*!50001 DROP VIEW IF EXISTS `v_employees`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_employees` AS select `e`.`id` AS `id`,`e`.`user_id` AS `user_id`,`e`.`employee_number` AS `employee_number`,`e`.`employment_type` AS `employment_type`,`e`.`status` AS `status`,`e`.`date_hired` AS `date_hired`,`e`.`basic_salary` AS `basic_salary`,`e`.`phone` AS `phone`,`e`.`birth_date` AS `birth_date`,`e`.`gender` AS `gender`,`e`.`department_id` AS `department_id`,`e`.`position_id` AS `position_id`,`e`.`manager_id` AS `manager_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `full_name`,`u`.`email` AS `email`,`u`.`avatar` AS `avatar`,`u`.`is_active` AS `user_active`,`d`.`name` AS `department_name`,`d`.`code` AS `department_code`,`p`.`title` AS `position_title`,`r`.`name` AS `role_name`,`r`.`slug` AS `role_slug`,`u`.`role_id` AS `role_id` from ((((`employees` `e` join `users` `u` on(`e`.`user_id` = `u`.`id`)) join `departments` `d` on(`e`.`department_id` = `d`.`id`)) join `positions` `p` on(`e`.`position_id` = `p`.`id`)) join `roles` `r` on(`u`.`role_id` = `r`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-19 17:38:01
