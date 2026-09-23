-- ========================================================
-- FTS PORTAL — COMPLETE DATABASE DDL SCHEMA DUMP
-- Database: fts_portal
-- Generated: 2026-09-18 15:57:56
-- Total Tables: 80
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Structure for table `amc_report_system_items` (0 rows, 6 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `amc_report_system_items`;
CREATE TABLE `amc_report_system_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `system_key` varchar(50) NOT NULL,
  `item_slug` varchar(191) NOT NULL,
  `item_label` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `amc_report_system_items_system_key_item_slug_unique` (`system_key`,`item_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `attendance_approvals` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendance_approvals`;
CREATE TABLE `attendance_approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned NOT NULL,
  `action` enum('approved','rejected') NOT NULL,
  `informed` enum('informed','uninformed') DEFAULT NULL,
  `specific_reason` varchar(191) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_approvals_attendance_id_index` (`attendance_id`),
  KEY `attendance_approvals_approved_by_index` (`approved_by`),
  CONSTRAINT `attendance_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_approvals_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `attendance_labor_details` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendance_labor_details`;
CREATE TABLE `attendance_labor_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint(20) unsigned NOT NULL,
  `labor_id` int(10) unsigned NOT NULL,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `site_id` bigint(20) unsigned DEFAULT NULL,
  `custom_site_name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_labor_details_attendance_id_labor_id_unique` (`attendance_id`,`labor_id`),
  KEY `attendance_labor_details_labor_id_foreign` (`labor_id`),
  CONSTRAINT `attendance_labor_details_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_labor_details_labor_id_foreign` FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `attendance_sessions` (0 rows, 20 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendance_sessions`;
CREATE TABLE `attendance_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `session_date` date NOT NULL,
  `clock_in_time` datetime NOT NULL,
  `clock_in_latitude` decimal(10,8) NOT NULL,
  `clock_in_longitude` decimal(11,8) NOT NULL,
  `clock_in_distance_meters` decimal(8,2) DEFAULT NULL,
  `clock_out_time` datetime DEFAULT NULL,
  `clock_out_latitude` decimal(10,8) DEFAULT NULL,
  `clock_out_longitude` decimal(11,8) DEFAULT NULL,
  `clock_out_distance_meters` decimal(8,2) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `shift_window` enum('shift_1','shift_2') NOT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `work_mode` enum('split_shift','continuous') NOT NULL DEFAULT 'split_shift',
  `session_status` enum('open','closed') NOT NULL DEFAULT 'open',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_sessions_user_id_index` (`user_id`),
  KEY `attendance_sessions_session_date_index` (`session_date`),
  KEY `attendance_sessions_user_id_session_date_index` (`user_id`,`session_date`),
  KEY `attendance_sessions_shift_window_index` (`shift_window`),
  KEY `attendance_sessions_is_late_index` (`is_late`),
  KEY `attendance_sessions_session_status_index` (`session_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `attendances` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `labor_id` int(10) unsigned DEFAULT NULL,
  `foreman_id` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `marked_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_labor_id_attendance_date_unique` (`labor_id`,`attendance_date`),
  KEY `attendances_approved_by_foreign` (`approved_by`),
  KEY `attendances_status_index` (`status`),
  KEY `attendances_attendance_date_index` (`attendance_date`),
  KEY `attendances_foreman_id_index` (`foreman_id`),
  CONSTRAINT `attendances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_foreman_id_foreign` FOREIGN KEY (`foreman_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_labor_id_foreign` FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `balance_accounts` (9 rows, 6 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `balance_accounts`;
CREATE TABLE `balance_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `balance` double NOT NULL DEFAULT 0,
  `type` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT 1,
  `code` varchar(191) DEFAULT NULL,
  `account_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `balance_accounts_user_id_foreign` (`user_id`),
  KEY `balance_accounts_type_index` (`type`),
  CONSTRAINT `balance_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `balance_transactions` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `balance_transactions`;
CREATE TABLE `balance_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `extra_account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reference_type` varchar(191) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `balance_transactions_account_id_foreign` (`account_id`),
  KEY `balance_transactions_extra_account_id_foreign` (`extra_account_id`),
  KEY `balance_transactions_reference_type_index` (`reference_type`),
  KEY `balance_transactions_reference_id_index` (`reference_id`),
  CONSTRAINT `balance_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `balance_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `balance_transactions_extra_account_id_foreign` FOREIGN KEY (`extra_account_id`) REFERENCES `balance_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `comments` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commentable_id` bigint(20) unsigned NOT NULL,
  `commentable_type` varchar(191) NOT NULL,
  `comments` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `companies` (3 rows, 23 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `contact_person` varchar(191) DEFAULT NULL,
  `contact_no` varchar(191) DEFAULT NULL,
  `contact_no_two` varchar(191) DEFAULT NULL,
  `vat_no` varchar(191) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `file` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `billing_contact_person` varchar(191) DEFAULT NULL,
  `billing_pob` varchar(191) DEFAULT NULL,
  `billing_email` varchar(191) DEFAULT NULL,
  `shipping_address` varchar(191) DEFAULT NULL,
  `shipping_contact_person` varchar(191) DEFAULT NULL,
  `shipping_pob` varchar(191) DEFAULT NULL,
  `shipping_email` varchar(191) DEFAULT NULL,
  `payment_terms` varchar(191) DEFAULT NULL,
  `credit_limit` varchar(191) DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `device_tokens` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `device_tokens`;
CREATE TABLE `device_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(512) NOT NULL,
  `platform` varchar(20) NOT NULL DEFAULT 'android',
  `device_name` varchar(191) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_tokens_token_unique` (`token`(191)),
  KEY `device_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `documents` (0 rows, 6 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `drawing_received_contributions` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `drawing_received_contributions`;
CREATE TABLE `drawing_received_contributions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `drawing_received_id` int(10) unsigned NOT NULL,
  `contributed_by_id` bigint(20) unsigned NOT NULL,
  `contribution_type` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drawing_received_contributions_drawing_received_id_index` (`drawing_received_id`),
  KEY `drawing_received_contributions_contributed_by_id_index` (`contributed_by_id`),
  KEY `drawing_received_contributions_contribution_type_index` (`contribution_type`),
  CONSTRAINT `drawing_received_contributions_contributed_by_id_foreign` FOREIGN KEY (`contributed_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drawing_received_contributions_drawing_received_id_foreign` FOREIGN KEY (`drawing_received_id`) REFERENCES `drawing_receiveds` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `drawing_receiveds` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `drawing_receiveds`;
CREATE TABLE `drawing_receiveds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lpoin_id` int(10) unsigned NOT NULL,
  `responsible_engineer_id` bigint(20) unsigned NOT NULL,
  `type_of_work` varchar(191) NOT NULL,
  `start_date` date NOT NULL,
  `review_comments_date` date DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'Under Review',
  `notes` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drawing_receiveds_lpoin_id_index` (`lpoin_id`),
  KEY `drawing_receiveds_responsible_engineer_id_index` (`responsible_engineer_id`),
  KEY `drawing_receiveds_status_index` (`status`),
  CONSTRAINT `drawing_receiveds_lpoin_id_foreign` FOREIGN KEY (`lpoin_id`) REFERENCES `lpoins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drawing_receiveds_responsible_engineer_id_foreign` FOREIGN KEY (`responsible_engineer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `employees` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `code` varchar(191) DEFAULT NULL,
  `contact_no` varchar(191) DEFAULT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `failed_jobs` (0 rows, 6 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiries` (2 rows, 21 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE `inquiries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_no` varchar(191) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `client_name` varchar(191) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `project` varchar(191) DEFAULT NULL,
  `inquiry_type` enum('Project','AMC','Installation','Other') NOT NULL,
  `other_type` varchar(191) DEFAULT NULL,
  `source` enum('Call','Email','Walk-in','Website','Referral') NOT NULL,
  `expected_price` decimal(15,2) DEFAULT NULL,
  `status` enum('New','Assigned','Under Review','Site Visit Pending','Site Visit Done','Sent to Sales','Quotation Created','Under Follow-up','Won','Lost','Closed') NOT NULL DEFAULT 'New',
  `priority` enum('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  `follow_up_date` date DEFAULT NULL,
  `expected_closing_date` date DEFAULT NULL,
  `assigned_department` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiries_inquiry_no_unique` (`inquiry_no`),
  KEY `inquiries_created_by_foreign` (`created_by`),
  CONSTRAINT `inquiries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiry_activities` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiry_activities`;
CREATE TABLE `inquiry_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_activities_inquiry_id_foreign` (`inquiry_id`),
  KEY `inquiry_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `inquiry_activities_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inquiry_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiry_department_reviews` (0 rows, 17 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiry_department_reviews`;
CREATE TABLE `inquiry_department_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint(20) unsigned NOT NULL,
  `assigned_department` varchar(191) NOT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `assignment_date` timestamp NULL DEFAULT NULL,
  `current_status` varchar(191) DEFAULT NULL,
  `internal_comments` text DEFAULT NULL,
  `priority` enum('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  `response_deadline` timestamp NULL DEFAULT NULL,
  `technical_review_status` varchar(191) DEFAULT NULL,
  `site_visit_required` tinyint(1) NOT NULL DEFAULT 0,
  `proposed_visit_date` date DEFAULT NULL,
  `visit_assigned_to` bigint(20) unsigned DEFAULT NULL,
  `visit_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_department_reviews_inquiry_id_unique` (`inquiry_id`),
  KEY `inquiry_department_reviews_assigned_to_foreign` (`assigned_to`),
  KEY `inquiry_department_reviews_visit_assigned_to_foreign` (`visit_assigned_to`),
  KEY `inquiry_department_reviews_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `inquiry_department_reviews_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inquiry_department_reviews_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inquiry_department_reviews_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inquiry_department_reviews_visit_assigned_to_foreign` FOREIGN KEY (`visit_assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiry_engineer_reports` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiry_engineer_reports`;
CREATE TABLE `inquiry_engineer_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint(20) unsigned NOT NULL,
  `visit_completed` tinyint(1) NOT NULL DEFAULT 0,
  `site_condition_notes` text DEFAULT NULL,
  `scope_understanding` text DEFAULT NULL,
  `materials_required` text DEFAULT NULL,
  `challenges_risks` text DEFAULT NULL,
  `estimated_cost` decimal(15,2) DEFAULT NULL,
  `submitted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_engineer_reports_inquiry_id_unique` (`inquiry_id`),
  KEY `inquiry_engineer_reports_submitted_by_foreign` (`submitted_by`),
  CONSTRAINT `inquiry_engineer_reports_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inquiry_engineer_reports_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiry_follow_ups` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiry_follow_ups`;
CREATE TABLE `inquiry_follow_ups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint(20) unsigned NOT NULL,
  `follow_up_date` date NOT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `client_feedback` text DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_follow_ups_inquiry_id_foreign` (`inquiry_id`),
  KEY `inquiry_follow_ups_created_by_foreign` (`created_by`),
  CONSTRAINT `inquiry_follow_ups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inquiry_follow_ups_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiry_quotations` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiry_quotations`;
CREATE TABLE `inquiry_quotations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint(20) unsigned NOT NULL,
  `quotation_amount` decimal(15,2) DEFAULT NULL,
  `scope_of_work` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `validity_date` date DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_quotations_inquiry_id_unique` (`inquiry_id`),
  KEY `inquiry_quotations_created_by_foreign` (`created_by`),
  CONSTRAINT `inquiry_quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inquiry_quotations_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `inquiry_routing_configs` (4 rows, 6 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiry_routing_configs`;
CREATE TABLE `inquiry_routing_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_type` varchar(191) NOT NULL,
  `department` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_routing_configs_inquiry_type_unique` (`inquiry_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoice_banks` (4 rows, 10 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoice_banks`;
CREATE TABLE `invoice_banks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `beneficary_account_name` varchar(191) NOT NULL,
  `bank_name` varchar(191) NOT NULL,
  `bank_branch` varchar(191) DEFAULT NULL,
  `account_no` varchar(191) NOT NULL,
  `account_currency` varchar(191) NOT NULL,
  `iban_no` varchar(191) DEFAULT NULL,
  `swift_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoice_product_details` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoice_product_details`;
CREATE TABLE `invoice_product_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `product` varchar(191) NOT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `rate` double NOT NULL,
  `amount` double NOT NULL,
  `vat` double NOT NULL,
  `total_amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_product_details_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_product_details_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoice_request_products` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoice_request_products`;
CREATE TABLE `invoice_request_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int(10) unsigned NOT NULL,
  `product` varchar(191) NOT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `rate` double NOT NULL,
  `amount` double NOT NULL,
  `vat` double NOT NULL,
  `total_amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_request_products_request_id_foreign` (`request_id`),
  CONSTRAINT `invoice_request_products_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `invoice_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoice_requests` (0 rows, 10 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoice_requests`;
CREATE TABLE `invoice_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `requestable_id` int(10) unsigned NOT NULL,
  `requestable_type` varchar(191) NOT NULL,
  `note` text DEFAULT NULL,
  `payment_terms` varchar(191) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_requests_user_id_foreign` (`user_id`),
  CONSTRAINT `invoice_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoice_service_details` (0 rows, 6 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoice_service_details`;
CREATE TABLE `invoice_service_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_service_details_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_service_details_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoice_types` (2 rows, 4 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoice_types`;
CREATE TABLE `invoice_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `invoices` (0 rows, 20 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(191) NOT NULL,
  `invoice_type_id` int(10) unsigned NOT NULL,
  `quotation_id` int(10) unsigned NOT NULL,
  `invoice_request_id` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `currency` varchar(191) NOT NULL DEFAULT 'AED',
  `payment_terms` varchar(191) DEFAULT NULL,
  `amount_in_word` varchar(191) DEFAULT NULL,
  `note1` text DEFAULT NULL,
  `note2` text DEFAULT NULL,
  `invoice_bank_id` int(10) unsigned NOT NULL DEFAULT 0,
  `amount` double NOT NULL DEFAULT 0,
  `vat` double NOT NULL DEFAULT 0,
  `total_amount` double NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_invoice_type_id_foreign` (`invoice_type_id`),
  KEY `invoices_quotation_id_foreign` (`quotation_id`),
  CONSTRAINT `invoices_invoice_type_id_foreign` FOREIGN KEY (`invoice_type_id`) REFERENCES `invoice_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `labor_requests` (0 rows, 10 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `labor_requests`;
CREATE TABLE `labor_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `labor_id` int(10) unsigned NOT NULL,
  `type` varchar(191) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `advance_money` double DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `labor_requests_labor_id_foreign` (`labor_id`),
  CONSTRAINT `labor_requests_labor_id_foreign` FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `labour_assignments` (0 rows, 10 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `labour_assignments`;
CREATE TABLE `labour_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `labor_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `visit_schedule_id` bigint(20) unsigned DEFAULT NULL,
  `assignment_start_date` date DEFAULT NULL,
  `assignment_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hours_worked` int(11) DEFAULT NULL,
  `overtime_hours` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `labour_assignments_labor_id_foreign` (`labor_id`),
  KEY `labour_assignments_project_id_foreign` (`project_id`),
  KEY `labour_assignments_visit_schedule_id_foreign` (`visit_schedule_id`),
  CONSTRAINT `labour_assignments_labor_id_foreign` FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE,
  CONSTRAINT `labour_assignments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `labour_assignments_visit_schedule_id_foreign` FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `letters` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `letters`;
CREATE TABLE `letters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(191) DEFAULT NULL,
  `days_deduct` int(10) unsigned DEFAULT NULL,
  `staff_profile_id` int(10) unsigned NOT NULL,
  `type` enum('warning','appreciation','general_notice','poor_performance_notice','accommodation_notice','vehicle_notice','attendance_notice','weather_notice','eid_holidays_notice') DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `issued_by` varchar(191) NOT NULL,
  `issued_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `letters_staff_profile_id_foreign` (`staff_profile_id`),
  CONSTRAINT `letters_staff_profile_id_foreign` FOREIGN KEY (`staff_profile_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `lookups` (13 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lookups`;
CREATE TABLE `lookups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `tag` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `lpo_out_types` (2 rows, 4 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lpo_out_types`;
CREATE TABLE `lpo_out_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `lpoins` (0 rows, 12 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lpoins`;
CREATE TABLE `lpoins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int(10) unsigned NOT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `payment_terms` varchar(191) DEFAULT NULL,
  `civil_defence_fee` double NOT NULL DEFAULT 0,
  `government_fee` double NOT NULL DEFAULT 0,
  `adjustment_fee` double NOT NULL DEFAULT 0,
  `date_issue` date DEFAULT NULL,
  `date_due` date DEFAULT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lpoins_quotation_id_foreign` (`quotation_id`),
  CONSTRAINT `lpoins_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `lpoouts` (0 rows, 32 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lpoouts`;
CREATE TABLE `lpoouts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lpo_invoice_no` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `lpo_out_type_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `vendor_id` int(10) unsigned NOT NULL,
  `trn_no` varchar(191) DEFAULT NULL,
  `kindly_attn` varchar(191) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` double NOT NULL,
  `terms` longtext DEFAULT NULL,
  `payment_type` varchar(191) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `pricing_mode` varchar(191) NOT NULL DEFAULT 'unit',
  `has_item_code` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Pending','Approved','Not Approved') NOT NULL DEFAULT 'Pending',
  `revision_number` int(11) NOT NULL DEFAULT 1,
  `parent_lpoout_id` bigint(20) unsigned DEFAULT NULL,
  `is_latest_revision` tinyint(1) NOT NULL DEFAULT 1,
  `revised_by` bigint(20) unsigned DEFAULT NULL,
  `revision_reason` text DEFAULT NULL,
  `revised_at` timestamp NULL DEFAULT NULL,
  `lpo_payment_preference_option` varchar(191) NOT NULL DEFAULT 'default',
  `lpo_payment_preference` varchar(191) DEFAULT NULL,
  `lpo_pdc_number_of_days` int(11) DEFAULT NULL,
  `lpo_pdc_payment_option` varchar(191) DEFAULT NULL,
  `vat` double NOT NULL,
  `total_amount` double NOT NULL,
  `file` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lpoouts_lpo_out_type_id_foreign` (`lpo_out_type_id`),
  KEY `lpoouts_vendor_id_foreign` (`vendor_id`),
  KEY `lpoouts_project_id_foreign` (`project_id`),
  CONSTRAINT `lpoouts_lpo_out_type_id_foreign` FOREIGN KEY (`lpo_out_type_id`) REFERENCES `lpo_out_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lpoouts_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lpoouts_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `media` (0 rows, 15 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `collection_name` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `mime_type` varchar(191) DEFAULT NULL,
  `disk` varchar(191) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `migrations` (131 rows, 3 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `model_has_permissions` (0 rows, 3 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `model_has_roles` (3 rows, 3 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `monthly_staff_reports` (0 rows, 15 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `monthly_staff_reports`;
CREATE TABLE `monthly_staff_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `safety_avg` double(8,2) NOT NULL,
  `communication_avg` double(8,2) NOT NULL,
  `attendance_avg` double(8,2) NOT NULL,
  `time_management_avg` double(8,2) NOT NULL,
  `job_responsibility_avg` double(8,2) NOT NULL,
  `material_handling_avg` double(8,2) NOT NULL,
  `document_handling_avg` double(8,2) NOT NULL,
  `competency_avg` double(8,2) NOT NULL,
  `final_score` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_monthly_report` (`staff_id`,`month`,`year`),
  CONSTRAINT `monthly_staff_reports_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `notifications` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `order_items` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `item_description` varchar(191) NOT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `quantity` double(8,2) NOT NULL,
  `unit_price` double(8,2) NOT NULL,
  `total` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `orders` (0 rows, 16 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `trn` varchar(191) NOT NULL,
  `vendor_id` int(10) unsigned NOT NULL,
  `attn` varchar(191) DEFAULT NULL,
  `ship_to` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(191) DEFAULT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `total_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_after_discount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_with_vat` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `password_resets` (0 rows, 3 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `payment_booking_approvals` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payment_booking_approvals`;
CREATE TABLE `payment_booking_approvals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_booking_id` int(10) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `decision` tinyint(3) unsigned NOT NULL,
  `note` text DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_booking_approvals_unique_level` (`payment_booking_id`,`level`),
  KEY `payment_booking_approvals_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `payment_bookings` (0 rows, 24 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payment_bookings`;
CREATE TABLE `payment_bookings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(40) NOT NULL,
  `booking_type` varchar(10) NOT NULL,
  `payee` varchar(191) NOT NULL,
  `payment_against` varchar(191) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `project_cost_centre` varchar(191) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `cheque_number` varchar(60) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `bank_account` varchar(191) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `cash_account` varchar(191) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_bookings_reference_no_unique` (`reference_no`),
  KEY `payment_bookings_booking_type_index` (`booking_type`),
  KEY `payment_bookings_status_index` (`status`),
  KEY `payment_bookings_created_by_index` (`created_by`),
  KEY `payment_bookings_release_date_index` (`release_date`),
  KEY `payment_bookings_payment_date_index` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `payment_invoices` (0 rows, 23 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payment_invoices`;
CREATE TABLE `payment_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) DEFAULT NULL,
  `invoice_no` varchar(191) DEFAULT NULL,
  `lpoout_id` int(10) unsigned DEFAULT NULL,
  `invoice_request_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `amount` double NOT NULL,
  `vat` double NOT NULL,
  `total_amount` double NOT NULL,
  `note` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `is_historical` tinyint(1) NOT NULL DEFAULT 0,
  `source_reference` varchar(191) DEFAULT NULL,
  `source_date` date DEFAULT NULL,
  `historical_party` varchar(191) DEFAULT NULL,
  `historical_project` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `document_path` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_invoices_lpoout_id_foreign` (`lpoout_id`),
  CONSTRAINT `payment_invoices_lpoout_id_foreign` FOREIGN KEY (`lpoout_id`) REFERENCES `lpoouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `payrolls` (0 rows, 11 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payrolls`;
CREATE TABLE `payrolls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `absents` int(11) DEFAULT NULL,
  `hours` double DEFAULT NULL,
  `plus_adjustment` double NOT NULL DEFAULT 0,
  `minus_adjustment` double NOT NULL DEFAULT 0,
  `total_amount` double NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payrolls_member_id_foreign` (`member_id`),
  CONSTRAINT `payrolls_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `permissions` (33 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `petty_cash_expenses` (0 rows, 7 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `petty_cash_expenses`;
CREATE TABLE `petty_cash_expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `category` varchar(191) DEFAULT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `petty_cashes` (0 rows, 16 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `petty_cashes`;
CREATE TABLE `petty_cashes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `voucher_no` varchar(191) DEFAULT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `vendor_id` int(10) unsigned DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` double(8,2) NOT NULL,
  `vat` double(8,2) NOT NULL,
  `total_amount` double(8,2) NOT NULL,
  `is_advance` int(11) DEFAULT NULL,
  `is_user_deduction` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `petty_cashes_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `petty_cashes_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `products` (6 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `project_extension` (0 rows, 7 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `project_extension`;
CREATE TABLE `project_extension` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `value` double NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `quotation_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_extension_project_id_foreign` (`project_id`),
  KEY `project_extension_quotation_id_foreign` (`quotation_id`),
  CONSTRAINT `project_extension_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `project_extension_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `project_lpoout` (0 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `project_lpoout`;
CREATE TABLE `project_lpoout` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `lpoout_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_lpoout_project_id_foreign` (`project_id`),
  KEY `project_lpoout_lpoout_id_foreign` (`lpoout_id`),
  CONSTRAINT `project_lpoout_lpoout_id_foreign` FOREIGN KEY (`lpoout_id`) REFERENCES `lpoouts` (`id`),
  CONSTRAINT `project_lpoout_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `project_types` (6 rows, 4 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `project_types`;
CREATE TABLE `project_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `projectreports` (0 rows, 45 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `projectreports`;
CREATE TABLE `projectreports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(191) NOT NULL,
  `date` date NOT NULL,
  `inspector_visiting_time` time DEFAULT NULL,
  `inspector_leaving_time` time DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `is_manual_client` tinyint(1) NOT NULL DEFAULT 0,
  `manual_client_name` varchar(191) DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `visit_schedule_id` bigint(20) unsigned DEFAULT NULL,
  `is_emergency_visit` tinyint(1) NOT NULL DEFAULT 0,
  `emergency_visit_date` date DEFAULT NULL,
  `amc_type` enum('existing','new') NOT NULL DEFAULT 'existing',
  `site_location` varchar(191) DEFAULT NULL,
  `site_name` varchar(191) DEFAULT NULL,
  `used_items` text DEFAULT NULL,
  `required_items` text DEFAULT NULL,
  `block_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`block_info`)),
  `note` text DEFAULT NULL,
  `client_signature` text DEFAULT NULL,
  `file_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_path`)),
  `status` enum('draft','pending','approved','disapproved') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fire_alarm_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fire_alarm_data`)),
  `fire_fighting_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fire_fighting_data`)),
  `fm200_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fm200_data`)),
  `foam_tank_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`foam_tank_data`)),
  `voice_evacuation_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`voice_evacuation_data`)),
  `emergency_lighting_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`emergency_lighting_data`)),
  `system_interfacing_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`system_interfacing_data`)),
  `exit_route_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exit_route_data`)),
  `storage_conditions_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`storage_conditions_data`)),
  `pump_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pump_data`)),
  `deluge_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`deluge_data`)),
  `urgent_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`urgent_summary`)),
  `photos_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos_data`)),
  `scope_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scope_data`)),
  `next_due_dates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`next_due_dates`)),
  `notes_used_items` text DEFAULT NULL,
  `next_inspection_due` date DEFAULT NULL,
  `expiry_update_required_on` date DEFAULT NULL,
  `client_eid_details` varchar(191) DEFAULT NULL,
  `client_phone` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projectreports_company_id_foreign` (`company_id`),
  KEY `projectreports_project_id_foreign` (`project_id`),
  KEY `projectreports_visit_schedule_id_foreign` (`visit_schedule_id`),
  KEY `projectreports_created_by_index` (`created_by`),
  CONSTRAINT `projectreports_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projectreports_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projectreports_visit_schedule_id_foreign` FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `projects` (1 rows, 17 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `quotation_id` int(10) unsigned NOT NULL,
  `project_type_id` int(10) unsigned NOT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `payment_terms` varchar(191) DEFAULT NULL,
  `labour_charges` double DEFAULT NULL,
  `material_charges` double DEFAULT NULL,
  `project_source` varchar(191) DEFAULT NULL,
  `project_estimation` varchar(191) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` varchar(191) NOT NULL DEFAULT 'normal',
  `visits` int(11) DEFAULT NULL,
  `visit_schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`visit_schedule`)),
  PRIMARY KEY (`id`),
  KEY `projects_quotation_id_foreign` (`quotation_id`),
  CONSTRAINT `projects_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `purchase_orders` (0 rows, 44 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_number` varchar(191) NOT NULL,
  `request_type` varchar(191) NOT NULL DEFAULT 'general',
  `quotation_id` bigint(20) DEFAULT NULL,
  `project_id` bigint(20) DEFAULT NULL,
  `other_info` text DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(191) NOT NULL DEFAULT 'Pending',
  `urgency_level` enum('normal','urgent') NOT NULL DEFAULT 'normal',
  `department_status` varchar(191) NOT NULL DEFAULT 'Pending',
  `department_notes` text DEFAULT NULL,
  `sent_back_notes` text DEFAULT NULL,
  `sent_back_at` timestamp NULL DEFAULT NULL,
  `sent_back_by` bigint(20) DEFAULT NULL,
  `sent_back_count` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_id` bigint(20) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `lpout_id` int(10) unsigned DEFAULT NULL,
  `revised_from_lpoout_id` int(10) unsigned DEFAULT NULL,
  `lpout_name` varchar(191) DEFAULT NULL,
  `lpout_vendor_id` bigint(20) DEFAULT NULL,
  `lpout_trn_no` varchar(191) DEFAULT NULL,
  `lpout_kindly_attn` varchar(191) DEFAULT NULL,
  `lpout_date` date DEFAULT NULL,
  `lpout_payment_type` varchar(191) DEFAULT NULL,
  `lpout_cheque_date` date DEFAULT NULL,
  `lpout_vat` tinyint(4) DEFAULT 0,
  `lpout_payment_preference_option` varchar(191) DEFAULT 'default',
  `lpout_payment_preference` varchar(191) DEFAULT NULL,
  `lpout_pdc_number_of_days` int(11) DEFAULT NULL,
  `lpout_pdc_payment_option` varchar(191) DEFAULT NULL,
  `lpout_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`lpout_items`)),
  `lpout_pricing_mode` varchar(191) NOT NULL DEFAULT 'unit',
  `lpout_manual_total` decimal(15,2) DEFAULT NULL,
  `has_item_code` tinyint(1) NOT NULL DEFAULT 0,
  `lpout_terms` longtext DEFAULT NULL,
  `payment_preference` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_request_number_unique` (`request_number`),
  KEY `purchase_orders_lpout_id_foreign` (`lpout_id`),
  KEY `purchase_orders_revised_from_lpoout_id_foreign` (`revised_from_lpoout_id`),
  CONSTRAINT `purchase_orders_lpout_id_foreign` FOREIGN KEY (`lpout_id`) REFERENCES `lpoouts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_revised_from_lpoout_id_foreign` FOREIGN KEY (`revised_from_lpoout_id`) REFERENCES `lpoouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `qr_staff_attendances` (0 rows, 25 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `qr_staff_attendances`;
CREATE TABLE `qr_staff_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(20) unsigned NOT NULL,
  `scanned_by` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned DEFAULT NULL,
  `custom_site_name` varchar(191) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `overtime_minutes` int(11) NOT NULL DEFAULT 0,
  `shift_end_time` time NOT NULL DEFAULT '17:00:00',
  `status` enum('checked_in','checked_out') NOT NULL DEFAULT 'checked_in',
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `final_overtime_source` enum('manual','qr','custom') DEFAULT NULL,
  `final_overtime_minutes` int(11) DEFAULT NULL,
  `finalized_by` bigint(20) unsigned DEFAULT NULL,
  `finalized_at` datetime DEFAULT NULL,
  `final_decision_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qr_staff_attendances_staff_id_index` (`staff_id`),
  KEY `qr_staff_attendances_scanned_by_index` (`scanned_by`),
  KEY `qr_staff_attendances_attendance_date_index` (`attendance_date`),
  KEY `qr_staff_attendances_staff_id_attendance_date_index` (`staff_id`,`attendance_date`),
  KEY `qr_staff_attendances_status_index` (`status`),
  KEY `qr_staff_attendances_review_status_index` (`review_status`),
  KEY `qr_staff_attendances_reviewed_by_index` (`reviewed_by`),
  KEY `qr_staff_attendances_final_overtime_source_index` (`final_overtime_source`),
  KEY `qr_staff_attendances_finalized_by_index` (`finalized_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `quotation_products` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `quotation_products`;
CREATE TABLE `quotation_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_products_quotation_id_foreign` (`quotation_id`),
  KEY `quotation_products_product_id_foreign` (`product_id`),
  CONSTRAINT `quotation_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotation_products_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `quotations` (0 rows, 22 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `quotations`;
CREATE TABLE `quotations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_project_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `quotation_type_id` int(11) DEFAULT NULL,
  `quotation_company` int(11) DEFAULT NULL,
  `amount` double NOT NULL,
  `vat` double NOT NULL,
  `total_amount` double NOT NULL,
  `date` date DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `file` text DEFAULT NULL,
  `payment` text DEFAULT NULL,
  `exclusion` text DEFAULT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT 0,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` varchar(191) DEFAULT NULL,
  `number_of_visits` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotations_company_id_foreign` (`company_id`),
  KEY `quotations_reference_project_id_foreign` (`reference_project_id`),
  KEY `quotations_approved_by_foreign` (`approved_by`),
  CONSTRAINT `quotations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotations_reference_project_id_foreign` FOREIGN KEY (`reference_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `request_approvals` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `request_approvals`;
CREATE TABLE `request_approvals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_type` varchar(20) NOT NULL,
  `request_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `decision` tinyint(3) unsigned NOT NULL,
  `note` text DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_approvals_unique_decision` (`request_type`,`request_id`,`user_id`),
  KEY `request_approvals_request_type_request_id_index` (`request_type`,`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `request_forms` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `request_forms`;
CREATE TABLE `request_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `note` text NOT NULL,
  `comments` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_forms_user_id_foreign` (`user_id`),
  CONSTRAINT `request_forms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `role_has_permissions` (50 rows, 2 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `roles` (2 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `sites` (0 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(191) NOT NULL,
  `engineer_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sites_site_name_unique` (`site_name`),
  KEY `sites_engineer_id_index` (`engineer_id`),
  KEY `sites_site_name_index` (`site_name`),
  CONSTRAINT `sites_engineer_id_foreign` FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `staf_dates` (0 rows, 7 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staf_dates`;
CREATE TABLE `staf_dates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `staf_profile` (2 rows, 26 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staf_profile`;
CREATE TABLE `staf_profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `staf_type` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `nationality` varchar(191) DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `passport_expiry` date DEFAULT NULL,
  `visa_expiry` date DEFAULT NULL,
  `emirates_id_expiry` date DEFAULT NULL,
  `labor_card_expiry` date DEFAULT NULL,
  `driver_permit_expiry` date DEFAULT NULL,
  `last_vacation_start` date DEFAULT NULL,
  `last_vacation_end` date DEFAULT NULL,
  `last_vacation_days` int(11) DEFAULT NULL,
  `last_increment` date DEFAULT NULL,
  `last_increment_amount` int(11) DEFAULT NULL,
  `basic_salary` double NOT NULL DEFAULT 0,
  `total_salary` double NOT NULL DEFAULT 0,
  `overtime_rate` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mobile_no` varchar(191) DEFAULT NULL,
  `home_mobile_no` varchar(191) DEFAULT NULL,
  `exclude_from_expiry` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `staff_ratings` (0 rows, 15 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff_ratings`;
CREATE TABLE `staff_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `engineer_id` bigint(20) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `safety_compliance` tinyint(3) unsigned NOT NULL,
  `communication` tinyint(3) unsigned NOT NULL,
  `attendance` tinyint(3) unsigned NOT NULL,
  `time_management` tinyint(3) unsigned NOT NULL,
  `job_responsibility` tinyint(3) unsigned NOT NULL,
  `material_handling` tinyint(3) unsigned NOT NULL,
  `document_handling` tinyint(3) unsigned NOT NULL,
  `competency` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating_per_engineer` (`staff_id`,`engineer_id`,`month`,`year`),
  KEY `staff_ratings_engineer_id_foreign` (`engineer_id`),
  CONSTRAINT `staff_ratings_engineer_id_foreign` FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_ratings_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `staff_requests` (0 rows, 12 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff_requests`;
CREATE TABLE `staff_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staf_id` int(10) unsigned NOT NULL,
  `type` varchar(191) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `advance_money` double DEFAULT NULL,
  `device_type` varchar(191) DEFAULT NULL,
  `letter_type` varchar(191) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_requests_staf_id_foreign` (`staf_id`),
  CONSTRAINT `staff_requests_staf_id_foreign` FOREIGN KEY (`staf_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `tasks` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `assigned` bigint(20) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `tickets` (0 rows, 14 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staf_id` int(10) unsigned NOT NULL,
  `ticket_date` date NOT NULL,
  `agent_name` varchar(191) NOT NULL,
  `travel_type` enum('One Way','Two Way') NOT NULL,
  `travel_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `total_value` decimal(10,2) NOT NULL,
  `payment_status` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_staf_id_foreign` (`staf_id`),
  KEY `tickets_updated_by_foreign` (`updated_by`),
  CONSTRAINT `tickets_staf_id_foreign` FOREIGN KEY (`staf_id`) REFERENCES `staf_profile` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `transactions` (0 rows, 20 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` varchar(191) NOT NULL,
  `transactionable_id` int(11) DEFAULT NULL,
  `transactionable_type` varchar(191) DEFAULT NULL,
  `expense_account` int(10) unsigned DEFAULT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `transaction_type` int(11) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `payment_no` varchar(191) DEFAULT NULL,
  `clearance_date` varchar(191) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `vat` double NOT NULL,
  `amount` double NOT NULL,
  `total` double NOT NULL,
  `note` text DEFAULT NULL,
  `from_account` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `users` (3 rows, 14 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `firebase_token` varchar(191) DEFAULT NULL,
  `api_token` varchar(80) DEFAULT NULL,
  `staf_profile_id` int(10) unsigned DEFAULT NULL,
  `balance` double(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`),
  KEY `users_staf_profile_id_foreign` (`staf_profile_id`),
  CONSTRAINT `users_staf_profile_id_foreign` FOREIGN KEY (`staf_profile_id`) REFERENCES `staf_profile` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `vendors` (2 rows, 19 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`emails`)),
  `contact_person` varchar(191) DEFAULT NULL,
  `contact_no` varchar(191) DEFAULT NULL,
  `contact_no_two` varchar(191) DEFAULT NULL,
  `vat_no` varchar(191) DEFAULT NULL,
  `payment_terms` varchar(191) DEFAULT NULL,
  `terms_and_conditions` longtext DEFAULT NULL,
  `credit_limit` varchar(191) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `file` text DEFAULT NULL,
  `payment_preference` varchar(191) NOT NULL DEFAULT 'cod',
  `pdc_number_of_days` int(11) DEFAULT NULL,
  `pdc_payment_option` varchar(191) DEFAULT NULL,
  `vendor_specialization` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendors_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `verify_emails` (0 rows, 4 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `verify_emails`;
CREATE TABLE `verify_emails` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `visit_schedule_comments` (0 rows, 5 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `visit_schedule_comments`;
CREATE TABLE `visit_schedule_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_schedule_id` bigint(20) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_schedule_comments_visit_schedule_id_foreign` (`visit_schedule_id`),
  CONSTRAINT `visit_schedule_comments_visit_schedule_id_foreign` FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `visit_schedule_history` (0 rows, 9 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `visit_schedule_history`;
CREATE TABLE `visit_schedule_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `visit_schedule_id` bigint(20) unsigned DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `company_name` varchar(191) NOT NULL,
  `project_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_schedule_history_project_id_foreign` (`project_id`),
  KEY `visit_schedule_history_visit_schedule_id_foreign` (`visit_schedule_id`),
  CONSTRAINT `visit_schedule_history_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visit_schedule_history_visit_schedule_id_foreign` FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `visit_schedules` (0 rows, 8 columns)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `visit_schedules`;
CREATE TABLE `visit_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `visit_date` date NOT NULL,
  `status` enum('pending','done','upcoming') NOT NULL DEFAULT 'pending',
  `file_uploaded` tinyint(1) NOT NULL DEFAULT 0,
  `file_path` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_schedules_project_id_foreign` (`project_id`),
  CONSTRAINT `visit_schedules_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
