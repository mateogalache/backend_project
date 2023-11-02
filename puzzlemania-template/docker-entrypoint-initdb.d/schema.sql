SET NAMES utf8;
SET
time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `puzzlemania`;
USE `puzzlemania`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `email`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `password`  VARCHAR(255)                                            NOT NULL,
    `createdAt` DATETIME                                                NOT NULL,
    `updatedAt` DATETIME                                                NOT NULL,
    `picture`   VARCHAR(255),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `riddles`
(
    `riddle_id`   INT          NOT NULL AUTO_INCREMENT,
    `user_id`    INT,
    `riddle`      VARCHAR(255) NOT NULL,
    `answer`    VARCHAR(255) NOT NULL,
    PRIMARY KEY (`riddle_id`),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `team`
(
    `team_id`       INT             NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(255)    NOT NULL,
    `member1`       INT,
    `member2`       INT,
    `total_points`  INT,
    PRIMARY KEY (`team_id`),
    FOREIGN KEY (member1) REFERENCES users (id),
    FOREIGN KEY (member2) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `game`
(
    `game_id`       INT         NOT NULL AUTO_INCREMENT,
    `riddle_id1`     INT         NOT NULL,
    `riddle_id2`     INT         NOT NULL,
    `riddle_id3`     INT         NOT NULL,
    `riddleGame_id` INT         NOT NULL,
    `team_id`       INT         NOT NULL,
    `current_points`    INT,
    PRIMARY KEY (`game_id`),
    FOREIGN KEY (riddle_id1) REFERENCES riddles (riddle_id),
    FOREIGN KEY (riddle_id2) REFERENCES riddles (riddle_id),
    FOREIGN KEY (riddle_id3) REFERENCES riddles (riddle_id),
    FOREIGN KEY (team_id) REFERENCES team (team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `riddles` (`riddle_id`, `user_id`, `riddle`, `answer`) VALUES
   (1,	NULL,	'It brings back the lost as though never gone, shines laughter and tears with light long since shone; a moment to make, a lifetime to shed; valued then but lost when your dead. What Is It?',	'Memory'),
   (2,	NULL,	'What do you get when you cross a fish with an elephant?',	'Swimming trunks'),
   (3,	NULL,	'I can be long, or I can be short.\nI can be grown, and I can be bought.\nI can be painted, or left bare.\nI can be round, or I can be square.\nWhat am I?',	'Fingernails'),
   (4,	NULL,	'I am lighter than a feather yet no man can hold me for long.',	'Breath'),
   (5,	NULL,	'What occurs once in every minute, twice in every moment and yet never in a thousand years?',	'The letter M'),
   (6,	NULL,	'What nationality is Santa Claus?',	'North Polish'),
   (7,	NULL,	'What animal is best at hitting a baseball?',	'A bat'),
   (8,	NULL,	'What do you call a cow that twitches?',	'Beef jerky');
