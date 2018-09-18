-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 18 2018 г., 14:25
-- Версия сервера: 5.6.38
-- Версия PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bookshop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `authors`
--

INSERT INTO `authors` (`id`, `title`) VALUES
(4, 'Brandi Reeds'),
(5, 'Loretta Nyhan'),
(6, 'Harold Schechter'),
(7, 'J. K. Rowling'),
(8, 'Bella Forrest'),
(9, 'Hannah Howard'),
(10, 'Melinda Leigh'),
(11, 'Madeleine L\'Engle'),
(12, 'Mary Burton'),
(14, 'asdfadf4123433333');

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `price` varchar(11) NOT NULL,
  `discount` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `price`, `discount`) VALUES
(1, 'First title booke', 'first description bo4', '102', 9),
(2, 'Second book', 'Second description for book', '200', 10),
(11, 'Science Fiction part 2', 'test', '134', 10),
(12, 'New Fantasy Book', 'Description for amazing book', '120', 10),
(13, 'Intresting book', 'test desc for this book', '33', 20),
(14, 'test', 'test', '11', 3),
(15, 'test2', 'test2', '111', 33),
(16, 'test23', 'test23', '111', 33),
(17, 'test2321', 'test23 3421    2314', '34', 1),
(18, 'test2321', 'test23 3421    2314', '34', 1),
(19, 'test2321', 'test23 3421    2314', '34', 1),
(20, 'test2321', 'test23 3421    2314', '34', 1),
(22, 'qwer', 'wqer', '333', 33),
(25, '11', '11', '11', 11);

-- --------------------------------------------------------

--
-- Структура таблицы `book_to_author`
--

CREATE TABLE `book_to_author` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `book_to_author`
--

INSERT INTO `book_to_author` (`id`, `book_id`, `author_id`) VALUES
(1, 1, 3),
(6, 1, 3),
(7, 2, 3),
(10, 1, 3),
(15, 10, 3),
(16, 10, 4),
(17, 10, 4),
(18, 11, 3),
(19, 11, 5),
(20, 11, 7),
(21, 11, 9),
(24, 12, 4),
(25, 12, 12),
(26, 12, 12),
(27, 2, 7),
(28, 2, 8),
(29, 13, 4),
(30, 13, 7),
(31, 13, 9),
(32, 13, 9),
(33, 14, 4),
(34, 14, 5),
(35, 14, 5),
(36, 13, 4),
(37, 13, 7),
(38, 13, 7),
(43, 1, 4),
(49, 1, 14),
(51, 1, 14),
(53, 1, 4),
(56, 25, 5),
(57, 25, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `book_to_genre`
--

CREATE TABLE `book_to_genre` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `book_to_genre`
--

INSERT INTO `book_to_genre` (`id`, `book_id`, `genre_id`) VALUES
(8, 1, 7),
(9, 10, 3),
(12, 11, 2),
(13, 11, 4),
(14, 11, 7),
(15, 12, 9),
(16, 12, 10),
(17, 2, 3),
(18, 2, 7),
(19, 13, 2),
(20, 13, 3),
(21, 13, 4),
(22, 13, 7),
(23, 13, 9),
(24, 13, 10),
(25, 14, 1),
(26, 14, 2),
(27, 13, 2),
(28, 13, 4),
(29, 1, 9),
(32, 25, 10),
(33, 25, 7);

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT '1',
  `id_user` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `genres`
--

INSERT INTO `genres` (`id`, `title`) VALUES
(1, 'Romance'),
(2, 'Action Adventure'),
(3, 'Science Fiction'),
(4, ' Fantasy'),
(7, 'Speculative Fiction'),
(9, 'Young Adult'),
(10, 'Police Procedurals');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `is_paymented` int(11) NOT NULL DEFAULT '0',
  `total_order` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `created`, `status`, `payment_id`, `is_paymented`, `total_order`) VALUES
(103, 13, '0000-00-00 00:00:00', 'pending', 3, 0, '189'),
(104, 13, '0000-00-00 00:00:00', 'confirmed', 1, 0, '189'),
(105, 13, '0000-00-00 00:00:00', NULL, 2, 0, '189'),
(106, 13, '0000-00-00 00:00:00', NULL, 4, 0, '82'),
(107, 13, '0000-00-00 00:00:00', NULL, 3, 0, '2057'),
(108, 13, '0000-00-00 00:00:00', NULL, 1, 0, '495'),
(109, 14, '0000-00-00 00:00:00', NULL, 4, 0, '427'),
(110, 14, '0000-00-00 00:00:00', NULL, 3, 0, '92');

-- --------------------------------------------------------

