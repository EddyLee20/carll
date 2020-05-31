/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : carll

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 29/05/2020 18:20:56
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sys_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `sys_admin_user`;
CREATE TABLE `sys_admin_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '账户名',
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `salt` char(8) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '盐值',
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户名称',
  `user_phone` char(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系人邮箱',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系地址',
  `add_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '分组id',
  `last_login_time` int(11) NULL DEFAULT NULL COMMENT '上次登录时间',
  `add_user_id` int(11) NULL DEFAULT NULL COMMENT '新增用户的id',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `del_flag` tinyint(1) NULL DEFAULT NULL COMMENT '是否删除 0未删除，1已删除',
  `del_user` int(11) NULL DEFAULT NULL COMMENT '删除人',
  `del_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '是否禁用 0禁用，1启用',
  `remark` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `disabled_reason` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '禁用理由',
  `disabled_time` int(11) NULL DEFAULT 0 COMMENT '禁用时间',
  `expired_time` int(11) NULL DEFAULT 0 COMMENT '失效时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sys_admin_user_ibfk_1`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台管理用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of sys_admin_user
-- ----------------------------
INSERT INTO `sys_admin_user` VALUES (1, 'admin', 'f717256cf5ff27f8b499a5ad7add0486', 'njyqCaTi', '公司账号', '13265555555', 'admin@oncloudnet.com', '', 1586838525, 1, 1590548905, 1, 1590548905, 0, NULL, NULL, 1, '超级管理员', '', 0, 1832947200);

