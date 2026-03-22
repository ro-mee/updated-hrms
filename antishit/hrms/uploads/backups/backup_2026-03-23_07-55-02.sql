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
  `is_archived` tinyint(1) DEFAULT 0,
  `interviewed_by` int(10) unsigned DEFAULT NULL,
  `interview_date` datetime DEFAULT NULL,
  `interview_location` varchar(255) DEFAULT NULL,
  `interview_notes` text DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `civil_status` enum('single','married','widowed','divorced') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `sss_number` varchar(30) DEFAULT NULL,
  `philhealth_number` varchar(30) DEFAULT NULL,
  `pagibig_number` varchar(30) DEFAULT NULL,
  `tin_number` varchar(30) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interviewed_by` (`interviewed_by`),
  KEY `idx_app_job` (`job_id`),
  KEY `idx_app_status` (`status`),
  CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`),
  CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`interviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicants`
--

LOCK TABLES `applicants` WRITE;
/*!40000 ALTER TABLE `applicants` DISABLE KEYS */;
INSERT INTO `applicants` VALUES (9,1,'Marco','Tan','marco.tan@email.com','09301234567',NULL,NULL,'hired',0,1,NULL,NULL,NULL,'LinkedIn','2026-03-18 14:33:56','2026-03-20 21:53:56',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,1,'Linda','Wong','linda.wong@email.com','09311234567',NULL,NULL,'reviewing',0,NULL,NULL,NULL,NULL,'JobStreet','2026-03-18 14:33:56','2026-03-18 14:33:56',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,2,'Bernard','Castro','bernard.c@email.com','09321234567',NULL,NULL,'interview',0,1,'2026-03-22 08:00:00','MV Bestlink College of the Philippines',NULL,'Referral','2026-03-18 14:33:56','2026-03-21 13:12:26',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,3,'Grace','Navarro','grace.n@email.com','09331234567',NULL,NULL,'hired',0,1,NULL,NULL,NULL,'Indeed','2026-03-18 14:33:56','2026-03-21 13:10:24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,1,'Mia','Lorente','kityanz09@gmail.com','+639171111001','7f3ca8e7b888401dac9bac30179caf63.docx','cqecwqckngsbntsrtbjsofgjidfjgisdfjbgisfjgsiberjtbioerjtbeirtb','hired',0,1,NULL,NULL,NULL,'Public Portal','2026-03-20 22:11:16','2026-03-20 22:15:51','2000-02-20','female','single','test city','cqwecwqewcq','056404056456','0456045645604','04564560450645','045645064560','Enzo Santos','+639171000111'),(14,2,'Zarah Jane','Santos','zarahlorente@gmail.com','09124181359','0ae73b74e98c6cbf899f1ef5de0c6f63.docx','cqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwjcqwecerbthwtibhwebtheihowjboqwj','hired',0,1,NULL,NULL,NULL,'Public Portal','2026-03-20 22:30:59','2026-03-20 23:25:26','2005-03-21','female','single','test city','cqcwe','056404056456','0456045645604','04564560450645','045645064560','Zarah Jane Lorente','+639171000111'),(15,4,'Yvonne','Rasalan','yvonne@gmail.com','09124181358','dcabaca3ced9a65bcc9d8b80ceac48c3.docx','kasi magaling ako','interview',0,1,'0000-00-00 00:00:00','Google Meet: https://meet.google.com/abc-defg-hij',NULL,'Public Portal','2026-03-21 00:08:09','2026-03-21 00:45:35','2003-02-20','female','single','test city','cqwecwqewcq','056404056456','0456045645604','04564560450645','045645064560','Enzo Santos','+639171000111'),(16,5,'rewin','rubio','crypticalrome@gmail.com','09124181358','32cf1a6a469ecee233a3457ec52213e6.docx','vqwevqljPOJVarjvoqrjoerjoerjqeovrjqopvrjqpoejrvqervq','hired',1,1,'2026-03-22 08:00:00','MV Bestlink College of the Philippines',NULL,'Public Portal','2026-03-21 09:48:02','2026-03-21 13:58:53','2000-02-20','male','single','test city','cqwecqwecwq','4234234234','23423423432','23423423432','234234324','Enzo Santos','+639171000111'),(17,2,'abigil','nery','abigilnery@gmail.com','09124181358','bf2fda33179405dd2d6a796376b2e218.docx','test citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest citytest city','hired',0,1,NULL,NULL,NULL,'Public Portal','2026-03-21 09:53:20','2026-03-21 09:53:34','2005-06-11','female','single','test city','cqwecwqecqw','056404056456','23423423432','23423423432','045645064560','Enzo Santos','');
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (15,7,'2026-03-12','2026-03-12 08:02:00','2026-03-12 17:05:00',8.98,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(16,7,'2026-03-13','2026-03-13 08:15:00','2026-03-13 17:00:00',8.75,0.00,'late',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(17,7,'2026-03-14','2026-03-14 08:01:00','2026-03-14 17:05:00',9.07,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(18,8,'2026-03-12','2026-03-12 08:00:00','2026-03-12 17:00:00',9.00,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(19,8,'2026-03-13','2026-03-13 08:00:00','2026-03-13 17:00:00',9.00,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(20,9,'2026-03-12','2026-03-12 09:15:00','2026-03-12 17:00:00',7.75,0.00,'late',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(21,10,'2026-03-12','2026-03-12 08:05:00','2026-03-12 17:00:00',8.92,0.00,'present',NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56'),(22,7,'2026-03-19','0000-00-00 00:00:00','0000-00-00 00:00:00',0.05,0.00,'late','',NULL,'2026-03-19 02:54:07','2026-03-19 04:09:47'),(23,10,'2026-03-18',NULL,NULL,0.00,0.00,'on_leave','',NULL,'2026-03-19 03:13:58','2026-03-19 03:48:59'),(27,1,'2026-03-19','2026-03-19 18:32:14','2026-03-19 19:55:39',1.39,0.00,'late',NULL,NULL,'2026-03-19 10:32:14','2026-03-19 11:55:39'),(28,1,'2026-03-21','2026-03-21 08:57:00','2026-03-21 09:00:05',0.05,0.00,'late',NULL,NULL,'2026-03-21 00:57:00','2026-03-21 01:00:05'),(29,11,'2026-03-22','2026-03-22 08:02:00',NULL,0.00,0.00,'present',NULL,NULL,'2026-03-22 00:02:00','2026-03-22 00:02:00'),(30,1,'2026-03-22','2026-03-22 08:03:17',NULL,0.00,0.00,'present',NULL,NULL,'2026-03-22 00:03:17','2026-03-22 00:03:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=435 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (5,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-18 14:34:08'),(6,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-18 14:34:17'),(7,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:36:13'),(8,1,'reject','leaves','Rejected leave request #11',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:46:19'),(9,1,'update','settings','Updated system settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:48:34'),(10,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:52:37'),(11,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:52:44'),(12,7,'request_leave','leaves','Submitted leave request',13,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:53:44'),(13,7,'clock_in','attendance','Employee clocked in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:54:07'),(14,7,'upload','documents','Uploaded document: zeri_1.jpg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:56:18'),(15,7,'clock_out','attendance','Employee clocked out',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:57:24'),(16,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:57:36'),(17,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 02:57:44'),(18,1,'update_profile','profile','User updated their profile',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:01:48'),(19,1,'create','payroll','Created payroll period: try',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:06:47'),(20,1,'create','payroll','Created payroll period: try',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:06:58'),(21,1,'reject','leaves','Rejected leave request #13',13,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:12:12'),(22,1,'approve','leaves','Approved leave request #12',12,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:13:58'),(23,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:15:35'),(24,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:15:42'),(25,7,'request_leave','leaves','Submitted leave request',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:16:24'),(26,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:17:50'),(27,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:17:58'),(28,1,'approve','leaves','Approved leave request #14',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:18:12'),(29,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:19:08'),(30,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:19:16'),(31,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:26:56'),(32,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:27:17'),(33,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:39:02'),(34,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:39:10'),(35,1,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:44:41'),(36,1,'edit','attendance','Edited attendance for emp #10 on 2026-03-18',10,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:49:00'),(37,1,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 03:58:39'),(38,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:07:59'),(39,1,'edit','attendance','Edited attendance for emp #7 on 2026-03-19',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:09:47'),(40,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:12'),(41,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:29'),(42,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:49'),(43,1,'update','employees','Updated employee #1',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:56'),(44,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:12:57'),(45,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:13:10'),(46,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:13:17'),(47,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:19:36'),(48,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:19:52'),(49,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:20:28'),(50,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:20:37'),(51,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:22:25'),(52,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:22:38'),(53,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:01'),(54,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:27'),(55,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:46'),(56,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:27:56'),(57,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:28:17'),(58,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:28:34'),(59,1,'update','employees','Updated employee #3',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:04'),(60,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:09'),(61,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:19'),(62,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:29:52'),(63,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:02'),(64,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:08'),(65,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:39'),(66,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:46'),(67,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:30:50'),(68,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:31:06'),(69,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:31:48'),(70,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:32:13'),(71,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:34:40'),(72,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:35:00'),(73,4,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:35:44'),(74,4,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:37:00'),(75,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:42:00'),(76,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:42:13'),(77,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:45:57'),(78,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:46:40'),(79,1,'create','payroll','Created payroll period: Verification Period',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:47:42'),(80,1,'generate','payroll','Generated payroll for period #3: 10 records',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:47:53'),(81,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:50:17'),(82,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:50:35'),(83,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:57:43'),(84,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 04:58:13'),(85,2,'approve','payroll','Approved payroll period #3',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:29:58'),(86,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:30:15'),(87,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:30:35'),(88,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:30:51'),(89,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:31:07'),(90,4,'generate','payroll','Generated payroll for period #2: 10 records',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:31:16'),(91,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:31:50'),(92,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:11'),(93,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:12'),(94,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:20'),(95,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:32:25'),(96,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:33:32'),(97,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:33:55'),(98,1,'create','employees','Added employee Rome Lorente',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:36:58'),(99,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:37:18'),(100,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:37:22'),(101,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:37:32'),(102,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:39:53'),(103,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:41:48'),(104,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:41:57'),(105,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:34'),(106,NULL,'login_fail','auth','Failed login attempt for: cruz@example.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:41'),(107,NULL,'login_fail','auth','Failed login attempt for: cruz@example.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:49'),(108,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:43:57'),(109,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:23'),(110,NULL,'login_fail','auth','Failed login attempt for: finance@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:26'),(111,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:34'),(112,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:46'),(113,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:44:49'),(114,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:45:41'),(115,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:45:43'),(116,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:47:21'),(117,NULL,'login_fail','auth','Failed login attempt for: Admin@1234superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:47:38'),(118,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:47:49'),(119,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:52:36'),(120,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:52:48'),(121,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:55:46'),(122,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:56:00'),(123,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:59:41'),(124,1,'approve','payroll','Approved payroll period #2',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 08:59:51'),(125,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:00:11'),(126,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:00:17'),(127,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:14:10'),(128,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:15:48'),(129,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:15:54'),(130,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:30:09'),(131,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:30:15'),(132,1,'update','settings','Updated system settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:30:39'),(133,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:31:55'),(134,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:32:09'),(135,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:35:20'),(136,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:35:30'),(137,1,'backup','settings','Created database backup: backup_2026-03-19_17-38-01.sql',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:38:01'),(138,1,'update_permissions','roles','Updated permissions for role ID: 1',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:58:06'),(139,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:58:17'),(140,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:58:25'),(141,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:58:36'),(142,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:58:41'),(143,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:58:47'),(144,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:59:09'),(145,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:59:11'),(146,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:59:20'),(147,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:59:44'),(148,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 09:59:49'),(149,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:24:03'),(150,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:24:06'),(151,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:24:34'),(152,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:24:43'),(153,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:24:59'),(154,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:25:07'),(155,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:25:09'),(156,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:25:26'),(157,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:25:39'),(158,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:26:05'),(159,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:26:17'),(160,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:26:32'),(161,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:26:48'),(162,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:26:51'),(163,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:27:11'),(164,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:27:35'),(165,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:27:47'),(166,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:28:08'),(167,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:28:10'),(168,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:28:27'),(169,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:28:51'),(170,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:29:03'),(171,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:29:09'),(172,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:29:44'),(173,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:30:11'),(174,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:31:08'),(175,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:31:19'),(176,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:31:34'),(177,1,'clock_in','attendance','Employee clocked in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:32:14'),(178,1,'update_permissions','roles','Updated permissions for role ID: 1',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:35:33'),(179,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:36:40'),(180,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:36:43'),(181,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:36:50'),(182,7,'update_profile','profile','User updated their profile',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:41:38'),(183,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:43:48'),(184,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:43:55'),(185,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:44:31'),(186,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:44:39'),(187,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:44:50'),(188,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:44:56'),(189,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:56:59'),(190,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 10:57:06'),(191,1,'update_permissions','roles','Updated permissions for role ID: 1',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:00:01'),(192,1,'update_permissions','roles','Updated permissions for role ID: 1',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:00:30'),(193,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:00:42'),(194,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:00:54'),(195,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:01:00'),(196,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:01:15'),(197,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:01:22'),(198,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:01:30'),(199,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:01:39'),(200,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:01:51'),(201,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:02:06'),(202,7,'login','auth','User logged in',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:02:13'),(203,7,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:42:08'),(204,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:55:04'),(205,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:55:11'),(206,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:55:23'),(207,1,'clock_out','attendance','Employee clocked out',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 11:55:39'),(208,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:41'),(209,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:48'),(210,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:50'),(211,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:51'),(212,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:54'),(213,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:55'),(214,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:57'),(215,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:51:58'),(216,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:52:02'),(217,1,'change_password','auth','User changed their password',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:57:46'),(218,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:59:13'),(219,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:59:20'),(220,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:59:22'),(221,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:59:24'),(222,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 12:59:27'),(223,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:25'),(224,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:34'),(225,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:37'),(226,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:38'),(227,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:39'),(228,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:40'),(229,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:41'),(230,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:42'),(231,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:43'),(232,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:43'),(233,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:44'),(234,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:45'),(235,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:46'),(236,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:47'),(237,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:48'),(238,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:49'),(239,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:50'),(240,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:01:51'),(241,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:02:39'),(242,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:16:46'),(243,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:16:48'),(244,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:16:53'),(245,1,'change_password','auth','User changed their password',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:26:25'),(246,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:30:39'),(247,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 13:30:46'),(248,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 14:14:13'),(249,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:05:56'),(250,NULL,'login_fail','auth','Failed login attempt for: superadmin@hrms.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:05:58'),(251,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:06:03'),(252,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:06:15'),(253,1,'change_password','auth','User changed their password',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:08:56'),(254,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:37:42'),(255,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:38:13'),(256,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:38:19'),(257,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-19 15:38:34'),(258,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:26:29'),(259,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:27:32'),(260,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:30:39'),(261,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:30:53'),(262,NULL,'login_fail','auth','Failed login attempt for: dobola3865@isfew.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:32:04'),(263,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:32:36'),(264,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:33:18'),(265,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:34:08'),(266,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:34:28'),(267,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:34:39'),(268,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:35:32'),(269,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:35:58'),(270,NULL,'2fa_failed','auth','Incorrect 2FA code entered for: dobola3865@isfew.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:36:35'),(271,NULL,'2fa_failed','auth','Incorrect 2FA code entered for: dobola3865@isfew.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:36:37'),(272,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:37:11'),(273,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:37:24'),(274,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:43:14'),(275,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:56:12'),(276,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:59:13'),(277,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:59:44'),(278,1,'revoke_session','profile','User revoked their own session ID #3',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 00:59:55'),(279,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:09:31'),(280,1,'revoke_session','profile','User revoked their own session ID #5',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:10:06'),(281,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:13:01'),(282,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:13:38'),(283,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:13:55'),(284,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:15:46'),(285,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:16:10'),(286,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:18:00'),(287,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:18:10'),(288,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:18:44'),(289,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:18:55'),(290,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:23:28'),(291,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 01:23:44'),(292,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 08:57:20'),(293,1,'update','settings','Updated system settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:04:58'),(294,1,'update','settings','Updated system settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:05:18'),(295,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:10:36'),(296,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:13:02'),(297,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:14:07'),(298,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:15:41'),(299,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:17:39'),(300,NULL,'forgot_password_request','auth','Password reset requested for: lorenteromejoseph@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 09:22:04'),(301,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 21:44:32'),(302,1,'update','recruitment','Updated applicant #9 status to hired',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 21:53:56'),(303,1,'update','recruitment','Updated applicant #13 status to hired',13,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:15:51'),(304,1,'edit','recruitment','Updated job: Software Developer',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:18:08'),(305,1,'create','employees','Added employee Mia Lorente',13,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:23:01'),(306,1,'update','recruitment','Updated applicant #14 status to hired',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:31:33'),(307,1,'update','recruitment','Updated applicant #14 status to hired',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:32:12'),(308,1,'update','recruitment','Updated applicant #14 status to offered',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:38:20'),(309,1,'update','recruitment','Updated applicant #14 status to hired',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:38:26'),(310,1,'update','recruitment','Updated applicant #14 status to offered',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:38:34'),(311,1,'create','employees','Added employee Zarah Jane Santos',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 22:42:03'),(312,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 23:24:46'),(313,1,'update','recruitment','Updated applicant #14 status to hired',14,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 23:25:26'),(314,NULL,'login_fail','auth','Failed login attempt for: dobola3865@isfew.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 23:57:01'),(315,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-20 23:57:12'),(316,1,'create','recruitment','Posted job: HR Specialist',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:06:01'),(317,1,'update','recruitment','Updated applicant #15 status to hired',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:08:39'),(318,1,'create','employees','Added employee Yvonne Rasalan',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:08:58'),(319,NULL,'login_fail','auth','Failed login attempt for: admin@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:14:34'),(320,NULL,'2fa_failed','auth','Incorrect 2FA code entered for: vwcxo9ptf6@xkxkud.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:18:57'),(321,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:19:20'),(322,1,'update','recruitment','Updated applicant #15 status to hired',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:20:58'),(323,1,'update','recruitment','Updated applicant #15 status to offered',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:38:27'),(324,1,'update','recruitment','Updated applicant #15 status to hired',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:38:39'),(325,1,'update','recruitment','Updated applicant #15 status to interview',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:43:49'),(326,1,'update','recruitment','Updated applicant #15 status to interview',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:44:31'),(327,1,'update','recruitment','Updated applicant #15 status to interview',15,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:45:35'),(328,1,'clock_in','attendance','Employee clocked in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 00:57:00'),(329,1,'clock_out','attendance','Employee clocked out',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 01:00:05'),(330,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 09:45:06'),(331,1,'create','recruitment','Posted job: IT Manager',5,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 09:47:06'),(332,1,'update','recruitment','Updated applicant #16 status to interview',16,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 09:49:01'),(333,1,'update','recruitment','Updated applicant #16 status to hired',16,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 09:49:33'),(334,1,'update','recruitment','Updated applicant #16 status to hired',16,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 09:49:49'),(335,1,'update','recruitment','Updated applicant #17 status to hired',17,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 09:53:34'),(336,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:05:47'),(337,1,'update','recruitment','Updated applicant #12 status to hired',12,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:10:24'),(338,1,'create','employees','Added employee Grace Navarro',16,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:11:11'),(339,1,'update','recruitment','Updated applicant #11 status to interview',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:12:26'),(340,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:13:46'),(341,NULL,'login_fail','auth','Failed login attempt for: nkooy22100@minitts.net',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:14:49'),(342,NULL,'forgot_password_request','auth','Password reset requested for: nkooy22100@minitts.net',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:15:04'),(343,NULL,'password_reset_success','auth','Password reset successfully for: nkooy22100@minitts.net',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:15:52'),(344,2,'login','auth','User logged in',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:16:30'),(345,2,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:17:09'),(346,NULL,'forgot_password_request','auth','Password reset requested for: jppsd14621@minitts.net',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:18:26'),(347,NULL,'password_reset_success','auth','Password reset successfully for: jppsd14621@minitts.net',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:18:43'),(348,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:19:26'),(349,NULL,'login_fail','auth','Failed login attempt for: vwcxo9ptf6@xkxkud.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:20:12'),(350,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:20:19'),(351,1,'update_permissions','roles','Updated permissions for role ID: 4',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:21:03'),(352,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:21:10'),(353,4,'login','auth','User logged in',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:21:22'),(354,1,'update_permissions','roles','Updated permissions for role ID: 4',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:22:16'),(355,4,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:23:35'),(356,NULL,'forgot_password_request','auth','Password reset requested for: lorenteromejoseph@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:23:50'),(357,NULL,'password_reset_success','auth','Password reset successfully for: lorenteromejoseph@gmail.com',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:24:14'),(358,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:24:56'),(359,1,'update','employees','Updated employee #7',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:25:56'),(360,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:26:03'),(361,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:26:13'),(362,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:26:18'),(363,1,'update','employees','Updated employee #7',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:26:43'),(364,1,'update','employees','Updated employee #7',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:27:56'),(365,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:28:04'),(366,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:28:36'),(367,NULL,'forgot_password_request','auth','Password reset requested for: mxgef10292@minitts.net',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:31:01'),(368,NULL,'password_reset_success','auth','Password reset successfully for: mxgef10292@minitts.net',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:31:17'),(369,3,'login','auth','User logged in',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:32:04'),(370,1,'update_permissions','roles','Updated permissions for role ID: 3',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:34:36'),(371,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:38:20'),(372,3,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:42:16'),(373,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:56:30'),(374,1,'archive','recruitment','Archived applicant #16',16,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 13:58:53'),(375,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 14:00:02'),(376,NULL,'login_fail','auth','Failed login attempt for: vwcxo9ptf6@xkxkud.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 23:54:49'),(377,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 23:55:25'),(378,1,'update','employees','Updated employee #11',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 23:56:14'),(379,NULL,'login_fail','auth','Failed login attempt for: lorenteromejoseph@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 23:56:25'),(380,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-21 23:57:19'),(381,11,'clock_in','attendance','Employee clocked in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:02:00'),(382,1,'clock_in','attendance','Employee clocked in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:03:17'),(383,1,'create','training','Created training: Tutorial pano hindi tablan ng selos',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:17:56'),(384,1,'update_permissions','roles','Updated permissions for role ID: 3',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:19:51'),(385,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:19:59'),(386,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:20:05'),(387,1,'create','training','Created training: Code Session',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:32:14'),(388,1,'update_permissions','roles','Updated permissions for role ID: 3',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:33:08'),(389,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:33:16'),(390,NULL,'login_fail','auth','Failed login attempt for: lorenteromejoseph@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:33:22'),(391,NULL,'login_fail','auth','Failed login attempt for: lorenteromejoseph@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:33:26'),(392,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:33:33'),(393,1,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:37:22'),(394,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:37:28'),(395,1,'update_permissions','roles','Updated permissions for role ID: 1',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:40:48'),(396,11,'logout','auth','User logged out',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:40:51'),(397,11,'login','auth','User logged in',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:40:59'),(398,1,'update_permissions','roles','Updated permissions for role ID: 2',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:41:46'),(399,1,'update_permissions','roles','Updated permissions for role ID: 4',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:42:56'),(400,1,'update_permissions','roles','Updated permissions for role ID: 5',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:43:21'),(401,1,'update_permissions','roles','Updated permissions for role ID: 6',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:44:15'),(402,1,'update_permissions','roles','Updated permissions for role ID: 6',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:44:26'),(403,1,'update_permissions','roles','Updated permissions for role ID: 7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:45:02'),(404,1,'update_permissions','roles','Updated permissions for role ID: 1',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:45:13'),(405,1,'create','performance','Created performance review #1',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:47:15'),(406,1,'enroll','training','Enrolled in training #3',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:50:54'),(407,1,'enroll','training','Enrolled in training #3',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:51:06'),(408,1,'enroll','training','Enrolled in training #3',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:51:08'),(409,1,'enroll','training','Enrolled in training #3',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:51:09'),(410,1,'enroll','training','Enrolled in training #3',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:51:12'),(411,1,'enroll','training','Enrolled in training #2',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:53:27'),(412,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:53:41'),(413,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:24'),(414,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:24'),(415,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:24'),(416,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:24'),(417,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:24'),(418,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:26'),(419,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:26'),(420,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:26'),(421,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:27'),(422,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:27'),(423,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:30'),(424,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:31'),(425,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:31'),(426,11,'enroll','training','Enrolled in training #3',11,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 00:54:31'),(427,1,'create','training','Created training: testing 123',5,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 01:26:27'),(428,NULL,'login_fail','auth','Failed login attempt for: admin@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 01:34:00'),(429,NULL,'login_fail','auth','Failed login attempt for: admin',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 01:34:27'),(430,NULL,'login_fail','auth','Failed login attempt for: admin@gmail.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 01:36:23'),(431,1,'create','performance','Created performance review #2',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 01:36:47'),(432,1,'create','performance','Created performance review #3',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 01:41:38'),(433,NULL,'login_fail','auth','Failed login attempt for: vwcxo9ptf6@xkxkud.com',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 23:52:17'),(434,1,'login','auth','User logged in',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-22 23:52:23');
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
  `basic_salary` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `civil_status` enum('single','married','widowed','divorced') DEFAULT NULL,
  `sss_number` varchar(255) DEFAULT NULL,
  `philhealth_number` varchar(255) DEFAULT NULL,
  `pagibig_number` varchar(255) DEFAULT NULL,
  `tin_number` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,1,'EMP-0001',6,13,NULL,'full_time','active','2020-01-01',NULL,NULL,'6qmsg8lYl/DnWbtX1FVdqTdFekVDRVVINU5TcW9WVnZETmtXdUE9PQ==','09171234568','','','1985-05-10','male','single','','','','','','','','2026-03-18 14:33:56','2026-03-19 14:56:15'),(2,2,'EMP-0002',1,1,NULL,'full_time','active','2020-01-15',NULL,NULL,'um5S0tV5r4rNLXcKsssC1mJDMmpPL1NmQ3FhZ1RKaEpsRCtnNUE9PQ==','09181234567',NULL,NULL,'1982-03-22','female',NULL,'jHDSMcIBRL5gA+0HPeJeAWI2ME1aYlRrQytEaXpucktDRGFuS2c9PQ==','+dAu+u7WE/SpdoSaeelyeW11STVDbFJnd1BEVHJ3OTI5RTQxc0E9PQ==','VPcJJb0ehtQHbAqqRcvSmThtUUlrSkg0c29MWkprbDBvUXVNZ2c9PQ==','U9WzJjvVjiNOypSycYfCdTEySWFOOEtndVJOUng2WDVxemhQTFE9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(3,3,'EMP-0003',1,6,NULL,'full_time','active','2020-02-01',NULL,NULL,'UPIB6kcJFvM8Rngv8p1l6HRpOVJvUzdFYlpXMlVwek9aYXpPMGc9PQ==','09191234567','','','1980-07-15','male','single','','','','','','','','2026-03-18 14:33:56','2026-03-19 14:56:15'),(4,4,'EMP-0004',2,4,NULL,'full_time','active','2020-03-01',NULL,NULL,'feJ3qxdXs165y7pKa3MFflE1NVBtalY1ZVhISko5eFEwNXhiekE9PQ==','09201234567',NULL,NULL,'1983-11-30','female',NULL,'/J7UdPIi+1VQUSiO5wcyR1phZXRkektLOGg4VU94U0xXRmw3ckE9PQ==','UFvupTgOwlzIWpW4mvLAgldScXMxdHBURHRPS0w0a2d0WUhwMWc9PQ==','2OmHp7pS+XDolyucWxlHKU9ueU5DRnlPZGtUdjd3N2JyL0pnQWc9PQ==','AbTtaaWbYQ4qFnayxyK4XFJtWnJWS0JtZmtpTmR0Q3dwc1hxR0E9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(5,5,'EMP-0005',1,2,NULL,'full_time','active','2021-01-10',NULL,NULL,'+ilyp0hDuMEY892ewP/OwHZqSGRUM2x0Nyt2bER6cE1YbHlYbkE9PQ==','09211234567',NULL,NULL,'1990-06-18','female',NULL,'7VldwtuqvuMXbFCj/WuMfkh1R1JHSlR6alVWbjZSeXZPVHF0WUE9PQ==','QGd9eQbwFimM7lW6MtU4UXZpVHd5S0l2dnFsRmVla28xdjVFZkE9PQ==','ALZVqJsvotI2iWAV+9jrOFBwQytNcmN0Y3liSnpCbnVlYWt2Unc9PQ==','6sDyzyocZLbB9L5b2D/vY045SU9TUlBrMmxJRmZKT0YxcGc3eGc9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(6,6,'EMP-0006',1,3,NULL,'full_time','active','2021-06-01',NULL,NULL,'KxjJ96E+whQChRGboJTKeW1kTHU5Y0dvNUh0cXUrSUxRcFpDc1E9PQ==','09221234567',NULL,NULL,'1993-09-25','male',NULL,'iOF3yeR/UKKIr8g3bxTcYHV4YVdZc0N6NW9lUmhaa0lYV3NGN2c9PQ==','6pcX6n1wHu3u+kGz+dyjuVJPSXFFR1FaZHpnNFgyWjFhTmtvYkE9PQ==','Etg7ASDQdZiB/7awHQLne1lGMmRaZnpoNW54QTM4Wk5pN01MN0E9PQ==','ghNZ1BTTvemOAYrND/dHCDNLY1ExN2dKbVJ1eUwwMHNuWnAwZmc9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(7,7,'EMP-0007',3,6,3,'full_time','active','2022-03-15',NULL,NULL,'kY0KpzQaL1Scfi6pTtb3mHpSRldGUWlBdVhwYnFseXRWTE1LR2c9PQ==','09231234567','','','1995-01-10','male','single','','','','','','','','2026-03-18 14:33:56','2026-03-21 13:27:56'),(8,8,'EMP-0008',5,12,NULL,'full_time','active','2022-07-01',NULL,NULL,'fb3p2DyYypvu4LbawBF34FJsbVZUY0w2emZ4ZlROTFU0MStHbXc9PQ==','09241234567',NULL,NULL,'1997-08-14','female',NULL,'X0DZfZO1N04HN3Ejuk7mx2xkVkx1VnVwWExDbFA5aStyeis1NHc9PQ==','t/ITVcifIkNN7pEDzHyyyUJETlgySTdienRBTHFmV3FFeTNURFE9PQ==','Hsd7YGv6qGOB6gCyP4+/MzZJTHBIR3dwajd6K05XNCtTZE5UZEE9PQ==','cqqrMvKkCqZI/FONvVqeNnpEVkZIWVUvQXgxUjBUajV4M3dwU0E9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(9,9,'EMP-0009',4,10,NULL,'full_time','active','2023-01-01',NULL,NULL,'c8Q9/7Dj3pl+MWpwEnlzlUJRVlJDTlk4Z2RSN1d3eWd6TVZ1RFE9PQ==','09251234567',NULL,NULL,'1999-03-05','male',NULL,'TyCbpfXr/UJfVU5loo7D+E5aTGIzTFFlMmsyVzFKWVpSZHJ5bXc9PQ==','aUAa30H5ZL2AwilSz4sciUNnY2FNNkpmczQvdndKdEVTcFRFdlE9PQ==','/01I+oMC7MbzvgYMYKCmPTJyNXlYR0wzNGpwUEdJMEwzbkxnZEE9PQ==','OEXaw8sRaE+DHckEjqckemRDcFhMbnlHdWx4djBpWWxpQzZiYXc9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(10,10,'EMP-0010',5,12,NULL,'full_time','active','2023-06-01',NULL,NULL,'sIpGkv5iQbIpgeik8OetBjRNbGhYRU5oVU1oUnYrRmRnZC9jRnc9PQ==','09261234567',NULL,NULL,'1998-12-20','female',NULL,'iyL1jHsG53G4TUGS/ppufnNBWFIwbEE1all4dDJmSDBLc3FwOFE9PQ==','iQgoWwwdUHqlD/tF58DLampIK2QzOEYwb2s4V1FPV2gzdHVuRlE9PQ==','6v23wEZI+I++AB6pF3lG+2NsUG9ZV2xkQVJBUUg5UXFUZnpPVkE9PQ==','nEMrD/hNcSYeAerIf6mC9lBNVlBvbERKb1RCUXFaQmw0UlFwOWc9PQ==',NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-19 14:56:15'),(11,11,'EMP-0011',3,6,3,'full_time','active','2026-03-19',NULL,NULL,'KQaYvzd0PF9cWlbgdqHpkTFiWHM3SVVmMGRJY054K0RveTBSK0E9PQ==','09124181359','','','2004-06-20','male','single','','','','','','','','2026-03-19 08:36:58','2026-03-21 23:56:14'),(12,12,'EMP-0012',3,7,7,'full_time','active','2026-03-21',NULL,NULL,'oHnYZCBek97rVWlIOPdRoFBvOWNGSGd3dW02TzlFcVBwcFRTTlE9PQ==','+639171111001','test city','cqwecwqewcq','2000-02-20','female','single','N8HbYTWBBYlPS4YKg5vphFFMUnNMdTNDWXM1N2g1NGFWTDkrSEE9PQ==','WnVeC3uw2Cb3gx/KIS4LqnRaUXFaSHQwSkdST056NlBuUjhuVHc9PQ==','h54BCOBs/dTHGVnVUwd02DdoSGtZSFZmOHl5U1d1dFBHK0NLcVE9PQ==','GVvXlGwnRaBg3ggCG9egxGk3c2JBd0pqRzNqRWtRVmtValYrWlE9PQ==','Enzo Santos','+639171000111',NULL,'2026-03-20 22:17:22','2026-03-20 22:17:22'),(13,13,'EMP-0013',3,7,3,'full_time','active','2026-03-21',NULL,NULL,'wOsMZvMdIaey54Gbfv+rZUF5WVJub0VKS2JyZ2ZNYXF6c0tUMVE9PQ==','+639171111001','test city','cqwecwqewcq','2000-02-20','female','single','87MrCAJIygmsCDOwLoaMgmZDM0szaHQwNTVrcld0MHc5YTFPWXc9PQ==','oTvvkOyUw1WNP0JQYmlylFpKQ29sdEZJYTZiV1NoQVNDSEI3NlE9PQ==','7L0w5rzUg2SN5PFoxEKPLFkwV1FvTTgrVDhGeXl0Vk1JN3dvZ0E9PQ==','6ZAu6MI4h1I5zUQds19Z63FCV0ZNRkRGbG1HZ09sN1FYa1pkalE9PQ==','Enzo Santos','+639171000111',NULL,'2026-03-20 22:22:55','2026-03-20 22:22:55'),(14,14,'EMP-0014',5,12,NULL,'full_time','active','2026-03-21',NULL,NULL,'iB3dBK3r6irwp5q9qMU8RGdjZzVNdjMxZitIUlpnR3lwcEpXY3c9PQ==','09124181359','test city','cqcwe','2005-03-21','female','single','xTdF4Xu4KGbMNzDzT4uVoEIwN2RwK0hIRkkzdi9DYWJJUmlTSmc9PQ==','WDYutWGf7WcQSUzJMuLx+zNuNWltRlhCaTI5ODNaU1F2alFLdWc9PQ==','3fwzgB7ZQq+e7OGETOWc+jRranE4bGc0djY2bm01dER5bXhVeVE9PQ==','NmWU2ABoRlcL8Xi5yond2FpGZ1ZXb3NITXphbGpkRWF3NDNXQUE9PQ==','Zarah Jane Lorente','+639171000111',NULL,'2026-03-20 22:41:58','2026-03-20 22:41:58'),(15,15,'EMP-0015',1,2,2,'full_time','active','2026-03-21',NULL,NULL,'ZAQ+Mxvuz7WfKcRAB2qkfnRqV3pMSG9OQ1BmUGZuaEl4dEltOEE9PQ==','09124181358','test city','cqwecwqewcq','2003-02-20','female','single','5qoNIJzPNnnY90TioUcJz1VXYXBRa3JiSEFFc2tyZzVsNmFRVWc9PQ==','XpnQEyOW0TFyHOcU+qpmnFJoYm1velV1NGpkeFNVOVdTK1EvMmc9PQ==','P29yisTXtdgEnjT5ZWkO+GpkNnFBNm5YaDJiTm0yY2taNW1XQkE9PQ==','F9pt99PahVYJMdkrMdMWaFVaVlpTb2I3Y2FlYy9LUHR1OUV1a2c9PQ==','Enzo Santos','+639171000111',NULL,'2026-03-21 00:08:53','2026-03-21 00:08:53'),(16,16,'EMP-0016',1,2,2,'full_time','active','2026-03-21',NULL,NULL,'JypMM5f1/8MbcjtUQ/6ShFBBcG5oRHEzT2lGRkJwNjlvL3M3c2c9PQ==','09331234567','','','0000-00-00','','','','','','','','',NULL,'2026-03-21 13:11:05','2026-03-21 13:11:05');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'Software Developer',3,7,'Looking for a skilled PHP/Laravel developer.','3+ years PHP, MySQL, REST APIs',NULL,NULL,'full_time',2,'closed',6,'2026-04-17','2026-03-18 14:33:56','2026-03-20 22:18:08'),(2,'Sales Representative',5,12,'Dynamic sales rep needed.','1+ year sales experience',NULL,NULL,'full_time',3,'open',6,'2026-05-02','2026-03-18 14:33:56','2026-03-18 14:33:56'),(3,'HR Specialist',1,2,'HR Specialist to support recruitment and engagement.','HR background, 2 years exp',NULL,NULL,'full_time',1,'closed',6,'2026-04-07','2026-03-18 14:33:56','2026-03-21 13:10:24'),(4,'HR Specialist',1,2,'We are looking for an HR Specialist to join our team. You will handle various HR tasks, including employee relations, benefits administration, and policy implementation.','• Bachelor\'s degree in HR or Psychology.\r\n• 3+ years of HR experience.\r\n• Strong interpersonal and communication skills.',35000.00,55000.00,'full_time',1,'closed',1,NULL,'2026-03-21 00:06:01','2026-03-21 00:20:58'),(5,'IT Manager',3,6,'We are looking for an IT Manager to lead our technology department. You will manage our IT infrastructure, software systems, and technical support team.','• Bachelor\'s degree in CS or IT.\r\n• 5+ years of experience in IT management.\r\n• Strong knowledge of network administration and cybersecurity.',75000.00,110000.00,'full_time',1,'closed',1,'2026-03-23','2026-03-21 09:47:06','2026-03-21 09:49:33');
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
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_balances`
--

LOCK TABLES `leave_balances` WRITE;
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
INSERT INTO `leave_balances` VALUES (41,1,1,2026,15.00,0.00,15.00),(42,1,2,2026,15.00,0.00,15.00),(43,1,5,2026,3.00,0.00,3.00),(44,1,6,2026,30.00,0.00,30.00),(45,2,1,2026,15.00,0.00,15.00),(46,2,2,2026,15.00,0.00,15.00),(47,2,5,2026,3.00,0.00,3.00),(48,2,6,2026,30.00,0.00,30.00),(49,3,1,2026,15.00,0.00,15.00),(50,3,2,2026,15.00,0.00,15.00),(51,3,5,2026,3.00,0.00,3.00),(52,3,6,2026,30.00,0.00,30.00),(53,4,1,2026,15.00,0.00,15.00),(54,4,2,2026,15.00,0.00,15.00),(55,4,5,2026,3.00,0.00,3.00),(56,4,6,2026,30.00,0.00,30.00),(57,5,1,2026,15.00,0.00,15.00),(58,5,2,2026,15.00,0.00,15.00),(59,5,5,2026,3.00,0.00,3.00),(60,5,6,2026,30.00,0.00,30.00),(61,6,1,2026,15.00,0.00,15.00),(62,6,2,2026,15.00,0.00,15.00),(63,6,5,2026,3.00,0.00,3.00),(64,6,6,2026,30.00,0.00,30.00),(65,7,1,2026,15.00,0.00,15.00),(66,7,2,2026,15.00,0.00,15.00),(67,7,5,2026,3.00,1.00,2.00),(68,7,6,2026,30.00,0.00,30.00),(69,8,1,2026,15.00,0.00,15.00),(70,8,2,2026,15.00,0.00,15.00),(71,8,5,2026,3.00,0.00,3.00),(72,8,6,2026,30.00,0.00,30.00),(73,9,1,2026,15.00,0.00,15.00),(74,9,2,2026,15.00,0.00,15.00),(75,9,5,2026,3.00,0.00,3.00),(76,9,6,2026,30.00,0.00,30.00),(77,10,1,2026,15.00,0.00,15.00),(78,10,2,2026,15.00,1.00,14.00),(79,10,5,2026,3.00,0.00,3.00),(80,10,6,2026,30.00,0.00,30.00),(81,11,5,2026,3.00,0.00,3.00),(82,11,3,2026,105.00,0.00,105.00),(83,11,4,2026,7.00,0.00,7.00),(84,11,2,2026,15.00,0.00,15.00),(85,11,6,2026,30.00,0.00,30.00),(86,11,1,2026,15.00,0.00,15.00),(87,12,5,2026,3.00,0.00,3.00),(88,12,3,2026,105.00,0.00,105.00),(89,12,4,2026,7.00,0.00,7.00),(90,12,2,2026,15.00,0.00,15.00),(91,12,6,2026,30.00,0.00,30.00),(92,12,1,2026,15.00,0.00,15.00),(93,13,5,2026,3.00,0.00,3.00),(94,13,3,2026,105.00,0.00,105.00),(95,13,4,2026,7.00,0.00,7.00),(96,13,2,2026,15.00,0.00,15.00),(97,13,6,2026,30.00,0.00,30.00),(98,13,1,2026,15.00,0.00,15.00),(99,14,5,2026,3.00,0.00,3.00),(100,14,3,2026,105.00,0.00,105.00),(101,14,4,2026,7.00,0.00,7.00),(102,14,2,2026,15.00,0.00,15.00),(103,14,6,2026,30.00,0.00,30.00),(104,14,1,2026,15.00,0.00,15.00),(105,15,5,2026,3.00,0.00,3.00),(106,15,3,2026,105.00,0.00,105.00),(107,15,4,2026,7.00,0.00,7.00),(108,15,2,2026,15.00,0.00,15.00),(109,15,6,2026,30.00,0.00,30.00),(110,15,1,2026,15.00,0.00,15.00),(111,16,5,2026,3.00,0.00,3.00),(112,16,3,2026,105.00,0.00,105.00),(113,16,4,2026,7.00,0.00,7.00),(114,16,2,2026,15.00,0.00,15.00),(115,16,6,2026,30.00,0.00,30.00),(116,16,1,2026,15.00,0.00,15.00);
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
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `isp` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `is_new_ip` tinyint(1) NOT NULL DEFAULT 0,
  `is_suspicious` tinyint(1) NOT NULL DEFAULT 0,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_login_log_user` (`user_id`),
  KEY `idx_login_log_time` (`login_time`),
  KEY `idx_login_log_ip` (`ip_address`),
  CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_logs`
--

LOCK TABLES `login_logs` WRITE;
/*!40000 ALTER TABLE `login_logs` DISABLE KEYS */;
INSERT INTO `login_logs` VALUES (1,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',1,1,'2026-03-19 13:16:51'),(2,1,'122.54.212.45','Philippines','Manila',14.5995000,120.9842000,'PLDT','Chrome on Windows 10',0,0,'2026-03-19 13:28:52'),(3,1,'45.12.33.10','Singapore','Singapore',1.3521000,103.8198000,'DigitalOcean','Firefox on Linux',1,1,'2026-03-18 13:28:52'),(4,1,'::1','Localhost','Local',NULL,NULL,NULL,'Edge on Windows 11',0,0,'2026-03-19 11:28:52'),(5,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-19 13:30:46'),(6,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-19 15:06:03'),(7,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-19 15:06:15'),(8,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',1,1,'2026-03-19 15:37:38'),(9,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-19 15:38:19'),(10,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:26:29'),(11,2,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',1,1,'2026-03-20 00:30:35'),(12,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:32:36'),(13,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:34:28'),(14,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:35:32'),(15,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:37:11'),(16,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:43:14'),(17,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:56:12'),(18,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:59:13'),(19,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 00:59:44'),(20,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 01:09:31'),(21,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 01:13:38'),(22,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 01:15:46'),(23,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 01:18:00'),(24,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 01:18:44'),(25,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 01:23:28'),(26,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 08:57:20'),(27,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 09:13:02'),(28,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 09:15:41'),(29,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 21:44:32'),(30,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 23:24:46'),(31,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-20 23:57:12'),(32,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 00:19:20'),(33,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 09:45:06'),(34,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:05:47'),(35,2,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:16:30'),(36,4,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',1,1,'2026-03-21 13:19:21'),(37,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:20:19'),(38,4,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:21:22'),(39,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:24:56'),(40,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:26:13'),(41,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:28:04'),(42,3,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',1,1,'2026-03-21 13:31:58'),(43,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 13:56:30'),(44,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 23:55:25'),(45,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-21 23:57:19'),(46,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-22 00:20:05'),(47,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-22 00:33:33'),(48,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-22 00:37:28'),(49,11,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-22 00:40:59'),(50,1,'::1','Localhost','Local',NULL,NULL,'','Chrome on Windows 11',0,0,'2026-03-22 23:52:23');
/*!40000 ALTER TABLE `login_logs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (7,7,'Leave Approved','Your vacation leave (Mar 10-12) has been approved.','success','leaves',NULL,1,'2026-03-18 14:33:56'),(8,9,'Leave Pending','Your leave request is under review.','info','leaves',NULL,0,'2026-03-18 14:33:56'),(9,2,'New Leave Request','Employee Juan Dela Cruz submitted a leave request.','info','leaves',NULL,1,'2026-03-18 14:33:56'),(10,7,'Leave Request Submitted','Your leave request is pending review.','info','leaves',NULL,1,'2026-03-19 02:53:44'),(11,10,'Leave Approved','Your leave request has been approved.','success','leaves',NULL,0,'2026-03-19 03:13:58'),(12,7,'Leave Request Submitted','Your leave request is pending review.','info','leaves',NULL,1,'2026-03-19 03:16:24'),(13,7,'Leave Approved','Your leave request has been approved.','success','leaves',NULL,1,'2026-03-19 03:18:12'),(14,1,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,1,'2026-03-19 08:58:03'),(15,2,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(16,3,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(17,4,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,1,'2026-03-19 08:58:03'),(18,5,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(19,6,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(20,7,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,1,'2026-03-19 08:58:03'),(21,8,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(22,9,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(23,10,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',NULL,0,'2026-03-19 08:58:03'),(29,1,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,1,'2026-03-19 08:59:51'),(30,2,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(31,3,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(32,4,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,1,'2026-03-19 08:59:51'),(33,5,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(34,6,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(35,7,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,1,'2026-03-19 08:59:51'),(36,8,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(37,9,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(38,10,'Payslip Available','Your payslip for try is now available.','success','payroll',NULL,0,'2026-03-19 08:59:51'),(44,1,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,1,'2026-03-19 09:11:21'),(45,2,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(46,3,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(47,4,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,1,'2026-03-19 09:11:21'),(48,5,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(49,6,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(50,7,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,1,'2026-03-19 09:11:21'),(51,8,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(52,9,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21'),(53,10,'Payslip Available','Your payslip for Verification Period is now available.','success','payroll',3,0,'2026-03-19 09:11:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_kpi_scores`
--

LOCK TABLES `performance_kpi_scores` WRITE;
/*!40000 ALTER TABLE `performance_kpi_scores` DISABLE KEYS */;
INSERT INTO `performance_kpi_scores` VALUES (1,1,12,3.0,NULL),(2,1,9,3.0,NULL),(3,1,11,3.0,NULL),(4,1,8,3.0,NULL),(5,1,7,3.0,NULL),(6,1,10,3.0,NULL),(7,2,12,5.0,NULL),(8,2,9,5.0,NULL),(9,2,11,5.0,NULL),(10,2,8,5.0,NULL),(11,2,7,5.0,NULL),(12,2,10,5.0,NULL),(13,3,12,1.0,NULL),(14,3,9,1.0,NULL),(15,3,11,1.0,NULL),(16,3,8,1.0,NULL),(17,3,7,1.0,NULL),(18,3,10,1.0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_reviews`
--

LOCK TABLES `performance_reviews` WRITE;
/*!40000 ALTER TABLE `performance_reviews` DISABLE KEYS */;
INSERT INTO `performance_reviews` VALUES (1,1,1,'Q1, 2026','2026-03-22',NULL,NULL,NULL,NULL,'submitted',0,'2026-03-22 00:47:15','2026-03-22 00:47:15'),(2,1,1,'Q1, 2026','2026-03-22',NULL,NULL,NULL,NULL,'submitted',0,'2026-03-22 01:36:47','2026-03-22 01:36:47'),(3,1,1,'Q1, 2026','2026-03-22',1.0,'pogi ko','ewan','siguro','submitted',0,'2026-03-22 01:41:38','2026-03-22 01:41:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=3164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','view','View Dashboard'),(2,'employees','view','View Employees'),(3,'employees','create','Add employee'),(4,'employees','edit','Edit employee'),(5,'employees','delete','Delete employee'),(6,'attendance','view','View attendance'),(7,'attendance','manage','Manage Attendance'),(8,'leaves','view','View leaves'),(9,'leaves','request','Request leave'),(10,'leaves','approve','Approve Leave'),(11,'payroll','view','View payroll'),(12,'payroll','generate','Generate payroll'),(13,'payroll','approve','Approve Payroll'),(14,'recruitment','view','View jobs & applicants'),(15,'recruitment','manage','Manage Recruitment'),(16,'performance','view','View reviews'),(17,'performance','manage','Manage Performance'),(18,'training','view','View trainings'),(19,'training','manage','Manage Training'),(20,'documents','view','View documents'),(21,'documents','upload','Upload documents'),(22,'documents','delete','Delete documents'),(23,'reports','view','View Reports'),(24,'reports','export','Export reports'),(25,'notifications','view','View notifications'),(26,'audit','view','View Audit Logs'),(27,'settings','view','View settings'),(28,'settings','manage','Manage Settings'),(29,'attendance','approve','Approve Attendance'),(31,'attendance','self','My Attendance'),(34,'documents','manage','Manage Documents'),(35,'documents','self','My Documents'),(36,'employees','view_dept','Department Employees'),(37,'employees','manage','Manage Employees'),(40,'leaves','manage','Manage Leave'),(41,'leaves','self','My Leaves'),(42,'notifications','manage','Manage Notifications'),(43,'notifications','self','My Notifications'),(45,'payroll','manage','Manage Payroll'),(46,'payroll','self','My Payslips'),(48,'performance','review','Review Performance'),(49,'profile','self','My Profile'),(50,'recruitment','manage_onboarding','Manage Onboarding'),(52,'reports','view_dept','Department Reports'),(53,'reports','view_finance','Finance Reports'),(55,'settings','manage_backups','Manage Backups'),(57,'settings','policy','Policy Settings'),(58,'settings','tax','Tax Settings'),(60,'users','manage_roles','Manage Roles'),(61,'users','manage_users','Manage Users');
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
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
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
INSERT INTO `positions` VALUES (1,'HR Director',1,'We are seeking an experienced HR Director to lead our human resources department. You will be responsible for overseeing all HR functions, including strategy, policy development, and organizational development.','• Master\'s degree in HR or related field.\n• 10+ years of HR experience with 5+ years in a leadership role.\n• Deep knowledge of labor laws and HR best practices.',80000.00,120000.00,1,'2026-03-18 14:33:56'),(2,'HR Specialist',1,'We are looking for an HR Specialist to join our team. You will handle various HR tasks, including employee relations, benefits administration, and policy implementation.','• Bachelor\'s degree in HR or Psychology.\n• 3+ years of HR experience.\n• Strong interpersonal and communication skills.',35000.00,55000.00,1,'2026-03-18 14:33:56'),(3,'Recruitment Officer',1,'We are looking for a Recruitment Officer to manage our end-to-end recruitment process. You will be responsible for sourcing, screening, and hiring the best talent for our company.','• Proven experience as a Recruiter.\n• Proficiency with ATS and job boards.\n• Strong interviewing and assessment skills.',30000.00,50000.00,1,'2026-03-18 14:33:56'),(4,'Finance Manager',2,'We are seeking a Finance Manager to oversee our financial operations. You will be responsible for financial planning, budgeting, and ensuring the company\'s financial health.','• Bachelor\'s or Master\'s degree in Finance or Accounting.\n• 5+ years of experience in financial management.\n• Proficiency in financial software and reporting.',70000.00,100000.00,1,'2026-03-18 14:33:56'),(5,'Accountant',2,'We are seeking an Accountant to manage all financial transactions, from fixed payments and variable expenses to bank deposits and budgets.','• Bachelor\'s degree in Accounting or Finance.\n• Hands-on experience with accounting software.\n• Excellent knowledge of MS Excel and financial regulations.',30000.00,50000.00,1,'2026-03-18 14:33:56'),(6,'IT Manager',3,'We are looking for an IT Manager to lead our technology department. You will manage our IT infrastructure, software systems, and technical support team.','• Bachelor\'s degree in CS or IT.\n• 5+ years of experience in IT management.\n• Strong knowledge of network administration and cybersecurity.',75000.00,110000.00,1,'2026-03-18 14:33:56'),(7,'Software Developer',3,'We are looking for a skilled Software Developer to join our IT team. You will be responsible for developing high-quality software that is aligned with user needs and business goals.','• Proven experience as a Software Developer.\n• Proficiency in PHP, JavaScript, and MySQL.\n• Familiarity with modern frameworks and version control (Git).',40000.00,80000.00,1,'2026-03-18 14:33:56'),(8,'System Administrator',3,'We are seeking a System Administrator to maintain our IT systems and networks. You will ensure that our technology infrastructure is reliable, secure, and efficient.','• Experience in system and network administration.\n• Proficiency in Linux/Windows server management.\n• Familiarity with cloud services and virtualization.',35000.00,60000.00,1,'2026-03-18 14:33:56'),(9,'Operations Manager',4,'We are looking for an Operations Manager to oversee our daily business activities. You will be responsible for improving efficiency, managing resources, and ensuring smooth operations.','• Bachelor\'s degree in Business Administration or related field.\n• 5+ years of experience in operations management.\n• Strong leadership and organizational skills.',60000.00,90000.00,1,'2026-03-18 14:33:56'),(10,'Operations Staff',4,'We are seeking Operations Staff to support our daily business functions. You will assist in coordinate activities, managing supplies, and ensuring operational tasks are completed.','• High school diploma or equivalent.\n• Strong attention to detail and ability to multitask.\n• Good communication and teamwork skills.',20000.00,35000.00,1,'2026-03-18 14:33:56'),(11,'Sales Manager',5,'We are looking for a Sales Manager to lead our sales team and drive revenue growth. You will be responsible for developing sales strategies, managing targets, and mentoring the sales team.','• Proven experience in sales management.\n• Strong track record of meeting or exceeding targets.\n• Excellent negotiation and relationship-building skills.',55000.00,85000.00,1,'2026-03-18 14:33:56'),(12,'Sales Representative',5,'We are seeking a Sales Representative to generate leads and close sales. You will be responsible for identifying potential customers, presenting our products, and building client relationships.','• Experience in sales or customer service.\n• Excellent communication and persuasion skills.\n• Ability to work independently and meet targets.',25000.00,40000.00,1,'2026-03-18 14:33:56'),(13,'Admin Officer',6,'We are looking for an Admin Officer to manage our office operations. You will be responsible for administrative support, office management, and coordinating company events.','• Proven experience as an Administrative Officer or similar role.\n• Proficiency in MS Office.\n• Strong organizational and time-management skills.',20000.00,30000.00,1,'2026-03-18 14:33:56');
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
INSERT INTO `role_permissions` VALUES (1,1),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,34),(1,37),(1,40),(1,42),(1,45),(1,48),(1,49),(1,50),(1,52),(1,53),(1,55),(1,57),(1,58),(1,60),(1,61),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,29),(2,31),(2,34),(2,35),(2,36),(2,37),(2,40),(2,41),(2,42),(2,43),(2,46),(2,48),(2,49),(2,50),(2,52),(2,53),(3,1),(3,2),(3,4),(3,6),(3,7),(3,8),(3,9),(3,10),(3,11),(3,16),(3,17),(3,18),(3,19),(3,20),(3,23),(3,25),(3,31),(3,36),(3,37),(3,40),(3,41),(3,43),(3,46),(3,48),(3,49),(4,1),(4,6),(4,8),(4,9),(4,11),(4,12),(4,13),(4,16),(4,18),(4,20),(4,21),(4,23),(4,24),(4,25),(4,31),(4,35),(4,41),(4,43),(4,45),(4,46),(4,49),(4,52),(4,53),(5,1),(5,2),(5,3),(5,4),(5,6),(5,7),(5,8),(5,9),(5,10),(5,11),(5,16),(5,18),(5,20),(5,21),(5,22),(5,23),(5,25),(5,31),(5,35),(5,41),(5,43),(5,46),(5,49),(6,1),(6,2),(6,3),(6,6),(6,8),(6,9),(6,11),(6,14),(6,15),(6,16),(6,18),(6,20),(6,21),(6,25),(6,31),(6,34),(6,35),(6,41),(6,43),(6,46),(6,49),(6,50),(7,1),(7,6),(7,8),(7,9),(7,11),(7,16),(7,18),(7,20),(7,21),(7,25),(7,31),(7,35),(7,41),(7,43),(7,46),(7,49);
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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (14,'company_name','NexaHR Corporation','Company Name','general','2026-03-20 09:04:58'),(15,'company_address','123 Business Ave, Makati City, Philippines','Company Address','general','2026-03-18 14:33:56'),(16,'company_phone','+63 2 8888 0000','Company Phone','general','2026-03-18 14:33:56'),(17,'company_email','info@NexaHR.com','Company Email','general','2026-03-20 09:05:18'),(18,'work_start_time','08:00','Work Start Time','attendance','2026-03-18 14:33:56'),(19,'work_end_time','17:00','Work End Time','attendance','2026-03-18 14:33:56'),(20,'late_threshold_min','15','Late Threshold (mins)','attendance','2026-03-18 14:33:56'),(21,'overtime_rate','1.25','Overtime Rate','payroll','2026-03-18 14:33:56'),(22,'sss_employee_rate','0.045','SSS Employee Rate','payroll','2026-03-18 14:33:56'),(23,'philhealth_rate','0.05','PhilHealth Rate','payroll','2026-03-18 14:33:56'),(24,'pagibig_rate','0.02','Pag-IBIG Rate','payroll','2026-03-18 14:33:56'),(25,'currency_symbol','₱','Currency Symbol','general','2026-03-18 14:33:56'),(26,'date_format','M d, Y','Date Format','general','2026-03-18 14:33:56'),(31,'work_hours_per_day','8',NULL,'general','2026-03-19 02:48:34'),(33,'late_deduction_rate','0',NULL,'general','2026-03-19 02:48:34'),(34,'sss_employer_rate','8',NULL,'general','2026-03-19 02:48:34'),(35,'office_start_time','08:00',NULL,'general','2026-03-19 02:48:34'),(36,'office_end_time','17:00',NULL,'general','2026-03-19 02:48:34'),(37,'late_grace_period','15',NULL,'general','2026-03-19 02:48:34');
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
  `feedback` text DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_enroll` (`training_id`,`employee_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `training_enrollments_ibfk_1` FOREIGN KEY (`training_id`) REFERENCES `trainings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_enrollments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_enrollments`
--

LOCK TABLES `training_enrollments` WRITE;
/*!40000 ALTER TABLE `training_enrollments` DISABLE KEYS */;
INSERT INTO `training_enrollments` VALUES (7,1,3,'enrolled',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56'),(8,1,4,'enrolled',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56'),(9,1,5,'enrolled',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56'),(10,1,6,'enrolled',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56'),(11,1,7,'enrolled',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56'),(12,1,8,'enrolled',NULL,NULL,NULL,NULL,'2026-03-18 14:33:56'),(13,3,1,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 00:50:54'),(18,2,1,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 00:53:27'),(19,3,11,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 00:53:41'),(34,5,1,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(35,5,2,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(36,5,3,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(37,5,4,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(38,5,5,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(39,5,6,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(40,5,7,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(41,5,8,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(42,5,9,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(43,5,10,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(44,5,11,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(45,5,12,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(46,5,13,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(47,5,14,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(48,5,15,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27'),(49,5,16,'enrolled',NULL,NULL,NULL,NULL,'2026-03-22 01:26:27');
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
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `cost` decimal(12,2) DEFAULT 0.00,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `fk_trainings_dept` (`department_id`),
  CONSTRAINT `fk_trainings_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainings`
--

LOCK TABLES `trainings` WRITE;
/*!40000 ALTER TABLE `trainings` DISABLE KEYS */;
INSERT INTO `trainings` VALUES (1,'Data Privacy Act Compliance','Annual DPA training for all employees','Legal Team','2026-03-18',NULL,NULL,'2026-03-18','Conference Room A',30,NULL,0,0.00,'completed',2,'2026-03-18 14:33:56'),(2,'Leadership Development Program','Management skills enhancement','HR Consulting Group','2026-03-25',NULL,NULL,'2026-03-25','Training Center',15,NULL,0,0.00,'scheduled',2,'2026-03-18 14:33:56'),(3,'Tutorial pano hindi tablan ng selos','dapat palo mag selos','Rome Joseph Lorente','2026-03-23',NULL,NULL,'2026-03-23','https://meet.google.com/yir-gvnx-mwp',20,NULL,0,30.00,'scheduled',1,'2026-03-22 00:17:56'),(4,'Code Session','Sharing Experience','Secret','2026-03-22',NULL,NULL,'2026-03-22','Conference B',NULL,3,0,20.00,'ongoing',1,'2026-03-22 00:32:14'),(5,'testing 123','testing ngani','Rome Joseph Lorente','2026-03-22','09:25:00','09:30:00',NULL,'https://meet.google.com/yir-gvnx-mwp',NULL,NULL,1,0.00,'completed',1,'2026-03-22 01:26:27');
/*!40000 ALTER TABLE `trainings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trusted_devices`
--

DROP TABLE IF EXISTS `trusted_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trusted_devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_UNIQUE` (`token`),
  KEY `fk_trusted_user_idx` (`user_id`),
  CONSTRAINT `fk_trusted_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trusted_devices`
--

LOCK TABLES `trusted_devices` WRITE;
/*!40000 ALTER TABLE `trusted_devices` DISABLE KEYS */;
INSERT INTO `trusted_devices` VALUES (1,11,'8720936070cc7d37b555e74a6b3d1e84450cf4924df3891f7adc1175281b7d9e','2026-03-29 23:37:38','2026-03-19 15:37:38'),(2,11,'b9423a5ca4cdcb18175522185d8005aa1165119b73dac12d2b82d419e8dbe4aa','2026-03-30 08:26:29','2026-03-20 00:26:29'),(3,2,'5e814464b6b6a1b98e767c71e517ea5ccb5599c9973651569fe4e672a8bd21fa','2026-03-30 08:30:35','2026-03-20 00:30:35'),(4,1,'fcb42a140e8fbc9013d33faf50406d5cf69e9349b45e66b84649ba5a01240ccb','2026-03-30 08:32:36','2026-03-20 00:32:36'),(5,11,'9f119379086b844f173079d7bbc45e4c31c62587a1a0e93b0fe16a48a1b786e1','2026-03-30 08:35:32','2026-03-20 00:35:32'),(6,1,'a9e16958e0ac758952e5c0794d200a195a2ded7f9668c2b00da381c447415ec3','2026-03-30 08:43:14','2026-03-20 00:43:14'),(7,1,'65ae04640a9a5903904330f1920b6c5da4c394628ed06403a39c820321bbe16e','2026-03-30 08:56:12','2026-03-20 00:56:12'),(8,1,'62d4dc771aafa0ae77c85789bb4eb6daaf560f74570e3616fc5553d2b46e82b8','2026-03-31 08:19:20','2026-03-21 00:19:20'),(9,2,'9e9de005bee7797e19c99d3ce33b30928591f377c5ff6c1ce5e05fca77207563','2026-03-31 21:16:30','2026-03-21 13:16:30'),(10,4,'e59b3ff1c60a37381c5092c989f22b8ae99c6f55cf61984d1c4829c9e84062f7','2026-03-31 21:19:21','2026-03-21 13:19:21'),(11,11,'386b06c1e6a89185c462b1d3f05dce74c5da7f763f942d30466e1a06a05d950a','2026-03-31 21:24:56','2026-03-21 13:24:56'),(12,3,'43cbf8de5c51bd84456f54ed42e97c2e35c5dd27a13276d6ae78f5090d77f061','2026-03-31 21:31:58','2026-03-21 13:31:58'),(13,11,'d3345d13baeb0be6c3402614a626cff1ebafc024beaea986b48575bf923ade93','2026-04-01 07:57:19','2026-03-21 23:57:19');
/*!40000 ALTER TABLE `trusted_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device` varchar(100) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_session_user` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
INSERT INTO `user_sessions` VALUES (33,1,'ffncimqpep2i8g65suqernsp74','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','Chrome on Windows 11','Local, Localhost','2026-03-22 00:37:28','2026-03-22 00:37:28'),(35,1,'f3vnv21c977fh9ccb9o5003218','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','Chrome on Windows 11','Local, Localhost','2026-03-22 23:52:23','2026-03-22 23:52:23');
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
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
  `session_token` varchar(64) DEFAULT NULL,
  `two_factor_code` varchar(10) DEFAULT NULL,
  `two_factor_expires_at` datetime DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expiry` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `lockout_until` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'vwcxo9ptf6@xkxkud.com','$2y$12$vuaoVJytluqq0T8nquR/Ieqio37iwH9uLyjgxm4WhU6jDaUF8eRpC','1bf5de69eb269b88e35331ba2105dfebdf14c3577cae3f3a5b73f33daacfee20','414912','2026-03-23 08:01:39','System','Administrator','e6ef74d4c0ac5e15c7c9644991b8e797.jpg',1,'2026-03-22 23:52:23',NULL,NULL,'2026-03-18 14:33:56','2026-03-22 23:52:23',0,NULL,NULL,NULL),(2,2,'nkooy22100@minitts.net','$2y$12$bz5Y.RFqsRAlaU6WBjKMrOmF5pFHHEhqvlHSPrQl5ueBHkRzmIVTq','ddad70a9bd645ec124414401c632de42d8ef08e7f793d15c6bdccd2170abc250',NULL,NULL,'Maria','Santos',NULL,1,'2026-03-21 13:16:30',NULL,NULL,'2026-03-18 14:33:56','2026-03-21 13:16:30',0,NULL,NULL,NULL),(3,3,'mxgef10292@minitts.net','$2y$12$iHNHChrYLFFt3V7xi.crbOTUd0NzZKBrtWGoarGlTGgYH99tuLGVe','840cf1f6f57cc011543134675a6b72847e4dfe59e2311a00a6b8777f8e72a28f',NULL,NULL,'Jose','Reyes',NULL,1,'2026-03-21 13:31:58',NULL,NULL,'2026-03-18 14:33:56','2026-03-21 13:31:58',0,NULL,NULL,NULL),(4,4,'jppsd14621@minitts.net','$2y$12$vKn.tDjaMUAC5bHu.dnXu.dlyW9866OHFRxUc5sh0/1Wg5WO05m2m','2d13e63b3afd3e09db130fe16cdc9c0d45cd1f7d31f7f226c02f4b41797f2d35',NULL,NULL,'Ana','Cruz',NULL,1,'2026-03-21 13:21:22',NULL,NULL,'2026-03-18 14:33:56','2026-03-21 13:21:22',0,NULL,NULL,NULL),(5,5,'hrspecialist@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942',NULL,NULL,NULL,'Lorna','Garcia',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56',0,NULL,NULL,NULL),(6,6,'recruiter@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942',NULL,NULL,NULL,'Ryan','Torres',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56',0,NULL,NULL,NULL),(7,3,'employee@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942',NULL,NULL,NULL,'Juan','Dela Cruz','1ca641025a6a1d9e518077c5401c1403.jpg',1,'2026-03-19 11:02:13',NULL,NULL,'2026-03-18 14:33:56','2026-03-21 13:27:56',0,NULL,NULL,NULL),(8,7,'employee2@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942',NULL,NULL,NULL,'Rosa','Mendoza',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56',0,NULL,NULL,NULL),(9,7,'employee3@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942',NULL,NULL,NULL,'Carlo','Buenaventura',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56',0,NULL,NULL,NULL),(10,7,'employee4@hrms.com','$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942',NULL,NULL,NULL,'Patricia','Villanueva',NULL,1,NULL,NULL,NULL,'2026-03-18 14:33:56','2026-03-18 14:33:56',0,NULL,NULL,NULL),(11,3,'lorenteromejoseph@gmail.com','$2y$12$XX/oAvlms.d5gQeK7VWwLOEi3JG1lH7aGJMrfmU7PUoGvN9ixBdBm','cc7ae2d8470b676f7101997d102bcb467207fc79d46d72279cf5ee569c458dfd',NULL,NULL,'Rome','Lorente',NULL,1,'2026-03-22 00:40:59',NULL,NULL,'2026-03-19 08:36:58','2026-03-22 00:40:59',0,NULL,NULL,NULL),(12,7,'crypticalrome@gmail.com','$2y$12$RqK1vsZfZruHdszkepDwweHY0Z6CqD87ZRBpJGV9IyU/2LrrbkWJ6',NULL,NULL,NULL,'Mia','Lorente',NULL,1,NULL,NULL,NULL,'2026-03-20 22:17:21','2026-03-20 22:17:21',0,NULL,NULL,NULL),(13,7,'kityanz09@gmail.com','$2y$12$RILxP0pnUbXaiJaDLQRXyOg/ftU8ELEGtKFlgTAg9APiPGmq9N3P2',NULL,NULL,NULL,'Mia','Lorente',NULL,1,NULL,NULL,NULL,'2026-03-20 22:22:55','2026-03-20 22:22:55',0,NULL,NULL,NULL),(14,7,'zarahlorente@gmail.com','$2y$12$tG8J5Hr3yf./hw81KCYYUeUAbwyp.d5TAA4aBVfDFJh4/RyC3n.26',NULL,NULL,NULL,'Zarah Jane','Santos',NULL,1,NULL,NULL,NULL,'2026-03-20 22:41:58','2026-03-20 22:41:58',0,NULL,NULL,NULL),(15,7,'yvonne@gmail.com','$2y$12$c49h9ZVisEvSY3/5Xpc6JuMNGUGbCBGPvdlYscbJQ8f7V6YctRzAO',NULL,NULL,NULL,'Yvonne','Rasalan',NULL,1,NULL,NULL,NULL,'2026-03-21 00:08:53','2026-03-21 00:08:53',0,NULL,NULL,NULL),(16,7,'grace.n@email.com','$2y$12$oMh2HA0NeXVmBi7ateNjpemfeE/cCHGbzdv5K6.uP9LN6O.8jV2mW',NULL,NULL,NULL,'Grace','Navarro',NULL,1,NULL,NULL,NULL,'2026-03-21 13:11:05','2026-03-21 13:11:05',0,NULL,NULL,NULL);
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

-- Dump completed on 2026-03-23  7:55:03
