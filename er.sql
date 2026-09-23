CREATE TABLE `users` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `email` varchar(255) UNIQUE,
  `password` varchar(255),
  `api_token` varchar(255) UNIQUE,
  `staf_profile_id` int,
  `balance` double,
  `firebase_token` varchar(255),
  `deleted_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `roles` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `guard_name` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `permissions` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `guard_name` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `model_has_roles` (
  `role_id` bigint,
  `model_type` varchar(255),
  `model_id` bigint,
  PRIMARY KEY (`role_id`, `model_type`, `model_id`)
);

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint,
  `model_type` varchar(255),
  `model_id` bigint,
  PRIMARY KEY (`permission_id`, `model_type`, `model_id`)
);

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint,
  `role_id` bigint,
  PRIMARY KEY (`permission_id`, `role_id`)
);

CREATE TABLE `companies` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `email` varchar(255) UNIQUE,
  `contact_person` varchar(255),
  `contact_no` varchar(255),
  `vat_no` varchar(255),
  `billing_address` text,
  `shipping_address` text,
  `payment_terms` varchar(255),
  `credit_limit` varchar(255),
  `password` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiries` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_no` varchar(255) UNIQUE,
  `created_by` bigint,
  `client_name` varchar(255),
  `phone` varchar(255),
  `email` varchar(255),
  `inquiry_type` varchar(255),
  `source` varchar(255),
  `expected_price` decimal,
  `status` varchar(255),
  `priority` varchar(255),
  `follow_up_date` date,
  `assigned_department` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiries_status_history` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_id` bigint,
  `changed_by` bigint,
  `old_status` varchar(255),
  `new_status` varchar(255),
  `comments` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiry_department_reviews` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_id` bigint,
  `assigned_department` varchar(255),
  `assigned_to` bigint,
  `priority` varchar(255),
  `reviewed_by` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiry_engineer_reports` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_id` bigint,
  `visit_completed` boolean,
  `scope_understanding` text,
  `estimated_cost` decimal,
  `submitted_by` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiry_follow_ups` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_id` bigint,
  `follow_up_date` date,
  `follow_up_notes` text,
  `client_feedback` text,
  `created_by` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiry_quotations` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_id` bigint,
  `quotation_amount` decimal,
  `scope_of_work` text,
  `validity_date` date,
  `created_by` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiry_activities` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_id` bigint,
  `user_id` bigint,
  `action` varchar(255),
  `description` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `inquiry_routing_configs` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `inquiry_type` varchar(255) UNIQUE,
  `department` varchar(255),
  `is_active` boolean,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `quotations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `company_id` int,
  `reference_project_id` int,
  `name` varchar(255),
  `ref_no` varchar(255),
  `amount` double,
  `vat` double,
  `total_amount` double,
  `date` date,
  `subject` varchar(255),
  `status` int,
  `approved_by` bigint,
  `category` varchar(255),
  `number_of_visits` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `quotation_products` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `quotation_id` int,
  `product_id` int,
  `quantity` int,
  `unit_price` decimal,
  `total_price` decimal,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `products` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `price` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `projects` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `quotation_id` int,
  `project_type_id` int,
  `user_id` int,
  `subject` varchar(255),
  `category` varchar(255),
  `date` date,
  `payment_terms` varchar(255),
  `labour_charges` double,
  `material_charges` double,
  `project_estimation` varchar(255),
  `visits` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `project_types` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `project_extension` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `project_id` int,
  `quotation_id` int,
  `name` varchar(255),
  `value` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `visit_schedules` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `project_id` int,
  `visit_date` date,
  `status` varchar(255),
  `file_uploaded` boolean,
  `file_path` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `visit_schedule_comments` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `visit_schedule_id` bigint,
  `comment` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `visit_schedule_history` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `project_id` int,
  `visit_schedule_id` bigint,
  `visit_date` date,
  `status` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `projectreports` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `reference_number` varchar(255),
  `date` date,
  `company_id` int,
  `project_id` int,
  `visit_schedule_id` bigint,
  `amc_type` varchar(255),
  `site_location` varchar(255),
  `status` varchar(255),
  `created_by` bigint,
  `next_inspection_due` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `amc_report_system_items` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `system_key` varchar(255),
  `item_slug` varchar(255) UNIQUE,
  `item_label` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `drawing_receiveds` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `lpoin_id` int,
  `responsible_engineer_id` bigint,
  `type_of_work` varchar(255),
  `start_date` date,
  `status` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `drawing_received_contributions` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `drawing_received_id` int,
  `contributed_by_id` bigint,
  `contribution_type` varchar(255),
  `status` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `vendors` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `email` varchar(255) UNIQUE,
  `contact_person` varchar(255),
  `vat_no` varchar(255),
  `payment_terms` varchar(255),
  `payment_preference` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `lpoins` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `quotation_id` int,
  `ref_no` varchar(255),
  `civil_defence_fee` double,
  `government_fee` double,
  `amount` double,
  `date_issue` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `lpoouts` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `vendor_id` int,
  `project_id` int,
  `lpo_out_type_id` int,
  `lpo_invoice_no` varchar(255),
  `name` varchar(255),
  `date` date,
  `amount` double,
  `vat` double,
  `total_amount` double,
  `payment_preference` varchar(255),
  `status` varchar(255),
  `revision_number` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `lpo_out_types` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `project_lpoout` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `project_id` int,
  `lpoout_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `purchase_orders` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `request_number` varchar(255) UNIQUE,
  `request_type` varchar(255),
  `quotation_id` bigint,
  `project_id` bigint,
  `created_by` bigint,
  `date` date,
  `status` varchar(255),
  `department_status` varchar(255),
  `admin_id` bigint,
  `urgency_level` varchar(255),
  `total_amount` decimal,
  `lpout_id` int,
  `due_date` date,
  `delivery_date` date,
  `revised_from_lpoout_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `orders` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `vendor_id` int,
  `date` date,
  `total_amount` decimal,
  `vat` decimal,
  `total_with_vat` decimal,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `order_items` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `order_id` bigint,
  `item_description` varchar(255),
  `quantity` double,
  `unit_price` double,
  `total` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoices` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `invoice_type_id` int,
  `quotation_id` int,
  `invoice_no` varchar(255),
  `amount` double,
  `vat` double,
  `total_amount` double,
  `status` int,
  `invoice_bank_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoice_types` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoice_banks` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `beneficary_account_name` varchar(255),
  `bank_name` varchar(255),
  `account_no` varchar(255),
  `iban_no` varchar(255),
  `swift_code` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoice_product_details` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `invoice_id` int,
  `product` varchar(255),
  `qty` int,
  `rate` double,
  `amount` double,
  `vat` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoice_service_details` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `invoice_id` int,
  `description` text,
  `amount` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoice_requests` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint,
  `requestable_id` int,
  `requestable_type` varchar(255),
  `status` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invoice_request_products` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `request_id` int,
  `product` varchar(255),
  `qty` int,
  `amount` double,
  `vat` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `payment_invoices` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `lpoout_id` int,
  `invoice_no` varchar(255),
  `amount` double,
  `vat` double,
  `total_amount` double,
  `status` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `transactions` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `account_id` int,
  `date_time` timestamp,
  `type` varchar(255),
  `transactionable_id` int,
  `transactionable_type` varchar(255),
  `transaction_type` int,
  `payment_type` int,
  `payment_no` varchar(255),
  `clearance_date` varchar(255),
  `bank_name` varchar(255),
  `vat` double,
  `amount` double,
  `total` double,
  `expense_account` int,
  `from_account` int,
  `note` text,
  `status` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `balance_accounts` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint,
  `balance` double,
  `type` varchar(255),
  `code` varchar(255),
  `account_type` varchar(255)
);

