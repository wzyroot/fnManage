/*
Navicat MySQL Data Transfer

Source Server         : inner.efunong.com
Source Server Version : 50713
Source Host           : inner.efunong.com:10006
Source Database       : fxk

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2018-05-25 19:42:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xz_manageinfo`
-- ----------------------------
DROP TABLE IF EXISTS `xz_manageinfo`;
CREATE TABLE `xz_manageinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xz_manageinfo
-- ----------------------------
INSERT INTO `xz_manageinfo` VALUES ('1', '豆粕');
INSERT INTO `xz_manageinfo` VALUES ('2', '玉米');
INSERT INTO `xz_manageinfo` VALUES ('3', '麸皮');
INSERT INTO `xz_manageinfo` VALUES ('4', '预混料');
INSERT INTO `xz_manageinfo` VALUES ('5', '全价料');
INSERT INTO `xz_manageinfo` VALUES ('6', '兽药');
