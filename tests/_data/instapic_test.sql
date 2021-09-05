-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2021-08-14 15:28:17
-- 服务器版本： 10.3.16-MariaDB
-- PHP 版本： 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `instapic_test`
--

-- --------------------------------------------------------

--
-- 表的结构 `post`
--

CREATE TABLE `post` (
  `postid` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  `picture` varchar(40) NOT NULL,
  `desc_` text NOT NULL,
  `createdtime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `post`
--

INSERT INTO `post` (`postid`, `userid`, `picture`, `desc_`, `createdtime`) VALUES
(4, 2, '25e45421471621673557fe1d9991bf21.png', 'An example description.', '2021-07-18 03:13:28'),
(5, 3, '66bf1cfefcd0c686f484f2044823844c.png', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2021-08-07 14:40:21'),
(6, 3, '21414c58c8a5331033e85db15b14cca5.png', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', '2021-08-08 08:40:23'),
(7, 3, '9583e9c9603f894a80733f9cfeac8d71.png', 'cccccccccccccccccccccccccccccc', '2021-08-08 09:18:08'),
(8, 3, 'd658c99253d5fa466c88f2de4a82d497.png', 'dddddddddddddddddddddddddddddddddddd', '2021-08-08 09:18:20'),
(9, 4, 'fa7c0b7288d68cff0b2f81dad13e70b9.jpg', 'This is the back side of the HK identity card', '2021-08-11 14:08:43'),
(10, 4, '2aeb7e808a0f85bf46424b6a35ec1ca1.jpg', 'This is the road to my home', '2021-08-11 14:11:12'),
(11, 3, '75e48a114aaa0f6c4239c382044e2c2c.png', 'eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', '2021-08-12 12:05:58'),
(12, 3, '4765121080e3b3842995e261ae292fd0.png', 'fffffffffffffffffffffffffffffffffffffff', '2021-08-12 12:06:22'),
(13, 3, '8abc8a5df015debb6ca0f11a6aebf88d.png', 'gggggggggggggggggggggggggggggggg', '2021-08-12 12:07:04'),
(14, 3, '4fa51e52bf7c881afdd4507917307a88.png', 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', '2021-08-12 13:00:25');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `userid` int(10) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(41) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`userid`, `username`, `password`) VALUES
(1, 'Tristan', 'ae18c88fae4628ad28e27f83b19dd4e8f0790b1d'),
(2, 'Jennifer', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220'),
(3, 'ttttt', '207e7ae7c7c61d77ad79be51537523123ecc2b75'),
(4, 'Felicia', 'bc40dc7343108692d9132295f8bd82942052b021');

--
-- 转储表的索引
--

--
-- 表的索引 `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`postid`),
  ADD KEY `userid` (`userid`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `post`
--
ALTER TABLE `post`
  MODIFY `postid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `userid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 限制导出的表
--

--
-- 限制表 `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
