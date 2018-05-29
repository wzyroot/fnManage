/*
Navicat MySQL Data Transfer

Source Server         : inner.efunong.com
Source Server Version : 50713
Source Host           : inner.efunong.com:10006
Source Database       : fxk

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2018-05-25 19:49:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xz_pusher`
-- ----------------------------
DROP TABLE IF EXISTS `xz_pusher`;
CREATE TABLE `xz_pusher` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '地推人员表',
  `name` varchar(40) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `groupid` int(10) DEFAULT NULL,
  `createtime` date DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `isuser` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xz_pusher
-- ----------------------------