-- ----------------------------
-- Table structure for sys_car_insure
-- ----------------------------
DROP TABLE IF EXISTS `sys_car_insure`;
CREATE TABLE `sys_car_insure`  (
  `insure_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(11) NULL DEFAULT NULL COMMENT 'sys_car_order表主键id',
  `quote_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '险种代码',
  `quote_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '险种名称',
  `amount` double NULL DEFAULT NULL COMMENT '投保金额',
  `other_amount` double NULL DEFAULT NULL COMMENT '其他保额',
  `show_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '保额显示名称',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '是否显示在前台',
  `standard_premium` decimal(10, 2) NULL DEFAULT NULL COMMENT '原始保费',
  `premium` decimal(10, 2) NULL DEFAULT NULL COMMENT '保费购买价',
  `premium_ratio` double NULL DEFAULT NULL COMMENT '折扣率',
  `insure_type` tinyint(1) NULL DEFAULT NULL COMMENT '险种标识1交强险，2商业险',
  PRIMARY KEY (`insure_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_car_order
-- ----------------------------
DROP TABLE IF EXISTS `sys_car_order`;
CREATE TABLE `sys_car_order`  (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) NULL DEFAULT NULL COMMENT 'sys_user表主键id',
  `target_id` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'sys_car_target表主键id',
  `plan_id` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '方案id',
  `organize_id` tinyint(1) NULL DEFAULT NULL COMMENT '网点ID',
  `organize` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '网点名称',
  `order_status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单状态:B0初始化,B1报价,B10报价失败,B11报价成功,B2核保,B21待上传资料,B22核保失败,B23上传资料中,B3待缴费,B31待承保,B5承保',
  `order_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'T10单商业,T01单交强,T11混保',
  `sys_order_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '系统支付订单号',
  `ins_order_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '保险公司支付订单号',
  `pay_type` tinyint(1) NULL DEFAULT NULL COMMENT '支付类型1微信，2支付宝，3银联',
  `pay_status` tinyint(1) NULL DEFAULT NULL COMMENT '1发起支付，2支付中，3支付成功，4支付失败',
  `pay_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '应支付总金额',
  `pay_time` int(11) NULL DEFAULT NULL COMMENT '支付时间',
  `insure_time` int(11) NULL DEFAULT NULL COMMENT '投保时间',
  `verify_time` int(11) NULL DEFAULT NULL COMMENT '核保确认时间',
  `policy_time` int(11) NULL DEFAULT NULL COMMENT '保单确认时间',
  `quote_time` int(11) NULL DEFAULT NULL COMMENT '报价时间',
  `insure_company_id` int(11) NULL DEFAULT NULL COMMENT '投保公司id',
  `insure_company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '投保公司信息',
  `last_car_order` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '上年投保信息',
  `last_car_order_id` int(11) NULL DEFAULT NULL COMMENT '上年投保ID',
  `del_flag` tinyint(1) NULL DEFAULT NULL COMMENT '删除状态0未删除，1已删除',
  `order_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单备注',
  PRIMARY KEY (`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_car_person
-- ----------------------------
DROP TABLE IF EXISTS `sys_car_person`;
CREATE TABLE `sys_car_person`  (
  `person_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(11) NULL DEFAULT NULL COMMENT 'sys_car_order表主键id',
  `person_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `id_type` tinyint(1) NULL DEFAULT NULL COMMENT '证件类型1身份证,2护照,3港澳身份证,31组织机构代码,32税务登记证,33营业执照,99其它',
  `id_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '证件号',
  `gender` enum('M','F','E') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '性别男M,女F,其他E',
  `province` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '省',
  `city` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '市',
  `area` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '区',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '地址',
  `birthday` date NULL DEFAULT NULL COMMENT '出生日期',
  `telphone` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收件人手机号',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '联系人邮箱',
  `post_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮编',
  `people_type` tinyint(1) NULL DEFAULT NULL COMMENT '组织类型1个人,2团体',
  `person_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '参保类型1:投保人信息,2:被保人,3:车主信息',
  PRIMARY KEY (`person_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_car_target
-- ----------------------------
DROP TABLE IF EXISTS `sys_car_target`;
CREATE TABLE `sys_car_target`  (
  `target_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `new_car_flag` tinyint(1) NULL DEFAULT NULL COMMENT '未上牌标记',
  `car_licence` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车牌号',
  `vin_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车架号',
  `engine_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发动机号',
  `auto_model_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车型名称',
  `other_model_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手动录入车型别名',
  `auto_model_code` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车型代码',
  `first_register_date` date NULL DEFAULT NULL COMMENT '初登日期',
  `exhaust_capability` double NULL DEFAULT NULL COMMENT '排气量(L)',
  `searts` smallint(1) NULL DEFAULT NULL COMMENT '座位数',
  `edit_searts` smallint(1) NULL DEFAULT NULL COMMENT '修改的座位数',
  `purchase_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '新车购置价',
  `tax_purchase_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '新车含税购置价',
  `whole_weight` double NULL DEFAULT NULL COMMENT '车身自重/整备质量(吨)',
  `vehicle_tonnages` double NULL DEFAULT NULL COMMENT '载重量/核定载质量(吨)',
  `vehicle_loss_insured_value` double NULL DEFAULT NULL COMMENT '车辆损失险实际价',
  `negotiated_value` double NULL DEFAULT NULL COMMENT '车辆损失险协商价',
  `fuel_type` tinyint(1) NULL DEFAULT NULL COMMENT '能源类型',
  `license_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '号牌底色',
  `transfer_date` date NULL DEFAULT NULL COMMENT '过户转移登记日期',
  `loan_vehicle_flag` tinyint(1) NULL DEFAULT NULL COMMENT '是否贷款车1贷款，2全款',
  `ecdemic_vehicle_flag` tinyint(1) NULL DEFAULT NULL COMMENT '外地车类型1外地车,2港澳车',
  `run_miles` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '平均行驶里程',
  `use_type` tinyint(1) NULL DEFAULT NULL COMMENT '使用性质',
  `ownership_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '所属性质',
  `ownership_attribute_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '使用性质细分',
  `vehicle_class_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车辆种类',
  `vehicle_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车辆类型',
  `owner_vehicle_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '交管/行驶证车辆类型',
  `licence_type_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '号牌种类',
  `first_sale_date` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '年款',
  PRIMARY KEY (`target_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_insure_policy
-- ----------------------------
DROP TABLE IF EXISTS `sys_insure_policy`;
CREATE TABLE `sys_insure_policy`  (
  `policy_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `insure_id` int(11) NULL DEFAULT NULL COMMENT 'sys_car_insurant表主键id',
  `start_date` date NULL DEFAULT NULL COMMENT '起保日期',
  `end_date` date NULL DEFAULT NULL COMMENT '终保日期',
  `quote_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '报价单号',
  `apply_policy_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '投保单号',
  `policy_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '承保单号',
  `duty_status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '保单状态',
  `total_tax_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '车船税总金额',
  `tax_fines` decimal(10, 2) NULL DEFAULT NULL COMMENT '车船税滞纳金',
  `tax_year_amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '车船税金额',
  `pay_tax_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '完税凭证号',
  `tax_org` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '开具税务机关',
  `tax_type` tinyint(1) NULL DEFAULT NULL COMMENT '纳税类型1.缴税 2.不缴税 3.减税 4.已完税 5.免税',
  PRIMARY KEY (`policy_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_product
-- ----------------------------
DROP TABLE IF EXISTS `sys_product`;
CREATE TABLE `sys_product`  (
  `product_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `product_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '产品名称',
  `product_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '保险代码',
  `product_type` tinyint(1) NULL DEFAULT NULL COMMENT '产品类型',
  `product_price` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '产品价格区间',
  `company_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '所属公司',
  `company_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '所属公司编号',
  `instructions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '产品说明',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '详情',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '访问地址',
  `sort` smallint(1) NULL DEFAULT NULL COMMENT '排序',
  `del_flag` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除0未删除，1已删除',
  PRIMARY KEY (`product_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_setting
-- ----------------------------
DROP TABLE IF EXISTS `sys_setting`;
CREATE TABLE `sys_setting`  (
  `key` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设置项标示',
  `describe` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设置内容（json格式）',
  `sign_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标识id',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0禁用，1启用',
  UNIQUE INDEX `unique_key`(`key`, `sign_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_setting
-- ----------------------------
INSERT INTO `sys_setting` VALUES ('sms', '短信通知', '{\"default\":\"tencentyun\",\"engine\":{\"tencentyun\":{\"appid\":\"1400263005\",\"appkey\":\"1877ea708f4e6764cf1dfd899078e0cf\",\"sign\":\"车了了\"}}}', 0, 1588834403, 1);

-- ----------------------------
-- Table structure for sys_sms_record
-- ----------------------------
DROP TABLE IF EXISTS `sys_sms_record`;
CREATE TABLE `sys_sms_record`  (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '短信记录id',
  `telphone` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `sms_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '短信类型',
  `sms_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '短信验证码',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '状态：0失败，1成功',
  `add_time` int(11) NULL DEFAULT NULL COMMENT '发送时间',
  PRIMARY KEY (`sms_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_sms_record
-- ----------------------------
INSERT INTO `sys_sms_record` VALUES (1, '18673104270', 'login', '236029', 1, 1590723841);
INSERT INTO `sys_sms_record` VALUES (2, '18673104270', 'login', '978281', 1, 1590724039);

-- ----------------------------
-- Table structure for sys_user
-- ----------------------------
DROP TABLE IF EXISTS `sys_user`;
CREATE TABLE `sys_user`  (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名称',
  `car_licenc` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '车牌号',
  `telphone` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '联系方式',
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户头像',
  `id_type` tinyint(1) NULL DEFAULT NULL COMMENT '证件类型1身份证,2护照,3港澳身份证',
  `id_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '证件号',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '是否禁用0禁用，1启用',
  `remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `disabled_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '禁用理由',
  `disabled_time` int(11) NULL DEFAULT 0 COMMENT '禁用时间',
  `admin_user` int(11) NULL DEFAULT NULL COMMENT '操作人id',
  `target_id` int(11) NULL DEFAULT NULL COMMENT '车辆信息id',
  `device_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '设备验证值',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `add_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `device_limit_time` int(11) NULL DEFAULT 0 COMMENT '登录失效时间',
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_user
-- ----------------------------
INSERT INTO `sys_user` VALUES (1, 'eddy', '粤B123456', '18673104270', NULL, 1, '411302200105101135', 1, NULL, NULL, 0, NULL, NULL, 'f75ecd3b79e3ffe8abc619d7d3d0de89ee0a6210', 1590724365, 1590724055, 1590724665);

-- ----------------------------
-- Table structure for sys_user_group
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_group`;
CREATE TABLE `sys_user_group`  (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组名称',
  `remark` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组描述',
  `view_ids` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '分组对应的视图id集',
  `view_module_ids` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '视图对应操作模块ids集',
  `del_flag` tinyint(1) NULL DEFAULT NULL COMMENT '是否删除1为是 0为否',
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_user_group
-- ----------------------------
INSERT INTO `sys_user_group` VALUES (1, '超级管理员', '超级管理员', '1,2,3', NULL, NULL);

-- ----------------------------
-- Table structure for sys_user_group_view
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_group_view`;
CREATE TABLE `sys_user_group_view`  (
  `view_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '上级id',
  `view_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '视图名称',
  `remark` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '视图标识',
  `_asc` int(5) NULL DEFAULT 999 COMMENT '排序，默认999排最后',
  `api` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '视图对应的api接口，有多个用逗号隔开',
  PRIMARY KEY (`view_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分组视图表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_user_group_view
-- ----------------------------
INSERT INTO `sys_user_group_view` VALUES (1, 0, '首页', 'info', 1, 'info,');
INSERT INTO `sys_user_group_view` VALUES (2, 0, '管理员管理', 'user', 2, 'user_list,');
INSERT INTO `sys_user_group_view` VALUES (3, 0, '车辆信息管理', 'car', 3, 'car_list,');

-- ----------------------------
-- Table structure for sys_user_illegal
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_illegal`;
CREATE TABLE `sys_user_illegal`  (
  `illegal_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `organize` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '违章开取机构',
  `area` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '违章地区',
  `illegal_amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '违章金额',
  `illegal_point` tinyint(1) NULL DEFAULT NULL COMMENT '扣分',
  `illegal_time` datetime(0) NULL DEFAULT NULL COMMENT '违章时间',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '是否处理0未处理，1已处理',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`illegal_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_user_view_module
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_view_module`;
CREATE TABLE `sys_user_view_module`  (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `view_id` int(11) NOT NULL DEFAULT 0 COMMENT '对应视图id',
  `module_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块名称',
  `remark` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块标识',
  `_asc` int(5) NULL DEFAULT 999 COMMENT '排序，默认999排最后',
  `api` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '方法对应的api接口，有多个用逗号隔开',
  PRIMARY KEY (`module_id`) USING BTREE,
  INDEX `sys_user_view_module_ibfk_1`(`view_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '视图对应管理模块表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