--
-- Структура таблицы `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `book_price` varchar(255) DEFAULT NULL,
  `title_book` varchar(255) DEFAULT NULL,
  `count` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `book_id`, `book_price`, `title_book`, `count`) VALUES
(48, 101, 1, '102', 'First title booke', 1),
(49, 101, 10, '234', 'test4', 1),
(50, 102, 1, '102', 'First title booke', 1),
(51, 103, 10, '234', 'test4', 1),
(52, 104, 10, '234', 'test4', 1),
(53, 105, 10, '234', 'test4', 1),
(54, 106, 1, '102', 'First title booke', 1),
(55, 107, 2, '200', 'Second book', 1),
(56, 107, 10, '234', 'test4', 10),
(57, 108, 1, '102', 'First title booke', 6),
(58, 109, 1, '102', 'First title booke', 1),
(59, 109, 2, '200', 'Second book', 1),
(60, 109, 11, '134', 'Science Fiction part 2', 1),
(61, 109, 20, '34', 'test2321', 1),
(62, 110, 1, '102', 'First title booke', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `payment`
--

INSERT INTO `payment` (`id`, `type`) VALUES
(1, 'cash'),
(2, 'card'),
(3, 'paypal'),
(4, 'webmoney');

-- --------------------------------------------------------

--
-- Структура таблицы `test_table`
--

CREATE TABLE `test_table` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` int(255) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `test_table`
--

INSERT INTO `test_table` (`id`, `name`, `category`, `description`) VALUES
(1, 'book1', 1, 'desck for book 1 '),
(2, 'book2 ', 2, 'desk for book2');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(255) DEFAULT NULL,
  `expire` datetime DEFAULT NULL,
  `user_discount` int(11) NOT NULL DEFAULT '0',
  `is_admin` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `created`, `token`, `expire`, `user_discount`, `is_admin`) VALUES
(3, 'sheva199.92@mail.ru', NULL, 'Jone', 'doe', '2018-09-09 10:36:26', NULL, NULL, 0, NULL),
(4, 'sheva199.92@mail.ru', '$2y$13$JDJ5JDEzJEFmOURtWENVW.8nEpidWzif.6SH3n8jajp3TOxrEQmMC', 'Jon', 'doe', '2018-09-09 10:36:30', 'asdfasdfasdfasdf', '2018-09-09 16:36:29', 0, 0),
(5, 'sheva1199.92@mail.ru', '$2y$13$JDJ5JDEzJDVlL3kyYThRcOMWC3MqPA9LixW9Vr48q3BQhiXxq2g4.', 'Jon', 'doe', '2018-09-09 10:37:49', 'c257948742232c2ca6d068e6eac568f1', '2018-09-09 16:37:49', 0, 0),
(6, 'www@mail.cz', '$2y$13$JDJ5JDEzJHFFYnppOTFZa.MHfWABxyxZ.BXB4iaHRIcDTdt6yXmSe', 'Ivan', 'Yasinskiy', '2018-09-09 10:38:34', 'eba26aed02e22ce4892ffddb87ab76bc', '2018-09-09 16:38:33', 0, 0),
(7, '1@mail.cz', '$2y$13$JDJ5JDEzJDZLRGtyTE5wVO6KIEb6lWPXj0WWtkYghYaVaCJ0BiRcm', 'a', 'e', '2018-09-09 11:44:05', '1b8029db1bb5875b328d00edb1d7cf6b', '2018-09-09 17:44:03', 0, 0),
(8, 'dev@mail.cz', '$2y$13$JDJ5JDEzJG9JTkhLLjV1bO13RTdm3jw5FpF5DhNFkVdbUD8ylHIka', 'dev', 'dev', '2018-09-09 13:00:56', NULL, NULL, 0, 0),
(9, 'dev1@mail.cz', '$2y$13$JDJ5JDEzJEdpWFFXMFpPcuX8dfhllhGyVZRXfJ2EadNCsxlbzQTUS', 'dev', 'dev', '2018-09-09 13:02:04', '291ae5d0f40469d620ad1dd9c9ae873e', NULL, 0, 0),
(11, 'qq@mail.cz', '$2y$13$JDJ5JDEzJGdHL2RkYXBhN.Pi1OgZ441O4Y75bLcA3K6u1qg3dwA8.', 'Jon', 'Jon', '2018-09-09 13:11:33', '727d3c77c491c0fd0542a601ff3087ac', NULL, 0, 0),
(12, '11@mail.cz', NULL, '22', '22', '2018-09-09 13:19:04', NULL, NULL, 11, NULL),
(13, '12@mail.cz', '$2y$13$JDJ5JDEzJC9IcXB2ek10RueLL5YP4VbNgZcvvHqyQVxFyoGmV7PQi', 'Jon', 'doe', '2018-09-09 13:19:57', 'd031c41aeee7ec90df7114d19b22c15e', '2018-09-18 11:07:32', 10, 1),
(14, '13@mail.cz', '$2y$13$JDJ5JDEzJEVKSXVPUEF0QuJL5MCzRTue0/kRDr8d7.iBbmkbznTl2', 'Jon', 'doe', '2018-09-17 09:41:26', '97695a2dbacb654cfd9363fd50ff5e3d', '2018-09-18 11:07:04', 0, NULL),
(15, NULL, NULL, NULL, NULL, '2018-09-17 10:27:21', NULL, NULL, 0, NULL),
(16, NULL, NULL, NULL, NULL, '2018-09-17 10:27:58', NULL, NULL, 0, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `book_to_author`
--
ALTER TABLE `book_to_author`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `book_to_genre`
--
ALTER TABLE `book_to_genre`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_table`
--
ALTER TABLE `test_table`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `book_to_author`
--
ALTER TABLE `book_to_author`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT для таблицы `book_to_genre`
--
ALTER TABLE `book_to_genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT для таблицы `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT для таблицы `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `test_table`
--
ALTER TABLE `test_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
