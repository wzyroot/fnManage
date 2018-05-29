/*
Navicat MySQL Data Transfer

Source Server         : inner.efunong.com
Source Server Version : 50713
Source Host           : inner.efunong.com:10006
Source Database       : fxk

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2018-05-25 19:48:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xz_landuser`
-- ----------------------------
DROP TABLE IF EXISTS `xz_landuser`;
CREATE TABLE `xz_landuser` (
  `userid` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户表',
  `username` varchar(40) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `typeid` int(11) DEFAULT NULL COMMENT '类别',
  `manageid` varchar(20) DEFAULT NULL COMMENT '经营范围',
  `development` varchar(20) DEFAULT NULL COMMENT '发展程度',
  `province` varchar(20) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `area` varchar(20) DEFAULT NULL,
  `address` text,
  `createtime` date DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `belongid` int(10) DEFAULT NULL COMMENT '所属地推者',
  `viewuser` varchar(20) DEFAULT NULL COMMENT '可见小组成员',
  `groupid` int(11) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xz_landuser
-- ----------------------------
