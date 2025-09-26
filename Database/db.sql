-- Database: foodfusion_db
CREATE DATABASE foodfusion_db;
USE foodfusion_db;

-- 1. Users table
CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    failed_attempts INT(11) NOT NULL DEFAULT 0,
    lockout_until DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Recipe Collections (admin curated)
CREATE TABLE recipe_collections (
    recipe_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    cuisine VARCHAR(50) DEFAULT NULL,
    difficulty_level ENUM('Easy', 'Medium', 'Hard') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (recipe_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Community Recipes (user submitted)
CREATE TABLE community_recipes (
    recipe_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    cuisine VARCHAR(50) DEFAULT NULL,
    difficulty_level ENUM('Easy', 'Medium', 'Hard') NOT NULL,
    user_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Categories
CREATE TABLE categories (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Recipe ↔ Categories (admin recipes)
CREATE TABLE recipe_collections_categories (
    recipe_id INT(11) NOT NULL,
    category_id INT(11) NOT NULL,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id) REFERENCES recipe_collections(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Community Recipes ↔ Categories
CREATE TABLE community_recipes_categories (
    recipe_id INT(11) NOT NULL,
    category_id INT(11) NOT NULL,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id) REFERENCES community_recipes(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Ratings (users rate community recipes)
CREATE TABLE ratings (
    rating_id INT(11) NOT NULL AUTO_INCREMENT,
    recipe_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    rating INT(1) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rating_id),
    FOREIGN KEY (recipe_id) REFERENCES community_recipes(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating (recipe_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Comments (users comment on community recipes)
CREATE TABLE comments (
    comment_id INT(11) NOT NULL AUTO_INCREMENT,
    recipe_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (comment_id),
    FOREIGN KEY (recipe_id) REFERENCES community_recipes(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Resources (culinary + educational)
CREATE TABLE resources (
    resource_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    resource_type ENUM('culinary', 'educational') NOT NULL,
    url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (resource_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
