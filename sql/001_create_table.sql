CREATE TABLE `users` (
  `id` varchar(32) NOT NULL PRIMARY KEY,
  `password_hash` varchar(64) NOT NULL,
  `email`    varchar(64) NOT NULL,
  `description` text DEFAULT '안녕하세요!',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` varchar(32) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(32) DEFAULT NULL,
  `image_type` varchar(16) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
);