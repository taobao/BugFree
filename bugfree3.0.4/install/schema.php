<?php
return "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- -----------------------------------------------------
-- Table `##PREFIX##test_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##test_user` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##test_user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) BINARY NOT NULL ,
  `password` VARCHAR(45) NOT NULL ,
  `realname` VARCHAR(45) NOT NULL ,
  `email` VARCHAR(45) NOT NULL ,
  `wangwang` VARCHAR(45) NULL ,
  `email_flag` ENUM('0','1') NOT NULL ,
  `wangwang_flag` ENUM('0','1') NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `is_dropped` ENUM('0','1') NOT NULL ,
  `authmode` ENUM('ldap','internal') NOT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  `full_pinyin` VARCHAR(45) NULL ,
  `first_pinyin` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `##PREFIX##_name_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `##PREFIX##_realname_UNIQUE` (`realname` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##user_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##user_group` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##user_group` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `is_dropped` ENUM('0','1') NOT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##product`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##product` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##product` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `is_dropped` ENUM('0','1') NOT NULL ,
  `solution_value` TEXT NULL ,
  `display_order` SMALLINT NOT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  `bug_step_template` TEXT NULL ,
  `case_step_template` TEXT NULL ,
  `bug_severity` VARCHAR(255) NOT NULL ,
  `bug_priority` VARCHAR(255) NOT NULL ,
  `case_priority` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `##PREFIX##_name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##user_query`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##user_query` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##user_query` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `query_type` ENUM('bug','case','result') NOT NULL ,
  `query_string` TEXT NULL ,
  `andorlist` TEXT NULL ,
  `fieldlist` TEXT NULL ,
  `operatorlist` TEXT NULL ,
  `left_parentheses` TEXT NULL ,
  `right_parentheses` TEXT NULL ,
  `product_id` INT NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `valuelist` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_USERQUERY_PRODUCT1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_USERQUERY_PRODUCT1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##product_module`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##product_module` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##product_module` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `grade` SMALLINT NOT NULL ,
  `owner` INT NULL ,
  `display_order` SMALLINT NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `full_path_name` TEXT NOT NULL ,
  `product_id` INT NOT NULL ,
  `parent_id` INT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_PRODUCTMODULE_PRODUCT1` (`product_id` ASC) ,
  INDEX `##PREFIX##_fk_PRODUCTMODULE_PRODUCTMODULE1` (`parent_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_PRODUCTMODULE_PRODUCT1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_PRODUCTMODULE_PRODUCTMODULE1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `##PREFIX##product_module` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##user_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##user_log` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##user_log` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `ip` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##field_config`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##field_config` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##field_config` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `field_name` VARCHAR(45) NOT NULL ,
  `field_type` VARCHAR(45) NOT NULL ,
  `field_value` TEXT NULL ,
  `default_value` TEXT NULL ,
  `is_dropped` ENUM('0','1') NOT NULL ,
  `field_label` VARCHAR(45) NOT NULL ,
  `type` ENUM('bug','case','result') NOT NULL ,
  `belong_group` VARCHAR(45) NOT NULL ,
  `display_order` SMALLINT NOT NULL ,
  `editable_action` VARCHAR(255) NULL ,
  `validate_rule` VARCHAR(45) NOT NULL ,
  `match_expression` VARCHAR(255) NULL ,
  `product_id` INT NOT NULL ,
  `edit_in_result` ENUM('0','1') NULL DEFAULT '0' ,
  `result_group` VARCHAR(45) NULL ,
  `lock_version` SMALLINT NOT NULL ,
  `is_required` ENUM('0', '1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_FIELDCONFIG_PRODUCT1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_FIELDCONFIG_PRODUCT1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##test_file`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##test_file` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##test_file` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `file_title` VARCHAR(255) NOT NULL ,
  `file_location` TEXT NOT NULL ,
  `file_type` VARCHAR(45) NULL ,
  `file_size` VARCHAR(45) NOT NULL ,
  `is_dropped` ENUM('0','1') NOT NULL ,
  `target_id` INT NOT NULL ,
  `target_type` ENUM('bug','case','result') NOT NULL ,
  `add_action_id` INT NOT NULL ,
  `delete_action_id` INT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##bug_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##bug_info` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##bug_info` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `bug_status` VARCHAR(45) NOT NULL ,
  `assign_to` INT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `mail_to` TEXT NULL ,
  `repeat_step` TEXT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  `resolved_at` DATETIME NULL ,
  `resolved_by` INT NULL ,
  `closed_at` DATETIME NULL ,
  `closed_by` INT NULL ,
  `related_bug` VARCHAR(255) NULL ,
  `related_case` VARCHAR(255) NULL ,
  `related_result` VARCHAR(255) NULL ,
  `productmodule_id` INT NULL ,
  `modified_by` TEXT NOT NULL ,
  `solution` VARCHAR(45) NULL ,
  `duplicate_id` VARCHAR(255) NULL ,
  `product_id` INT NOT NULL ,
  `reopen_count` INT NOT NULL ,
  `priority` TINYINT(4) NULL,
  `severity` TINYINT(4) NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_bug_status` (`bug_status` ASC) ,
  INDEX `##PREFIX##_assign_to` (`assign_to` ASC) ,
  INDEX `##PREFIX##_title` (`title` ASC) ,
  INDEX `##PREFIX##_resolved_by` (`resolved_by` ASC) ,
  INDEX `##PREFIX##_closed_by` (`closed_by` ASC) ,
  INDEX `##PREFIX##_updated_by` (`updated_by` ASC) ,
  INDEX `##PREFIX##_created_by` (`created_by` ASC) ,
  INDEX `##PREFIX##_idx_productid_id` (`product_id`,`id`) ,
  INDEX `##PREFIX##_fk_bug_info_product1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_bug_info_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##case_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##case_info` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##case_info` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `case_status` VARCHAR(45) NOT NULL ,
  `assign_to` INT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `mail_to` TEXT NULL ,
  `case_step` TEXT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  `related_bug` VARCHAR(255) NULL ,
  `related_case` VARCHAR(255) NULL ,
  `related_result` VARCHAR(255) NULL ,
  `productmodule_id` INT NULL ,
  `modified_by` TEXT NOT NULL ,
  `delete_flag` ENUM('0','1') NOT NULL ,
  `product_id` INT NULL ,
  `priority` TINYINT(4) NULL,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_created_by` (`created_by` ASC) ,
  INDEX `##PREFIX##_updated_by` (`updated_by` ASC) ,
  INDEX `##PREFIX##_assign_to` (`assign_to` ASC) ,
  INDEX `##PREFIX##_title` (`title` ASC) ,
  INDEX `##PREFIX##_idx_productid_id` (`product_id`,`id`) ,
  INDEX `##PREFIX##_fk_case_info_product1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_case_info_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##result_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##result_info` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##result_info` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `result_status` VARCHAR(45) NOT NULL ,
  `assign_to` INT NULL ,
  `result_value` VARCHAR(45) NOT NULL ,
  `mail_to` TEXT NULL ,
  `result_step` TEXT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  `related_bug` VARCHAR(255) NULL ,
  `productmodule_id` INT NULL ,
  `modified_by` TEXT NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `related_case_id` INT NOT NULL ,
  `product_id` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_created_by` (`created_by` ASC) ,
  INDEX `##PREFIX##_updated_by` (`updated_by` ASC) ,
  INDEX `##PREFIX##_idx_productid_id` (`product_id`,`id`) ,
  INDEX `##PREFIX##_fk_result_info_case_info1` (`related_case_id` ASC) ,
  INDEX `##PREFIX##_fk_result_info_product1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_result_info_case_info1`
    FOREIGN KEY (`related_case_id` )
    REFERENCES `##PREFIX##case_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_result_info_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##bug_action`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##bug_action` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##bug_action` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `action_type` VARCHAR(255) NOT NULL ,
  `action_note` TEXT NULL ,
  `buginfo_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_BUGACTION_BUGINFO1` (`buginfo_id` ASC) ,
  INDEX `##PREFIX##_action_type` (`action_type` ASC) ,
  CONSTRAINT `##PREFIX##_fk_BUGACTION_BUGINFO1`
    FOREIGN KEY (`buginfo_id` )
    REFERENCES `##PREFIX##bug_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##bug_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##bug_history` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##bug_history` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `action_field` VARCHAR(45) NOT NULL ,
  `old_value` TEXT NULL ,
  `new_value` TEXT NULL ,
  `bugaction_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_BUGHISTORY_BUGACTION1` (`bugaction_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_BUGHISTORY_BUGACTION1`
    FOREIGN KEY (`bugaction_id` )
    REFERENCES `##PREFIX##bug_action` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##case_action`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##case_action` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##case_action` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `action_type` VARCHAR(255) NOT NULL ,
  `action_note` TEXT NULL ,
  `caseinfo_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_CASEACTION_CASEINFO1` (`caseinfo_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_CASEACTION_CASEINFO1`
    FOREIGN KEY (`caseinfo_id` )
    REFERENCES `##PREFIX##case_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##result_action`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##result_action` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##result_action` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `action_type` VARCHAR(255) NOT NULL ,
  `action_note` TEXT NULL ,
  `resultinfo_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_RESULTACTION_RESULTINFO1` (`resultinfo_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_RESULTACTION_RESULTINFO1`
    FOREIGN KEY (`resultinfo_id` )
    REFERENCES `##PREFIX##result_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##case_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##case_history` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##case_history` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `action_field` VARCHAR(45) NOT NULL ,
  `old_value` TEXT NULL ,
  `new_value` TEXT NULL ,
  `caseaction_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_CASEHISTORY_CASEACTION` (`caseaction_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_CASEHISTORY_CASEACTION`
    FOREIGN KEY (`caseaction_id` )
    REFERENCES `##PREFIX##case_action` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##result_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##result_history` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##result_history` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `action_field` VARCHAR(45) NOT NULL ,
  `old_value` TEXT NULL ,
  `new_value` TEXT NULL ,
  `resultaction_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_RESULTHISTORY_RESULTACTION1` (`resultaction_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_RESULTHISTORY_RESULTACTION1`
    FOREIGN KEY (`resultaction_id` )
    REFERENCES `##PREFIX##result_action` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##admin_action`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##admin_action` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##admin_action` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `action_type` VARCHAR(255) NOT NULL ,
  `target_table` VARCHAR(45) NOT NULL ,
  `target_id` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##admin_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##admin_history` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##admin_history` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `action_field` VARCHAR(45) NOT NULL ,
  `old_value` TEXT NULL ,
  `new_value` TEXT NULL ,
  `adminaction_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_RESULTHISTORY_RESULTACTION1` (`adminaction_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_RESULTHISTORY_RESULTACTION10`
    FOREIGN KEY (`adminaction_id` )
    REFERENCES `##PREFIX##admin_action` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##user_template`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##user_template` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##user_template` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `type` ENUM('bug','case','result') NOT NULL ,
  `template_content` TEXT NULL ,
  `title` VARCHAR(45) NOT NULL ,
  `product_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_user_template_product1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_user_template_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##test_option`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##test_option` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##test_option` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `option_name` VARCHAR(45) NOT NULL ,
  `option_value` TEXT NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `created_by` INT NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  `updated_by` INT NOT NULL ,
  `lock_version` SMALLINT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##map_user_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##map_user_group` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##map_user_group` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `test_user_id` INT NOT NULL ,
  `user_group_id` INT NOT NULL ,
  `is_admin` ENUM('0','1') NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_map_user_group_test_user1` (`test_user_id` ASC) ,
  INDEX `##PREFIX##_fk_map_user_group_user_group1` (`user_group_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_map_user_group_test_user1`
    FOREIGN KEY (`test_user_id` )
    REFERENCES `##PREFIX##test_user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_map_user_group_user_group1`
    FOREIGN KEY (`user_group_id` )
    REFERENCES `##PREFIX##user_group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##map_product_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##map_product_user` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##map_product_user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `test_user_id` INT NOT NULL ,
  `product_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_map_product_user_test_user1` (`test_user_id` ASC) ,
  INDEX `##PREFIX##_fk_map_product_user_product1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_map_product_user_test_user1`
    FOREIGN KEY (`test_user_id` )
    REFERENCES `##PREFIX##test_user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_map_product_user_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##map_product_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##map_product_group` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##map_product_group` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_group_id` INT NOT NULL ,
  `product_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_map_product_group_user_group1` (`user_group_id` ASC) ,
  INDEX `##PREFIX##_fk_map_product_group_product1` (`product_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_map_product_group_user_group1`
    FOREIGN KEY (`user_group_id` )
    REFERENCES `##PREFIX##user_group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_map_product_group_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `##PREFIX##product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##map_user_case`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##map_user_case` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##map_user_case` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `test_user_id` INT NOT NULL ,
  `info_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_map_user_case_test_user1` (`test_user_id` ASC) ,
  INDEX `##PREFIX##_fk_map_user_case_case_info1` (`info_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_map_user_case_test_user1`
    FOREIGN KEY (`test_user_id` )
    REFERENCES `##PREFIX##test_user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_map_user_case_case_info1`
    FOREIGN KEY (`info_id` )
    REFERENCES `##PREFIX##case_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##map_user_bug`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##map_user_bug` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##map_user_bug` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `test_user_id` INT NOT NULL ,
  `info_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_map_user_case_test_user1` (`test_user_id` ASC) ,
  INDEX `##PREFIX##_fk_map_user_bug_bug_info1` (`info_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_map_user_case_test_user10`
    FOREIGN KEY (`test_user_id` )
    REFERENCES `##PREFIX##test_user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_map_user_bug_bug_info1`
    FOREIGN KEY (`info_id` )
    REFERENCES `##PREFIX##bug_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `##PREFIX##map_user_result`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `##PREFIX##map_user_result` ;

CREATE  TABLE IF NOT EXISTS `##PREFIX##map_user_result` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `test_user_id` INT NOT NULL ,
  `info_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `##PREFIX##_fk_map_user_case_test_user1` (`test_user_id` ASC) ,
  INDEX `##PREFIX##_fk_map_user_result_result_info1` (`info_id` ASC) ,
  CONSTRAINT `##PREFIX##_fk_map_user_case_test_user100`
    FOREIGN KEY (`test_user_id` )
    REFERENCES `##PREFIX##test_user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `##PREFIX##_fk_map_user_result_result_info1`
    FOREIGN KEY (`info_id` )
    REFERENCES `##PREFIX##result_info` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Placeholder table for view `##PREFIX##bugview`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `##PREFIX##bugview` (`id` INT, `created_at` INT, `created_by` INT, `updated_at` INT, `updated_by` INT, `bug_status` INT, `assign_to` INT, `title` INT, `mail_to` INT, `repeat_step` INT, `lock_version` INT, `resolved_at` INT, `resolved_by` INT, `closed_at` INT, `closed_by` INT, `related_bug` INT, `related_case` INT, `related_result` INT, `productmodule_id` INT, `modified_by` INT, `solution` INT, `duplicate_id` INT, `product_id` INT, `reopen_count` INT, `product_name` INT, `module_name` INT, `created_by_name` INT, `updated_by_name` INT, `resolved_by_name` INT, `closed_by_name` INT, `assign_to_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `##PREFIX##caseview`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `##PREFIX##caseview` (`id` INT, `created_at` INT, `created_by` INT, `updated_at` INT, `updated_by` INT, `case_status` INT, `assign_to` INT, `title` INT, `mail_to` INT, `case_step` INT, `lock_version` INT, `related_bug` INT, `related_case` INT, `related_result` INT, `productmodule_id` INT, `modified_by` INT, `delete_flag` INT, `product_id` INT, `product_name` INT, `module_name` INT, `created_by_name` INT, `updated_by_name` INT, `assign_to_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `##PREFIX##resultview`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `##PREFIX##resultview` (`id` INT, `created_at` INT, `created_by` INT, `updated_at` INT, `updated_by` INT, `result_status` INT, `assign_to` INT, `result_value` INT, `mail_to` INT, `result_step` INT, `lock_version` INT, `related_bug` INT, `productmodule_id` INT, `modified_by` INT, `title` INT, `module_path` INT, `related_case_id` INT, `product_id` INT, `product_name` INT, `module_name` INT, `created_by_name` INT, `updated_by_name` INT, `assign_to_name` INT);

-- -----------------------------------------------------
-- View `##PREFIX##bugview`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `##PREFIX##bugview` ;
DROP TABLE IF EXISTS `##PREFIX##bugview`;

CREATE  OR REPLACE VIEW `##PREFIX##bugview` AS SElECT 
`##PREFIX##bug_info`.*,
`##PREFIX##product`.`name` AS `product_name`,
CONCAT_WS('/',`##PREFIX##product`.`name`, `##PREFIX##product_module`.`full_path_name`) AS `module_name`,
`uc`.`realname` AS `created_by_name`,
`uu`.`realname` AS `updated_by_name`,
`ur`.`realname` AS `resolved_by_name`,
`uclo`.`realname` AS `closed_by_name`,
`ua`.`realname` AS `assign_to_name`
FROM
`##PREFIX##bug_info`
LEFT JOIN `##PREFIX##test_user` `uc` ON (`##PREFIX##bug_info`.`created_by` = `uc`.`id`)
LEFT JOIN `##PREFIX##test_user` `uu` ON (`##PREFIX##bug_info`.`updated_by` = `uu`.`id`)
LEFT JOIN `##PREFIX##test_user` `ur` ON (`##PREFIX##bug_info`.`resolved_by` = `ur`.`id`)
LEFT JOIN `##PREFIX##test_user` `uclo` ON (`##PREFIX##bug_info`.`closed_by` = `uclo`.`id`)
LEFT JOIN `##PREFIX##test_user` `ua` ON (`##PREFIX##bug_info`.`assign_to` = `ua`.`id`)
LEFT JOIN `##PREFIX##product` ON (`##PREFIX##bug_info`.`product_id` = `##PREFIX##product`.`id`)
LEFT JOIN `##PREFIX##product_module` ON (`##PREFIX##bug_info`.`productmodule_id` = `##PREFIX##product_module`.`id`)

;

-- -----------------------------------------------------
-- View `##PREFIX##caseview`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `##PREFIX##caseview` ;
DROP TABLE IF EXISTS `##PREFIX##caseview`;

CREATE  OR REPLACE VIEW `##PREFIX##caseview` AS SElECT 
`##PREFIX##case_info`.*,
`##PREFIX##product`.`name` AS `product_name`,
CONCAT_WS('/',`##PREFIX##product`.`name`, `##PREFIX##product_module`.`full_path_name`) AS `module_name`,
`uc`.`realname` AS `created_by_name`,
`uu`.`realname` AS `updated_by_name`,
`ua`.`realname` AS `assign_to_name`
FROM
`##PREFIX##case_info`
LEFT JOIN `##PREFIX##test_user` `uc` ON (`##PREFIX##case_info`.`created_by` = `uc`.`id`)
LEFT JOIN `##PREFIX##test_user` `uu` ON (`##PREFIX##case_info`.`updated_by` = `uu`.`id`)
LEFT JOIN `##PREFIX##test_user` `ua` ON (`##PREFIX##case_info`.`assign_to` = `ua`.`id`)
LEFT JOIN `##PREFIX##product` ON (`##PREFIX##case_info`.`product_id` = `##PREFIX##product`.`id`)
LEFT JOIN `##PREFIX##product_module` ON (`##PREFIX##case_info`.`productmodule_id` = `##PREFIX##product_module`.`id`)

;

-- -----------------------------------------------------
-- View `##PREFIX##resultview`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `##PREFIX##resultview` ;
DROP TABLE IF EXISTS `##PREFIX##resultview`;

CREATE  OR REPLACE VIEW `##PREFIX##resultview` AS SElECT 
`##PREFIX##result_info`.*,
`##PREFIX##product`.`name` AS `product_name`,
CONCAT_WS('/',`##PREFIX##product`.`name`, `##PREFIX##product_module`.`full_path_name`) AS `module_name`,
`uc`.`realname` AS `created_by_name`,
`uu`.`realname` AS `updated_by_name`,
`ua`.`realname` AS `assign_to_name`
FROM
`##PREFIX##result_info`
LEFT JOIN `##PREFIX##test_user` `uc` ON (`##PREFIX##result_info`.`created_by` = `uc`.`id`)
LEFT JOIN `##PREFIX##test_user` `uu` ON (`##PREFIX##result_info`.`updated_by` = `uu`.`id`)
LEFT JOIN `##PREFIX##test_user` `ua` ON (`##PREFIX##result_info`.`assign_to` = `ua`.`id`)
LEFT JOIN `##PREFIX##product` ON (`##PREFIX##result_info`.`product_id` = `##PREFIX##product`.`id`)
LEFT JOIN `##PREFIX##product_module` ON (`##PREFIX##result_info`.`productmodule_id` = `##PREFIX##product_module`.`id`)

;

CREATE TABLE IF NOT EXISTS `##PREFIX##ettonbug_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bug_id` int(11) NOT NULL,
  `BugType` varchar(255) default NULL,
  `HowFound` varchar(255) default NULL,
  `BugBrowser` varchar(255) default NULL,
  `BugOS` varchar(255) default NULL,
  `OpenedBuild` varchar(255) default NULL,
  `ResolvedBuild` varchar(255) default NULL,
  `BugSubStatus` varchar(255) default NULL,
  `BugMachine` varchar(255) default NULL,
  `BugKeyword` varchar(255) default NULL,
  INDEX `##PREFIX##_idx_bug_id` (`bug_id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `##PREFIX##ettoncase_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `CaseType` varchar(255) default NULL,
  `CaseMethod` varchar(255) default NULL,
  `CasePlan` varchar(255) default NULL,
  `ScriptStatus` varchar(255) default NULL,
  `ScriptedBy` varchar(255) default NULL,
  `ScriptedDate` date default NULL,
  `ScriptLocation` varchar(255) default NULL,
  `CaseKeyword` varchar(255) default NULL,
  `DisplayOrder` varchar(255) default NULL,
  INDEX `##PREFIX##_idx_case_id` (`case_id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `##PREFIX##ettonresult_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `result_id` int(11) NOT NULL,
  `OpenedBuild` varchar(255) default NULL,
  `BugOS` varchar(255) default NULL,
  `BugBrowser` varchar(255) default NULL,
  `BugMachine` varchar(255) default NULL,
  `ResultKeyword` varchar(255) default NULL,
  INDEX `##PREFIX##_idx_result_id` (`result_id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `##PREFIX##test_user` (`id`, `username`, `password`, `realname`, `email`, `wangwang`, `email_flag`, `wangwang_flag`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_dropped`, `authmode`, `lock_version`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '" . t('bugfree', 'Administrator') . "', '', '', '0', '0', NOW(), 0, NOW(), 0, '0', 'internal', 1);
INSERT INTO `##PREFIX##test_option` (`id`, `option_name`, `option_value`, `created_at`, `created_by`, `updated_at`, `updated_by`, `lock_version`) VALUES
(1, 'db_version', '20', NOW(), 1, NOW(), 1, 1),(2, 'SYSTEM_ADMIN', 'admin', NOW(), 1, NOW(), 1, 1),(3, 'DEFAULT_PAGESIZE', '20', NOW(), 1, NOW(), 1, 1),(4, 'MAX_FILE_SIZE', '2097152', NOW(), 1, NOW(), 1, 1),(5, 'QUERY_FIELD_NUMBER', '8', NOW(), 1, NOW(), 1, 1);
INSERT INTO `##PREFIX##product` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_dropped`, `solution_value`, `display_order`, `lock_version`, `bug_step_template`, `case_step_template`, `bug_severity`, `bug_priority`, `case_priority`) VALUES
(1, 'Sample Product', NOW(), 1, NOW(), 1, '0', 'By Design,Duplicate,External,Fixed,Not Repro,Postponed,Won\'t Fix', 1, 1, \"" . t('bugfree', 'Bug step template') . "\", \"" . t('bugfree', 'Case step template') . "\",'1,2,3,4','1,2,3,4','1,2,3,4');
INSERT INTO `##PREFIX##field_config` (`id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `field_name`, `field_type`, `field_value`, `default_value`, `is_dropped`, `field_label`, `type`, `belong_group`, `display_order`, `editable_action`, `validate_rule`, `match_expression`, `product_id`, `edit_in_result`, `result_group`, `lock_version`, `is_required`) VALUES
(1, NOW(), 1, NOW(), 1, 'BugType', 'single select', '" . t('bugfree', 'CodeError') . ',' . t('bugfree', 'Interface') . ',' . t('bugfree', 'DesignChange') . ',' . t('bugfree', 'NewFeature') . ',' . t('bugfree', 'SpecDefect') . ',' . t('bugfree', 'DesignDefect') . ',' . t('bugfree', 'Config') . ',' . t('bugfree', 'Install') . ',' . t('bugfree', 'Security') . ',' . t('bugfree', 'Performance') . ',' . t('bugfree', 'Standard') . ',' . t('bugfree', 'Automation') . ',' . t('bugfree', 'TrackThings') . ',' . t('bugfree', 'BadCase') . ',' . t('bugfree', 'Others') . "', '', '0', '" . t('bugfree', 'Bug type') . "', 'bug', 'bug_status', 8, 'opened,resolved,closed', 'no', '', 1, '0', '', 1, '1'),
(2, NOW(), 1, NOW(), 1, 'HowFound', 'single select', '" . t('bugfree', 'FuncTest') . ',' . t('bugfree', 'UnitTest') . ',' . t('bugfree', 'BVT') . ',' . t('bugfree', 'Integrate') . ',' . t('bugfree', 'System') . ',' . t('bugfree', 'SmokeTest') . ',' . t('bugfree', 'Acceptance') . ',' . t('bugfree', 'BugBash') . ',' . t('bugfree', 'AdHoc') . ',' . t('bugfree', 'Regression') . ',' . t('bugfree', 'SpecReview') . ',' . t('bugfree', 'DesignReview') . ',' . t('bugfree', 'CodeReview') . ',' . t('bugfree', 'PostRTW') . ',' . t('bugfree', 'Customer') . ',' . t('bugfree', 'Partner') . ',' . t('bugfree', 'Other') . "', '', '0', '" . t('bugfree', 'Bug how found') . "', 'bug', 'bug_status', 7, 'opened,resolved,closed', 'no', '', 1, '0', '', 1, '1'),
(3, NOW(), 1, NOW(), 1, 'BugOS', 'single select', '" . t('bugfree', 'All') . ',' . t('bugfree', 'Win7') . ',' . t('bugfree', 'WinVista') . ',' . t('bugfree', 'WinXP') . ',' . t('bugfree', 'Win2000') . ',' . t('bugfree', 'Linux') . ',' . t('bugfree', 'FreeBSD') . ',' . t('bugfree', 'Unix') . ',' . t('bugfree', 'MacOS') . ',' . t('bugfree', 'Others') . "', '', '0', '" . t('bugfree', 'OS') . "', 'bug', 'bug_status', 6, 'opened,resolved,closed', 'no', '', 1, '1', 'result_environment', 1, '0'),
(4, NOW(), 1, NOW(), 1, 'BugBrowser', 'single select', '" . t('bugfree', 'All') . ',' . t('bugfree', 'IE8') . ',' . t('bugfree', 'IE7') . ',' . t('bugfree', 'IE6') . ',' . t('bugfree', 'FireFox4.0') . ',' . t('bugfree', 'FireFox3.0') . ',' . t('bugfree', 'FireFox2.0') . ',' . t('bugfree', 'Chrome') . ',' . t('bugfree', 'Safari') . ',' . t('bugfree', 'Opera') . ',' . t('bugfree', 'Others') . "', '', '0', '" . t('bugfree', 'Browser'). "', 'bug', 'bug_status', 5, 'opened,resolved,closed', 'no', '', 1, '1', 'result_environment', 1, '0'),
(5, NOW(), 1, NOW(), 1, 'OpenedBuild', 'text', '', '', '0', '" . t('bugfree', 'Open build') . "', 'bug', 'bug_open', 5, 'opened,resolved,closed', 'no', '', 1, '1', 'result_environment', 1, '1'),
(6, NOW(), 1, NOW(), 1, 'ResolvedBuild', 'text', '', '', '0', '" . t('bugfree', 'Resolve build') . "', 'bug', 'bug_resolve', 5, 'resolved,closed', 'no', '', 1, '0', '', 1, '1'),
(7, NOW(), 1, NOW(), 1, 'BugSubStatus', 'single select', ',Hold,Local Fix,Checked In,Can''t Regress', '', '0', '" . t('bugfree', 'Bug sub status') . "', 'bug', 'bug_other', 10, 'opened,resolved,closed', 'no', '', 1, '0', '', 1, '0'),
(8, NOW(), 1, NOW(), 1, 'BugMachine', 'text', '', '', '0', '" . t('bugfree', 'Machine') . "', 'bug', 'bug_other', 9, 'opened,resolved,closed', 'no', '', 1, '0', '', 1, '0'),
(9, NOW(), 1, NOW(), 1, 'BugKeyword', 'text', '', '', '0', '" . t('bugfree', 'Keyword') . "', 'bug', 'bug_other', 8, 'opened,resolved,closed', 'no', '', 1, '0', '', 1, '0'),
(10, NOW(), 1, NOW(), 1, 'CaseType', 'single select', '" . t('bugfree', 'Functional') . ',' . t('bugfree', 'Configuration') . ',' . t('bugfree', 'Setup') . ',' . t('bugfree', 'Security') . ',' . t('bugfree', 'Performance') . ',' . t('bugfree', 'Other')  . "', '', '0', '" . t('bugfree', 'Case type') . "', 'case', 'case_status', 8, '', 'no', '', 1, '0', '', 1, '1'),
(11, NOW(), 1, NOW(), 1, 'CaseMethod', 'single select', '" . t('bugfree', 'Manual') . ',' . t('bugfree', 'Automation') . "', '', '0', '" . t('bugfree', 'Case method') . "', 'case', 'case_status', 7, '', 'no', '', 1, '0', '', 1, '1'),
(12, NOW(), 1, NOW(), 1, 'CasePlan', 'single select', '" . t('bugfree', 'Function') . ',' . t('bugfree', 'UnitTest') . ',' . t('bugfree', 'BVT') . ',' . t('bugfree', 'Intergrate') . ',' . t('bugfree', 'System') . ',' . t('bugfree', 'Smoke') . ',' . t('bugfree', 'Acceptance') . "', '', '0', '" . t('bugfree', 'Case plan') . "', 'case', 'case_status', 6, '', 'no', '', 1, '0', '', 1, '0'),
(13, NOW(), 1, NOW(), 1, 'ScriptStatus', 'single select', '" . t('bugfree', 'NotPlanned') . ',' . t('bugfree', 'Planning') . ',' . t('bugfree', 'Blocked') . ',' . t('bugfree', 'Coding') . ',' . t('bugfree', 'CodingDone') . ',' . t('bugfree', 'Reviewed') . "', '', '0', '" . t('bugfree', 'Script status') . "', 'case', 'case_script', 5, '', 'no', '', 1, '0', '', 1, '0'),
(14, NOW(), 1, NOW(), 1, 'ScriptedBy', 'single user', '', '', '0', '" . t('bugfree', 'Scripted by') . "', 'case', 'case_script', 4, '', 'no', '', 1, '0', '', 1, '0'),
(15, NOW(), 1, NOW(), 1, 'ScriptedDate', 'date', '', '', '0', '" . t('bugfree', 'Scripted date') . "', 'case', 'case_script', 3, '', 'no', '', 1, '0', '', 1, '0'),
(16, NOW(), 1, NOW(), 1, 'ScriptLocation', 'text', '', '', '0', '" . t('bugfree', 'Script location') . "', 'case', 'case_script', 2, '', 'no', '', 1, '0', '', 1, '0'),
(17, NOW(), 1, NOW(), 1, 'CaseKeyword', 'text', '', '', '0', '" . t('bugfree', 'Keyword') . "', 'case', 'case_other', 8, '', 'no', '', 1, '0', '', 1, '0'),
(18, NOW(), 1, NOW(), 1, 'DisplayOrder', 'text', '', '0', '0', '" . t('bugfree', 'Display order') . "', 'case', 'case_other', 7, '', 'no', '', 1, '0', '', 1, '0'),
(19, NOW(), 1, NOW(), 1, 'OpenedBuild', 'text', '', '', '0', '" . t('bugfree', 'Run build') . "', 'result', 'result_environment', 9, '', 'no', '', 1, '1', 'bug_open', 1, '1'),
(20, NOW(), 1, NOW(), 1, 'BugOS', 'single select', '" . t('bugfree', 'All') . ',' . t('bugfree', 'Win7') . ',' . t('bugfree', 'WinVista') . ',' . t('bugfree', 'WinXP') . ',' . t('bugfree', 'Win2000') . ',' . t('bugfree', 'Linux') . ',' . t('bugfree', 'FreeBSD') . ',' . t('bugfree', 'Unix') . ',' . t('bugfree', 'MacOS') . ',' . t('bugfree', 'Others') . "', '', '0', '" . t('bugfree', 'OS') . "', 'result', 'result_environment', 8, '', 'no', '', 1, '1', 'bug_open', 1, '0'),
(21, NOW(), 1, NOW(), 1, 'BugBrowser', 'single select', '" . t('bugfree', 'All') . ',' . t('bugfree', 'IE8') . ',' . t('bugfree', 'IE7') . ',' . t('bugfree', 'IE6') . ',' . t('bugfree', 'FireFox4.0') . ',' . t('bugfree', 'FireFox3.0') . ',' . t('bugfree', 'FireFox2.0') . ',' . t('bugfree', 'Chrome') . ',' . t('bugfree', 'Safari') . ',' . t('bugfree', 'Opera') . ',' . t('bugfree', 'Others') . "', '', '0', '" . t('bugfree', 'Browser') . "', 'result', 'result_environment', 7, '', 'no', '', 1, '1', 'bug_status', 1, '0'),
(22, NOW(), 1, NOW(), 1, 'BugMachine', 'text', '', '', '0', '" . t('bugfree', 'Machine') . "', 'result', 'result_other', 6, '', 'no', '', 1, '1', 'bug_other', 1, '0'),
(23, NOW(), 1, NOW(), 1, 'ResultKeyword', 'text', '', '', '0', '" . t('bugfree', 'Keyword') . "', 'result', 'result_other', 5, '', 'no', '', 1, '0', '', 1, '0');
INSERT INTO `##PREFIX##bug_info` (`id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `bug_status`, `assign_to`, `title`, `mail_to`, `repeat_step`, `lock_version`, `resolved_at`, `resolved_by`, `closed_at`, `closed_by`, `related_bug`, `related_case`, `related_result`, `productmodule_id`, `modified_by`, `solution`, `duplicate_id`, `product_id`, `reopen_count`, `priority`, `severity`) VALUES
(1, NOW(), 1, NOW(), 1, 'Active', -1, '" . t('bugfree', 'Sample bug title') . "', '', '" . t('bugfree', 'Sample bug repeat step') . "', 1, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '1', '', '', 1, 0, 4, 4);
INSERT INTO `##PREFIX##ettonbug_1` (`id`, `bug_id`, `BugType`, `HowFound`, `BugBrowser`, `BugOS`, `OpenedBuild`, `ResolvedBuild`, `BugSubStatus`, `BugMachine`, `BugKeyword`) VALUES
(1, 1, '" . t('bugfree', 'Other') . "', '" . t('bugfree', 'Other') . "', '', '', 'N/A', '', '', '', '');
INSERT INTO `##PREFIX##user_group` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_dropped`, `lock_version`) VALUES
(1, '[All Users]', NOW(), 0, NOW(), 0, '0', 1);
INSERT INTO `##PREFIX##test_user` (`id`, `username`, `password`, `realname`, `email`, `wangwang`, `email_flag`, `wangwang_flag`, `created_by`, `created_at`, `updated_by`, `updated_at`, `is_dropped`, `authmode`, `lock_version`) VALUES 
(-1, \"Active\", \"Active\", \"Active\", \"\", \"\", \"0\", \"0\", 0, NOW(), 0, NOW(), \"0\", \"ldap\", 1),
(-2, \"Closed\", \"Closed\", \"Closed\", \"\", \"\", \"0\", \"0\", 0, NOW(), 0, NOW(), \"0\", \"ldap\", 1);
";
?>