CREATE TABLE `balance_transactions` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `account_id` bigint,
  `extra_account_id` bigint,
  `amount` double,
  `reference_type` varchar(255),
  `reference_id` bigint,
  `created_at` timestamp
);

CREATE TABLE `petty_cashes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `vendor_id` int,
  `voucher_no` varchar(255),
  `amount` double,
  `vat` double,
  `total_amount` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `petty_cash_expenses` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `category` varchar(255),
  `amount` float,
  `date` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `payment_bookings` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `reference_no` varchar(255) UNIQUE,
  `booking_type` varchar(255),
  `payee` varchar(255),
  `amount` decimal,
  `cheque_number` varchar(255),
  `bank_account` varchar(255),
  `cheque_date` date,
  `release_date` date,
  `payment_date` date,
  `status` tinyint,
  `created_by` bigint,
  `submitted_at` timestamp,
  `approved_at` timestamp,
  `deleted_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `payment_booking_approvals` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `payment_booking_id` int,
  `level` tinyint,
  `user_id` bigint,
  `decision` tinyint,
  `note` text,
  `decided_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `staf_profile` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `last_name` varchar(255),
  `staf_type` varchar(255),
  `nationality` varchar(255),
  `gender` varchar(255),
  `joining_date` date,
  `dob` date,
  `passport_expiry` date,
  `visa_expiry` date,
  `emirates_id_expiry` date,
  `labor_card_expiry` date,
  `driver_permit_expiry` date,
  `last_vacation_start` date,
  `last_vacation_end` date,
  `last_vacation_days` int,
  `last_increment` date,
  `last_increment_amount` int,
  `basic_salary` double,
  `total_salary` double,
  `overtime_rate` double,
  `mobile_no` varchar(255),
  `exclude_from_expiry` boolean,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `staff_requests` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `staf_id` int,
  `type` varchar(255),
  `start_date` date,
  `end_date` date,
  `advance_money` double,
  `status` tinyint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `request_approvals` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `request_type` varchar(255),
  `request_id` int,
  `user_id` int,
  `decision` tinyint,
  `note` text,
  `decided_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `request_forms` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint,
  `name` varchar(255),
  `note` text,
  `status` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `labor_requests` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `labor_id` int,
  `type` varchar(255),
  `start_date` date,
  `end_date` date,
  `advance_money` double,
  `status` tinyint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `labour_assignments` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `labor_id` int,
  `project_id` int,
  `visit_schedule_id` bigint,
  `assignment_start_date` date,
  `assignment_end_date` date,
  `hours_worked` int,
  `overtime_hours` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `employees` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `email` varchar(255),
  `code` varchar(255),
  `contact_no` varchar(255),
  `balance` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `letters` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `staff_profile_id` int,
  `type` varchar(255),
  `title` varchar(255),
  `content` text,
  `issued_by` varchar(255),
  `issued_at` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `staff_ratings` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `staff_id` int,
  `engineer_id` bigint,
  `month` tinyint,
  `year` smallint,
  `safety_compliance` tinyint,
  `communication` tinyint,
  `attendance` tinyint,
  `competency` tinyint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `monthly_staff_reports` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `staff_id` int,
  `month` tinyint,
  `year` smallint,
  `safety_avg` double,
  `communication_avg` double,
  `attendance_avg` double,
  `final_score` double,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `tickets` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `staf_id` int,
  `ticket_date` date,
  `travel_type` varchar(255),
  `travel_date` date,
  `amount` decimal,
  `total_value` decimal,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `payrolls` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `member_id` int,
  `date` date,
  `absents` int,
  `hours` double,
  `plus_adjustment` double,
  `minus_adjustment` double,
  `total_amount` double,
  `note` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `staf_dates` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `staff_id` int,
  `start_date` date,
  `end_date` date,
  `days` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `sites` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `site_name` varchar(255) UNIQUE,
  `engineer_id` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `attendances` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `labor_id` int,
  `foreman_id` bigint,
  `approved_by` bigint,
  `attendance_date` date,
  `status` varchar(255),
  `marked_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `attendance_labor_details` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `attendance_id` bigint,
  `labor_id` int,
  `overtime_hours` decimal,
  `site_id` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `attendance_approvals` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `attendance_id` bigint,
  `approved_by` bigint,
  `action` varchar(255),
  `informed` varchar(255),
  `reason` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `attendance_sessions` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint,
  `session_date` date,
  `clock_in_time` datetime,
  `clock_in_latitude` decimal,
  `clock_in_longitude` decimal,
  `clock_in_distance_meters` decimal,
  `clock_out_time` datetime,
  `duration_minutes` int,
  `shift_window` varchar(255),
  `is_late` boolean,
  `session_status` varchar(255),
  `deleted_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `qr_staff_attendances` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `staff_id` bigint,
  `scanned_by` bigint,
  `site_id` bigint,
  `attendance_date` date,
  `check_in_time` datetime,
  `check_out_time` datetime,
  `duration_minutes` int,
  `overtime_minutes` int,
  `status` varchar(255),
  `review_status` varchar(255),
  `reviewed_by` bigint,
  `finalized_by` bigint,
  `deleted_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `media` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `model_type` varchar(255),
  `model_id` bigint,
  `collection_name` varchar(255),
  `file_name` varchar(255),
  `mime_type` varchar(255),
  `disk` varchar(255),
  `size` bigint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `notifications` (
  `id` char PRIMARY KEY,
  `type` varchar(255),
  `notifiable_type` varchar(255),
  `notifiable_id` bigint,
  `data` text,
  `read_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `device_tokens` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint,
  `token` varchar(255) UNIQUE,
  `platform` varchar(255),
  `device_name` varchar(255),
  `last_used_at` timestamp,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `comments` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `commentable_id` bigint,
  `commentable_type` varchar(255),
  `comments` text,
  `status` int,
  `user_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `lookups` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `tag` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `tasks` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `assigned` bigint,
  `title` varchar(255),
  `start_date` datetime,
  `end_date` datetime,
  `status` tinyint,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `documents` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `type` varchar(255),
  `date` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

ALTER TABLE `users` ADD FOREIGN KEY (`staf_profile_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `model_has_roles` ADD FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `model_has_permissions` ADD FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`);

ALTER TABLE `role_has_permissions` ADD FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`);

