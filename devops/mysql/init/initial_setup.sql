-- MariaDB dump 10.17  Distrib 10.4.6-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: youtube
-- ------------------------------------------------------
-- Server version	10.4.6-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `added_to_playlist`
--

DROP TABLE IF EXISTS `added_to_playlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `added_to_playlist` (
  `playlist_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `date_added` date DEFAULT NULL,
  PRIMARY KEY (`playlist_id`,`video_id`),
  KEY `added_to_playlist_ibfk_2` (`video_id`),
  CONSTRAINT `added_to_playlist_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`),
  CONSTRAINT `added_to_playlist_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `added_to_playlist`
--

LOCK TABLES `added_to_playlist` WRITE;
/*!40000 ALTER TABLE `added_to_playlist` DISABLE KEYS */;
INSERT INTO `added_to_playlist` VALUES (11,32,'2020-01-15'),(16,32,'2020-01-15');
/*!40000 ALTER TABLE `added_to_playlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,'Autos & Vehicles'),(11,'Education'),(9,'Entertainment'),(1,'Film & Animation'),(7,'Gaming'),(3,'Music'),(10,'News & Politics'),(8,'People & Blogs'),(4,'Pets & Animals'),(12,'Science & Technology'),(5,'Sports'),(6,'Travel & Events');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `video_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `comments_ibfk_1` (`video_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (14,'Cool bunny!','2020-01-15 09:45:20',14,32),(15,'Very cool!','2020-01-15 10:21:32',29,32),(16,'Nice!','2020-01-15 10:22:54',30,32),(17,'Wow!','2020-01-15 10:23:33',30,33),(18,'Nice video','2020-01-15 19:19:57',29,32),(19,'I love it!','2020-01-15 20:36:36',32,32),(20,'That\'s one big bunny','2020-01-15 20:37:22',14,35),(21,'I love GOT!','2020-01-15 20:37:56',29,35),(22,'I\'m addicted','2020-01-15 20:40:24',32,33),(23,'So peaceful','2020-01-15 20:40:46',33,33),(24,'Don\'t like it','2020-01-15 20:41:12',14,33);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playlists`
--

DROP TABLE IF EXISTS `playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_title` varchar(200) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `playlist_index` (`playlist_title`),
  KEY `playlists_ibfk_1` (`owner_id`),
  CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playlists`
--

