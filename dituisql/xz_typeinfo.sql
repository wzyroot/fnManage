/*
Navicat MySQL Data Transfer

Source Server         : inner.efunong.com
Source Server Version : 50713
Source Host           : inner.efunong.com:10006
Source Database       : fxk

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2018-05-25 19:41:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xz_typeinfo`
-- ----------------------------
DROP TABLE IF EXISTS `xz_typeinfo`;
CREATE TABLE `xz_typeinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xz_typeinfo
-- ----------------------------
INSERT INTO `xz_typeinfo` VALUES ('1', '小B');
INSERT INTO `xz_typeinfo` VALUES ('2', '大B');
INSERT INTO `xz_typeinfo` VALUES ('3', '养殖户');
INSERT INTO `xz_typeinfo` VALUES ('4', '饲料厂');