ALTER TABLE `role_has_permissions` ADD FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `inquiries` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `inquiries_status_history` ADD FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`);

ALTER TABLE `inquiries_status_history` ADD FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

ALTER TABLE `inquiry_department_reviews` ADD FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`);

ALTER TABLE `inquiry_department_reviews` ADD FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

ALTER TABLE `inquiry_department_reviews` ADD FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

ALTER TABLE `inquiry_engineer_reports` ADD FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`);

ALTER TABLE `inquiry_engineer_reports` ADD FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`);

ALTER TABLE `inquiry_follow_ups` ADD FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`);

ALTER TABLE `inquiry_follow_ups` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `inquiry_quotations` ADD FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`);

ALTER TABLE `inquiry_quotations` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `inquiry_activities` ADD FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`);

ALTER TABLE `inquiry_activities` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `quotations` ADD FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

ALTER TABLE `quotations` ADD FOREIGN KEY (`reference_project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `quotations` ADD FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

ALTER TABLE `quotation_products` ADD FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

ALTER TABLE `quotation_products` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `projects` ADD FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

ALTER TABLE `projects` ADD FOREIGN KEY (`project_type_id`) REFERENCES `project_types` (`id`);

ALTER TABLE `projects` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `project_extension` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `project_extension` ADD FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