LOCK TABLES `playlists` WRITE;
/*!40000 ALTER TABLE `playlists` DISABLE KEYS */;
INSERT INTO `playlists` VALUES (10,'Watch Later',33,'2020-01-15'),(11,'Gaming',33,'2020-01-15'),(14,'Watch Later',35,'2020-01-15'),(15,'Watch later',32,'2020-01-15'),(16,'TV series',32,'2020-01-15');
/*!40000 ALTER TABLE `playlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `registration_date` date NOT NULL,
  `avatar_url` varchar(300) NOT NULL DEFAULT 'https://www.pngkey.com/png/full/52-523516_empty-profile-picture-circle.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `username_index` (`username`),
  KEY `email_index` (`email`),
  KEY `name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (32,'Vasilen Sotirov','sireneto35@abv.bg','$2y$10$U0jzAjoMX.J2qTVvDfsSwuqc1hcutQQ2mQ9BQLHCus135XCB7XEka','Vasilen Sotirov','2020-01-07','uploads\\Vasilen Sotirov-1579092097.jpg'),(33,'chokoBiceps','chavdar.sotirov@abv.bg','$2y$10$I3NWPOFl0CQCWJxpVnajoOtSX/SVvpzuAJK7ya6XVyec9mnURn5WK','Chavdar Sotirov','2020-01-15','uploads\\chokoBiceps-1579081392.jpg'),(35,'yuliansotirov','jema71@abv.bg','$2y$10$TodC/xL6a1PwzZnhCWV.cO/Z5jDe2748YKjw31d./Zr7l4WyrBfGS','Yulian Sotirov','2020-01-15','uploads\\yuliansotirov-1579122504.jpg');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_follow_users`
--

DROP TABLE IF EXISTS `users_follow_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_follow_users` (
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  PRIMARY KEY (`follower_id`,`followed_id`),
  KEY `users_follow_users_ibfk_2` (`followed_id`),
  CONSTRAINT `users_follow_users_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_follow_users_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_follow_users`
--

LOCK TABLES `users_follow_users` WRITE;
/*!40000 ALTER TABLE `users_follow_users` DISABLE KEYS */;
INSERT INTO `users_follow_users` VALUES (32,33),(32,35),(33,32),(33,35),(35,32),(35,33);
/*!40000 ALTER TABLE `users_follow_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_react_comments`
--

DROP TABLE IF EXISTS `users_react_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_react_comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`comment_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_react_comments_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_react_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_react_comments`
--

LOCK TABLES `users_react_comments` WRITE;
/*!40000 ALTER TABLE `users_react_comments` DISABLE KEYS */;
INSERT INTO `users_react_comments` VALUES (14,33,0),(14,35,1),(15,35,1),(16,33,1),(18,35,1),(19,33,1);
/*!40000 ALTER TABLE `users_react_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_react_videos`
--

DROP TABLE IF EXISTS `users_react_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_react_videos` (
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`video_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_react_videos_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_react_videos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_react_videos`
--

LOCK TABLES `users_react_videos` WRITE;
/*!40000 ALTER TABLE `users_react_videos` DISABLE KEYS */;
INSERT INTO `users_react_videos` VALUES (14,32,1),(14,33,0),(14,35,1),(29,32,1),(29,35,1),(30,33,1),(32,32,1),(32,33,1),(33,33,1);
/*!40000 ALTER TABLE `users_react_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_watch_videos`
--

DROP TABLE IF EXISTS `users_watch_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_watch_videos` (
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`video_id`,`user_id`),
  KEY `users_watch_videos_ibfk_2` (`user_id`),
  CONSTRAINT `users_watch_videos_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_watch_videos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_watch_videos`
--

LOCK TABLES `users_watch_videos` WRITE;
/*!40000 ALTER TABLE `users_watch_videos` DISABLE KEYS */;
INSERT INTO `users_watch_videos` VALUES (14,32,'2020-01-15 09:19:12'),(14,33,'2020-01-15 09:42:09'),(14,35,'2020-01-15 20:17:47'),(29,32,'2020-01-15 10:21:20'),(29,33,'2020-01-15 10:18:25'),(29,35,'2020-01-15 20:37:48'),(30,32,'2020-01-15 10:22:29'),(30,33,'2020-01-15 10:23:29'),(32,32,'2020-01-15 20:33:10'),(32,33,'2020-01-15 20:40:03'),(32,35,'2020-01-15 20:28:05'),(33,32,'2020-01-15 20:32:50'),(33,33,'2020-01-15 20:40:34');
/*!40000 ALTER TABLE `users_watch_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_uploaded` datetime NOT NULL,
  `owner_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `video_url` varchar(300) NOT NULL,
  `duration` time NOT NULL DEFAULT '00:00:00',
  `thumbnail_url` varchar(300) NOT NULL DEFAULT 'https://therisingnetwork.com/wp-content/plugins/video-thumbnails/default.jpg',
  `views` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `video_url` (`video_url`),
  KEY `owner_id` (`owner_id`),
  KEY `category_id` (`category_id`),
  KEY `title_index` (`title`),
  CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `videos_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
INSERT INTO `videos` VALUES (14,'Big buck bunny','Golqm zaek','2020-01-15 11:18:41',32,1,'uploads\\sireneto35-1579083521.mp4','00:00:00','uploads\\sireneto35-1579083700.jpg',38),(29,'GOT main theme','got','2020-01-15 12:18:18',33,1,'uploads\\chokoBiceps-1579087098.mp4','00:00:00','uploads\\chokoBiceps-1579087098.jpg',20),(30,'AWP god','good awp player','2020-01-15 12:22:20',32,7,'uploads\\sireneto35-1579087340.mp4','00:00:00','uploads\\sireneto35-1579087340.jpeg',11),(32,'The Witcher trailer','The witcher is a new series made by the books.','2020-01-15 22:28:00',35,1,'uploads\\yuliansotirov-1579123680.mp4','00:00:00','uploads\\yuliansotirov-1579123680.jpg',7),(33,'Nature','Nature beautiful short video','2020-01-15 22:32:46',32,6,'uploads\\Vasilen Sotirov-1579123966.mp4','00:00:00','uploads\\Vasilen Sotirov-1579123966.png',10);
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-04 16:44:04
