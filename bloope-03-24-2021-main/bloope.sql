-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 18, 2021 at 10:39 PM
-- Server version: 5.7.26
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bloope`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `posted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment`, `user_id`, `posted_at`, `post_id`) VALUES
(1, 'Mean is the same as average', 22, '2021-03-10 12:24:39', 4),
(6, 'case', 22, '2021-03-10 12:54:57', 1),
(7, 'Another comment', 22, '2021-03-12 16:41:36', 4),
(8, 'vbnm', 22, '2021-03-17 10:45:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `user_id`, `follower_id`) VALUES
(5, 21, 20),
(7, 20, 24),
(8, 24, 22);

-- --------------------------------------------------------

--
-- Table structure for table `login_tokens`
--

CREATE TABLE `login_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_tokens`
--

INSERT INTO `login_tokens` (`id`, `token`, `user_id`) VALUES
(29, 'c77f6f5617d1dfd5a46dfc66b5c40cd4672f93e9', 20),
(35, '63be663e38299a9ffd38c43ae511aafdfeb77e0d', 23),
(43, 'f06115af68acd1228b9d5d74c1f348eb79294447', 22),
(44, '5c8e3fed20eb60d7e8e191471928661701ab88d5', 22),
(47, 'a0a0408a5f8faf85bb235ace559d349a61a1e86b', 22),
(51, '38bf58263846213e9ab6ee550d02652bf078d370', 30),
(52, 'b73d246c87221c5c589131408b1a422f480d531b', 22),
(53, 'f8ec684e60cfe3a66f916a2084d239728a833cb7', 22);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `body` text NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `hasBeenRead` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `body`, `sender`, `receiver`, `hasBeenRead`) VALUES
(1, 'hello world', 22, 20, 1),
(2, 'Hi back', 22, 20, 0),
(3, 'Who is this?', 20, 22, 0),
(4, 'I got them online', 24, 22, 0),
(5, 'Where did you get this from', 22, 24, 0),
(6, 'Do you want one?', 24, 22, 0),
(7, 'No thank you.', 22, 24, 0),
(8, 'Who are you?', 22, 20, 0),
(9, 'I Like the one i have', 22, 24, 0),
(10, 'Okay bye', 24, 22, 0),
(11, 'I am James', 22, 20, 0),
(12, 'Your group member', 22, 20, 0),
(13, 'remember?', 22, 20, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `extra` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `receiver`, `sender`, `extra`) VALUES
(3, 1, 23, 22, ''),
(5, 1, 22, 22, ''),
(6, 1, 22, 22, NULL),
(7, 1, 22, 22, ' { \"postbody\": \"@james fg\" } '),
(8, 1, 22, 22, ' { \"postbody\": \"@james but..\" } '),
(9, 1, 22, 22, ' { \"postbody\": \"can you get me high @james\" } '),
(10, 1, 22, 22, ' { \"postbody\": \"@james don\'t you grow up in a hurry\" } '),
(11, 1, 22, 22, ' { \"postbody\": \"@james no\" } '),
(12, 2, 22, 22, NULL),
(13, 2, 22, 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_tokens`
--