ALTER TABLE `visit_schedules` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `visit_schedule_comments` ADD FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`);

ALTER TABLE `visit_schedule_history` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `visit_schedule_history` ADD FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`);

ALTER TABLE `projectreports` ADD FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

ALTER TABLE `projectreports` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `projectreports` ADD FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`);

ALTER TABLE `projectreports` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `drawing_receiveds` ADD FOREIGN KEY (`lpoin_id`) REFERENCES `lpoins` (`id`);

ALTER TABLE `drawing_receiveds` ADD FOREIGN KEY (`responsible_engineer_id`) REFERENCES `users` (`id`);

ALTER TABLE `drawing_received_contributions` ADD FOREIGN KEY (`drawing_received_id`) REFERENCES `drawing_receiveds` (`id`);

ALTER TABLE `drawing_received_contributions` ADD FOREIGN KEY (`contributed_by_id`) REFERENCES `users` (`id`);

ALTER TABLE `lpoins` ADD FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

ALTER TABLE `lpoouts` ADD FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

ALTER TABLE `lpoouts` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `lpoouts` ADD FOREIGN KEY (`lpo_out_type_id`) REFERENCES `lpo_out_types` (`id`);

ALTER TABLE `project_lpoout` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `project_lpoout` ADD FOREIGN KEY (`lpoout_id`) REFERENCES `lpoouts` (`id`);

ALTER TABLE `purchase_orders` ADD FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

ALTER TABLE `purchase_orders` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `purchase_orders` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `purchase_orders` ADD FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

ALTER TABLE `purchase_orders` ADD FOREIGN KEY (`lpout_id`) REFERENCES `lpoouts` (`id`);

ALTER TABLE `purchase_orders` ADD FOREIGN KEY (`revised_from_lpoout_id`) REFERENCES `lpoouts` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `invoices` ADD FOREIGN KEY (`invoice_type_id`) REFERENCES `invoice_types` (`id`);

ALTER TABLE `invoices` ADD FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`);

