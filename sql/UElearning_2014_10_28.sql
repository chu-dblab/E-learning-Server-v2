-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2014 年 10 月 28 日 07:28
-- 伺服器版本: 5.6.16
-- PHP 版本： 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `UElearning`
--

-- --------------------------------------------------------

--
-- 資料表結構 `chu__AGroup`
--

CREATE TABLE IF NOT EXISTS `chu__AGroup` (
  `GID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `GName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GMemo` tinytext COLLATE utf8_unicode_ci,
  `GBuildTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `GModifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '權限群組資訊修改時間',
  `GAuth_Admin` tinyint(1) NOT NULL,
  `GAuth_ClientAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`GID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者群組權限管理分類';

--
-- 資料表的匯出資料 `chu__AGroup`
--

INSERT INTO `chu__AGroup` (`GID`, `GName`, `GMemo`, `GBuildTime`, `GModifyTime`, `GAuth_Admin`, `GAuth_ClientAdmin`) VALUES
('admin', '管理員', NULL, '2014-10-07 08:38:03', '2014-10-23 05:33:32', 0, 0),
('student', '學生', NULL, '2014-10-07 08:38:03', '2014-10-23 05:33:32', 0, 0),
('teacher', '老師', NULL, '2014-10-07 08:38:03', '2014-10-23 05:33:32', 0, 0),
('user', '一般使用者', NULL, '2014-10-23 20:14:52', '2014-10-23 20:14:52', 0, 1);

-- --------------------------------------------------------

--
-- 替換檢視表以便查看 `chu__AGroup_with_people`
--
CREATE TABLE IF NOT EXISTS `chu__AGroup_with_people` (
);
-- --------------------------------------------------------

--
-- 資料表結構 `chu__Area`
--

CREATE TABLE IF NOT EXISTS `chu__Area` (
  `AID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '區域編號',
  `HID` int(10) DEFAULT NULL COMMENT '屬於哪個廳',
  `AFloor` int(3) DEFAULT NULL COMMENT '區域所在樓層',
  `ANum` int(11) DEFAULT NULL COMMENT '區域地圖上的編號',
  `AName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '區域名稱',
  `AMapID` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '區域地圖編號',
  `AIntroduction` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`AID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='標的所在的區域分類' AUTO_INCREMENT=15 ;

--
-- 資料表的匯出資料 `chu__Area`
--

INSERT INTO `chu__Area` (`AID`, `HID`, `AFloor`, `ANum`, `AName`, `AMapID`, `AIntroduction`) VALUES
(1, 1, 1, 1, '眾妙之門', NULL, NULL),
(2, 1, 1, 2, '生命的起源', NULL, NULL),
(3, 1, 1, 3, '生命上的陸地', NULL, NULL),
(4, 1, 1, 4, '植物的演化', NULL, NULL),
(5, 1, 1, 5, '恐龍時代', NULL, NULL),
(6, 1, 2, 1, '生命征服天空', NULL, NULL),
(7, 1, 2, 2, '滅絕', NULL, NULL),
(8, 1, 2, 3, '哺乳類的演化與適應', NULL, NULL),
(9, 1, 2, 4, '人類的故事', NULL, NULL),
(10, 1, 2, 5, '我們的身體一生老病', NULL, NULL),
(11, 1, -1, 1, '數與形', NULL, NULL),
(12, 1, -1, 2, '生彩色世界', NULL, NULL),
(13, 1, -1, 3, '大自然的聲音', NULL, NULL),
(14, 1, -1, 4, '多用途劇場', NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `chu__CGroup`
--

CREATE TABLE IF NOT EXISTS `chu__CGroup` (
  `CID` int(11) NOT NULL AUTO_INCREMENT,
  `CName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CMemo` tinytext COLLATE utf8_unicode_ci,
  `CBuildTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CModifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者班級分類' AUTO_INCREMENT=3 ;

--
-- 資料表的匯出資料 `chu__CGroup`
--

INSERT INTO `chu__CGroup` (`CID`, `CName`, `CMemo`, `CBuildTime`, `CModifyTime`) VALUES
(1, NULL, NULL, '2014-10-26 11:00:39', '2014-10-26 11:00:39'),
(2, NULL, NULL, '2014-10-26 11:00:55', '2014-10-26 11:00:55');

-- --------------------------------------------------------

--
-- 替換檢視表以便查看 `chu__CGroup_with_people`
--
CREATE TABLE IF NOT EXISTS `chu__CGroup_with_people` (
);
-- --------------------------------------------------------

--
-- 資料表結構 `chu__Edge`
--

CREATE TABLE IF NOT EXISTS `chu__Edge` (
  `Ti` int(11) NOT NULL,
  `Tj` int(11) NOT NULL,
  `MoveTime` int(4) NOT NULL COMMENT '移動時間(分鐘)',
  `Destance` int(11) NOT NULL COMMENT '距離(M)',
  PRIMARY KEY (`Ti`,`Tj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='標的和標的之間';

--
-- 資料表的匯出資料 `chu__Edge`
--

INSERT INTO `chu__Edge` (`Ti`, `Tj`, `MoveTime`, `Destance`) VALUES
(0, 1, 0, 0),
(0, 2, 0, 0),
(0, 3, 0, 0),
(0, 4, 0, 0),
(0, 5, 0, 0),
(0, 6, 0, 0),
(0, 7, 0, 0),
(0, 8, 0, 0),
(0, 9, 0, 0),
(0, 10, 0, 0),
(0, 11, 0, 0),
(0, 12, 0, 0),
(0, 13, 0, 0),
(0, 14, 0, 0),
(0, 15, 0, 0),
(0, 16, 0, 2),
(0, 17, 1, 2),
(0, 18, 1, 2),
(0, 19, 1, 2),
(0, 20, 2, 3),
(0, 21, 2, 3),
(0, 22, 3, 3),
(0, 23, 3, 4),
(0, 24, 3, 4),
(0, 25, 4, 4),
(0, 26, 4, 5),
(0, 27, 5, 5),
(0, 28, 5, 6),
(0, 29, 6, 6),
(0, 30, 6, 7),
(1, 2, 0, 0),
(1, 3, 0, 0),
(1, 4, 0, 0),
(1, 5, 0, 0),
(1, 6, 0, 0),
(1, 7, 0, 0),
(1, 8, 0, 0),
(1, 9, 0, 0),
(1, 10, 0, 0),
(1, 11, 0, 0),
(1, 12, 0, 0),
(1, 13, 0, 0),
(1, 14, 0, 0),
(1, 15, 0, 0),
(1, 16, 1, 0),
(1, 17, 1, 0),
(1, 18, 1, 0),
(1, 19, 2, 0),
(1, 20, 2, 0),
(1, 21, 2, 0),
(1, 22, 2, 0),
(1, 23, 2, 0),
(1, 24, 3, 0),
(1, 25, 3, 0),
(1, 26, 4, 0),
(1, 27, 4, 0),
(1, 28, 4, 0),
(1, 29, 6, 0),
(1, 30, 1, 0),
(2, 1, 0, 0),
(2, 3, 0, 0),
(2, 4, 0, 0),
(2, 5, 0, 0),
(2, 6, 0, 0),
(2, 7, 0, 0),
(2, 8, 0, 0),
(2, 9, 0, 0),
(2, 10, 0, 0),
(2, 11, 0, 0),
(2, 12, 0, 0),
(2, 13, 0, 0),
(2, 14, 0, 0),
(2, 15, 0, 0),
(2, 16, 1, 0),
(2, 17, 1, 0),
(2, 18, 1, 0),
(2, 19, 1, 0),
(2, 20, 1, 0),
(2, 21, 1, 0),
(2, 22, 1, 0),
(2, 23, 1, 0),
(2, 24, 2, 0),
(2, 25, 2, 0),
(2, 26, 3, 0),
(2, 27, 3, 0),
(2, 28, 3, 0),
(2, 29, 5, 0),
(2, 30, 1, 0),
(3, 1, 0, 0),
(3, 2, 0, 0),
(3, 4, 0, 0),
(3, 5, 0, 0),
(3, 6, 0, 0),
(3, 7, 0, 0),
(3, 8, 0, 0),
(3, 9, 0, 0),
(3, 10, 0, 0),
(3, 11, 0, 0),
(3, 12, 0, 0),
(3, 13, 0, 0),
(3, 14, 0, 0),
(3, 15, 0, 0),
(3, 16, 1, 0),
(3, 17, 1, 0),
(3, 18, 1, 0),
(3, 19, 1, 0),
(3, 20, 1, 0),
(3, 21, 1, 0),
(3, 22, 1, 0),
(3, 23, 1, 0),
(3, 24, 2, 0),
(3, 25, 2, 0),
(3, 26, 3, 0),
(3, 27, 3, 0),
(3, 28, 3, 0),
(3, 29, 5, 0),
(3, 30, 1, 0),
(4, 1, 0, 0),
(4, 2, 0, 0),
(4, 3, 0, 0),
(4, 5, 0, 0),
(4, 6, 0, 0),
(4, 7, 0, 0),
(4, 8, 0, 0),
(4, 9, 0, 0),
(4, 10, 0, 0),
(4, 11, 0, 0),
(4, 12, 0, 0),
(4, 13, 0, 0),
(4, 14, 0, 0),
(4, 15, 0, 0),
(4, 16, 1, 0),
(4, 17, 1, 0),
(4, 18, 1, 0),
(4, 19, 1, 0),
(4, 20, 1, 0),
(4, 21, 1, 0),
(4, 22, 1, 0),
(4, 23, 1, 0),
(4, 24, 2, 0),
(4, 25, 2, 0),
(4, 26, 3, 0),
(4, 27, 3, 0),
(4, 28, 3, 0),
(4, 29, 5, 0),
(4, 30, 1, 0),
(5, 1, 0, 0),
(5, 2, 0, 0),
(5, 3, 0, 0),
(5, 4, 0, 0),
(5, 6, 0, 0),
(5, 7, 0, 0),
(5, 8, 0, 0),
(5, 9, 0, 0),
(5, 10, 0, 0),
(5, 11, 0, 0),
(5, 12, 0, 0),
(5, 13, 0, 0),
(5, 14, 0, 0),
(5, 15, 0, 0),
(5, 16, 2, 0),
(5, 17, 1, 0),
(5, 18, 1, 0),
(5, 19, 1, 0),
(5, 20, 1, 0),
(5, 21, 1, 0),
(5, 22, 1, 0),
(5, 23, 1, 0),
(5, 24, 1, 0),
(5, 25, 1, 0),
(5, 26, 2, 0),
(5, 27, 2, 0),
(5, 28, 2, 0),
(5, 29, 4, 0),
(5, 30, 2, 0),
(6, 1, 0, 0),
(6, 2, 0, 0),
(6, 3, 0, 0),
(6, 4, 0, 0),
(6, 5, 0, 0),
(6, 7, 0, 0),
(6, 8, 0, 0),
(6, 9, 0, 0),
(6, 10, 0, 0),
(6, 11, 0, 0),
(6, 12, 0, 0),
(6, 13, 0, 0),
(6, 14, 0, 0),
(6, 15, 0, 0),
(6, 16, 2, 0),
(6, 17, 1, 0),
(6, 18, 1, 0),
(6, 19, 1, 0),
(6, 20, 1, 0),
(6, 21, 1, 0),
(6, 22, 1, 0),
(6, 23, 1, 0),
(6, 24, 1, 0),
(6, 25, 1, 0),
(6, 26, 2, 0),
(6, 27, 2, 0),
(6, 28, 2, 0),
(6, 29, 4, 0),
(6, 30, 2, 0),
(7, 1, 0, 0),
(7, 2, 0, 0),
(7, 3, 0, 0),
(7, 4, 0, 0),
(7, 5, 0, 0),
(7, 6, 0, 0),
(7, 8, 0, 0),
(7, 9, 0, 0),
(7, 10, 0, 0),
(7, 11, 0, 0),
(7, 12, 0, 0),
(7, 13, 0, 0),
(7, 14, 0, 0),
(7, 15, 0, 0),
(7, 16, 2, 0),
(7, 17, 1, 0),
(7, 18, 1, 0),
(7, 19, 1, 0),
(7, 20, 1, 0),
(7, 21, 1, 0),
(7, 22, 1, 0),
(7, 23, 1, 0),
(7, 24, 1, 0),
(7, 25, 1, 0),
(7, 26, 2, 0),
(7, 27, 2, 0),
(7, 28, 2, 0),
(7, 29, 4, 0),
(7, 30, 2, 0),
(8, 1, 0, 0),
(8, 2, 0, 0),
(8, 3, 0, 0),
(8, 4, 0, 0),
(8, 5, 0, 0),
(8, 6, 0, 0),
(8, 7, 0, 0),
(8, 9, 0, 0),
(8, 10, 0, 0),
(8, 11, 0, 0),
(8, 12, 0, 0),
(8, 13, 0, 0),
(8, 14, 0, 0),
(8, 15, 0, 0),
(8, 16, 2, 0),
(8, 17, 1, 0),
(8, 18, 1, 0),
(8, 19, 1, 0),
(8, 20, 1, 0),
(8, 21, 1, 0),
(8, 22, 1, 0),
(8, 23, 1, 0),
(8, 24, 1, 0),
(8, 25, 1, 0),
(8, 26, 2, 0),
(8, 27, 2, 0),
(8, 28, 2, 0),
(8, 29, 4, 0),
(8, 30, 2, 0),
(9, 1, 0, 0),
(9, 2, 0, 0),
(9, 3, 0, 0),
(9, 4, 0, 0),
(9, 5, 0, 0),
(9, 6, 0, 0),
(9, 7, 0, 0),
(9, 8, 0, 0),
(9, 10, 0, 0),
(9, 11, 0, 0),
(9, 12, 0, 0),
(9, 13, 0, 0),
(9, 14, 0, 0),
(9, 15, 0, 0),
(9, 16, 2, 0),
(9, 17, 1, 0),
(9, 18, 1, 0),
(9, 19, 1, 0),
(9, 20, 1, 0),
(9, 21, 1, 0),
(9, 22, 1, 0),
(9, 23, 1, 0),
(9, 24, 1, 0),
(9, 25, 1, 0),
(9, 26, 2, 0),
(9, 27, 2, 0),
(9, 28, 2, 0),
(9, 29, 4, 0),
(9, 30, 2, 0),
(10, 1, 0, 0),
(10, 2, 0, 0),
(10, 3, 0, 0),
(10, 4, 0, 0),
(10, 5, 0, 0),
(10, 6, 0, 0),
(10, 7, 0, 0),
(10, 8, 0, 0),
(10, 9, 0, 0),
(10, 11, 0, 0),
(10, 12, 0, 0),
(10, 13, 0, 0),
(10, 14, 0, 0),
(10, 15, 0, 0),
(10, 16, 3, 0),
(10, 17, 2, 0),
(10, 18, 2, 0),
(10, 19, 2, 0),
(10, 20, 1, 0),
(10, 21, 1, 0),
(10, 22, 1, 0),
(10, 23, 1, 0),
(10, 24, 1, 0),
(10, 25, 1, 0),
(10, 26, 1, 0),
(10, 27, 1, 0),
(10, 28, 1, 0),
(10, 29, 3, 0),
(10, 30, 3, 0),
(11, 1, 0, 0),
(11, 2, 0, 0),
(11, 3, 0, 0),
(11, 4, 0, 0),
(11, 5, 0, 0),
(11, 6, 0, 0),
(11, 7, 0, 0),
(11, 8, 0, 0),
(11, 9, 0, 0),
(11, 10, 0, 0),
(11, 12, 0, 0),
(11, 13, 0, 0),
(11, 14, 0, 0),
(11, 15, 0, 0),
(11, 16, 3, 0),
(11, 17, 2, 0),
(11, 18, 2, 0),
(11, 19, 2, 0),
(11, 20, 1, 0),
(11, 21, 1, 0),
(11, 22, 1, 0),
(11, 23, 1, 0),
(11, 24, 1, 0),
(11, 25, 1, 0),
(11, 26, 1, 0),
(11, 27, 1, 0),
(11, 28, 1, 0),
(11, 29, 3, 0),
(11, 30, 3, 0),
(12, 1, 0, 0),
(12, 2, 0, 0),
(12, 3, 0, 0),
(12, 4, 0, 0),
(12, 5, 0, 0),
(12, 6, 0, 0),
(12, 7, 0, 0),
(12, 8, 0, 0),
(12, 9, 0, 0),
(12, 10, 0, 0),
(12, 11, 0, 0),
(12, 13, 0, 0),
(12, 14, 0, 0),
(12, 15, 0, 0),
(12, 16, 4, 0),
(12, 17, 3, 0),
(12, 18, 3, 0),
(12, 19, 3, 0),
(12, 20, 2, 0),
(12, 21, 2, 0),
(12, 22, 2, 0),
(12, 23, 2, 0),
(12, 24, 2, 0),
(12, 25, 1, 0),
(12, 26, 1, 0),
(12, 27, 1, 0),
(12, 28, 1, 0),
(12, 29, 2, 0),
(12, 30, 4, 0),
(13, 1, 0, 0),
(13, 2, 0, 0),
(13, 3, 0, 0),
(13, 4, 0, 0),
(13, 5, 0, 0),
(13, 6, 0, 0),
(13, 7, 0, 0),
(13, 8, 0, 0),
(13, 9, 0, 0),
(13, 10, 0, 0),
(13, 11, 0, 0),
(13, 12, 0, 0),
(13, 14, 0, 0),
(13, 15, 0, 0),
(13, 16, 4, 0),
(13, 17, 3, 0),
(13, 18, 3, 0),
(13, 19, 3, 0),
(13, 20, 2, 0),
(13, 21, 2, 0),
(13, 22, 2, 0),
(13, 23, 2, 0),
(13, 24, 2, 0),
(13, 25, 1, 0),
(13, 26, 1, 0),
(13, 27, 1, 0),
(13, 28, 1, 0),
(13, 29, 2, 0),
(13, 30, 4, 0),
(14, 1, 0, 0),
(14, 2, 0, 0),
(14, 3, 0, 0),
(14, 4, 0, 0),
(14, 5, 0, 0),
(14, 6, 0, 0),
(14, 7, 0, 0),
(14, 8, 0, 0),
(14, 9, 0, 0),
(14, 10, 0, 0),
(14, 11, 0, 0),
(14, 12, 0, 0),
(14, 13, 0, 0),
(14, 15, 0, 0),
(14, 16, 4, 0),
(14, 17, 3, 0),
(14, 18, 3, 0),
(14, 19, 3, 0),
(14, 20, 2, 0),
(14, 21, 2, 0),
(14, 22, 2, 0),
(14, 23, 2, 0),
(14, 24, 2, 0),
(14, 25, 1, 0),
(14, 26, 1, 0),
(14, 27, 1, 0),
(14, 28, 1, 0),
(14, 29, 1, 0),
(14, 30, 4, 0),
(15, 1, 0, 0),
(15, 2, 0, 0),
(15, 3, 0, 0),
(15, 4, 0, 0),
(15, 5, 0, 0),
(15, 6, 0, 0),
(15, 7, 0, 0),
(15, 8, 0, 0),
(15, 9, 0, 0),
(15, 10, 0, 0),
(15, 11, 0, 0),
(15, 12, 0, 0),
(15, 13, 0, 0),
(15, 14, 0, 0),
(15, 16, 0, 0),
(15, 17, 0, 0),
(15, 18, 0, 0),
(15, 19, 0, 0),
(15, 20, 0, 0),
(15, 21, 0, 0),
(15, 22, 0, 0),
(15, 23, 0, 0),
(15, 24, 0, 0),
(15, 25, 0, 0),
(15, 26, 0, 0),
(15, 27, 0, 0),
(15, 28, 0, 0),
(15, 29, 0, 0),
(15, 30, 0, 0),
(16, 1, 0, 1),
(16, 2, 0, 1),
(16, 3, 0, 1),
(16, 4, 0, 1),
(16, 5, 0, 1),
(16, 6, 0, 1),
(16, 7, 0, 1),
(16, 8, 0, 1),
(16, 9, 0, 1),
(16, 10, 0, 1),
(16, 11, 0, 1),
(16, 12, 0, 1),
(16, 13, 0, 1),
(16, 14, 0, 1),
(16, 15, 0, 1),
(16, 17, 1, 1),
(16, 18, 1, 1),
(16, 19, 1, 1),
(16, 20, 2, 1),
(16, 21, 2, 1),
(16, 22, 2, 1),
(16, 23, 2, 1),
(16, 24, 2, 1),
(16, 25, 3, 1),
(16, 26, 3, 1),
(16, 27, 4, 1),
(16, 28, 4, 1),
(16, 29, 4, 1),
(16, 30, 6, 1),
(17, 1, 0, 2),
(17, 2, 0, 2),
(17, 3, 0, 2),
(17, 4, 0, 2),
(17, 5, 0, 2),
(17, 6, 0, 2),
(17, 7, 0, 2),
(17, 8, 0, 2),
(17, 9, 0, 2),
(17, 10, 0, 2),
(17, 11, 0, 2),
(17, 12, 0, 2),
(17, 13, 0, 2),
(17, 14, 0, 2),
(17, 15, 0, 2),
(17, 16, 1, 2),
(17, 18, 1, 2),
(17, 19, 1, 2),
(17, 20, 1, 2),
(17, 21, 1, 2),
(17, 22, 1, 2),
(17, 23, 1, 2),
(17, 24, 1, 2),
(17, 25, 2, 2),
(17, 26, 2, 2),
(17, 27, 3, 2),
(17, 28, 3, 2),
(17, 29, 3, 2),
(17, 30, 5, 2),
(18, 1, 0, 3),
(18, 2, 0, 3),
(18, 3, 0, 3),
(18, 4, 0, 3),
(18, 5, 0, 3),
(18, 6, 0, 3),
(18, 7, 0, 3),
(18, 8, 0, 3),
(18, 9, 0, 3),
(18, 10, 0, 3),
(18, 11, 0, 3),
(18, 12, 0, 3),
(18, 13, 0, 3),
(18, 14, 0, 3),
(18, 15, 0, 3),
(18, 16, 1, 3),
(18, 17, 1, 3),
(18, 19, 1, 3),
(18, 20, 1, 3),
(18, 21, 1, 3),
(18, 22, 1, 3),
(18, 23, 1, 3),
(18, 24, 1, 3),
(18, 25, 2, 3),
(18, 26, 2, 3),
(18, 27, 3, 3),
(18, 28, 3, 3),
(18, 29, 3, 3),
(18, 30, 5, 3),
(19, 1, 0, 4),
(19, 2, 0, 4),
(19, 3, 0, 4),
(19, 4, 0, 4),
(19, 5, 0, 4),
(19, 6, 0, 4),
(19, 7, 0, 4),
(19, 8, 0, 4),
(19, 9, 0, 4),
(19, 10, 0, 4),
(19, 11, 0, 4),
(19, 12, 0, 4),
(19, 13, 0, 4),
(19, 14, 0, 4),
(19, 15, 0, 4),
(19, 16, 1, 4),
(19, 17, 1, 4),
(19, 18, 1, 4),
(19, 20, 1, 4),
(19, 21, 1, 4),
(19, 22, 1, 4),
(19, 23, 1, 4),
(19, 24, 1, 4),
(19, 25, 2, 4),
(19, 26, 2, 4),
(19, 27, 3, 4),
(19, 28, 3, 4),
(19, 29, 3, 4),
(19, 30, 5, 4),
(20, 1, 0, 2),
(20, 2, 0, 2),
(20, 3, 0, 2),
(20, 4, 0, 2),
(20, 5, 0, 2),
(20, 6, 0, 2),
(20, 7, 0, 2),
(20, 8, 0, 2),
(20, 9, 0, 2),
(20, 10, 0, 2),
(20, 11, 0, 2),
(20, 12, 0, 2),
(20, 13, 0, 2),
(20, 14, 0, 2),
(20, 15, 0, 2),
(20, 16, 2, 2),
(20, 17, 1, 2),
(20, 18, 1, 2),
(20, 19, 1, 2),
(20, 21, 1, 2),
(20, 22, 1, 2),
(20, 23, 1, 2),
(20, 24, 1, 2),
(20, 25, 1, 2),
(20, 26, 1, 2),
(20, 27, 2, 2),
(20, 28, 2, 2),
(20, 29, 2, 2),
(20, 30, 4, 2),
(21, 1, 0, 3),
(21, 2, 0, 3),
(21, 3, 0, 3),
(21, 4, 0, 3),
(21, 5, 0, 3),
(21, 6, 0, 3),
(21, 7, 0, 3),
(21, 8, 0, 3),
(21, 9, 0, 3),
(21, 10, 0, 3),
(21, 11, 0, 3),
(21, 12, 0, 3),
(21, 13, 0, 3),
(21, 14, 0, 3),
(21, 15, 0, 3),
(21, 16, 2, 3),
(21, 17, 1, 3),
(21, 18, 1, 3),
(21, 19, 1, 3),
(21, 20, 1, 3),
(21, 22, 1, 3),
(21, 23, 1, 3),
(21, 24, 1, 3),
(21, 25, 1, 3),
(21, 26, 1, 3),
(21, 27, 2, 3),
(21, 28, 2, 3),
(21, 29, 2, 3),
(21, 30, 4, 3),
(22, 1, 0, 6),
(22, 2, 0, 6),
(22, 3, 0, 6),
(22, 4, 0, 6),
(22, 5, 0, 6),
(22, 6, 0, 6),
(22, 7, 0, 6),
(22, 8, 0, 6),
(22, 9, 0, 6),
(22, 10, 0, 6),
(22, 11, 0, 6),
(22, 12, 0, 6),
(22, 13, 0, 6),
(22, 14, 0, 6),
(22, 15, 0, 6),
(22, 16, 2, 6),
(22, 17, 1, 6),
(22, 18, 1, 6),
(22, 19, 1, 6),
(22, 20, 1, 6),
(22, 21, 1, 6),
(22, 23, 1, 6),
(22, 24, 1, 6),
(22, 25, 1, 6),
(22, 26, 1, 6),
(22, 27, 2, 6),
(22, 28, 2, 6),
(22, 29, 2, 6),
(22, 30, 4, 6),
(23, 1, 0, 5),
(23, 2, 0, 5),
(23, 3, 0, 5),
(23, 4, 0, 5),
(23, 5, 0, 5),
(23, 6, 0, 5),
(23, 7, 0, 5),
(23, 8, 0, 5),
(23, 9, 0, 5),
(23, 10, 0, 5),
(23, 11, 0, 5),
(23, 12, 0, 5),
(23, 13, 0, 5),
(23, 14, 0, 5),
(23, 15, 0, 5),
(23, 16, 2, 5),
(23, 17, 1, 5),
(23, 18, 1, 5),
(23, 19, 1, 5),
(23, 20, 1, 5),
(23, 21, 1, 5),
(23, 22, 1, 5),
(23, 24, 1, 5),
(23, 25, 1, 5),
(23, 26, 1, 5),
(23, 27, 2, 5),
(23, 28, 2, 5),
(23, 29, 2, 5),
(23, 30, 4, 5),
(24, 1, 0, 4),
(24, 2, 0, 4),
(24, 3, 0, 4),
(24, 4, 0, 4),
(24, 5, 0, 4),
(24, 6, 0, 4),
(24, 7, 0, 4),
(24, 8, 0, 4),
(24, 9, 0, 4),
(24, 10, 0, 4),
(24, 11, 0, 4),
(24, 12, 0, 4),
(24, 13, 0, 4),
(24, 14, 0, 4),
(24, 15, 0, 4),
(24, 16, 2, 4),
(24, 17, 1, 4),
(24, 18, 1, 4),
(24, 19, 1, 4),
(24, 20, 1, 4),
(24, 21, 1, 4),
(24, 22, 1, 4),
(24, 23, 1, 4),
(24, 25, 1, 4),
(24, 26, 1, 4),
(24, 27, 2, 4),
(24, 28, 2, 4),
(24, 29, 2, 4),
(24, 30, 4, 4),
(25, 1, 0, 7),
(25, 2, 0, 7),
(25, 3, 0, 7),
(25, 4, 0, 7),
(25, 5, 0, 7),
(25, 6, 0, 7),
(25, 7, 0, 7),
(25, 8, 0, 7),
(25, 9, 0, 7),
(25, 10, 0, 7),
(25, 11, 0, 7),
(25, 12, 0, 7),
(25, 13, 0, 7),
(25, 14, 0, 7),
(25, 15, 0, 7),
(25, 16, 3, 7),
(25, 17, 2, 7),
(25, 18, 2, 7),
(25, 19, 2, 7),
(25, 20, 1, 7),
(25, 21, 1, 7),
(25, 22, 1, 7),
(25, 23, 1, 7),
(25, 24, 1, 7),
(25, 26, 1, 7),
(25, 27, 1, 7),
(25, 28, 1, 7),
(25, 29, 1, 7),
(25, 30, 3, 7),
(26, 1, 0, 8),
(26, 2, 0, 8),
(26, 3, 0, 8),
(26, 4, 0, 8),
(26, 5, 0, 8),
(26, 6, 0, 8),
(26, 7, 0, 8),
(26, 8, 0, 8),
(26, 9, 0, 8),
(26, 10, 0, 8),
(26, 11, 0, 8),
(26, 12, 0, 8),
(26, 13, 0, 8),
(26, 14, 0, 8),
(26, 15, 0, 8),
(26, 16, 3, 8),
(26, 17, 2, 8),
(26, 18, 2, 8),
(26, 19, 2, 8),
(26, 20, 1, 8),
(26, 21, 1, 8),
(26, 22, 1, 8),
(26, 23, 1, 8),
(26, 24, 1, 8),
(26, 25, 1, 8),
(26, 27, 1, 8),
(26, 28, 1, 8),
(26, 29, 1, 8),
(26, 30, 3, 8),
(27, 1, 0, 6),
(27, 2, 0, 6),
(27, 3, 0, 6),
(27, 4, 0, 6),
(27, 5, 0, 6),
(27, 6, 0, 6),
(27, 7, 0, 6),
(27, 8, 0, 6),
(27, 9, 0, 6),
(27, 10, 0, 6),
(27, 11, 0, 6),
(27, 12, 0, 6),
(27, 13, 0, 6),
(27, 14, 0, 6),
(27, 15, 0, 6),
(27, 16, 4, 6),
(27, 17, 3, 6),
(27, 18, 3, 6),
(27, 19, 3, 6),
(27, 20, 2, 6),
(27, 21, 2, 6),
(27, 22, 2, 6),
(27, 23, 2, 6),
(27, 24, 2, 6),
(27, 25, 1, 6),
(27, 26, 1, 6),
(27, 28, 1, 6),
(27, 29, 1, 6),
(27, 30, 2, 6),
(28, 1, 0, 8),
(28, 2, 0, 8),
(28, 3, 0, 8),
(28, 4, 0, 8),
(28, 5, 0, 8),
(28, 6, 0, 8),
(28, 7, 0, 8),
(28, 8, 0, 8),
(28, 9, 0, 8),
(28, 10, 0, 8),
(28, 11, 0, 8),
(28, 12, 0, 8),
(28, 13, 0, 8),
(28, 14, 0, 8),
(28, 15, 0, 8),
(28, 16, 4, 8),
(28, 17, 3, 8),
(28, 18, 3, 8),
(28, 19, 3, 8),
(28, 20, 2, 8),
(28, 21, 2, 8),
(28, 22, 2, 8),
(28, 23, 2, 8),
(28, 24, 2, 8),
(28, 25, 1, 8),
(28, 26, 1, 8),
(28, 27, 1, 8),
(28, 29, 1, 8),
(28, 30, 2, 8),
(29, 1, 0, 7),
(29, 2, 0, 7),
(29, 3, 0, 7),
(29, 4, 0, 7),
(29, 5, 0, 7),
(29, 6, 0, 7),
(29, 7, 0, 7),
(29, 8, 0, 7),
(29, 9, 0, 7),
(29, 10, 0, 7),
(29, 11, 0, 7),
(29, 12, 0, 7),
(29, 13, 0, 7),
(29, 14, 0, 7),
(29, 15, 0, 7),
(29, 16, 4, 7),
(29, 17, 3, 7),
(29, 18, 3, 7),
(29, 19, 3, 7),
(29, 20, 2, 7),
(29, 21, 2, 7),
(29, 22, 2, 7),
(29, 23, 2, 7),
(29, 24, 2, 7),
(29, 25, 1, 7),
(29, 26, 1, 7),
(29, 27, 1, 7),
(29, 28, 1, 7),
(29, 30, 1, 7),
(30, 1, 0, 9),
(30, 2, 0, 9),
(30, 3, 0, 9),
(30, 4, 0, 9),
(30, 5, 0, 9),
(30, 6, 0, 9),
(30, 7, 0, 9),
(30, 8, 0, 9),
(30, 9, 0, 9),
(30, 10, 0, 9),
(30, 11, 0, 9),
(30, 12, 0, 9),
(30, 13, 0, 9),
(30, 14, 0, 9),
(30, 15, 0, 9),
(30, 16, 6, 9),
(30, 17, 5, 9),
(30, 18, 5, 9),
(30, 19, 5, 9),
(30, 20, 4, 9),
(30, 21, 4, 9),
(30, 22, 4, 9),
(30, 23, 4, 9),
(30, 24, 4, 9),
(30, 25, 3, 9),
(30, 26, 3, 9),
(30, 27, 2, 9),
(30, 28, 2, 9),
(30, 29, 1, 9);

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Hall`
--

CREATE TABLE IF NOT EXISTS `chu__Hall` (
  `HID` int(10) NOT NULL AUTO_INCREMENT,
  `HName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '廳的名稱',
  `HMapID` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '廳的地圖編號',
  `HIntroduction` tinytext COLLATE utf8_unicode_ci COMMENT '廳的簡介',
  PRIMARY KEY (`HID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='區域所在的廳分類' AUTO_INCREMENT=2 ;

--
-- 資料表的匯出資料 `chu__Hall`
--

INSERT INTO `chu__Hall` (`HID`, `HName`, `HMapID`, `HIntroduction`) VALUES
(1, '生命科學廳', NULL, '人類從何而來？與自然的關係為何？而自然又是如何發展它的生命？諸多疑惑，自古以來，未曾停歇。\r\n\r\n本廳以大自然的奧祕為總主題，利用13個展示區分別呈現大自然的現象及演化的動態。從');

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Log`
--

CREATE TABLE IF NOT EXISTS `chu__Log` (
  `LID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Encode` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`LID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Material`
--

CREATE TABLE IF NOT EXISTS `chu__Material` (
  `MID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '教材內部編號',
  `TID` int(10) unsigned NOT NULL COMMENT '標的內部編號',
  `MEntity` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否為實體教材',
  `MMode` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '教材模式',
  `MUrl` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT '教材檔案路徑',
  PRIMARY KEY (`MID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='教材' AUTO_INCREMENT=31 ;

--
-- 資料表的匯出資料 `chu__Material`
--

INSERT INTO `chu__Material` (`MID`, `TID`, `MEntity`, `MMode`, `MUrl`) VALUES
(1, 1, 1, 'normal', '01.html'),
(2, 2, 1, 'normal', '02.html'),
(3, 3, 1, 'normal', '03.html'),
(4, 4, 1, 'normal', '04.html'),
(5, 5, 1, 'normal', '05.html'),
(6, 6, 1, 'normal', '06.html'),
(7, 7, 1, 'normal', '07.html'),
(8, 8, 1, 'normal', '08.html'),
(9, 9, 1, 'normal', '09.html'),
(10, 10, 1, 'normal', '10.html'),
(11, 11, 1, 'normal', '11.html'),
(12, 12, 1, 'normal', '12.html'),
(13, 13, 1, 'normal', '13.html'),
(14, 14, 1, 'normal', '14.html'),
(15, 15, 1, 'normal', '15.html'),
(16, 1, 0, 'normal', '16.html'),
(17, 2, 0, 'normal', '17.html'),
(18, 3, 0, 'normal', '18.html'),
(19, 4, 0, 'normal', '19.html'),
(20, 5, 0, 'normal', '20.html'),
(21, 6, 0, 'normal', '21.html'),
(22, 7, 0, 'normal', '22.html'),
(23, 8, 0, 'normal', '23.html'),
(24, 9, 0, 'normal', '24.html'),
(25, 10, 0, 'normal', '25.html'),
(26, 11, 0, 'normal', '26.html'),
(27, 12, 0, 'normal', '27.html'),
(28, 13, 0, 'normal', '28.html'),
(29, 14, 0, 'normal', '29.html'),
(30, 15, 0, 'normal', '30.html');

-- --------------------------------------------------------

--
-- 資料表結構 `chu__MaterialKind`
--

CREATE TABLE IF NOT EXISTS `chu__MaterialKind` (
  `MkID` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `MkName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`MkID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `chu__MaterialKind`
--

INSERT INTO `chu__MaterialKind` (`MkID`, `MkName`) VALUES
('normal', '一般教材');

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Recommand`
--

CREATE TABLE IF NOT EXISTS `chu__Recommand` (
  `TID` int(3) NOT NULL COMMENT '標的內部編號',
  `UID` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '使用者帳號',
  `gradation` int(11) NOT NULL COMMENT '系統推薦標地順序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='推薦';

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Study`
--

CREATE TABLE IF NOT EXISTS `chu__Study` (
  `SID` int(10) NOT NULL AUTO_INCREMENT,
  `SaID` int(10) NOT NULL,
  `UID` int(30) NOT NULL,
  `TID` int(10) NOT NULL COMMENT '標的編號',
  `In_TargetTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '進入標的時間',
  `Out_TargetTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '離開標的時間',
  PRIMARY KEY (`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者經過哪些標的的紀錄' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表結構 `chu__StudyActivity`
--

CREATE TABLE IF NOT EXISTS `chu__StudyActivity` (
  `SaID` int(10) NOT NULL AUTO_INCREMENT COMMENT '學習活動流水編號',
  `UID` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '使用者ID',
  `ThID` int(10) NOT NULL COMMENT '主題編號',
  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '開始學習時間',
  `EndTime` timestamp NULL DEFAULT NULL COMMENT '結束學習時間（學習中為NULL）',
  `LearnTime` int(4) NOT NULL COMMENT '預定學習所需時間',
  `Delay` int(11) NOT NULL DEFAULT '0' COMMENT '時間延長',
  `TimeForce` tinyint(1) NOT NULL DEFAULT '0' COMMENT '學習時間已過是否強制中止學習',
  `LMode` int(2) NOT NULL DEFAULT '1' COMMENT '學習導引模式',
  `LModeForce` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否拒絕前往非推薦點進行學習',
  `MMode` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '教材模式',
  PRIMARY KEY (`SaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='學習活動' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表結構 `chu__StudyWill`
--

CREATE TABLE IF NOT EXISTS `chu__StudyWill` (
  `SwID` int(10) NOT NULL AUTO_INCREMENT COMMENT '預約學習活動流水編號',
  `UID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ThID` int(10) NOT NULL COMMENT '主題編號',
  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '預約生效時間',
  `ExpiredTime` timestamp NULL DEFAULT NULL COMMENT '過期時間',
  `LearnTime` int(4) NOT NULL,
  `TimeForce` tinyint(1) NOT NULL DEFAULT '1' COMMENT '學習時間已過是否強制中止學習',
  `LMode` int(2) NOT NULL DEFAULT '1' COMMENT '學習導引模式',
  `LModeForce` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否拒絕前往非推薦點進行學習',
  `MMode` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '教材模式',
  `Lock` tinyint(1) NOT NULL DEFAULT '1' COMMENT '鎖定不讓學生更改',
  `BuildTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ModifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SwID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='預約學習活動' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Target`
--

CREATE TABLE IF NOT EXISTS `chu__Target` (
  `TID` int(10) unsigned NOT NULL COMMENT '標的內部編號',
  `AID` int(10) DEFAULT NULL COMMENT '標的所在的區域編號',
  `TNum` int(10) DEFAULT NULL COMMENT '標的地圖上的編號',
  `TName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '標的名稱',
  `TMapID` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '地圖圖檔名稱',
  `TLearnTime` int(4) unsigned NOT NULL COMMENT '預估此標的應該學習的時間',
  `PLj` int(11) unsigned NOT NULL COMMENT '學習標的的人數限制',
  `Mj` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '目前人數',
  `S` int(11) unsigned DEFAULT NULL COMMENT '學習標的飽和率上限',
  PRIMARY KEY (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='標的資訊';

--
-- 資料表的匯出資料 `chu__Target`
--

INSERT INTO `chu__Target` (`TID`, `AID`, `TNum`, `TName`, `TMapID`, `TLearnTime`, `PLj`, `Mj`, `S`) VALUES
(1, 1, NULL, '含有生物遺跡的岩石', 'map_01_02_03.png', 7, 2, 0, 1),
(2, 1, NULL, '岩石中的紀錄', 'map_01_02_03.png', 8, 2, 0, 1),
(3, 4, NULL, '生命在水中的演化', 'map_01_02_03.png', 3, 2, 0, 1),
(4, 4, NULL, '最早的森林', 'map_04.jpg', 3, 2, 0, 1),
(5, 3, NULL, '古代的兩棲類', 'map_05.jpg', 5, 2, 0, 1),
(6, 5, NULL, '恐龍時代', 'map_06.jpg', 6, 2, 0, 1),
(7, 5, NULL, '蒙古的恐龍', 'map_07.jpg', 4, 2, 0, 1),
(8, 5, NULL, '恐龍再現', 'map_08.jpg', 4, 2, 0, 1),
(9, 5, NULL, '竊蛋龍', 'map_09.jpg', 4, 2, 0, 1),
(10, 5, NULL, '巨龍的腳印', 'map_10.jpg', 4, 2, 0, 1),
(11, 6, NULL, '始祖鳥與帶有羽毛的恐龍', 'map_11.jpg', 8, 2, 0, 1),
(12, 8, NULL, '阿法南猿', 'map_12.jpg', 4, 2, 0, 1),
(13, 9, NULL, '探索人類的過去', 'map_13.jpg', 5, 1, 0, 1),
(14, 9, NULL, '周口店北京人', 'map_14.jpg', 3, 2, 0, 1),
(15, 10, NULL, '木乃伊', 'map_15.jpg', 8, 2, 0, 1);

-- --------------------------------------------------------

--
-- 替換檢視表以便查看 `chu__target_full_data`
--
CREATE TABLE IF NOT EXISTS `chu__target_full_data` (
);
-- --------------------------------------------------------

--
-- 替換檢視表以便查看 `chu__Target_with_Area`
--
CREATE TABLE IF NOT EXISTS `chu__Target_with_Area` (
);
-- --------------------------------------------------------

--
-- 資料表結構 `chu__TBelong`
--

CREATE TABLE IF NOT EXISTS `chu__TBelong` (
  `ThID` int(10) NOT NULL COMMENT '主題編號',
  `TID` int(10) NOT NULL COMMENT '標的編號',
  `Weights` int(3) NOT NULL COMMENT '當次學習主題的某一個學習標的之權重',
  PRIMARY KEY (`TID`,`ThID`),
  KEY `TID` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='標的和主題之間';

--
-- 資料表的匯出資料 `chu__TBelong`
--

INSERT INTO `chu__TBelong` (`ThID`, `TID`, `Weights`) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 4),
(1, 4, 7),
(1, 5, 4),
(1, 6, 5),
(1, 7, 2),
(1, 8, 6),
(1, 9, 7),
(1, 10, 9),
(1, 11, 6),
(1, 12, 4),
(1, 13, 5),
(1, 14, 5),
(1, 15, 9);

-- --------------------------------------------------------

--
-- 資料表結構 `chu__Theme`
--

CREATE TABLE IF NOT EXISTS `chu__Theme` (
  `ThID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ThName` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '主題名稱',
  `ThLearnTime` int(4) NOT NULL COMMENT '學習此主題要花的總時間(m)',
  `ThIntroduction` tinytext COLLATE utf8_unicode_ci COMMENT '介紹',
  `ThBuildTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ThModifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ThID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='主題' AUTO_INCREMENT=2 ;

--
-- 資料表的匯出資料 `chu__Theme`
--

INSERT INTO `chu__Theme` (`ThID`, `ThName`, `ThLearnTime`, `ThIntroduction`, `ThBuildTime`, `ThModifyTime`) VALUES
(1, '生命科學', 40, NULL, '2014-10-23 09:21:03', '2014-10-23 09:21:03');

-- --------------------------------------------------------

--
-- 資料表結構 `chu__User`
--

CREATE TABLE IF NOT EXISTS `chu__User` (
  `UID` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '使用者帳號',
  `UPassword` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '密碼',
  `GID` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '使用者群組',
  `CID` int(11) DEFAULT NULL COMMENT '使用者班級',
  `UEnabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '帳號啟用狀態',
  `UBuildTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '帳號建立時間',
  `UModifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '帳號資訊修改時間',
  `LMode` int(2) DEFAULT NULL COMMENT '學習導引模式',
  `MMode` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'normal' COMMENT '教材模式',
  `UEnable_NoAppoint` tinyint(1) NOT NULL DEFAULT '1' COMMENT '開放非預約學習',
  `UNickname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '暱稱',
  `URealName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '真實姓名',
  `UEmail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '使用者email',
  `UMemo` tinytext COLLATE utf8_unicode_ci COMMENT '備註',
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者帳號';

-- --------------------------------------------------------

--
-- 資料表結構 `chu__UserSession`
--

CREATE TABLE IF NOT EXISTS `chu__UserSession` (
  `UsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UToken` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '此登入階段的token',
  `UID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `UAgent` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '使用哪個裝置登入',
  `ULoginDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登入時間',
  `ULogoutDate` timestamp NULL DEFAULT NULL COMMENT '登出時間',
  PRIMARY KEY (`UsID`),
  UNIQUE KEY `UToken` (`UToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者登入紀錄' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 檢視表結構 `chu__AGroup_with_people`
--
DROP TABLE IF EXISTS `chu__AGroup_with_people`;
-- 使用中(#1356 - View 'UElearning.chu__AGroup_with_people' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them)

-- --------------------------------------------------------

--
-- 檢視表結構 `chu__CGroup_with_people`
--
DROP TABLE IF EXISTS `chu__CGroup_with_people`;
-- 使用中(#1356 - View 'UElearning.chu__CGroup_with_people' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them)

-- --------------------------------------------------------

--
-- 檢視表結構 `chu__target_full_data`
--
DROP TABLE IF EXISTS `chu__target_full_data`;
-- 使用中(#1356 - View 'UElearning.chu__target_full_data' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them)

-- --------------------------------------------------------

--
-- 檢視表結構 `chu__Target_with_Area`
--
DROP TABLE IF EXISTS `chu__Target_with_Area`;
-- 使用中(#1356 - View 'UElearning.chu__Target_with_Area' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them)

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;