-- Optimize Indexes for ClubGo Database
SET sql_mode='';

-- Fix any legacy 0000-00-00 datetime values
UPDATE tbl_event SET updated_at = CURRENT_TIMESTAMP WHERE updated_at IS NULL OR updated_at = '0000-00-00 00:00:00';
ALTER TABLE tbl_event MODIFY COLUMN updated_at datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- tbl_user (105,000+ rows)
ALTER TABLE `tbl_user` ADD INDEX `idx_user_mobile` (`mobile`);
ALTER TABLE `tbl_user` ADD INDEX `idx_user_email` (`email`);
ALTER TABLE `tbl_user` ADD INDEX `idx_user_code` (`code`);
ALTER TABLE `tbl_user` ADD INDEX `idx_user_status_mobile` (`status`, `mobile`);

-- tbl_ticket
ALTER TABLE `tbl_ticket` ADD INDEX `idx_ticket_eid_uid` (`eid`, `uid`);
ALTER TABLE `tbl_ticket` ADD INDEX `idx_ticket_uid` (`uid`);
ALTER TABLE `tbl_ticket` ADD INDEX `idx_ticket_eid_type` (`eid`, `ticket_type`);

-- tbl_fav & tbl_fav_venue
ALTER TABLE `tbl_fav` ADD INDEX `idx_fav_uid_eid` (`uid`, `eid`);
ALTER TABLE `tbl_fav_venue` ADD INDEX `idx_fav_venue_uid_vid` (`uid`, `vid`);

-- tbl_event
ALTER TABLE `tbl_event` ADD INDEX `idx_event_status_sdate` (`status`, `sdate`);
ALTER TABLE `tbl_event` ADD INDEX `idx_event_status_id` (`status`, `id`);
ALTER TABLE `tbl_event` ADD INDEX `idx_event_loc_id` (`loc_id`, `status`);

-- tbl_type_price
ALTER TABLE `tbl_type_price` ADD INDEX `idx_typeprice_eid_price` (`eid`, `price`);

-- Media & Meta tables
ALTER TABLE `tbl_sponsore` ADD INDEX `idx_sponsore_eid_status` (`eid`, `status`);
ALTER TABLE `tbl_gallery` ADD INDEX `idx_gallery_eid_status` (`eid`, `status`);
ALTER TABLE `tbl_cover` ADD INDEX `idx_cover_eid_status` (`eid`, `status`);

-- OTP verification (21,000+ rows)
ALTER TABLE `tbl_otp_verification` ADD INDEX `idx_otp_mobile` (`mobile`, `otp`);

-- Venue status & city
ALTER TABLE `tbl_veneu` ADD INDEX `idx_venue_status_id` (`loc_status`, `loc_id`);