ALTER TABLE `invoices` ADD FOREIGN KEY (`invoice_bank_id`) REFERENCES `invoice_banks` (`id`);

ALTER TABLE `invoice_product_details` ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

ALTER TABLE `invoice_service_details` ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

ALTER TABLE `invoice_requests` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `invoice_request_products` ADD FOREIGN KEY (`request_id`) REFERENCES `invoice_requests` (`id`);

ALTER TABLE `payment_invoices` ADD FOREIGN KEY (`lpoout_id`) REFERENCES `lpoouts` (`id`);

ALTER TABLE `transactions` ADD FOREIGN KEY (`account_id`) REFERENCES `balance_accounts` (`id`);

ALTER TABLE `transactions` ADD FOREIGN KEY (`payment_type`) REFERENCES `lookups` (`id`);

ALTER TABLE `transactions` ADD FOREIGN KEY (`expense_account`) REFERENCES `balance_accounts` (`id`);

ALTER TABLE `transactions` ADD FOREIGN KEY (`from_account`) REFERENCES `balance_accounts` (`id`);

ALTER TABLE `balance_accounts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `balance_transactions` ADD FOREIGN KEY (`account_id`) REFERENCES `balance_accounts` (`id`);

ALTER TABLE `balance_transactions` ADD FOREIGN KEY (`extra_account_id`) REFERENCES `balance_accounts` (`id`);

ALTER TABLE `petty_cashes` ADD FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

ALTER TABLE `payment_bookings` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `payment_booking_approvals` ADD FOREIGN KEY (`payment_booking_id`) REFERENCES `payment_bookings` (`id`);

ALTER TABLE `payment_booking_approvals` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `staff_requests` ADD FOREIGN KEY (`staf_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `request_approvals` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `request_forms` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `labor_requests` ADD FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `labour_assignments` ADD FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `labour_assignments` ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

ALTER TABLE `labour_assignments` ADD FOREIGN KEY (`visit_schedule_id`) REFERENCES `visit_schedules` (`id`);

ALTER TABLE `letters` ADD FOREIGN KEY (`staff_profile_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `staff_ratings` ADD FOREIGN KEY (`staff_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `staff_ratings` ADD FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`);

ALTER TABLE `monthly_staff_reports` ADD FOREIGN KEY (`staff_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `tickets` ADD FOREIGN KEY (`staf_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `payrolls` ADD FOREIGN KEY (`member_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `staf_dates` ADD FOREIGN KEY (`staff_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `sites` ADD FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`);

ALTER TABLE `attendances` ADD FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `attendances` ADD FOREIGN KEY (`foreman_id`) REFERENCES `users` (`id`);

ALTER TABLE `attendances` ADD FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

ALTER TABLE `attendance_labor_details` ADD FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`);

ALTER TABLE `attendance_labor_details` ADD FOREIGN KEY (`labor_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `attendance_labor_details` ADD FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`);

ALTER TABLE `attendance_approvals` ADD FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`);

ALTER TABLE `attendance_approvals` ADD FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

ALTER TABLE `attendance_sessions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `qr_staff_attendances` ADD FOREIGN KEY (`staff_id`) REFERENCES `staf_profile` (`id`);

ALTER TABLE `qr_staff_attendances` ADD FOREIGN KEY (`scanned_by`) REFERENCES `users` (`id`);

ALTER TABLE `qr_staff_attendances` ADD FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`);

ALTER TABLE `qr_staff_attendances` ADD FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

ALTER TABLE `qr_staff_attendances` ADD FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`);

ALTER TABLE `device_tokens` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `tasks` ADD FOREIGN KEY (`assigned`) REFERENCES `users` (`id`);
