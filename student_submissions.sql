-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 19, 2025 at 06:12 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prototype-4`
--

-- --------------------------------------------------------

--
-- Table structure for table `student_submissions`
--

CREATE TABLE `student_submissions` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `scholarship_batch_id` bigint UNSIGNED NOT NULL,
  `submitted_by_teacher_id` bigint UNSIGNED NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `raw_criteria_values` json NOT NULL,
  `normalized_scores` json DEFAULT NULL,
  `final_saw_score` decimal(8,4) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_review',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_submissions`
--

INSERT INTO `student_submissions` (`id`, `student_id`, `scholarship_batch_id`, `submitted_by_teacher_id`, `submission_date`, `raw_criteria_values`, `normalized_scores`, `final_saw_score`, `status`, `created_at`, `updated_at`) VALUES
(3, 2, 2, 2, '2025-05-19 08:58:18', '{\"average_score\": \"90\", \"class_attendance_percentage\": \"80\"}', '{\"average_score\": 0.75, \"class_attendance_percentage\": 0.6}', 0.6750, 'pending', '2025-05-19 08:58:18', '2025-05-19 11:03:57'),
(4, 1, 2, 2, '2025-05-19 08:58:29', '{\"average_score\": \"90\", \"class_attendance_percentage\": \"90\"}', '{\"average_score\": 0.75, \"class_attendance_percentage\": 0.8}', 0.7750, 'pending', '2025-05-19 08:58:29', '2025-05-19 11:03:57'),
(5, 5, 2, 2, '2025-05-19 10:50:18', '{\"average_score\": \"60\", \"class_attendance_percentage\": \"50\"}', '{\"average_score\": 0, \"class_attendance_percentage\": 0}', 0.0000, 'pending', '2025-05-19 10:50:18', '2025-05-19 11:03:57'),
(6, 6, 2, 2, '2025-05-19 10:50:28', '{\"average_score\": \"70\", \"class_attendance_percentage\": \"70\"}', '{\"average_score\": 0.25, \"class_attendance_percentage\": 0.4}', 0.3250, 'pending', '2025-05-19 10:50:28', '2025-05-19 11:03:57'),
(7, 3, 2, 2, '2025-05-19 10:50:38', '{\"average_score\": \"90\", \"class_attendance_percentage\": \"50\"}', '{\"average_score\": 0.75, \"class_attendance_percentage\": 0}', 0.3750, 'pending', '2025-05-19 10:50:38', '2025-05-19 11:03:57'),
(8, 10, 2, 2, '2025-05-19 10:50:54', '{\"average_score\": \"60\", \"class_attendance_percentage\": \"70\"}', '{\"average_score\": 0, \"class_attendance_percentage\": 0.4}', 0.2000, 'pending', '2025-05-19 10:50:54', '2025-05-19 11:03:57'),
(9, 9, 2, 2, '2025-05-19 10:51:03', '{\"average_score\": \"100\", \"class_attendance_percentage\": \"100\"}', '{\"average_score\": 1, \"class_attendance_percentage\": 1}', 1.0000, 'pending', '2025-05-19 10:51:03', '2025-05-19 11:03:57'),
(10, 8, 2, 2, '2025-05-19 10:51:16', '{\"average_score\": \"80\", \"class_attendance_percentage\": \"65\"}', '{\"average_score\": 0.5, \"class_attendance_percentage\": 0.3}', 0.4000, 'pending', '2025-05-19 10:51:16', '2025-05-19 11:03:57'),
(11, 12, 2, 2, '2025-05-19 10:51:30', '{\"average_score\": \"78\", \"class_attendance_percentage\": \"80\"}', '{\"average_score\": 0.45, \"class_attendance_percentage\": 0.6}', 0.5250, 'pending', '2025-05-19 10:51:30', '2025-05-19 11:03:57'),
(12, 7, 2, 2, '2025-05-19 10:51:50', '{\"average_score\": \"65\", \"class_attendance_percentage\": \"80\"}', '{\"average_score\": 0.125, \"class_attendance_percentage\": 0.6}', 0.3625, 'pending', '2025-05-19 10:51:50', '2025-05-19 11:03:57'),
(13, 11, 2, 2, '2025-05-19 10:52:01', '{\"average_score\": \"90\", \"class_attendance_percentage\": \"86\"}', '{\"average_score\": 0.75, \"class_attendance_percentage\": 0.72}', 0.7350, 'pending', '2025-05-19 10:52:01', '2025-05-19 11:03:57'),
(14, 4, 2, 2, '2025-05-19 10:52:10', '{\"average_score\": \"75\", \"class_attendance_percentage\": \"80\"}', '{\"average_score\": 0.375, \"class_attendance_percentage\": 0.6}', 0.4875, 'pending', '2025-05-19 10:52:10', '2025-05-19 11:03:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_submissions_student_id_foreign` (`student_id`),
  ADD KEY `student_submissions_scholarship_batch_id_foreign` (`scholarship_batch_id`),
  ADD KEY `student_submissions_submitted_by_teacher_id_foreign` (`submitted_by_teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_submissions`
--
ALTER TABLE `student_submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD CONSTRAINT `student_submissions_scholarship_batch_id_foreign` FOREIGN KEY (`scholarship_batch_id`) REFERENCES `scholarship_batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_submissions_submitted_by_teacher_id_foreign` FOREIGN KEY (`submitted_by_teacher_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
