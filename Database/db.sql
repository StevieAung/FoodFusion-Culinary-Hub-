--
-- FoodFusion Database System - Complete Setup Script
--
-- This script performs the following actions:
-- 1. Creates the 'foodfusion_db' database if it does not exist.
-- 2. Selects 'foodfusion_db' for all subsequent operations.
-- 3. Creates all 10 required tables with Primary Keys, Indexes, and Foreign Keys.
-- 4. Inserts initial lookup data (Categories and Cuisine Types).
--
-- Note: DROP TABLE statements have been removed to prioritize the "fresh setup" workflow.
--

-- Database setup
CREATE DATABASE IF NOT EXISTS `foodfusion_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `foodfusion_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------
-- 1. CORE USER & LOOKUP TABLES
-- --------------------------------------------------------

--
-- Table structure for table `users`
-- Stores all registered user information.
--
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `categories`
-- Lookup table for recipe categories (e.g., Dessert, Main Course).
--
CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `cuisine_types`
-- Lookup table for cuisine origins (e.g., Italian, Mexican).
--
CREATE TABLE `cuisine_types` (
  `cuisine_id` int(10) UNSIGNED NOT NULL,
  `cuisine_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 2. RECIPE TABLES
-- --------------------------------------------------------

--
-- Table structure for table `community_recipes`
-- Recipes submitted by users.
--
CREATE TABLE `community_recipes` (
  `recipe_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `difficulty_level` enum('Easy','Medium','Hard') NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cuisine_image` varchar(255) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `cuisine_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `recipe_collections`
-- Pre-curated/official recipes.
--
CREATE TABLE `recipe_collections` (
  `recipe_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ingredients` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `difficulty_level` enum('Easy','Medium','Hard') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `cuisine_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 3. INTERACTION TABLES (Ratings & Comments)
-- --------------------------------------------------------

--
-- Table structure for table `recipe_ratings`
-- Stores user ratings (1-5 stars) for both recipe types.
--
CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `recipe_type` ENUM('community', 'collection') NOT NULL COMMENT 'Specifies which recipe table this rating applies to',
  `recipe_fk_id` int(11) NOT NULL COMMENT 'References recipe_id in either community_recipes or recipe_collections',
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `recipe_comments`
-- Stores user comments for both recipe types, supporting nested replies.
--
CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `recipe_type` ENUM('community', 'collection') NOT NULL COMMENT 'Specifies which recipe table this comment applies to',
  `recipe_fk_id` int(11) NOT NULL COMMENT 'References recipe_id in either community_recipes or recipe_collections',
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL COMMENT 'For nested replies',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 4. UTILITY TABLES (Contact & Resources)
-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
-- Stores messages from the contact form.
--
CREATE TABLE `contact_messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `resources`
-- Stores links to external culinary and educational resources.
--
CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `resource_type` enum('culinary','educational') NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 5. PRIMARY KEY & INDEX DEFINITIONS
-- --------------------------------------------------------

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

ALTER TABLE `cuisine_types`
  ADD PRIMARY KEY (`cuisine_id`),
  ADD UNIQUE KEY `cuisine_name` (`cuisine_name`);

ALTER TABLE `community_recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_community_recipes_category` (`category_id`),
  ADD KEY `fk_community_recipes_cuisine` (`cuisine_id`);

ALTER TABLE `recipe_collections`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `fk_recipe_collections_category` (`category_id`),
  ADD KEY `fk_recipe_collections_cuisine` (`cuisine_id`);

ALTER TABLE `recipe_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `unique_rating_per_user` (`recipe_type`, `recipe_fk_id`, `user_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `recipe_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

-- --------------------------------------------------------
-- 6. AUTO_INCREMENT DEFINITIONS
-- --------------------------------------------------------

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cuisine_types`
  MODIFY `cuisine_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `community_recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `recipe_collections`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `recipe_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `recipe_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contact_messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- 7. FOREIGN KEY CONSTRAINTS
-- --------------------------------------------------------

ALTER TABLE `community_recipes`
  ADD CONSTRAINT `community_recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_community_recipes_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_community_recipes_cuisine` FOREIGN KEY (`cuisine_id`) REFERENCES `cuisine_types` (`cuisine_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `recipe_collections`
  ADD CONSTRAINT `fk_recipe_collections_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recipe_collections_cuisine` FOREIGN KEY (`cuisine_id`) REFERENCES `cuisine_types` (`cuisine_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `recipe_ratings`
  ADD CONSTRAINT `fk_rating_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `recipe_comments`
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_parent` FOREIGN KEY (`parent_comment_id`) REFERENCES `recipe_comments` (`comment_id`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- 8. INITIAL DATA INSERTION
-- --------------------------------------------------------

-- Initial Categories
INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(2, 'Appetizer'),
(4, 'Beverage'),
(1, 'Dessert'),
(3, 'Main Course');

-- Initial Cuisine Types
INSERT INTO `cuisine_types` (`cuisine_name`) VALUES
('Italian'),
('Mexican'),
('Asian'),
('American'),
('Chinese');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