CREATE TABLE `password_tokens` (
  `id` int(11) NOT NULL,
  `token` char(64) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `body` varchar(160) NOT NULL,
  `posted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `likes` int(11) NOT NULL,
  `postImg` varchar(255) DEFAULT NULL,
  `topics` varchar(400) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `body`, `posted_at`, `user_id`, `likes`, `postImg`, `topics`) VALUES
(1, 'Verified Account saying Hi', '2021-03-03 04:17:33', 24, 0, NULL, NULL),
(2, 'Hello World!', '2021-03-03 04:17:38', 24, 0, NULL, NULL),
(3, 'To be verified we have to be following you', '2021-03-03 15:50:10', 24, 1, NULL, NULL),
(4, 'Kanye West is the richest black man in American HISTORY', '2021-03-03 15:50:19', 24, 0, NULL, NULL),
(6, 'Gunie', '2021-03-05 18:45:04', 22, 1, NULL, NULL),
(8, 'nothing', '2021-03-12 03:20:32', 22, 0, NULL, NULL),
(9, '', '2021-03-12 03:21:17', 22, 0, 'https://i.imgur.com/TpVOVYa.png', NULL),
(10, 'lifestyle @dave', '2021-03-12 14:13:26', 22, 0, NULL, NULL),
(13, 'How i met your mother #show #best #movie', '2021-03-12 14:58:36', 22, 0, NULL, 'show,best,movie'),
(14, 'school is a mess #faithAcademy #ota', '2021-03-12 14:59:35', 22, 0, NULL, 'faithAcademy,ota,'),
(15, 'Figth club is my favourite movie #movie', '2021-03-12 15:07:16', 22, 1, NULL, 'movie,'),
(54, 'can you get me a free ticket? @james', '2021-03-15 23:25:02', 22, 1, NULL, ''),
(55, 'Kid Cudi is a great #music #movie', '2021-03-15 23:25:37', 22, 0, NULL, 'music,movie,'),
(56, 'Burna boy won a grammy finally #music ', '2021-03-15 23:25:56', 22, 0, NULL, 'music,'),
(57, '@james don\'t you grow up in a hurry', '2021-03-16 00:20:04', 22, 1, NULL, ''),
(58, '@james no', '2021-03-16 00:31:21', 22, 1, NULL, ''),
(59, '123456789', '2021-03-18 11:19:16', 22, 0, NULL, ''),
(61, 'nnn', '2021-03-18 11:30:53', 22, 0, 'https://i.imgur.com/UiatuWg.jpg', '');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`) VALUES
(33, 15, 22),
(41, 54, 22),
(51, 57, 22),
(59, 58, 22),
(61, 3, 22),
(63, 6, 22);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(225) DEFAULT NULL,
  `email` text,
  `phone_number` varchar(60) DEFAULT NULL,
  `isVerified` tinyint(1) DEFAULT '0',
  `profileImg` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone_number`, `isVerified`, `profileImg`) VALUES
(20, 'blackowned', '$2y$10$jUoNIuaDaX9y6xAw7eyN8eVQbDjeFU48AGA71pxlCk5Te4KlQd2bO', 'blackowned@gmail.com', 'blackowned@gmail.com', 1, NULL),
(21, 'dave', '$2y$10$g5mqf0FmdRUjwzgTFS/9Du/3X9ZPgHGmpm0emM5H2h6K4JkrYt.Si', 'dan@bla.com', 'dan@bla.com', 0, NULL),
(22, 'james', '$2y$10$XINPfTFt7C9n9EVzxYDJkOh0ShuggzcT3o3rKWwv5zLqaFnFOnzT6', 'test123@gmail.com', 'test123@gmail.com', 0, 'https://i.imgur.com/38vPnVC.png'),
(23, 'stig', '$2y$10$a8JEwxef1fyOGTS6DEEyhOJMyPJd9JkDz3TmoroDHmCluVsHcdby.', 'stig@gmail.com', 'stig@gmail.com', 0, NULL),
(24, 'Verified', '$2y$10$o2QbgZaD.ZLaxanV/Vc5u.VfFi2SSGQvDUS6qit1hcjA9tQLO/LC.', 'verified@gmail.com', 'verified@gmail.com', 1, NULL),
(30, 'david@blackowned.com', '$2y$10$EgTjDjNdbhv0X8FmPrKIwOlwRUDFIWoxDsLI7pGwB9D5nEhDc42iK', 'david@blackowned.com', 'david@blackowned.com', 0, NULL),
(31, 'dancer245', '$2y$10$OyUhCpgD/Ltg40ZunBHcLujNsrv.DQFNn7F1Ug5/SeEFM/Gxuhtfy', 'dancer245@gom.com', 'dancer245', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_tokens`
--
ALTER TABLE `password_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_tokens`
--
ALTER TABLE `login_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `password_tokens`
--
ALTER TABLE `password_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Constraints for table `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD CONSTRAINT `login_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
