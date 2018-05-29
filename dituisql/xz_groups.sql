/*
Navicat MySQL Data Transfer

Source Server         : inner.efunong.com
Source Server Version : 50713
Source Host           : inner.efunong.com:10006
Source Database       : fxk

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2018-05-25 19:48:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xz_groups`
-- ----------------------------
DROP TABLE IF EXISTS `xz_groups`;
CREATE TABLE `xz_groups` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT COMMENT '小组',
  `name` varchar(40) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xz_groups
-- ----------------------------
