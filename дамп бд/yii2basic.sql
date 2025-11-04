-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 04 2025 г., 03:03
-- Версия сервера: 10.8.4-MariaDB
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `yii2basic`
--
CREATE DATABASE IF NOT EXISTS `yii2basic` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `yii2basic`;

-- --------------------------------------------------------

--
-- Структура таблицы `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `text` varchar(1000) DEFAULT NULL,
  `creation_time` int(20) UNSIGNED NOT NULL,
  `update_time` int(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `user_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `message`
--

INSERT INTO `message` (`id`, `name`, `text`, `creation_time`, `update_time`, `status`, `user_id`) VALUES
(1, 'maria', 'Соображения высшего порядка, а также сложившаяся структура организации обеспечивает широкому кругу специалистов участие в формировании модели развития.', 1762211639, NULL, 1, 1),
(3, 'alex', 'Разнообразный и богатый опыт постоянный количественный рост и сфера нашей активности обеспечивает широкому кругу специалистов участие в формировании ключевых компонентов планируемого обновления гражданского сознания играет важную роль в формировании направлений прогрессивного развития', 1112212625, 1762213137, 1, 2),
(5, 'андрей', 'Таким образом повышение уровня гражданского сознания представляет собой интересный эксперимент проверки направлений прогрессивного развития', 1762213295, NULL, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip` varchar(130) DEFAULT NULL,
  `postsCount` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `session_id` varchar(255) DEFAULT NULL,
  `creation_time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `ip`, `postsCount`, `session_id`, `creation_time`) VALUES
(1, 'test@test.com', '2001:0DB8:AA10:0001:0000:0000:0000:00FB', 1, 'hh3ba9mpmgno54ar45h8e7d0bk8u7jbb', 1762211639),
(2, 'test@gmail.com', '527.31.0.1', 6, 'hh3ba9mpmgno54ar45h8e7d0bk8u7jbb', 1762212625);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip` (`ip`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
