-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 09, 2023 at 04:05 PM
-- Server version: 10.6.5-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `7s_rummy_db`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `admins_login`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `admins_login` (IN `in_uname` VARCHAR(255), IN `in_password` VARCHAR(255), IN `in_ip` VARCHAR(15), IN `in_device` VARCHAR(512))  BEGIN

DECLARE u_id INT DEFAULT 0;

    if exists(select * from admins where username = in_uname and password = in_password) then

        SELECT au.id into u_id from admins as au LEFT JOIN master_admin_roles as mr ON mr.role_id = au.role_id where au.username= in_uname and au.password = in_password;

        if exists(SELECT * from admins where active = 1 and id = u_id) then
                
            if exists(SELECT * from admins where ip_restrict = '1' and id = u_id) then 
            
                if exists(SELECT * from admins as au LEFT JOIN admin_ip_list as ai ON ai.admin_id = au.id where au.id = u_id AND ai.ip_address = in_ip) THEN
                
                    SELECT "success" as res, au.*, mr.role_name as role_name, mr.role_type as role_type, mr.scope_list as scope from admins as au LEFT JOIN master_admin_roles as mr ON mr.role_id = au.role_id where au.id= u_id and au.password = in_password;

                    INSERT INTO admin_log_history(admin_id, login_device, location_ip, action) VALUES (u_id, in_device, in_ip, 'Login');
                     
                else
                
                    SELECT "failed" as res, "Unauthorized Login IP!" as msg;
                    
                end if;   
                    
            else

                SELECT "success" as res, au.*, mr.role_name as role_name, mr.role_type as role_type, mr.scope_list as scope from admins as au LEFT JOIN master_admin_roles as mr ON mr.role_id = au.role_id where au.id= u_id and au.password = in_password;

                INSERT INTO admin_log_history(admin_id, login_device, location_ip, action) VALUES (u_id, in_device, in_ip, 'Login');

            end if;
 
        else

            SELECT "failed" as res, "You have been Blocked!" as msg;

        end if;

    else

        SELECT "failed" as res, "Invalid Login!" as msg;

    end if;     
END$$

DROP PROCEDURE IF EXISTS `create_order`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `create_order` (IN `in_customer_details` TEXT, IN `in_order_amount` FLOAT, IN `in_order_currency` VARCHAR(255), IN `in_order_expiry_time` DATETIME, IN `in_order_note` VARCHAR(150), IN `in_notify_url` VARCHAR(512), IN `in_payment_methods` VARCHAR(255), IN `in_user_id` INT(11), IN `in_remarks` VARCHAR(1000), IN `in_order_type` ENUM('withdraw','deposit'))  BEGIN

DECLARE order_id INT DEFAULT 0;

INSERT INTO users_payment_order(user_id,order_type,order_status,remarks,order_amount) VALUES (in_user_id, in_order_type, 'inprogress', in_remarks, in_order_amount);

SELECT "success" as res, "Data Insert Successfully" as msg;

SELECT LAST_INSERT_ID() into order_id;

INSERT INTO users_cash_chips_deposit(user_id, order_id, customer_details, order_amount, order_currency, order_expiry_time, order_note, notify_url, payment_methods) VALUES (in_user_id, order_id, in_customer_details,in_order_amount,in_order_currency,in_order_expiry_time,in_order_note,in_notify_url,in_payment_methods);

 
END$$

DROP PROCEDURE IF EXISTS `game_point_to_claim_bonus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `game_point_to_claim_bonus` (IN `in_bonus` INT, IN `in_user_id` INT)  BEGIN

	DECLARE bonus_point INT DEFAULT 0;
    DECLARE deposit_point INT DEFAULT 0;

    if exists(select * from master_game_point_to_claim_bonus where bonus = in_bonus) then

        SELECT point_value into bonus_point from master_game_point_to_claim_bonus where bonus = in_bonus;
        
		SELECT min_deposit into deposit_point from master_game_point_to_claim_bonus where bonus = in_bonus;

        if exists(SELECT * from users_cash_chips where user_id = in_user_id AND total_deposit_point >= deposit_point) then
                
              if exists(SELECT * from users_point where user_id = in_user_id AND point_inhand >= bonus_point) then
              
				 SELECT "success" as res, "Claim your bonus" as msg;
                 
                 #insert query for point to claim bonus hoistory table
				#update query for users bonus total
                
                #insert query for point history table
                #update query for users points totale             
              
			  else
                
                 SELECT "failed" as res, "Your Point Balance is Low" as msg;
              
              end if;
 
        else

            SELECT "failed" as res, "Deposit Cash Minimum Value is Atleast " as msg;

        end if;

    else

        SELECT "failed" as res, "Invalid Bonus Value!" as msg;

    end if;     

END$$

DROP PROCEDURE IF EXISTS `get_free_cash_room`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_free_cash_room` (IN `in_game_type` VARCHAR(255), IN `in_joker_type` VARCHAR(255), IN `in_deck` VARCHAR(255), IN `in_sitting_capacity` VARCHAR(255), IN `in_bet_value` VARCHAR(255), IN `in_table_status` VARCHAR(255), IN `in_search_val` VARCHAR(255), IN `in_offset` VARCHAR(255), IN `in_limit` VARCHAR(255))  BEGIN

declare where_game_type varchar(255) default '';
declare where_sitting_capacity varchar(255) default '';
declare where_deck varchar(255) default '';
declare where_joker_type varchar(255) default '';
declare where_bet_value varchar(255) default '';
declare where_table_status varchar(255) default '';
declare where_search_val varchar(255) default '';

declare final_query longtext default '';
declare select_query longtext default ''; 
declare select_total_query longtext default ''; 

set @total_rows=0;

set select_total_query ='SELECT count(gt.id) into @total_rows FROM game_room_free grf LEFT JOIN master_game_tables gt ON gt.id = grf.game_table_id LEFT JOIN master_game_types mgt ON mgt.id = gt.game_id WHERE 1=1';

set select_query ='SELECT @total_rows AS total,grf.*,mgt.name as game_type, gt.max_player, gt.entry_fees FROM game_room_free grf LEFT JOIN master_game_tables gt ON gt.id = grf.game_table_id LEFT JOIN master_game_types mgt ON mgt.id = gt.game_id WHERE 1=1';

if(in_game_type <>'') THEN
  set  where_game_type = concat(" and gt.game_id =",in_game_type,"");
End if;

if(in_sitting_capacity <>'') THEN
  set  where_sitting_capacity = concat(" and gt.max_player =",in_sitting_capacity,"");
End if;

if(in_joker_type <>'') THEN
  set  where_joker_type = concat(" and grf.joker_type='",in_joker_type,"'");
End if;

if(in_deck <>'') THEN
  set  where_deck = concat(" and grf.deck='",in_deck,"'");
End if;

if(in_bet_value <>'') THEN
begin
 if(in_bet_value = '1') THEN
 	set  where_bet_value = concat(" and gt.entry_fees between 1 and 100");
 elseif(in_bet_value = '2')THEN
 	set  where_bet_value = concat(" and gt.entry_fees between 101 and 1000");
 else
    set  where_bet_value = concat(" and gt.entry_fees >= 1001");
 end if;
end;
End if;

if(in_table_status <>'') THEN
  set  where_table_status = concat(" and grf.active=", in_table_status,"");
End if;

if(in_search_val <>'') THEN
  set  where_search_val = concat(" and mgt.name like '%",in_search_val,"%'");
End if;

if(in_limit <> '' and in_offset <> '') THEN
	set @limit_query=concat(' limit ',in_offset,',',in_limit);
ELSE
	set @limit_query='';
end if;

set @final_total_query = concat(select_total_query, where_deck, where_joker_type, where_sitting_capacity, where_bet_value, where_game_type, where_table_status, where_search_val);

set @final_query = concat(select_query, where_deck, where_joker_type, where_sitting_capacity, where_bet_value, where_game_type, where_table_status, where_search_val, @limit_query);

#select @final_query;
#select @final_total_query; 

prepare total_stmt from @final_total_query;
execute total_stmt;
prepare stmt from @final_query;
execute stmt;

END$$

DROP PROCEDURE IF EXISTS `get_game_tables`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_game_tables` (IN `in_match_type` INT(11), IN `in_game_type` INT(11), IN `in_sitting_capacity` INT(11), IN `in_bet_value` INT(11), IN `in_table_status` INT(11), IN `in_search_val` VARCHAR(255), IN `in_offset` INT(11), IN `in_limit` INT(11))  BEGIN

declare where_match_type varchar(255) default '';
declare where_game_type varchar(255) default '';
declare where_sitting_capacity varchar(255) default '';
declare where_bet_value varchar(255) default '';
declare where_table_status varchar(255) default '';
declare where_search_val varchar(255) default '';

#declare final_query longtext default '';
declare select_query longtext default ''; 
declare select_total_query longtext default ''; 

set @total_rows=0;
set select_total_query ='SELECT count(gt.room_id) into @total_rows FROM master_rummy_main_rooms gt LEFT JOIN master_rummy_type gmt ON gmt.type_id = gt.type_id LEFT JOIN master_rummy_format mgt ON mgt.format_id = gt.format_id WHERE 1=1';
set select_query ='SELECT @total_rows AS total,gt.*,gmt.name as game_match_name, mgt.name as game_type_name FROM master_rummy_main_rooms gt LEFT JOIN master_rummy_type gmt ON gmt.type_id = gt.type_id LEFT JOIN master_rummy_format mgt ON mgt.format_id = gt.format_id WHERE 1=1';
if(in_match_type <>'') THEN
  set  where_match_type = concat(" and gt.match_id='",in_match_type,"'");
End if;

if(in_game_type <>'') THEN
  set  where_game_type = concat(" and gt.format_id =",in_game_type,"");
End if;

if(in_sitting_capacity <>'') THEN
  set  where_sitting_capacity = concat(" and gt.max_seat =",in_sitting_capacity,"");
End if;

if(in_bet_value <>'') THEN
begin
 if(in_bet_value = '1') THEN
 	set  where_bet_value = concat(" and gt.entry_chip between 1 and 100");
 elseif(in_bet_value = '2')THEN
 	set  where_bet_value = concat(" and gt.entry_chip between 101 and 1000");
 else
    set  where_bet_value = concat(" and gt.entry_chip >= 1001");
 end if;
end;
End if;

if(in_table_status <>'') THEN
  set  where_table_status = concat(" and gt.active=", in_table_status,"");
End if;

if(in_search_val <>'') THEN
  set  where_search_val = concat(" and gmt.name  like '%",in_search_val,"%'");
End if;

if(in_limit <> '' and in_offset <> '') THEN
	set @limit_query=concat(' limit ',in_offset,',',in_limit);
ELSE
	set @limit_query='';
end if;

set @final_total_query = concat(select_total_query, where_match_type, where_sitting_capacity, where_bet_value, where_game_type, where_table_status, where_search_val);

set @final_query = concat(select_query, where_match_type, where_sitting_capacity, where_bet_value, where_game_type, where_table_status, where_search_val, @limit_query);

#select @final_query;
#select @final_total_query; 

prepare total_stmt from @final_total_query;
execute total_stmt;
prepare stmt from @final_query;
execute stmt;

END$$

DROP PROCEDURE IF EXISTS `get_menu_list`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_menu_list` ()  BEGIN
	DROP TEMPORARY TABLE if exists ordered_menu;
	CREATE TEMPORARY TABLE ordered_menu SELECT * FROM master_admin_roles_menu where active=1 and order_id!=0 order by order_id asc;
    DROP TEMPORARY TABLE if exists unordered_menu;
    CREATE TEMPORARY TABLE unordered_menu SELECT * FROM master_admin_roles_menu where active=1 and order_id=0;
    select * from ordered_menu union all select * from unordered_menu; 
END$$

DROP PROCEDURE IF EXISTS `get_real_cash_room`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_real_cash_room` (IN `in_game_type` VARCHAR(255), IN `in_joker_type` VARCHAR(255), IN `in_deck` VARCHAR(255), IN `in_sitting_capacity` VARCHAR(255), IN `in_bet_value` VARCHAR(255), IN `in_table_status` VARCHAR(255), IN `in_search_val` VARCHAR(255), IN `in_offset` VARCHAR(255), IN `in_limit` VARCHAR(255))  BEGIN

declare where_game_type varchar(255) default '';
declare where_sitting_capacity varchar(255) default '';
declare where_deck varchar(255) default '';
declare where_joker_type varchar(255) default '';
declare where_bet_value varchar(255) default '';
declare where_table_status varchar(255) default '';
declare where_search_val varchar(255) default '';

declare final_query longtext default '';
declare select_query longtext default ''; 
declare select_total_query longtext default ''; 

set @total_rows=0;

set select_total_query ='SELECT count(gt.id) into @total_rows FROM game_room_cash grc LEFT JOIN master_game_tables gt ON gt.id = grc.game_table_id LEFT JOIN master_game_types mgt ON mgt.id = gt.game_id WHERE 1=1';

set select_query ='SELECT @total_rows AS total,grc.*,mgt.name as game_type, gt.max_player, gt.entry_fees FROM game_room_cash grc LEFT JOIN master_game_tables gt ON gt.id = grc.game_table_id LEFT JOIN master_game_types mgt ON mgt.id = gt.game_id WHERE 1=1';

if(in_game_type <>'') THEN
  set  where_game_type = concat(" and gt.game_id =",in_game_type,"");
End if;

if(in_sitting_capacity <>'') THEN
  set  where_sitting_capacity = concat(" and gt.max_player =",in_sitting_capacity,"");
End if;

if(in_joker_type <>'') THEN
  set  where_joker_type = concat(" and grc.joker_type='",in_joker_type,"'");
End if;

if(in_deck <>'') THEN
  set  where_deck = concat(" and grc.deck='",in_deck,"'");
End if;

if(in_bet_value <>'') THEN
begin
 if(in_bet_value = '1') THEN
 	set  where_bet_value = concat(" and gt.entry_fees between 1 and 100");
 elseif(in_bet_value = '2')THEN
 	set  where_bet_value = concat(" and gt.entry_fees between 101 and 1000");
 else
    set  where_bet_value = concat(" and gt.entry_fees >= 1001");
 end if;
end;
End if;

if(in_table_status <>'') THEN
  set  where_table_status = concat(" and grc.active=", in_table_status,"");
End if;

if(in_search_val <>'') THEN
  set  where_search_val = concat(" and mgt.name like '%",in_search_val,"%'");
End if;

if(in_limit <> '' and in_offset <> '') THEN
	set @limit_query=concat(' limit ',in_offset,',',in_limit);
ELSE
	set @limit_query='';
end if;

set @final_total_query = concat(select_total_query, where_deck, where_joker_type, where_sitting_capacity, where_bet_value, where_game_type, where_table_status, where_search_val);

set @final_query = concat(select_query, where_deck, where_joker_type, where_sitting_capacity, where_bet_value, where_game_type, where_table_status, where_search_val, @limit_query);

#select @final_query;
#select @final_total_query; 

prepare total_stmt from @final_total_query;
execute total_stmt;
prepare stmt from @final_query;
execute stmt;

END$$

DROP PROCEDURE IF EXISTS `get_tourney_rooms`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_tourney_rooms` (IN `in_from_date` DATE, IN `in_to_date` DATE, IN `in_game_type` VARCHAR(255), IN `in_table_status` VARCHAR(255), IN `in_active_status` VARCHAR(255), IN `in_search_val` VARCHAR(255), IN `in_offset` VARCHAR(255), IN `in_limit` VARCHAR(255))  BEGIN

declare where_date_time varchar(255) default '';
declare where_game_type varchar(255) default '';
declare where_table_status varchar(255) default '';
declare where_active_status varchar(255) default '';
declare where_search_val varchar(255) default '';

declare final_query longtext default '';
declare select_query longtext default ''; 
declare select_total_query longtext default ''; 

set @total_rows=0;
set @from = date_add(in_from_date,interval 0 minute);
set @to = date_add(in_to_date,interval 86399 second);

set select_total_query ='SELECT count(grt.id) into @total_rows FROM vw_game_room_tourney grt WHERE 1=1';

set select_query ='SELECT @total_rows AS total,grt.* FROM vw_game_room_tourney grt WHERE 1=1';

if(in_from_date <>'' && in_to_date <> '') THEN
  set  where_date_time = concat(" and DATE(grt.start_date) BETWEEN '",@from,"' and '",@to,"'");
End if;

if(in_game_type <>'') THEN
  set  where_game_type = concat(" and grt.game_id =",in_game_type,"");
End if;

if(in_table_status <>'') THEN
  set  where_table_status = concat(" and grt.status=", in_table_status,"");
End if;

if(in_active_status <>'') THEN
  set  where_active_status = concat(" and grt.active=", in_active_status,"");
End if;

if(in_search_val <>'') THEN
  set  where_search_val = concat(" and grt.game_type like '%",in_search_val,"%'");
End if;

if(in_limit <> '' and in_offset <> '') THEN
	set @limit_query=concat(' limit ',in_offset,',',in_limit);
ELSE
	set @limit_query='';
end if;

set @final_total_query = concat(select_total_query, where_date_time, where_game_type, where_active_status, where_table_status, where_search_val);

set @final_query = concat(select_query, where_date_time, where_game_type, where_active_status, where_table_status, where_search_val, @limit_query);

#select @final_query;
#select @final_total_query; 

prepare total_stmt from @final_total_query;
execute total_stmt;
prepare stmt from @final_query;
execute stmt;

END$$

DROP PROCEDURE IF EXISTS `send_email_otp_verification`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `send_email_otp_verification` (IN `in_email` VARCHAR(255), IN `in_otp_no` INT(6), IN `in_user_id` INT(11), IN `in_current_date` DATETIME, IN `in_device_ip` VARCHAR(255), IN `in_device_info` LONGTEXT)  BEGIN

DECLARE in_id INT DEFAULT 0;

    IF exists(SELECT * FROM users_verification_email WHERE email = in_email AND otp_no = in_otp_no AND user_id = in_user_id) then

        SELECT id into in_id from users_verification_email WHERE email = in_email AND otp_no = in_otp_no AND user_id = in_user_id;

        IF exists(SELECT * FROM users_verification_email WHERE otp_expiry_on > in_current_date AND id = in_id) then
 
            SELECT "success" as res, "OTP Verify Successfully" as msg, name from vw_users where id= in_user_id;

            UPDATE users_verification_email SET otp_verify_status = 1, otp_verify_on = in_current_date, device_id = in_device_ip, device_details = in_device_info  WHERE id = in_id;

            UPDATE users SET email_verify_status = 1 WHERE id = in_user_id;

        ELSE

            SELECT "failed" as res, "Exp OTP" as msg;

        END IF;

    ELSE

        SELECT "failed" as res, "Invalid OTP" as msg;

    END IF;

END$$

DROP PROCEDURE IF EXISTS `send_mobile_otp_verification`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `send_mobile_otp_verification` (IN `in_mobile_no` VARCHAR(10), IN `in_otp_no` INT(6), IN `in_user_id` INT(11), IN `in_current_date` DATETIME, IN `in_device_ip` VARCHAR(255), IN `in_device_info` VARCHAR(255))  BEGIN

DECLARE in_id INT DEFAULT 0;

    IF exists(SELECT * FROM users_verification_phone_no WHERE mobile = in_mobile_no AND mobile_otp = in_otp_no AND user_id = in_user_id) then

        SELECT id into in_id from users_verification_phone_no WHERE mobile = in_mobile_no AND mobile_otp = in_otp_no AND user_id = in_user_id;

        IF exists(SELECT * FROM users_verification_phone_no WHERE otp_expiry_on > in_current_date AND id = in_id) then
 
            SELECT "success" as res, "OTP Verify successfully" as msg;

            UPDATE users_verification_phone_no SET otp_verify_status = 1, otp_verify_on = in_current_date, device_id = in_device_ip, device_details = in_device_info  WHERE id = in_id;

            UPDATE users SET phone_verify_status = 1 WHERE id = in_user_id;

        ELSE

            SELECT "failed" as res, "Exp OTP Number" as msg;

        END IF;

    ELSE

        SELECT "failed" as res, "Invalid OTP" as msg;

    END IF;

END$$

DROP PROCEDURE IF EXISTS `send_withdraw_request`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `send_withdraw_request` (IN `in_user_id` INT, IN `in_amount` DOUBLE)  BEGIN	

	declare in_cash double;
    set @in_cash=0;
    set @in_haoda_payout_req_id=0;
    
	if(check_user_active(in_user_id) and check_user_email_verified(in_user_id) and check_user_profile_complete(in_user_id)) then
    
		SELECT cash_inhand into @in_cash FROM `users_cash_chips` where user_id=in_user_id;
        
        if exists(SELECT id FROM `users_details_bank` where user_id=in_user_id and status=1) then
        
			if(@in_cash > in_amount) then
            
				#Order id
				insert into users_cash_chips_withdraw_request (user_id,req_amount) values(in_user_id, in_amount);
                
                set @order_id = last_insert_id();
                
                set @beneficiary_account_name=''; set @beneficiary_account_number=''; set @beneficiary_account_ifsc=''; set @beneficiary_bank_name=''; 
				select customer_name,account_no,ifsc_code,bank_name  into  @beneficiary_account_name, @beneficiary_account_number, @beneficiary_account_ifsc, @beneficiary_bank_name   FROM `users_details_bank` where user_id=in_user_id;
                
                INSERT INTO `haoda_payout_response`(`user_id`, `order_id`, `amount`, `remarks`, `beneficiary_bank_name`, `beneficiary_account_ifsc`, `beneficiary_account_name`, `beneficiary_account_number`, `current_status`) VALUES (in_user_id, @order_id, in_amount,'user_cash_withdraw',@beneficiary_bank_name, @beneficiary_account_ifsc, @beneficiary_account_name, @beneficiary_account_number, null);
                
                set @in_haoda_payout_req_id = last_insert_id();
                
                Update users_cash_chips_withdraw_request set haoda_payout_id = @in_haoda_payout_req_id WHERE order_id = @order_id;
                
                select 'success' as msg,  @order_id as order_id , @beneficiary_bank_name as beneficiary_bank_name, @beneficiary_account_ifsc as beneficiary_account_ifsc, @beneficiary_account_name as beneficiary_account_name, @beneficiary_account_number as beneficiary_account_number;
            
            else
				select 'insufficient_balance' as msg;
			end if;
            
        else
			select 'invalid_bank_account' as msg;
        end if;
        
	else
		select 'user_not_active_unverified' as msg;
    end if;
END$$

DROP PROCEDURE IF EXISTS `send_withdraw_request_response`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `send_withdraw_request_response` (IN `in_order_id` INT(11), IN `in_order_status` VARCHAR(255), IN `in_payout_id` VARCHAR(255), IN `in_res_str` LONGTEXT, IN `in_user_id` INT(11), IN `in_amount` DECIMAL(12,2))  BEGIN

set @balance=0;
set @in_hand =0;

INSERT INTO `haoda_payout_response_history`(`order_id`, `order_type`, `order_status`, `res_str`) VALUES (in_order_id,'Withdraw',in_order_status,in_res_str);

UPDATE `haoda_payout_response` SET payout_id = in_payout_id, current_status = in_order_status WHERE order_id = in_order_id;

if(in_order_status = 'Processing') then

	SELECT cash_inhand into @in_hand FROM `users_cash_chips` WHERE user_id = in_user_id;
    
     set @balance =  @in_hand - in_amount;
    
	UPDATE `users_cash_chips` SET cash_inhand = @balance WHERE user_id = in_user_id;
    
end if;   
    
END$$

DROP PROCEDURE IF EXISTS `send_withdraw_request_success_response`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `send_withdraw_request_success_response` (IN `in_amount` DECIMAL(12,2), IN `in_remarks` VARCHAR(255), IN `in_created_at` DATETIME, IN `in_payment_mode` VARCHAR(255), IN `in_transfer_date` VARCHAR(255), IN `in_beneficiary_bank_name` VARCHAR(255), IN `in_payout_id` VARCHAR(255), IN `in_beneficiary_account_ifsc` VARCHAR(255), IN `in_beneficiary_account_name` VARCHAR(255), IN `in_beneficiary_account_number` VARCHAR(255), IN `in_beneficiary_upi_handle` VARCHAR(255), IN `in_utr` VARCHAR(255), IN `in_status` VARCHAR(255), IN `in_res_str` VARCHAR(1024))  BEGIN 

set @order_id = 0;
set @user_id = 0;
set @in_hand = 0;
set @balance = 0;

UPDATE `haoda_payout_response` SET `amount`= in_amount,`remarks`= in_remarks,`created_at`= in_created_at,`payment_mode`= in_payment_mode,`transfer_date`= in_transfer_date,`beneficiary_bank_name`= in_beneficiary_bank_name,`payout_id`= in_payout_id,`beneficiary_account_ifsc`= in_beneficiary_account_ifsc,`beneficiary_account_name`= in_beneficiary_account_name,`beneficiary_account_number`= in_beneficiary_account_number,`beneficiary_upi_handle`= in_beneficiary_upi_handle,`UTR`= in_utr, `current_status` = in_status WHERE payout_id = in_payout_id;

SELECT order_id into @order_id FROM `haoda_payout_response` where payout_id = in_payout_id;
SELECT user_id into @user_id FROM `haoda_payout_response` where payout_id = in_payout_id;

INSERT INTO `haoda_payout_response_history` (order_id, order_type, order_status, res_str) VALUES (@order_id, 'Withdraw', in_status, in_res_str);

if(in_status = 'Failed') then

	SELECT cash_inhand into @in_hand FROM `users_cash_chips` WHERE user_id = @user_id;
    
	set @balance =  @in_hand + in_amount;
    
	UPDATE `users_cash_chips` SET cash_inhand = @balance WHERE user_id = @user_id;
    
elseif(in_status = 'success')then

	UPDATE `users_cash_chips` SET total_withdraw_cash = in_amount WHERE user_id = @user_id;
    
    #add history table, #user chips table
    
end if;   

SELECT "success" as res, "data updated!" as msg;

END$$

DROP PROCEDURE IF EXISTS `update_welcome_bonus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_welcome_bonus` (`in_user_id` DECIMAL, `in_deposit_value` DECIMAL, `in_transaction_id` INT)  BEGIN
	declare deposit_count int default 0;
    
    declare ref_user_id int default 0; 
    declare ref_tier_level int default 0;
    declare ref_deposit_count int default 0;
    
    declare current_deposit decimal(12,2) default 0.00;
    declare deposit_cash decimal(12,2) default 0.00;
    declare welcome_bonus decimal(12,2) default 0.00;
    declare instant_cash decimal(12,2) default 0.00;
    
     declare self_ref_bonus decimal(12,2) default 0.00;
     declare referrer_ref_bonus decimal(12,2) default 0.00;
    
    #declare deposit_count int default 0;
    DECLARE exit handler for sqlexception
    BEGIN
		ROLLBACK;
    END;
    DECLARE exit handler for sqlwarning
    BEGIN
		ROLLBACK;
    END;
    
   
		#previous deposit count
		select get_deposit_count(in_user_id) into deposit_count;
        
        #current deposit count
        set current_deposit=deposit_count+1;
        
        #check if deposit bonus is available for the deposit amount and deposit count
        set @check_valuable_deposit_for_bonus=0;
        select check_valuable_deposit_for_bonus(in_deposit_value, current_deposit) into @check_valuable_deposit_for_bonus;
        
        ### WELCOME BONUS CALCULATION START ###
        #calculate bonus valus and instant cash value
        if(@check_valuable_deposit_for_bonus = 1) then 
		begin
			select  in_deposit_value, (in_deposit_value/100)*(mgwb.bonus_per), (in_deposit_value/100)*(mgwb.instant_per) into deposit_cash,welcome_bonus,instant_cash  from master_game_welcome_bonus as mgwb where mgwb.deposit_count=current_deposit and in_deposit_value >= mgwb.min_value and in_deposit_value <= mgwb.max_value;
			#select cash, bonus, instant_cash;
        end;
		else 
		begin
			select in_deposit_value into deposit_cash;
		end;
		end if;
        ### WELCOME BONUS CALCULATION END ###
        
        
        
        ### REFERRAL BONUS CALCULATION START ###
        
        #Check Referral id
        select ref_by into ref_user_id from users where id=in_user_id;
        
        #Check referaal id is active
        set @ref_by_active_status=0;
		select check_user_active(ref_user_id) into @ref_by_active_status;
        
        #Check referral is available and active
        if((ref_user_id is not null and ref_user_id > 0 and @ref_by_active_status=1)) then 
        begin
			#previous deposit count for referral user
			select get_deposit_count(ref_user_id) into ref_deposit_count;
            if(ref_deposit_count >= 1) then
            begin
				#get tier level of referrer
				select get_tier_level(ref_user_id) into ref_tier_level;
                
                #check referral bouns is eligible
				set @check_valuable_deposit_for_referral_bonus=0; 
				select check_valuable_deposit_for_referral_bonus(in_deposit_value,ref_tier_level) into @check_valuable_deposit_for_referral_bonus;
				
                #get the referral bonus for self and referrer
                if(@check_valuable_deposit_for_referral_bonus = 1) then
				begin
					select mgrb.bonus_self, mgrb.bonus_ref into self_ref_bonus,referrer_ref_bonus from master_game_referral_bonus as mgrb where mgrb.min_deposit_value != 0 and in_deposit_value >= mgrb.min_deposit_value and in_deposit_value <= mgrb.max_deposit_value limit 1;
				end;
                end if;
            end;
            end if;
        end;
		end if;
        
        ### REFERRAL BONUS CALCULATION END 
        START TRANSACTION;
        if exists(select id from `users_cash_chips` where user_id=in_user_id) then
		begin
			set @table_id=0;
			select id into @table_id from `users_cash_chips` where user_id=in_user_id;
            set @user_cash_chips=0;
            set @user_inhand_chips=0;
            select total_cash, cash_inhand into @user_cash_chips,@user_inhand_chips from `users_cash_chips` where id=@table_id;
            update `users_cash_chips` set total_cash=(@user_cash_chips+deposit_cash),  cash_inhand=(@user_inhand_chips+deposit_cash)  where id=@table_id;
        end;
        else
        begin
			INSERT INTO `users_cash_chips`(`user_id`, `total_cash`, `cash_inplay`, `cash_inwin`, `cash_inhand`) VALUES (in_user_id, deposit_cash,0,0,deposit_cash); 
        end;
        end if;
        
        if exists(select id from `users_bonus` where user_id=in_user_id) then
		begin
			set @table_id=0;
			select id into @table_id from `users_bonus` where user_id=in_user_id;
			set @user_total_bonus=0;
            set @user_bonus_inhand=0;
            select total_bonus, bonus_inhand into @user_total_bonus,@user_bonus_inhand from `users_bonus` where id=@table_id;
            update `users_bonus` set total_bonus=(@user_total_bonus+welcome_bonus),  bonus_inhand=(@user_bonus_inhand+welcome_bonus)  where id=@table_id;
        end;
        else
        begin
			INSERT INTO `users_bonus`(`user_id`, `total_bonus`, `bonus_used`, `bonus_inhand`) VALUES (in_user_id, welcome_bonus,0,welcome_bonus); 
        end;
        end if;
        
        
        
        INSERT INTO `users_cash_chips_instantcash_history`(`user_id`, `instantcash`, `transaction_id`) VALUES (in_user_id, instant_cash, in_transaction_id);
        
		INSERT INTO `users_cash_chips_buy_history`(`user_id`, `transaction_id`, `purchase_amount`) VALUES (in_user_id, in_transaction_id, in_deposit_value);
	
		set @action_welcome_json=concat('{"credited_by":"welcome","transaction_type":"deposit","transaction_id":"',in_transaction_id,'"}');
		INSERT INTO `users_bonus_history`(`bonus_id`,`user_id`,`chips`,`action_type`,`action_from`) VALUES (1, in_user_id, welcome_bonus, 'credit', @action_welcome_json);
		
        set @action_self_json=concat('{"credited_by":"self","transaction_type":"deposit","transaction_id":"',in_transaction_id,'"}');
		INSERT INTO `users_bonus_history`(`bonus_id`,`user_id`,`chips`,`action_type`,`action_from`) VALUES (2, in_user_id, self_ref_bonus, 'credit', @action_self_json);
       
		set @action_referrer_json=concat('{"credited_by":"',in_user_id,'","transaction_type":"deposit","transaction_id":"',in_transaction_id,'"}');
		INSERT INTO `users_bonus_history`(`bonus_id`,`user_id`,`chips`,`action_type`,`action_from`) VALUES (2, ref_user_id, referrer_ref_bonus, 'credit', @action_referrer_json);
		select deposit_cash, welcome_bonus, instant_cash, ref_user_id, self_ref_bonus, referrer_ref_bonus;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `users_login_with_google`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_login_with_google` (IN `in_email` VARCHAR(255), IN `in_country` VARCHAR(255), IN `in_state` VARCHAR(255), IN `in_city` VARCHAR(255), IN `in_ip_address` VARCHAR(255), IN `in_device_type` VARCHAR(255), IN `in_ref_code` VARCHAR(255))  BEGIN

DECLARE email_count INT DEFAULT 0;
DECLARE u_id INT DEFAULT 0;

DECLARE username int;
DECLARE user_id_length int default 5;
DECLARE id_prefix varchar(15) default '';

DECLARE uname varchar(15) DEFAULT '';
DECLARE remaining_length int;

    SELECT COUNT(id) INTO email_count from users WHERE email = in_email;

    IF (email_count <> 0) then 
     
        IF exists(SELECT * FROM users WHERE email = in_email AND active = 1) then

            SELECT id INTO u_id from users WHERE email = in_email;
           
            SELECT "success" as res, u.* FROM users as u WHERE u.id = u_id;

            INSERT INTO users_log_history(user_id, login_device, country_name, state_name, city_name, location_ip, action) VALUES (u_id, in_device_type, in_country, in_state, in_city, in_ip_address, 'Login');
	
        ELSE

            SELECT "failed" as res, "You have been blocked!" as msg;

        END IF;
   
    ELSEIF (email_count = 0) then

        begin

            ALTER TABLE users_id_generate AUTO_INCREMENT = 10000;

            INSERT INTO users_id_generate (assigned) VALUES (0);

            SELECT user_id INTO username FROM users_id_generate ORDER BY user_id DESC limit 1;

            set remaining_length=user_id_length-length(username);

            while remaining_length>0 DO

                set id_prefix = concat(id_prefix,'0'); 
                set remaining_length=remaining_length-1;

            end while;

            set uname = concat('001', id_prefix, username);

        INSERT INTO users (username, email, ip_registration, email_verify_status, login_type, refer_code) VALUES (uname, in_email, in_ip_address, 1, 'google', in_ref_code);

        SELECT LAST_INSERT_ID() into u_id;

        SELECT "success" as res, u.* FROM users as u WHERE u.id = u_id;

        INSERT INTO users_profile(user_id) VALUES (u_id);

        INSERT INTO users_log_history(user_id, login_device, country_name, state_name, city_name, location_ip, action) VALUES (u_id, in_device_type, in_country, in_state, in_city, in_ip_address, 'Login');

	 end;
    ELSE

        SELECT "failed" as res, "Something Went Wrong" as msg;

    END IF;

END$$

DROP PROCEDURE IF EXISTS `users_login_with_otp_verify`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_login_with_otp_verify` (IN `in_mobile_no` VARCHAR(10), IN `in_otp_no` INT(6), IN `in_current_date` DATETIME, IN `in_country` VARCHAR(255), IN `in_state` VARCHAR(255), IN `in_city` VARCHAR(255), IN `in_ip_address` VARCHAR(255), IN `in_device_type` VARCHAR(255))  BEGIN

DECLARE in_id INT DEFAULT 0;

DECLARE u_id INT DEFAULT 0;

    IF exists(SELECT * FROM users_signin_otp_verify WHERE mobile = in_mobile_no AND mobile_otp = in_otp_no ORDER BY otp_sent_on DESC LIMIT 1) then

        SELECT id into in_id from users_signin_otp_verify WHERE mobile = in_mobile_no AND mobile_otp = in_otp_no ORDER BY otp_sent_on DESC LIMIT 1;

        SELECT user_id into u_id from users_signin_otp_verify WHERE mobile = in_mobile_no AND mobile_otp = in_otp_no ORDER BY otp_sent_on DESC LIMIT 1;

        IF exists(SELECT * FROM users_signin_otp_verify WHERE otp_expiry_on >= in_current_date AND id = in_id) then

            IF exists(SELECT * FROM users WHERE active = 1 AND id = u_id) then
 
                UPDATE users_signin_otp_verify SET otp_verify_status = 1, otp_verify_on = in_current_date, device_id = in_ip_address, device_details = in_device_type  WHERE id = in_id;

                INSERT INTO users_log_history(user_id, login_device, country_name, state_name, city_name, location_ip, action) VALUES (u_id, in_device_type, in_country, in_state, in_city, in_ip_address, 'Login');

                SELECT "success" as res, u.id,udp.name, u.username FROM users as u INNER JOIN users_details_profile as udp  WHERE u.id = udp.id;

            ELSE

                SELECT "failed" as res, "You have been blocked!" as msg;

            END IF;

        ELSE

            SELECT "failed" as res, "Exp OTP Number" as msg;

        END IF;

    ELSE

        SELECT "failed" as res, "Invalid OTP" as msg;

    END IF;

END$$

DROP PROCEDURE IF EXISTS `users_login_with_password`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_login_with_password` (IN `in_user` VARCHAR(255), IN `in_password` VARCHAR(255), IN `in_device` VARCHAR(255), IN `in_country` VARCHAR(255), IN `in_state` VARCHAR(255), IN `in_city` VARCHAR(255), IN `in_ip` VARCHAR(255))  BEGIN

  DECLARE u_id INT DEFAULT 0;

  if exists(select * from users where phone_no = in_user OR email = in_user and password = in_password) then

    SELECT id into u_id from users where phone_no= in_user OR email = in_user and password = in_password;

    if exists(SELECT * from users where active = 1 and id = u_id) then
            
      SELECT "success" as res, u.* from users as u where u.id = u_id;

      INSERT INTO users_log_history(user_id, login_device, country_name, state_name, city_name, location_ip, action) VALUES (u_id, in_device, in_country, in_state, in_city, in_ip, 'Login');                 
      
    else

      SELECT "failed" as res, "You have been Blocked!" as msg;

    end if;

  else

    SELECT "failed" as res, "Invalid Login!" as msg;

  end if;   

END$$

DROP PROCEDURE IF EXISTS `users_signup_otp_verify`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_signup_otp_verify` (IN `in_phone_no` VARCHAR(255), IN `in_ref_code` VARCHAR(255), IN `in_otp_code` VARCHAR(10), IN `in_current_date` DATETIME, IN `in_ip_address` VARCHAR(255), IN `in_device_type` VARCHAR(255), IN `in_country` VARCHAR(255), IN `in_state` VARCHAR(255), IN `in_city` VARCHAR(255), IN `in_user_ref_code` VARCHAR(255))  BEGIN

DECLARE in_id INT DEFAULT 0;
DECLARE u_id INT DEFAULT 0;
DECLARE username int;
DECLARE user_id_length int default 5;
DECLARE id_prefix varchar(15) default '';

DECLARE uname varchar(15) DEFAULT '';
DECLARE remaining_length int;

    IF exists(SELECT * FROM users_signup_otp_verify WHERE mobile = in_phone_no AND mobile_otp = in_otp_code ORDER BY otp_sent_on DESC LIMIT 1) then

        SELECT id into in_id from users_signup_otp_verify WHERE mobile = in_phone_no AND mobile_otp = in_otp_code ORDER BY otp_sent_on DESC LIMIT 1;

        IF exists(SELECT * FROM users_signup_otp_verify WHERE otp_expiry_on >= in_current_date AND id = in_id) then
   
            UPDATE users_signup_otp_verify SET otp_verify_status = 1, otp_verify_on = in_current_date, device_id = in_ip_address, device_details = in_device_type  WHERE id = in_id;
            
            begin

                ALTER TABLE users_id_generate AUTO_INCREMENT = 10000;

                INSERT INTO users_id_generate (assigned) VALUES (0);

                SELECT user_id INTO username FROM users_id_generate ORDER BY user_id DESC limit 1;

                set remaining_length=user_id_length-length(username);

                while remaining_length>0 DO

                    set id_prefix = concat(id_prefix,'0'); 
                    set remaining_length=remaining_length-1;

                end while;

                set uname = concat('001', id_prefix, username);

            end;

            if(in_ref_code = 'null' OR in_ref_code = 'undefined' OR in_ref_code = '') then
            
                INSERT INTO users (username, phone_no, phone_verify_status, ip_registration, refer_code) VALUES (uname, in_phone_no, 1, in_ip_address, in_user_ref_code);
              
            else
            
                INSERT INTO users (username, phone_no, phone_verify_status, ip_registration, reg_type, refer_code) VALUES (uname, in_phone_no, 1, in_ip_address, 'refer', in_user_ref_code);
               
            end if;
           
           SELECT LAST_INSERT_ID() into u_id;

           SELECT "success" as res, u.* FROM users as u WHERE u.id = u_id;
           
               INSERT INTO users_profile(user_id) VALUES (u_id);

        INSERT INTO users_log_history(user_id, login_device, country_name, state_name, city_name, action, location_ip, action) VALUES (u_id, in_device_type, in_country, in_state, in_city, in_ip_address, 'Login');

        ELSE

            SELECT "failed" as res, "Expiry OTP Code" as msg;

        END IF;

    ELSE

        SELECT "failed" as res, "Invalid OTP Code" as msg;

    END IF;

END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `check_user_active`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `check_user_active` (`in_user_id` INT) RETURNS BIT(1) BEGIN
	if exists(select id  from users where id=in_user_id and active=1) then
    begin
		return true;
    end;
    else
    begin
		return false;
    end;
    end if;
END$$

DROP FUNCTION IF EXISTS `check_user_email_verified`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `check_user_email_verified` (`in_user_id` INT) RETURNS BIT(1) BEGIN
	if exists(select id  from users where id=in_user_id and email_verify_status=1) then
    begin
		return true;
    end;
    else
    begin
		return false;
    end;
    end if;
END$$

DROP FUNCTION IF EXISTS `check_user_profile_complete`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `check_user_profile_complete` (`in_user_id` INT) RETURNS BIT(1) BEGIN
	if exists(select id  from users where id=in_user_id and profile_completed=1) then
    begin
		return true;
    end;
    else
    begin
		return false;
    end;
    end if;
END$$

DROP FUNCTION IF EXISTS `check_valuable_deposit_for_bonus`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `check_valuable_deposit_for_bonus` (`in_deposit_value` DECIMAL, `in_deposit_number` INT) RETURNS BIT(1) BEGIN
	
    if(select id from master_game_welcome_bonus where deposit_count=in_deposit_number and in_deposit_value >= min_value and in_deposit_value <= max_value limit 1) then
    begin
		return 1;
    end;
    else
    begin
		return 0;
    end;
    end if;
END$$

DROP FUNCTION IF EXISTS `check_valuable_deposit_for_referral_bonus`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `check_valuable_deposit_for_referral_bonus` (`in_deposit_value` DECIMAL, `in_user_tier` INT) RETURNS BIT(1) BEGIN
    if(select id from master_game_referral_bonus where user_tier_level=in_user_tier and in_deposit_value >= min_deposit_value and in_deposit_value <= max_deposit_value limit 1) then
    begin
		return true;
    end;
    else
    begin
		return false;
    end;
    end if;
END$$

DROP FUNCTION IF EXISTS `check_valuable_deposit_for_welcome_bonus`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `check_valuable_deposit_for_welcome_bonus` (`in_deposit_value` DECIMAL, `in_deposit_number` INT) RETURNS BIT(1) BEGIN
	
    if(select id from master_game_welcome_bonus where deposit_count=in_deposit_number and in_deposit_value >= min_value and in_deposit_value <= max_value limit 1) then
    begin
		return 1;
    end;
    else
    begin
		return 0;
    end;
    end if;
END$$

DROP FUNCTION IF EXISTS `get_deposit_count`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_deposit_count` (`in_user_id` INT) RETURNS INT(11) BEGIN
	declare deposit_count int default 0;
	select count(id) into deposit_count from  users_cash_chips_buy_history where user_id=in_user_id;
	RETURN deposit_count;
END$$

DROP FUNCTION IF EXISTS `get_referral_user_id`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_referral_user_id` (`in_user_id` INT) RETURNS INT(11) BEGIN
	declare ref_user_id int default 0;
	select ref_by into ref_user_id from users where id=in_user_id and active=1 order by id desc limit 1;
	return ref_user_id;
END$$

DROP FUNCTION IF EXISTS `get_tier_level`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_tier_level` (`in_user_id` INT) RETURNS INT(11) BEGIN
	declare tier_level int default 0;
	select user_tier_level into tier_level from users where id=in_user_id order by id desc limit 1;
	return tier_level;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(800) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone_no` varchar(10) NOT NULL,
  `role_id` int(5) NOT NULL DEFAULT 0,
  `active` int(5) NOT NULL DEFAULT 0,
  `time_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_out` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_restrict` enum('0','1') NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `username`, `password`, `email`, `phone_no`, `role_id`, `active`, `time_in`, `time_out`, `ip_restrict`, `created_at`) VALUES
(1, 'Vinora', 'admin', 'C79GqgJFLlmq', 'testing@mail.com', '9876543210', 1, 1, '2023-07-09 20:32:05', '0000-00-00 00:00:00', '0', '2023-05-29 09:27:40'),
(2, 'Client', 'Cadmin', 'KbpPgy80L1g=', 'test@gmail.com', '9874562130', 2, 1, '2023-05-29 19:27:17', '0000-00-00 00:00:00', '0', '2023-05-29 13:13:41');

-- --------------------------------------------------------

--
-- Table structure for table `admin_ip_list`
--

DROP TABLE IF EXISTS `admin_ip_list`;
CREATE TABLE IF NOT EXISTS `admin_ip_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_ip_list`
--

INSERT INTO `admin_ip_list` (`id`, `admin_id`, `ip_address`, `created_at`) VALUES
(1, 1, '135.89.1.63', '2023-05-29 12:06:42'),
(4, 2, '192.168.1.51', '2023-05-29 17:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `admin_log_history`
--

DROP TABLE IF EXISTS `admin_log_history`;
CREATE TABLE IF NOT EXISTS `admin_log_history` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `admin_id` int(255) NOT NULL,
  `login_device` text NOT NULL,
  `action` varchar(16) NOT NULL DEFAULT 'login',
  `location_ip` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_log_history`
--

INSERT INTO `admin_log_history` (`id`, `admin_id`, `login_device`, `action`, `location_ip`, `created_at`) VALUES
(1, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 10:08:08'),
(2, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 11:57:55'),
(3, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:00:50'),
(4, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:05:26'),
(5, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:06:54'),
(6, 1, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:09:26'),
(7, 1, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:10:07'),
(8, 1, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:10:14'),
(9, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:14:02'),
(10, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:14:10'),
(11, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:28:07'),
(12, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:28:16'),
(13, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:28:21'),
(14, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:28:22'),
(15, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 12:31:05'),
(16, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 12:31:11'),
(17, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:21:35'),
(18, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:21:49'),
(19, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:21:55'),
(20, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:22:34'),
(21, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:24:11'),
(22, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:25:00'),
(23, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 13:37:04'),
(24, 2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:45:16'),
(25, 2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 13:51:33'),
(26, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:51:34'),
(27, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 13:51:37'),
(28, 2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:51:58'),
(29, 2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-29 13:52:45'),
(30, 2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-29 13:57:16'),
(31, 2, 'PostmanRuntime/7.32.2', 'Login', '27.5.216.189', '2023-05-29 16:49:53'),
(32, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-30 08:24:38'),
(33, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-30 14:44:04'),
(34, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-30 14:44:06'),
(35, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Logout', '127.0.0.1', '2023-05-30 16:09:40'),
(36, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-30 16:09:41'),
(37, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36', 'Login', '127.0.0.1', '2023-05-31 07:48:48'),
(38, 2, 'PostmanRuntime/7.32.2', 'Login', '27.5.216.189', '2023-05-31 08:17:56'),
(39, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36', 'Login', '27.5.216.189', '2023-07-02 07:53:54'),
(40, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36', 'Login', '27.5.216.189', '2023-07-02 14:59:20'),
(41, 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36', 'Login', '27.5.216.189', '2023-07-09 15:02:05');

-- --------------------------------------------------------

--
-- Table structure for table `create_regular_room_tables`
--

DROP TABLE IF EXISTS `create_regular_room_tables`;
CREATE TABLE IF NOT EXISTS `create_regular_room_tables` (
  `table_id` varchar(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `total_players` int(5) DEFAULT 0,
  `total_chips` decimal(7,2) DEFAULT 0.00,
  `total_rejoin` int(2) DEFAULT 0,
  `total_rounds` int(2) DEFAULT 0,
  `total_win_chips` decimal(7,2) DEFAULT 0.00,
  `total_commision_chips` decimal(7,2) DEFAULT 0.00,
  `status` enum('create','destroy','running','finished') NOT NULL DEFAULT 'create',
  `createtime` datetime DEFAULT current_timestamp(),
  `endtime` datetime DEFAULT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `create_regular_room_tables`
--

INSERT INTO `create_regular_room_tables` (`table_id`, `room_id`, `total_players`, `total_chips`, `total_rejoin`, `total_rounds`, `total_win_chips`, `total_commision_chips`, `status`, `createtime`, `endtime`) VALUES
('0000000037', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-14 16:42:16', '2023-06-14 16:50:30'),
('0000000038', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'destroy', '2023-06-14 17:00:25', NULL),
('0000000039', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-14 17:04:25', '2023-06-14 17:12:14'),
('0000000040', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-14 22:14:19', '2023-06-14 22:26:28'),
('0000000041', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-14 23:23:58', '2023-06-14 23:31:33'),
('0000000042', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'destroy', '2023-06-14 23:34:32', NULL),
('0000000043', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-14 23:38:04', '2023-06-14 23:44:15'),
('0000000044', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-15 00:05:49', '2023-06-15 00:13:39'),
('0000000045', 1, 1, '0.00', 0, 0, '0.00', '0.00', 'destroy', '2023-06-15 00:51:58', NULL),
('0000000046', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-15 00:53:38', '2023-06-15 01:01:29'),
('0000000047', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-15 01:05:54', '2023-06-15 01:25:18'),
('0000000048', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-15 16:38:09', '2023-06-15 16:59:31'),
('0000000049', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-15 17:08:46', '2023-06-15 17:14:55'),
('0000000050', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-15 20:46:06', '2023-06-15 20:51:57'),
('0000000051', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-16 00:34:51', '2023-06-16 00:45:05'),
('0000000052', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-19 15:39:18', '2023-06-19 15:46:18'),
('0000000053', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-19 15:52:55', '2023-06-19 16:00:16'),
('0000000054', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-22 00:24:45', '2023-06-22 00:43:21'),
('0000000055', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-22 00:45:42', '2023-06-22 00:52:57'),
('0000000056', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-22 00:59:37', '2023-06-22 01:05:37'),
('0000000057', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-24 23:25:37', '2023-06-24 23:32:26'),
('0000000058', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-25 00:11:21', '2023-06-25 00:20:12'),
('0000000059', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-25 00:51:57', '2023-06-25 01:01:11'),
('0000000060', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-25 09:24:27', '2023-06-25 09:34:53'),
('0000000061', 21, 2, '0.00', 0, 0, '0.00', '0.00', 'destroy', '2023-06-25 10:29:14', NULL),
('0000000062', 1, 2, '0.00', 0, 0, '0.00', '0.00', 'finished', '2023-06-26 22:05:06', '2023-06-26 22:13:16');

--
-- Triggers `create_regular_room_tables`
--
DROP TRIGGER IF EXISTS `user_table_status_update`;
DELIMITER $$
CREATE TRIGGER `user_table_status_update` BEFORE UPDATE ON `create_regular_room_tables` FOR EACH ROW IF (NEW.status = 'run') THEN		
		UPDATE users_table SET status = 'play' WHERE table_id = NEW.table_id AND status = 'join'; 
    END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `game_bonus_deposit`
--

DROP TABLE IF EXISTS `game_bonus_deposit`;
CREATE TABLE IF NOT EXISTS `game_bonus_deposit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_id` tinyint(4) NOT NULL,
  `deposit_count` int(11) NOT NULL,
  `min_chips` bigint(20) NOT NULL,
  `max_chips` bigint(20) NOT NULL,
  `min_credit_percentage` int(11) NOT NULL,
  `max_allowed_chips` bigint(20) NOT NULL,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime NOT NULL DEFAULT current_timestamp(),
  `bonus_chips` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `bonus_description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_bonus_deposit`
--

INSERT INTO `game_bonus_deposit` (`id`, `bonus_id`, `deposit_count`, `min_chips`, `max_chips`, `min_credit_percentage`, `max_allowed_chips`, `start_date`, `end_date`, `bonus_chips`, `is_active`, `is_delete`, `bonus_description`) VALUES
(1, 1, 1, 25, 100, 10, 100, '2023-01-18 11:29:45', '2023-01-18 11:29:45', 50, 0, 0, 'this is a deposit bonus'),
(2, 1, 2, 100, 500, 10, 100, '2023-01-18 11:29:49', '2023-01-18 11:29:49', 100, 0, 0, 'this is a deposit bonus');

-- --------------------------------------------------------

--
-- Table structure for table `game_bonus_referral`
--

DROP TABLE IF EXISTS `game_bonus_referral`;
CREATE TABLE IF NOT EXISTS `game_bonus_referral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_id` tinytext NOT NULL,
  `bonus_description` varchar(128) DEFAULT NULL,
  `chips` bigint(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_bonus_referral`
--

INSERT INTO `game_bonus_referral` (`id`, `bonus_id`, `bonus_description`, `chips`, `is_active`, `is_deleted`) VALUES
(1, '2', 'bonus', 1200, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `game_bonus_welcome`
--

DROP TABLE IF EXISTS `game_bonus_welcome`;
CREATE TABLE IF NOT EXISTS `game_bonus_welcome` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deposit_number` int(11) NOT NULL,
  `bonus_type` int(11) NOT NULL,
  `start_cash` decimal(11,2) NOT NULL,
  `end_cash` decimal(11,2) NOT NULL,
  `bonus_percentage` int(11) NOT NULL,
  `maximum_bonus` int(11) NOT NULL,
  `instant_cash_percentage` int(11) NOT NULL,
  `maximum_instant` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  `order_by` int(11) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  `added_on` int(11) NOT NULL,
  `last_updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_bonus_welcome`
--

INSERT INTO `game_bonus_welcome` (`id`, `deposit_number`, `bonus_type`, `start_cash`, `end_cash`, `bonus_percentage`, `maximum_bonus`, `instant_cash_percentage`, `maximum_instant`, `is_active`, `is_deleted`, `order_by`, `added_by`, `last_update`, `added_on`, `last_updated_on`) VALUES
(1, 1, 1, '25.00', '99.00', 100, 600, 5, 100, 1, 0, NULL, 1, '2023-01-12 09:35:15', 1, '2023-01-12 09:35:15'),
(2, 2, 1, '25.00', '99.00', 100, 600, 5, 100, 1, 0, NULL, 1, '2023-01-12 09:35:15', 1, '2023-01-12 09:35:15'),
(3, 3, 2, '50.00', '250.00', 100, 600, 10, 100, 1, 1, 2, 2, '2023-01-12 09:35:17', 2, '2023-01-12 09:35:17'),
(4, 2, 3, '50.00', '250.00', 100, 600, 10, 100, 1, 0, 1, 2, '2023-01-12 09:35:15', 2, '2023-01-12 09:35:15'),
(5, 2, 3, '50.00', '250.00', 100, 600, 10, 100, 1, 1, 1, 2, '2023-01-12 09:35:15', 2, '2023-01-12 09:35:15'),
(6, 3, 2, '50.00', '250.00', 100, 600, 10, 100, 1, 0, 2, 2, '2023-01-12 09:35:17', 2, '2023-01-12 09:35:17'),
(7, 3, 2, '50.00', '250.00', 100, 600, 10, 100, 1, 1, 2, 2, '2023-01-12 09:35:17', 2, '2023-01-12 09:35:17');

-- --------------------------------------------------------

--
-- Table structure for table `game_contact`
--

DROP TABLE IF EXISTS `game_contact`;
CREATE TABLE IF NOT EXISTS `game_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(26) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `message` longtext NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_contact`
--

INSERT INTO `game_contact` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Barath', 'vivekanandar068@gmail.com', 'dfsdf', 'sddd', '0', '2022-12-09 05:38:36'),
(2, 'lavanya', 'vivekanandar068@gmail.com', 'dfsdf', 'sddd', '0', '2022-12-08 05:38:36'),
(5, 'test', 'tset@gmail.com', 'tset', 'test', '0', '2023-01-14 07:43:32'),
(6, 'test', 'tset@gmail.com', 'tset', 'test', '0', '2023-01-14 07:48:48'),
(7, 'test', 'tset@gmail.com', 'tset', 'test', '0', '2023-01-14 07:51:20'),
(8, 'test', 'tset@gmail.com', 'tset', 'test', '0', '2023-01-18 05:25:23');

-- --------------------------------------------------------

--
-- Table structure for table `game_coupons`
--

DROP TABLE IF EXISTS `game_coupons`;
CREATE TABLE IF NOT EXISTS `game_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_title` varchar(255) NOT NULL,
  `coupon_code` varchar(255) NOT NULL,
  `valid_from_date` datetime NOT NULL,
  `valid_to_date` datetime NOT NULL,
  `bonus_type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `bonus_value` int(11) NOT NULL,
  `max_price` int(11) NOT NULL,
  `reusable` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_coupons`
--

INSERT INTO `game_coupons` (`id`, `coupon_title`, `coupon_code`, `valid_from_date`, `valid_to_date`, `bonus_type`, `bonus_value`, `max_price`, `reusable`, `created_at`) VALUES
(1, 'test', 'MDFDNDJ123', '2022-12-31 11:38:55', '2023-01-02 11:38:55', 'percent', 5, 50, 0, '2022-12-31 06:08:55'),
(2, 'demo1', 'MASSJDA', '2022-12-31 11:43:40', '2023-01-03 11:43:40', 'percent', 5, 80, 0, '2022-12-31 06:13:40'),
(3, 'demo2', 'GHFDJSDFN', '2022-12-31 11:43:40', '2023-01-04 11:43:40', 'fixed', 6, 98, 1, '2022-12-31 06:13:40'),
(4, 'demoz', 'MSJDSOD', '2022-12-31 11:43:40', '2023-01-03 11:43:40', 'percent', 5, 88, 1, '2022-12-31 06:51:32'),
(5, 'demoz', 'MSJDSOD', '2022-12-31 11:43:40', '2023-01-03 11:43:40', 'percent', 5, 88, 1, '2022-12-31 06:52:31'),
(8, 'jason', 'MSJDSOdD', '2022-12-31 11:43:40', '2023-01-03 11:43:40', 'percent', 5, 885, 1, '2023-01-18 09:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `game_room_cash`
--

DROP TABLE IF EXISTS `game_room_cash`;
CREATE TABLE IF NOT EXISTS `game_room_cash` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `game_table_id` int(11) NOT NULL,
  `joker_type` enum('joker','nojoker') NOT NULL DEFAULT 'joker',
  `deck` enum('1','2','3') NOT NULL DEFAULT '2',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_room_cash`
--

INSERT INTO `game_room_cash` (`id`, `game_table_id`, `joker_type`, `deck`, `active`, `created_at`) VALUES
(1, 1, 'joker', '2', '1', '2023-01-04 06:52:35'),
(2, 1, 'joker', '3', '1', '2023-01-05 11:08:02'),
(5, 2, 'nojoker', '2', '1', '2023-01-10 09:46:22'),
(7, 2, 'nojoker', '2', '1', '2023-01-10 10:09:02'),
(10, 30, 'joker', '3', '1', '2023-01-19 08:36:06');

-- --------------------------------------------------------

--
-- Table structure for table `game_room_cash_play_history`
--

DROP TABLE IF EXISTS `game_room_cash_play_history`;
CREATE TABLE IF NOT EXISTS `game_room_cash_play_history` (
  `id` bigint(20) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_bet` int(11) NOT NULL,
  `win_point` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_room_club`
--

DROP TABLE IF EXISTS `game_room_club`;
CREATE TABLE IF NOT EXISTS `game_room_club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_table_id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status` enum('0','1','2') NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_room_club`
--

INSERT INTO `game_room_club` (`id`, `game_table_id`, `code`, `status`, `user_id`, `created_at`) VALUES
(1, 32, '7SBAR01', '0', 4, '2023-01-23 06:58:32'),
(2, 32, '7SBAR02', '0', 118, '2023-01-23 07:34:07'),
(3, 32, '7SBAR03', '0', 118, '2023-01-23 07:34:47'),
(4, 32, '7SBAR04', '0', 118, '2023-01-23 07:35:08'),
(5, 32, '7SBAR05', '0', 118, '2023-01-23 07:35:41'),
(6, 32, '7SBAR06', '0', 118, '2023-01-23 07:35:59'),
(7, 32, '7SBAR07', '0', 118, '2023-01-23 10:37:31'),
(8, 34, '7SBAR08', '0', 4, '2023-02-13 10:15:31');

-- --------------------------------------------------------

--
-- Table structure for table `game_room_free`
--

DROP TABLE IF EXISTS `game_room_free`;
CREATE TABLE IF NOT EXISTS `game_room_free` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `game_table_id` int(11) NOT NULL,
  `joker_type` enum('joker','nojoker') NOT NULL DEFAULT 'joker',
  `deck` enum('1','2','3') NOT NULL DEFAULT '2',
  `total_bet` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_room_free`
--

INSERT INTO `game_room_free` (`id`, `game_table_id`, `joker_type`, `deck`, `total_bet`, `active`, `created_at`) VALUES
(1, 1, 'joker', '2', 45, '1', '2023-01-06 07:51:11'),
(2, 2, 'nojoker', '2', 35, '0', '2023-01-06 07:51:39'),
(7, 2, 'nojoker', '3', 85, '1', '2023-02-13 12:30:39');

-- --------------------------------------------------------

--
-- Table structure for table `game_room_free_play_history`
--

DROP TABLE IF EXISTS `game_room_free_play_history`;
CREATE TABLE IF NOT EXISTS `game_room_free_play_history` (
  `id` bigint(20) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_bet` int(11) NOT NULL,
  `win_point` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_room_tourney`
--

DROP TABLE IF EXISTS `game_room_tourney`;
CREATE TABLE IF NOT EXISTS `game_room_tourney` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `start_time` time NOT NULL,
  `reg_start_date` date NOT NULL,
  `reg_start_time` time NOT NULL,
  `reg_end_date` date NOT NULL,
  `reg_end_time` time NOT NULL,
  `price_amount` int(11) NOT NULL,
  `game_table_id` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0-upcoming,1-live,2-end',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_room_tourney`
--

INSERT INTO `game_room_tourney` (`id`, `title`, `start_date`, `start_time`, `reg_start_date`, `reg_start_time`, `reg_end_date`, `reg_end_time`, `price_amount`, `game_table_id`, `active`, `status`, `created_at`) VALUES
(11, 'Title', '2023-01-07', '10:09:40', '2023-01-07', '10:09:40', '2023-01-07', '10:09:40', 100, 31, '1', '0', '2023-02-13 12:34:20'),
(7, 'Title', '2023-01-10', '10:09:40', '2023-01-07', '10:09:40', '2023-01-07', '10:09:40', 100, 1, '1', '2', '2023-01-10 06:41:27'),
(8, 'deal_rummy', '2022-12-12', '14:29:23', '2022-12-12', '14:29:23', '2022-12-14', '14:29:23', 200, 2, '1', '1', '2023-01-10 07:32:39'),
(9, 'pool_rummy', '2022-12-12', '14:29:23', '2022-12-12', '14:29:23', '2022-12-14', '14:29:23', 200, 1, '1', '0', '2023-01-18 13:14:13');

-- --------------------------------------------------------

--
-- Table structure for table `game_room_tourney_play_history`
--

DROP TABLE IF EXISTS `game_room_tourney_play_history`;
CREATE TABLE IF NOT EXISTS `game_room_tourney_play_history` (
  `id` bigint(20) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_bet` int(11) NOT NULL,
  `win_point` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `haoda_payout_response`
--

DROP TABLE IF EXISTS `haoda_payout_response`;
CREATE TABLE IF NOT EXISTS `haoda_payout_response` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `payout_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `transfer_date` varchar(255) DEFAULT NULL,
  `beneficiary_bank_name` varchar(255) DEFAULT NULL,
  `beneficiary_account_ifsc` varchar(255) DEFAULT NULL,
  `beneficiary_account_name` varchar(255) DEFAULT NULL,
  `beneficiary_account_number` varchar(255) DEFAULT NULL,
  `beneficiary_upi_handle` varchar(255) DEFAULT NULL,
  `UTR` varchar(255) DEFAULT NULL,
  `created_at_timestamp` datetime DEFAULT NULL,
  `transfer_date_timestamp` datetime DEFAULT NULL,
  `current_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `haoda_payout_response`
--

INSERT INTO `haoda_payout_response` (`id`, `payout_id`, `user_id`, `order_id`, `amount`, `remarks`, `created_at`, `payment_mode`, `transfer_date`, `beneficiary_bank_name`, `beneficiary_account_ifsc`, `beneficiary_account_name`, `beneficiary_account_number`, `beneficiary_upi_handle`, `UTR`, `created_at_timestamp`, `transfer_date_timestamp`, `current_status`) VALUES
(1, 'HOAD974138602500', 4, 136, '10.00', 'Withdraw from Test', '2022-07-10 08:03:55', 'IMPS', '2022-07-10T08:03:55.', 'test bank', 'TEST0123', 'Test', '1234567890', 'Null', '12345678', NULL, NULL, 'success'),
(2, NULL, 4, 137, '10.00', 'user_cash_withdraw', NULL, NULL, NULL, 'SBI', 'SBI01TJHKF', 'Jack', '125545712454', NULL, NULL, NULL, NULL, NULL),
(3, NULL, 4, 138, '6.00', 'user_cash_withdraw', NULL, NULL, NULL, 'SBI', 'SBI01TJHKF', 'Jack', '125545712454', NULL, NULL, NULL, NULL, NULL),
(4, NULL, 4, 139, '6.00', 'user_cash_withdraw', NULL, NULL, NULL, 'SBI', 'SBI01TJHKF', 'Jack', '125545712454', NULL, NULL, NULL, NULL, NULL),
(5, 'HOAD974138602500', 4, 140, '10.00', 'Withdraw from Test', '2022-07-10 08:03:55', 'IMPS', '2022-07-10T08:03:55.', 'test bank', 'TEST0123', 'Test', '1234567890', 'Null', '12345678', NULL, NULL, 'success'),
(6, 'HOAD974138602500', 4, 141, '6.00', 'user_cash_withdraw', NULL, NULL, NULL, 'SBI', 'SBI01TJHKF', 'Jack', '125545712454', NULL, NULL, NULL, NULL, 'Processing'),
(7, NULL, 4, 142, '6.00', 'user_cash_withdraw', NULL, NULL, NULL, 'SBI', 'SBI01TJHKF', 'Jack', '125545712454', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `haoda_payout_response_history`
--

DROP TABLE IF EXISTS `haoda_payout_response_history`;
CREATE TABLE IF NOT EXISTS `haoda_payout_response_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_type` varchar(255) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `res_str` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `haoda_payout_response_history`
--

INSERT INTO `haoda_payout_response_history` (`id`, `order_id`, `order_type`, `order_status`, `res_str`) VALUES
(1, 136, 'Withdraw', 'Processing', '{\"status_code\": \"200\", \"status\": \"Processing\", \"message\": \"Kindly allow some time for the payout to process\",\"payout_id\": \"HOAD974138602500\"}'),
(2, 136, 'Withdraw', 'Success', '{\"event\":\"TRANSFER_STATUS_UPDATE\",\"status\":\"success\",\"data\":{\"amount\":\"10\",\"remarks\":\"Withdraw from Test\",\"created_at\":\"2022-07-10T08:03:55.\",\"payment_mode\":\"IMPS\",\"transfer_date\":\"2022-07-10T08:03:55.\",\"beneficiary_bank_name\":\"test bank\",\"payout_id\":\"HOAD974138602500\",\"beneficiary_account_ifsc\":\"TEST0123\",\"beneficiary_account_name\":\"Test\",\"beneficiary_account_number\":\"1234567890\",\"beneficiary_upi_handle\":\"Null\",\"UTR\":\"12345678\"}}'),
(3, 136, 'Withdraw', 'Success', '{\"event\":\"TRANSFER_STATUS_UPDATE\",\"status\":\"success\",\"data\":{\"amount\":\"10\",\"remarks\":\"Withdraw from Test\",\"created_at\":\"2022-07-10T08:03:55.\",\"payment_mode\":\"IMPS\",\"transfer_date\":\"2022-07-10T08:03:55.\",\"beneficiary_bank_name\":\"test bank\",\"payout_id\":\"HOAD974138602500\",\"beneficiary_account_ifsc\":\"TEST0123\",\"beneficiary_account_name\":\"Test\",\"beneficiary_account_number\":\"1234567890\",\"beneficiary_upi_handle\":\"Null\",\"UTR\":\"12345678\"}}'),
(4, 136, 'Withdraw', 'Success', '{\"event\":\"TRANSFER_STATUS_UPDATE\",\"status\":\"success\",\"data\":{\"amount\":\"10\",\"remarks\":\"Withdraw from Test\",\"created_at\":\"2022-07-10T08:03:55.\",\"payment_mode\":\"IMPS\",\"transfer_date\":\"2022-07-10T08:03:55.\",\"beneficiary_bank_name\":\"test bank\",\"payout_id\":\"HOAD974138602500\",\"beneficiary_account_ifsc\":\"TEST0123\",\"beneficiary_account_name\":\"Test\",\"beneficiary_account_number\":\"1234567890\",\"beneficiary_upi_handle\":\"Null\",\"UTR\":\"12345678\"}}'),
(5, 136, 'Withdraw', 'Success', '{\"event\":\"TRANSFER_STATUS_UPDATE\",\"status\":\"success\",\"data\":{\"amount\":\"10\",\"remarks\":\"Withdraw from Test\",\"created_at\":\"2022-07-10T08:03:55.\",\"payment_mode\":\"IMPS\",\"transfer_date\":\"2022-07-10T08:03:55.\",\"beneficiary_bank_name\":\"test bank\",\"payout_id\":\"HOAD974138602500\",\"beneficiary_account_ifsc\":\"TEST0123\",\"beneficiary_account_name\":\"Test\",\"beneficiary_account_number\":\"1234567890\",\"beneficiary_upi_handle\":\"Null\",\"UTR\":\"12345678\"}}'),
(6, 140, 'Withdraw', 'Processing', '{\"status_code\": \"200\", \"status\": \"Processing\", \"message\": \"Kindly allow some time for the payout to process\",\"payout_id\": \"HOAD974138602500\"}'),
(7, 141, 'Withdraw', 'Processing', '{\"status_code\": \"200\", \"status\": \"Processing\", \"message\": \"Kindly allow some time for the payout to process\",\"payout_id\": \"HOAD974138602500\"}');

-- --------------------------------------------------------

--
-- Table structure for table `master_admin_roles`
--

DROP TABLE IF EXISTS `master_admin_roles`;
CREATE TABLE IF NOT EXISTS `master_admin_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_type` varchar(16) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `scope_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_admin_roles`
--

INSERT INTO `master_admin_roles` (`role_id`, `role_type`, `role_name`, `scope_list`) VALUES
(1, 'super_admin', 'Super Admin', '[\"get_dashboard\",\"get_admin\",\"add_admin\",\"edit_admin\",\"status_admin\",\"ip_status_admin\",\"delete_admin\",\"admin_log\",\"get_user\",\"status_user\",\"user_detail\",\"user_log\",\"user_bank_detail\",\"get_user_kyc_Verify\",\"status_kyc\",\"get_role\",\"add_role\",\"edit_role\",\"delete_role\",\"get_scope\",\"edit_scope\",\"get_game_type\",\"add_game_type\",\"edit_game_type\",\"status_game_type\",\"delete_game_type\",\"get_match_type\",\"add_match_type\",\"edit_match_type\",\"status_match_type\",\"delete_match_type\",\"get_game_table\",\"add_game_table\",\"edit_game_table\",\"status_game_table\",\"delete_game_table\",\"get_cash_table\",\"add_cash_table\",\"edit_cash_table\",\"status_cash_room\",\"delete_cash_table\",\"add_free_table\",\"edit_free_table\",\"status_free_room\",\"delete_free_table\",\"get_tourney\",\"add_tourney\",\"status_tourney\",\"get_withdraw_req\",\"status_withdraw\",\"get_config\",\"edit_config\",\"get_social_media\",\"edit_social_media\",\"get_faq\",\"add_faq\",\"edit_faq\",\"status_faq\",\"delete_faq\",\"get_news_list\",\"add_news_list\",\"edit_news_list\",\"status_news\",\"delete_news_list\",\"get_sms_templates\",\"edit_sms_templates\",\"get_mail_templates\",\"edit_mail_templates\",\"get_web_setting\",\"edit_web_setting\"]'),
(2, 'admin', 'Admin', '[\"get_dashboard\",\"get_admin\",\"add_admin\",\"edit_admin\",\"status_admin\",\"ip_status_admin\",\"delete_admin\",\"admin_log\",\"get_user\",\"status_user\",\"user_detail\",\"user_log\",\"user_bank_detail\",\"get_user_kyc_Verify\",\"status_kyc\",\"get_role\",\"add_role\",\"edit_role\",\"delete_role\",\"get_scope\",\"edit_scope\",\"get_game_type\",\"add_game_type\",\"edit_game_type\",\"status_game_type\",\"delete_game_type\",\"get_match_type\",\"add_match_type\",\"edit_match_type\",\"status_match_type\",\"delete_match_type\",\"get_game_table\",\"add_game_table\",\"edit_game_table\",\"status_game_table\",\"delete_game_table\",\"get_cash_table\",\"add_cash_table\",\"edit_cash_table\",\"status_cash_room\",\"delete_cash_table\",\"add_free_table\",\"edit_free_table\",\"status_free_room\",\"delete_free_table\",\"get_tourney\",\"add_tourney\",\"status_tourney\",\"get_withdraw_req\",\"status_withdraw\",\"get_config\",\"edit_config\",\"get_social_media\",\"edit_social_media\",\"get_faq\",\"add_faq\",\"edit_faq\",\"status_faq\",\"delete_faq\",\"get_news_list\",\"add_news_list\",\"edit_news_list\",\"status_news\",\"delete_news_list\",\"get_sms_templates\",\"edit_sms_templates\",\"get_mail_templates\",\"edit_mail_templates\",\"get_web_setting\",\"edit_web_setting\"]');

-- --------------------------------------------------------

--
-- Table structure for table `master_admin_roles_scope`
--

DROP TABLE IF EXISTS `master_admin_roles_scope`;
CREATE TABLE IF NOT EXISTS `master_admin_roles_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope_group` varchar(255) NOT NULL,
  `scope_name` varchar(50) NOT NULL,
  `scope` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_admin_roles_scope`
--

INSERT INTO `master_admin_roles_scope` (`id`, `scope_group`, `scope_name`, `scope`) VALUES
(1, 'Dashboard', 'View Dashboard', 'get_dashboard'),
(2, 'Admin', 'View Admin', 'get_admin'),
(3, 'Admin', 'Add Admin', 'add_admin'),
(4, 'Admin', 'Edit Admin', 'edit_admin'),
(5, 'Admin', 'Change Active Status', 'status_admin'),
(6, 'Admin', 'IP Restict', 'ip_status_admin'),
(7, 'Admin', 'Delete Admin', 'delete_admin'),
(8, 'Admin', 'Admin Log', 'admin_log'),
(9, 'Player', 'View user', 'get_user'),
(10, 'Player', 'Change Active Status', 'status_user'),
(11, 'Player', 'Detail View', 'user_detail'),
(12, 'Player', 'Users Log Detail', 'user_log'),
(13, 'Player', 'User Bank Detail', 'user_bank_detail'),
(14, 'User KYC Verify', 'View KYC Detail', 'get_user_kyc_Verify'),
(15, 'User KYC Verify', 'Change KYC Status', 'status_kyc'),
(16, 'Roles', 'View Roles', 'get_role'),
(17, 'Roles', 'Add Role', 'add_role'),
(18, 'Roles', 'Edit Role', 'edit_role'),
(19, 'Roles', 'Delete Role', 'delete_role'),
(20, 'Roles', 'View Scope Permission', 'get_scope'),
(21, 'Roles', 'Edit Scope Permission', 'edit_scope'),
(22, 'Game Type', 'View Game Type', 'get_game_type'),
(23, 'Game Type', 'Add Game Type', 'add_game_type'),
(24, 'Game Type', 'Edit Game Type', 'edit_game_type'),
(25, 'Game Type', 'Change Status', 'status_game_type'),
(26, 'Game Type', 'Delete Game Type', 'delete_game_type'),
(27, 'Match Type', 'View Match Type', 'get_match_type'),
(28, 'Match Type', 'Add Match Type', 'add_match_type'),
(29, 'Match Type', 'Edit Match Type', 'edit_match_type'),
(30, 'Match Type', 'Change Status', 'status_match_type'),
(31, 'Match Type', 'Delete Match Type', 'delete_match_type'),
(32, 'Game Table', 'View Table', 'get_game_table'),
(33, 'Game Table', 'Add Table', 'add_game_table'),
(34, 'Game Table', 'Edit Game Table', 'edit_game_table'),
(35, 'Game Table', 'Change Status', 'status_game_table'),
(36, 'Game Table', 'Delete Table', 'delete_game_table'),
(37, 'Cash Room Table', 'View Cash Table', 'get_cash_table'),
(38, 'Cash Room Table', 'Add Cash Table', 'add_cash_table'),
(39, 'Cash Room Table', 'Edit Cash Table', 'edit_cash_table'),
(40, 'Cash Room Table', 'Change Status', 'status_cash_room'),
(41, 'Cash Room Table', 'Delete Cash Table', 'delete_cash_table'),
(42, 'Free Room Table', 'View Free Table', 'add_free_table'),
(43, 'Free Room Table', 'View Free Table', 'edit_free_table'),
(44, 'Free Room Table', 'Change Status', 'status_free_room'),
(45, 'Free Room Table', 'View Free Table', 'delete_free_table'),
(46, 'Tournaments', 'View Tournament ', 'get_tourney'),
(47, 'Tournaments', 'Add Tournament ', 'add_tourney'),
(48, 'Tournaments', 'Status Change', 'status_tourney'),
(49, 'Withdraw Request', 'View Withdraw', 'get_withdraw_req'),
(50, 'Withdraw Request', 'Change Status', 'status_withdraw'),
(51, 'Configuration', 'View Config', 'get_config'),
(52, 'Configuration', 'Edit Config', 'edit_config'),
(53, 'Social Media', 'View Social Media', 'get_social_media'),
(54, 'Social Media', 'Edit Social Media', 'edit_social_media'),
(55, 'FAQ\'s', 'View FAQ\'s', 'get_faq'),
(56, 'FAQ\'s', 'Add FAQ\'s', 'add_faq'),
(57, 'FAQ\'s', 'Edit FAQ\'s', 'edit_faq'),
(58, 'FAQ\'s', 'Change Status', 'status_faq'),
(59, 'FAQ\'s', 'Delete FAQ\'s', 'delete_faq'),
(60, 'News', 'View News', 'get_news_list'),
(61, 'News', 'Add News', 'add_news_list'),
(62, 'News', 'Edit News', 'edit_news_list'),
(63, 'News', 'Change Status', 'status_news'),
(64, 'News', 'Delete', 'delete_news_list'),
(65, 'SMS Templates', 'View SMS Templates', 'get_sms_templates'),
(66, 'SMS Templates', 'Edit SMSTemplates', 'edit_sms_templates'),
(67, 'Mail Templates', 'View Mail Templates', 'get_mail_templates'),
(68, 'Mail Templates', 'Edit Mail Templates', 'edit_mail_templates'),
(69, 'Web Setting', 'View Web Setting', 'get_web_setting'),
(70, 'Web Setting', 'Edit Web Setting', 'edit_web_setting');

-- --------------------------------------------------------

--
-- Table structure for table `master_config_ip`
--

DROP TABLE IF EXISTS `master_config_ip`;
CREATE TABLE IF NOT EXISTS `master_config_ip` (
  `game_ip_address` varchar(255) NOT NULL,
  `game_port_number` int(15) NOT NULL,
  `game_domain_name` varchar(255) NOT NULL,
  `tourney_ip_address` varchar(255) NOT NULL,
  `tourney_port_number` int(11) NOT NULL,
  `tourney_domain_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_config_ip`
--

INSERT INTO `master_config_ip` (`game_ip_address`, `game_port_number`, `game_domain_name`, `tourney_ip_address`, `tourney_port_number`, `tourney_domain_name`) VALUES
('161.97.154.118', 3010, 'http://rummysahara.com:3010', '161.97.154.118', 3011, 'http://rummysahara.com:3011');

-- --------------------------------------------------------

--
-- Table structure for table `master_config_mail`
--

DROP TABLE IF EXISTS `master_config_mail`;
CREATE TABLE IF NOT EXISTS `master_config_mail` (
  `sender_mail` varchar(255) NOT NULL,
  `from_name` varchar(50) NOT NULL,
  `smtp_host` varchar(255) NOT NULL,
  `smtp_type` enum('ssl','tls') NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_username` varchar(255) NOT NULL,
  `smtp_password` varchar(255) NOT NULL,
  `smtp_auth` enum('true','false') NOT NULL DEFAULT 'true'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_config_mail`
--

INSERT INTO `master_config_mail` (`sender_mail`, `from_name`, `smtp_host`, `smtp_type`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_auth`) VALUES
('lavanya@vinorastudios.in', '7S Rummys', 'smtp.gmail.com', 'tls', 587, 'vivekanandar068@gmail.com', 'crmdqphcgqmwepyf', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `master_config_sms`
--

DROP TABLE IF EXISTS `master_config_sms`;
CREATE TABLE IF NOT EXISTS `master_config_sms` (
  `username` varchar(255) NOT NULL,
  `password` varchar(800) NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `auth_key` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_config_sms`
--

INSERT INTO `master_config_sms` (`username`, `password`, `sender_id`, `auth_key`) VALUES
('smsConfig', '1234', 'BOXINF', 'm84GSq5lf1Q60bEDpMctUTsuwxByhXAPdNgJ2zO97jHRFi3Zar...');

-- --------------------------------------------------------

--
-- Table structure for table `master_config_social`
--

DROP TABLE IF EXISTS `master_config_social`;
CREATE TABLE IF NOT EXISTS `master_config_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `social_login_id` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'inactive' COMMENT 'active,inactive\r\n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_config_social`
--

INSERT INTO `master_config_social` (`id`, `social_login_id`, `version`, `status`) VALUES
(1, '322558735297480', 'v3.3', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `master_game_bonus_type`
--

DROP TABLE IF EXISTS `master_game_bonus_type`;
CREATE TABLE IF NOT EXISTS `master_game_bonus_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_bonus_type`
--

INSERT INTO `master_game_bonus_type` (`id`, `name`, `status`) VALUES
(1, 'Welcome Bonus', 1),
(2, 'Referral Bonus', 1),
(3, 'Game Bonus ', 0),
(4, 'LevelUp Bonus', 0),
(5, 'Daily Bonus ', 0),
(6, 'testing', 1),
(7, 'testing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `master_game_max_palyer`
--

DROP TABLE IF EXISTS `master_game_max_palyer`;
CREATE TABLE IF NOT EXISTS `master_game_max_palyer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_max_palyer`
--

INSERT INTO `master_game_max_palyer` (`id`, `player`, `created_at`) VALUES
(1, '2', '2023-04-27 07:54:21'),
(2, '6', '2023-04-27 07:54:21');

-- --------------------------------------------------------

--
-- Table structure for table `master_game_point_to_claim_bonus`
--

DROP TABLE IF EXISTS `master_game_point_to_claim_bonus`;
CREATE TABLE IF NOT EXISTS `master_game_point_to_claim_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_value` int(11) NOT NULL,
  `bonus` int(11) NOT NULL,
  `min_deposit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_point_to_claim_bonus`
--

INSERT INTO `master_game_point_to_claim_bonus` (`id`, `point_value`, `bonus`, `min_deposit`) VALUES
(1, 1000, 100, 500),
(2, 2000, 200, 1000),
(3, 3000, 300, 2000),
(4, 5000, 500, 5000),
(5, 10000, 1000, 10000),
(6, 20000, 2000, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `master_game_referral_bonus`
--

DROP TABLE IF EXISTS `master_game_referral_bonus`;
CREATE TABLE IF NOT EXISTS `master_game_referral_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_id` int(11) NOT NULL,
  `user_tier_level` int(11) NOT NULL,
  `min_deposit_value` decimal(12,2) NOT NULL,
  `max_deposit_value` decimal(12,2) NOT NULL,
  `bonus_self` decimal(12,2) NOT NULL,
  `bonus_ref` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_referral_bonus`
--

INSERT INTO `master_game_referral_bonus` (`id`, `bonus_id`, `user_tier_level`, `min_deposit_value`, `max_deposit_value`, `bonus_self`, `bonus_ref`) VALUES
(1, 2, 0, '0.00', '299.00', '100.00', '100.00'),
(2, 2, 0, '300.00', '699.00', '150.00', '150.00'),
(3, 2, 0, '700.00', '9999.00', '200.00', '200.00'),
(4, 2, 0, '10000.00', '29999.00', '700.00', '700.00'),
(5, 2, 0, '30000.00', '49999.00', '1000.00', '1000.00'),
(6, 2, 0, '50000.00', '99999.00', '2500.00', '2500.00'),
(7, 2, 0, '100000.00', '100000000.00', '5350.00', '5350.00'),
(8, 2, 1, '0.00', '299.00', '100.00', '100.00'),
(9, 2, 1, '300.00', '699.00', '150.00', '150.00'),
(10, 2, 1, '700.00', '9999.00', '200.00', '200.00'),
(11, 2, 1, '10000.00', '29999.00', '700.00', '700.00'),
(12, 2, 1, '30000.00', '49999.00', '1000.00', '1000.00'),
(13, 2, 1, '50000.00', '99999.00', '2500.00', '2500.00'),
(14, 2, 1, '100000.00', '100000000.00', '5350.00', '5350.00'),
(15, 2, 2, '0.00', '0.00', '100.00', '100.00'),
(16, 2, 2, '300.00', '0.00', '150.00', '150.00'),
(17, 2, 2, '700.00', '0.00', '200.00', '200.00'),
(18, 2, 2, '10000.00', '0.00', '700.00', '700.00'),
(19, 2, 2, '30000.00', '0.00', '1000.00', '1000.00'),
(20, 2, 2, '50000.00', '0.00', '2500.00', '2500.00'),
(21, 2, 2, '100000.00', '0.00', '100.00', '100.00'),
(22, 2, 0, '300.00', '0.00', '150.00', '150.00'),
(23, 2, 0, '700.00', '0.00', '200.00', '200.00'),
(24, 2, 0, '10000.00', '0.00', '700.00', '700.00'),
(25, 2, 0, '30000.00', '0.00', '1000.00', '1000.00'),
(26, 2, 0, '50000.00', '0.00', '2500.00', '2500.00'),
(27, 2, 0, '100000.00', '0.00', '5350.00', '5350.00'),
(28, 2, 1, '0.00', '0.00', '100.00', '100.00'),
(29, 2, 1, '300.00', '0.00', '150.00', '150.00'),
(30, 2, 1, '700.00', '0.00', '200.00', '200.00'),
(31, 2, 1, '10000.00', '0.00', '700.00', '700.00'),
(32, 2, 1, '30000.00', '0.00', '1000.00', '1000.00'),
(33, 2, 1, '50000.00', '0.00', '2500.00', '2500.00'),
(34, 2, 1, '100000.00', '0.00', '5350.00', '5350.00'),
(35, 2, 2, '0.00', '0.00', '100.00', '100.00'),
(36, 2, 2, '300.00', '0.00', '150.00', '150.00'),
(37, 2, 2, '700.00', '0.00', '200.00', '200.00'),
(38, 2, 2, '10000.00', '0.00', '700.00', '700.00'),
(39, 2, 2, '30000.00', '0.00', '1000.00', '1000.00'),
(40, 2, 2, '50000.00', '0.00', '2500.00', '2500.00'),
(41, 2, 2, '100000.00', '0.00', '100.00', '100.00');

-- --------------------------------------------------------

--
-- Table structure for table `master_game_rummy_tourney`
--

DROP TABLE IF EXISTS `master_game_rummy_tourney`;
CREATE TABLE IF NOT EXISTS `master_game_rummy_tourney` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_game_rummy_tourney_entry`
--

DROP TABLE IF EXISTS `master_game_rummy_tourney_entry`;
CREATE TABLE IF NOT EXISTS `master_game_rummy_tourney_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tourney_type_id` int(11) NOT NULL,
  `rummy_type_id` int(11) NOT NULL,
  `entry_fees` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_game_states`
--

DROP TABLE IF EXISTS `master_game_states`;
CREATE TABLE IF NOT EXISTS `master_game_states` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `state_code` varchar(255) NOT NULL,
  `state_name` varchar(255) NOT NULL,
  `user_alert_message` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_states`
--

INSERT INTO `master_game_states` (`id`, `state_code`, `state_name`, `user_alert_message`, `status`) VALUES
(1, 'TN', 'Tamil Nadu', 'tamil nadu ', '1'),
(2, 'AP', 'Andhra Pradesh', 'andhra', '1'),
(3, 'AR', 'Arunachal Pradesh', NULL, '1'),
(4, 'AS', 'Assam', NULL, '1'),
(5, 'BR', 'Bihar', NULL, '1'),
(6, 'CG', 'Chhattisgarh', NULL, '1'),
(7, 'GA', 'Goa', NULL, '1'),
(8, 'GJ', 'Gujarat', NULL, '1'),
(9, 'HR', 'Haryana', NULL, '1'),
(10, 'HP', 'Himachal Pradesh', NULL, '1'),
(11, 'JK', 'Jammu and Kashmir\n', NULL, '1'),
(12, 'JH', 'Jharkhand', NULL, '1'),
(13, 'KA', 'Karnataka', NULL, '1'),
(14, 'KL', 'Kerala', NULL, '1'),
(15, 'MP', 'Madhya Pradesh', NULL, '1'),
(16, 'MH', 'Maharashtra', NULL, '1'),
(17, 'MN', 'Manipur', NULL, '1'),
(18, 'ML', 'Meghalaya', NULL, '1'),
(19, 'MZ', 'Mizoram', NULL, '1'),
(20, 'NL', 'Nagaland', NULL, '1'),
(21, 'OR', 'Orissa', NULL, '1'),
(22, 'PB', 'Punjab', NULL, '1'),
(23, 'RJ', 'Rajasthan', NULL, '1'),
(24, 'SK', 'Sikkim', NULL, '1'),
(25, 'TR', 'Tripura', NULL, '1'),
(26, 'UK', 'Uttarakhand', NULL, '1'),
(27, 'UP', 'Uttar Pradesh', NULL, '1'),
(28, 'WB', 'West Bengal', NULL, '1'),
(29, 'AN', 'Andaman and Nicobar Islands', NULL, '1'),
(30, 'CH', 'Chandigarh', NULL, '1'),
(31, 'DH', 'Dadra and Nagar Haveli\n', NULL, '1'),
(32, 'DD', 'Daman and Diu\n', NULL, '1'),
(33, 'DL', 'Delhi', NULL, '1'),
(34, 'LD', 'Lakshadweep', NULL, '1'),
(35, 'PY', 'Pondicherry', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `master_game_tables`
--

DROP TABLE IF EXISTS `master_game_tables`;
CREATE TABLE IF NOT EXISTS `master_game_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `max_player` int(11) NOT NULL,
  `entry_fees` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_tables`
--

INSERT INTO `master_game_tables` (`id`, `match_id`, `game_id`, `max_player`, `entry_fees`, `active`, `created_at`) VALUES
(1, 1, 1, 2, 70, '1', '2023-01-04 06:46:46'),
(2, 2, 3, 6, 10, '1', '2023-01-04 06:46:46'),
(6, 1, 1, 6, 20, '1', '2023-01-04 06:46:46'),
(27, 2, 3, 2, 50, '1', '2023-01-05 09:27:20'),
(31, 1, 1, 2, 20, '1', '2023-01-18 10:30:59'),
(30, 1, 4, 6, 10, '1', '2023-01-05 09:29:17'),
(32, 7, 5, 6, 20, '1', '2023-01-23 06:33:31');

-- --------------------------------------------------------

--
-- Table structure for table `master_game_tier_level`
--

DROP TABLE IF EXISTS `master_game_tier_level`;
CREATE TABLE IF NOT EXISTS `master_game_tier_level` (
  `id` int(11) NOT NULL,
  `tier_level` varchar(255) NOT NULL,
  `stars_earned` int(11) NOT NULL,
  `tier_name` varchar(255) NOT NULL,
  `min_value` varchar(255) NOT NULL,
  `max_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_tier_level`
--

INSERT INTO `master_game_tier_level` (`id`, `tier_level`, `stars_earned`, `tier_name`, `min_value`, `max_value`) VALUES
(1, 'bronze', 1, 'BRONZE', '0', '499'),
(2, 'silver', 2, 'SILVER', '500', '999'),
(3, 'gold', 3, 'GOLD', '1000', '7499'),
(4, 'diamond', 4, 'DIAMOND', '7500', '19999'),
(5, 'platinum', 5, 'PLATINUM', '20000', '');

-- --------------------------------------------------------

--
-- Table structure for table `master_game_welcome_bonus`
--

DROP TABLE IF EXISTS `master_game_welcome_bonus`;
CREATE TABLE IF NOT EXISTS `master_game_welcome_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_id` int(11) NOT NULL,
  `deposit_count` int(11) NOT NULL,
  `min_value` int(11) NOT NULL,
  `max_value` int(11) NOT NULL,
  `bonus_per` int(11) NOT NULL,
  `instant_per` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_game_welcome_bonus`
--

INSERT INTO `master_game_welcome_bonus` (`id`, `bonus_id`, `deposit_count`, `min_value`, `max_value`, `bonus_per`, `instant_per`) VALUES
(1, 1, 1, 25, 99, 100, 10),
(2, 1, 1, 100, 499, 120, 10),
(3, 1, 1, 500, 999, 150, 10),
(4, 1, 1, 1000, 2499, 200, 10),
(5, 1, 1, 3000, 5000, 250, 10),
(6, 1, 2, 25, 99, 50, 5),
(7, 1, 2, 100, 499, 60, 5),
(8, 1, 2, 500, 999, 75, 5),
(9, 1, 2, 1000, 2499, 100, 5),
(10, 1, 2, 3000, 5000, 125, 5),
(11, 1, 3, 25, 99, 25, 5),
(12, 1, 3, 100, 499, 30, 5),
(13, 1, 3, 500, 999, 40, 5),
(14, 1, 3, 1000, 2499, 50, 5),
(15, 1, 3, 3000, 5000, 60, 5);

-- --------------------------------------------------------

--
-- Table structure for table `master_mail_defult_templates`
--

DROP TABLE IF EXISTS `master_mail_defult_templates`;
CREATE TABLE IF NOT EXISTS `master_mail_defult_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_mail` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_mail_defult_templates`
--

INSERT INTO `master_mail_defult_templates` (`id`, `type_mail`, `name`, `subject`, `message`) VALUES
(1, 'user_mail_verify', '7S Rummy', 'Email Verification', '<!DOCTYPE html>\n<html>\n<head>\n</head>\n<body>\n<p>Hi, $name,</p>\n<p>Your Email Verification is a Success.</p>\n<p>&nbsp;</p>\n<p>Thank You,</p>\n<p>7S Rummy</p>\n</body>\n</html>'),
(2, 'user_kyc_success', '7S Rummy', 'KYC Verification Success', 'Hi $name, Your KYC Verification is Success\n\n\n\n'),
(3, 'user_kyc_reject', '7S Rummy', 'KYC Verification is Rejected', 'Hi $name, Your KYC Verification has been Rejected.'),
(4, 'user_withdraw_success', '7S Rummy', 'Withdraw Request Success', 'Hi $name, Your Withdraw Request has been Approved.\n'),
(5, 'user_withdraw_reject', '7S Rummy', 'Withdraw Request Reject', 'Hi $name, Your Withdraw Request has been Rejected.');

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_chip_type`
--

DROP TABLE IF EXISTS `master_rummy_chip_type`;
CREATE TABLE IF NOT EXISTS `master_rummy_chip_type` (
  `chip_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `discription` varchar(240) DEFAULT NULL,
  `createtime` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`chip_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `master_rummy_chip_type`
--

INSERT INTO `master_rummy_chip_type` (`chip_type_id`, `name`, `active`, `discription`, `createtime`) VALUES
(1, 'cash', '1', NULL, '2019-11-20 19:56:02'),
(2, 'free', '1', NULL, '2019-11-20 19:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_format`
--

DROP TABLE IF EXISTS `master_rummy_format`;
CREATE TABLE IF NOT EXISTS `master_rummy_format` (
  `format_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `discription` varchar(240) DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`format_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `master_rummy_format`
--

INSERT INTO `master_rummy_format` (`format_id`, `name`, `discription`, `active`, `createtime`) VALUES
(1, 'Pool', NULL, '1', '2019-11-20 19:56:02'),
(2, 'Points', NULL, '1', '2019-11-20 19:56:02'),
(3, 'Best of x', NULL, '1', '2019-11-20 19:56:02'),
(4, 'Do or Die', NULL, '1', '2019-11-20 19:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_format_types`
--

DROP TABLE IF EXISTS `master_rummy_format_types`;
CREATE TABLE IF NOT EXISTS `master_rummy_format_types` (
  `format_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `discription` varchar(240) DEFAULT NULL,
  `format_id` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`format_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `master_rummy_format_types`
--

INSERT INTO `master_rummy_format_types` (`format_type_id`, `name`, `discription`, `format_id`, `active`, `createtime`) VALUES
(1, '80', NULL, 1, '1', '2019-11-20 19:56:54'),
(2, '160', NULL, 1, '1', '2019-11-20 19:56:54'),
(3, '240', NULL, 1, '1', '2019-11-20 19:56:54'),
(4, 'Points', NULL, 2, '1', '2019-11-20 19:56:54'),
(5, 'BO3', NULL, 3, '1', '2019-11-20 19:56:54'),
(6, 'BO5', NULL, 3, '1', '2019-11-20 19:56:54'),
(7, 'Do or Die', NULL, 4, '1', '2019-11-20 19:56:54');

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_helper`
--

DROP TABLE IF EXISTS `master_rummy_helper`;
CREATE TABLE IF NOT EXISTS `master_rummy_helper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id_prefix` varchar(45) DEFAULT NULL,
  `table_id_lenth` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `master_rummy_helper`
--

INSERT INTO `master_rummy_helper` (`id`, `table_id_prefix`, `table_id_lenth`) VALUES
(1, NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_main_rooms`
--

DROP TABLE IF EXISTS `master_rummy_main_rooms`;
CREATE TABLE IF NOT EXISTS `master_rummy_main_rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `chip_type_id` int(11) NOT NULL,
  `format_id` int(11) NOT NULL,
  `format_type_id` int(11) NOT NULL,
  `max_seat` int(1) NOT NULL DEFAULT 0,
  `entry_chip` decimal(10,2) DEFAULT 0.00,
  `comm_per` decimal(4,2) DEFAULT 0.00,
  `total_players` int(4) NOT NULL DEFAULT 0,
  `total_chips` decimal(4,2) DEFAULT 0.00,
  `total_commision` decimal(4,2) DEFAULT 0.00,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_rummy_main_rooms`
--

INSERT INTO `master_rummy_main_rooms` (`room_id`, `type_id`, `chip_type_id`, `format_id`, `format_type_id`, `max_seat`, `entry_chip`, `comm_per`, `total_players`, `total_chips`, `total_commision`, `active`, `createtime`) VALUES
(1, 1, 1, 1, 1, 2, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(2, 1, 1, 1, 1, 2, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(3, 1, 1, 1, 1, 2, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(4, 1, 1, 1, 1, 2, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(5, 1, 1, 1, 1, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(6, 1, 1, 1, 1, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(7, 1, 1, 1, 1, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(8, 1, 1, 1, 1, 2, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(9, 1, 1, 1, 1, 2, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(10, 1, 1, 1, 1, 2, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(11, 1, 1, 1, 1, 6, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(12, 1, 1, 1, 1, 6, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(13, 1, 1, 1, 1, 6, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(14, 1, 1, 1, 1, 6, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(15, 1, 1, 1, 1, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(16, 1, 1, 1, 1, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(17, 1, 1, 1, 1, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(18, 1, 1, 1, 1, 6, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(19, 1, 1, 1, 1, 6, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(20, 1, 1, 1, 1, 6, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(21, 1, 1, 1, 2, 2, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(22, 1, 1, 1, 2, 2, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(23, 1, 1, 1, 2, 2, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(24, 1, 1, 1, 2, 2, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(25, 1, 1, 1, 2, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(26, 1, 1, 1, 2, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(27, 1, 1, 1, 2, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(28, 1, 1, 1, 2, 2, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(29, 1, 1, 1, 2, 2, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(30, 1, 1, 1, 2, 2, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(31, 1, 1, 1, 2, 6, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(32, 1, 1, 1, 2, 6, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(33, 1, 1, 1, 2, 6, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(34, 1, 1, 1, 2, 6, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(35, 1, 1, 1, 2, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(36, 1, 1, 1, 2, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(37, 1, 1, 1, 2, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(38, 1, 1, 1, 2, 6, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(39, 1, 1, 1, 2, 6, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(40, 1, 1, 1, 2, 6, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(41, 1, 1, 1, 3, 2, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(42, 1, 1, 1, 3, 2, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(43, 1, 1, 1, 3, 2, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(44, 1, 1, 1, 3, 2, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(45, 1, 1, 1, 3, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(46, 1, 1, 1, 3, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(47, 1, 1, 1, 3, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(48, 1, 1, 1, 3, 2, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(49, 1, 1, 1, 3, 2, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(50, 1, 1, 1, 3, 2, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(51, 1, 1, 1, 3, 6, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(52, 1, 1, 1, 3, 6, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(53, 1, 1, 1, 3, 6, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(54, 1, 1, 1, 3, 6, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(55, 1, 1, 1, 3, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(56, 1, 1, 1, 3, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(57, 1, 1, 1, 3, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(58, 1, 1, 1, 3, 6, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(59, 1, 1, 1, 3, 6, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(60, 1, 1, 1, 3, 6, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(61, 1, 1, 2, 4, 2, '8.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(62, 1, 1, 2, 4, 2, '20.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(63, 1, 1, 2, 4, 2, '40.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(64, 1, 1, 2, 4, 2, '80.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(65, 1, 1, 2, 4, 2, '160.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(66, 1, 1, 2, 4, 2, '400.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(67, 1, 1, 2, 4, 2, '800.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(68, 1, 1, 2, 4, 2, '1600.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(69, 1, 1, 2, 4, 2, '4000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(70, 1, 1, 2, 4, 2, '8000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(71, 1, 1, 2, 4, 6, '8.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(72, 1, 1, 2, 4, 6, '20.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(73, 1, 1, 2, 4, 6, '40.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(74, 1, 1, 2, 4, 6, '80.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(75, 1, 1, 2, 4, 6, '160.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(76, 1, 1, 2, 4, 6, '400.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(77, 1, 1, 2, 4, 6, '800.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(78, 1, 1, 2, 4, 6, '1600.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(79, 1, 1, 2, 4, 6, '4000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(80, 1, 1, 2, 4, 6, '8000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(81, 1, 1, 3, 5, 2, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(82, 1, 1, 3, 5, 2, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(83, 1, 1, 3, 5, 2, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(84, 1, 1, 3, 5, 2, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(85, 1, 1, 3, 5, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(86, 1, 1, 3, 5, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(87, 1, 1, 3, 5, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(88, 1, 1, 3, 5, 2, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(89, 1, 1, 3, 5, 2, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(90, 1, 1, 3, 5, 2, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(91, 1, 1, 3, 5, 6, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(92, 1, 1, 3, 5, 6, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(93, 1, 1, 3, 5, 6, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(94, 1, 1, 3, 5, 6, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(95, 1, 1, 3, 5, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(96, 1, 1, 3, 5, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(97, 1, 1, 3, 5, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(98, 1, 1, 3, 5, 6, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(99, 1, 1, 3, 5, 6, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(100, 1, 1, 3, 5, 6, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(101, 1, 1, 3, 6, 2, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(102, 1, 1, 3, 6, 2, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(103, 1, 1, 3, 6, 2, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(104, 1, 1, 3, 6, 2, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(105, 1, 1, 3, 6, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(106, 1, 1, 3, 6, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(107, 1, 1, 3, 6, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(108, 1, 1, 3, 6, 2, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(109, 1, 1, 3, 6, 2, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(110, 1, 1, 3, 6, 2, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(111, 1, 1, 3, 6, 6, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(112, 1, 1, 3, 6, 6, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(113, 1, 1, 3, 6, 6, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(114, 1, 1, 3, 6, 6, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(115, 1, 1, 3, 6, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(116, 1, 1, 3, 6, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(117, 1, 1, 3, 6, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(118, 1, 1, 3, 6, 6, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(119, 1, 1, 3, 6, 6, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(120, 1, 1, 3, 6, 6, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(121, 1, 1, 4, 7, 2, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(122, 1, 1, 4, 7, 2, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(123, 1, 1, 4, 7, 2, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(124, 1, 1, 4, 7, 2, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(125, 1, 1, 4, 7, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(126, 1, 1, 4, 7, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(127, 1, 1, 4, 7, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(128, 1, 1, 4, 7, 2, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(129, 1, 1, 4, 7, 2, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(130, 1, 1, 4, 7, 2, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(131, 1, 1, 4, 7, 6, '10.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(132, 1, 1, 4, 7, 6, '25.00', '10.00', 0, '0.00', '0.00', '1', '2019-09-30 16:06:15'),
(133, 1, 1, 4, 7, 6, '50.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(134, 1, 1, 4, 7, 6, '100.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:30:04'),
(135, 1, 1, 4, 7, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(136, 1, 1, 4, 7, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(137, 1, 1, 4, 7, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:43:29'),
(138, 1, 1, 4, 7, 6, '2000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 11:44:41'),
(139, 1, 1, 4, 7, 6, '5000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(140, 1, 1, 4, 7, 6, '10000.00', '10.00', 0, '0.00', '0.00', '1', '2019-11-20 17:14:16'),
(141, 2, 2, 1, 1, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(142, 2, 2, 1, 1, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(143, 2, 2, 1, 1, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(144, 2, 2, 1, 1, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(145, 2, 2, 1, 1, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(146, 2, 2, 1, 1, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(147, 2, 2, 1, 2, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(148, 2, 2, 1, 2, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(149, 2, 2, 1, 2, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(150, 2, 2, 1, 2, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(151, 2, 2, 1, 2, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(152, 2, 2, 1, 2, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(153, 2, 2, 1, 3, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(154, 2, 2, 1, 3, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(155, 2, 2, 1, 3, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(156, 2, 2, 1, 3, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(157, 2, 2, 1, 3, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(158, 2, 2, 1, 3, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(159, 2, 2, 2, 4, 2, '80.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(160, 2, 2, 2, 4, 2, '400.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(161, 2, 2, 2, 4, 2, '800.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(162, 2, 2, 2, 4, 6, '80.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(163, 2, 2, 2, 4, 6, '400.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(164, 2, 2, 2, 4, 6, '800.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(165, 2, 2, 3, 5, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(166, 2, 2, 3, 5, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(167, 2, 2, 3, 5, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(168, 2, 2, 3, 5, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(169, 2, 2, 3, 5, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(170, 2, 2, 3, 5, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(171, 2, 2, 3, 6, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(172, 2, 2, 3, 6, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(173, 2, 2, 3, 6, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(174, 2, 2, 3, 6, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(175, 2, 2, 3, 6, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(176, 2, 2, 3, 6, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(177, 2, 2, 4, 7, 2, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(178, 2, 2, 4, 7, 2, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(179, 2, 2, 4, 7, 2, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(180, 2, 2, 4, 7, 6, '250.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(181, 2, 2, 4, 7, 6, '500.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11'),
(182, 2, 2, 4, 7, 6, '1000.00', '10.00', 0, '0.00', '0.00', '1', '2023-05-10 01:03:11');

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_max_seats`
--

DROP TABLE IF EXISTS `master_rummy_max_seats`;
CREATE TABLE IF NOT EXISTS `master_rummy_max_seats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seats` varchar(45) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `master_rummy_max_seats`
--

INSERT INTO `master_rummy_max_seats` (`id`, `seats`, `active`, `createtime`) VALUES
(1, '2', '1', '2023-07-02 21:27:59'),
(2, '4', '1', '2023-07-02 21:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `master_rummy_type`
--

DROP TABLE IF EXISTS `master_rummy_type`;
CREATE TABLE IF NOT EXISTS `master_rummy_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_rummy_type`
--

INSERT INTO `master_rummy_type` (`type_id`, `name`, `description`, `active`, `createtime`) VALUES
(1, 'Cash', 'Cash Game', '1', '2023-05-01 21:26:00'),
(2, 'Practice', 'Free Game', '1', '2023-05-01 21:26:00'),
(3, 'Tournaments', 'Tournaments', '1', '2023-05-01 21:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `master_sms_default_templates`
--

DROP TABLE IF EXISTS `master_sms_default_templates`;
CREATE TABLE IF NOT EXISTS `master_sms_default_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_sms` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_sms_default_templates`
--

INSERT INTO `master_sms_default_templates` (`id`, `type_sms`, `name`, `subject`, `message`) VALUES
(1, 'user_register_otp', '7S RUMMY', 'Register OTP ', 'Dear $name Your $otp is here and You are Successfully Register 7S Rummy Game.\n'),
(2, 'user_login_with_otp', '7S RUMMY', 'Login OTP ', 'Dear $name Your $otp is here');

-- --------------------------------------------------------

--
-- Table structure for table `master_software`
--

DROP TABLE IF EXISTS `master_software`;
CREATE TABLE IF NOT EXISTS `master_software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_type` enum('android','ios') NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_software`
--

INSERT INTO `master_software` (`id`, `app_type`, `app_version`, `status`, `created_at`) VALUES
(1, 'android', 'v.01', '0', '2023-02-07 07:24:09'),
(2, 'android', 'v.01', '0', '2023-02-07 07:20:09'),
(3, 'ios', 'v.02', '1', '2023-02-07 07:25:31'),
(4, 'ios', 'v.02', '0', '2023-02-07 07:26:31'),
(5, 'android', 'v.03', '0', '2023-02-09 06:15:16'),
(6, 'ios', 'v.04', '0', '2023-02-09 06:39:03'),
(7, 'ios', 'v.05', '0', '2023-02-10 04:46:12');

-- --------------------------------------------------------

--
-- Table structure for table `master_table_id_generate`
--

DROP TABLE IF EXISTS `master_table_id_generate`;
CREATE TABLE IF NOT EXISTS `master_table_id_generate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `master_table_id_generate`
--

INSERT INTO `master_table_id_generate` (`id`, `createtime`) VALUES
(1, '2023-05-18 04:14:11'),
(2, '2023-05-18 04:23:19'),
(3, '2023-05-18 04:30:35'),
(4, '2023-05-18 04:32:09'),
(5, '2023-05-18 04:37:39'),
(6, '2023-05-18 04:50:40'),
(7, '2023-05-18 04:51:43'),
(8, '2023-05-18 05:19:22'),
(9, '2023-05-18 05:42:02'),
(10, '2023-05-18 05:43:32'),
(11, '2023-05-18 05:46:08'),
(12, '2023-05-19 00:52:55'),
(13, '2023-05-19 01:00:17'),
(14, '2023-05-19 01:05:02'),
(15, '2023-05-19 01:45:22'),
(16, '2023-05-19 02:59:19'),
(17, '2023-05-19 03:25:45'),
(18, '2023-05-19 03:29:25'),
(19, '2023-05-19 13:14:17'),
(20, '2023-05-19 15:51:23'),
(21, '2023-05-20 04:20:00'),
(22, '2023-05-20 05:04:34'),
(23, '2023-05-20 05:49:25'),
(24, '2023-05-24 23:27:53'),
(25, '2023-05-25 02:07:16'),
(26, '2023-05-25 17:31:08'),
(27, '2023-05-26 03:05:59'),
(28, '2023-05-26 12:54:28'),
(29, '2023-05-27 04:44:51'),
(30, '2023-05-27 04:54:32'),
(31, '2023-05-27 13:18:48'),
(32, '2023-05-27 13:46:55'),
(33, '2023-05-27 17:39:48'),
(34, '2023-05-27 18:58:18'),
(35, '2023-05-31 13:51:59'),
(36, '2023-06-01 14:07:20'),
(37, '2023-06-14 16:42:16'),
(38, '2023-06-14 17:00:25'),
(39, '2023-06-14 17:04:25'),
(40, '2023-06-14 22:14:19'),
(41, '2023-06-14 23:23:58'),
(42, '2023-06-14 23:34:32'),
(43, '2023-06-14 23:38:04'),
(44, '2023-06-15 00:05:49'),
(45, '2023-06-15 00:51:58'),
(46, '2023-06-15 00:53:38'),
(47, '2023-06-15 01:05:54'),
(48, '2023-06-15 16:38:09'),
(49, '2023-06-15 17:08:46'),
(50, '2023-06-15 20:46:06'),
(51, '2023-06-16 00:34:51'),
(52, '2023-06-19 15:39:18'),
(53, '2023-06-19 15:52:55'),
(54, '2023-06-22 00:24:45'),
(55, '2023-06-22 00:45:42'),
(56, '2023-06-22 00:59:37'),
(57, '2023-06-24 23:25:37'),
(58, '2023-06-25 00:11:21'),
(59, '2023-06-25 00:51:57'),
(60, '2023-06-25 09:24:27'),
(61, '2023-06-25 10:29:14'),
(62, '2023-06-26 22:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verify_status` int(11) NOT NULL DEFAULT 0,
  `phone_no` varchar(10) DEFAULT NULL,
  `phone_verify_status` int(11) NOT NULL DEFAULT 0,
  `user_tier_level` int(11) NOT NULL DEFAULT 0,
  `user_star_level` int(11) NOT NULL DEFAULT 0,
  `user_rank_level` int(11) NOT NULL DEFAULT 0,
  `premium_flag` enum('normal','vip') NOT NULL DEFAULT 'normal',
  `active` int(2) NOT NULL DEFAULT 1,
  `online_status` int(11) NOT NULL DEFAULT 0,
  `last_action_time` datetime DEFAULT NULL,
  `profile_completed` enum('yes','no') DEFAULT NULL,
  `ip_registration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_type` varchar(255) NOT NULL DEFAULT 'normal',
  `reg_type` enum('normal','refer') DEFAULT 'normal',
  `ref_by` bigint(20) DEFAULT NULL,
  `refer_code` varchar(255) NOT NULL,
  `allow_points` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `email_verify_status`, `phone_no`, `phone_verify_status`, `user_tier_level`, `user_star_level`, `user_rank_level`, `premium_flag`, `active`, `online_status`, `last_action_time`, `profile_completed`, `ip_registration`, `created_at`, `login_type`, `reg_type`, `ref_by`, `refer_code`, `allow_points`) VALUES
(4, 'demo', 'e+kY9w==', 'barath@vinorastudios.in', 1, '9944915577', 1, 5, 5, 4, 'vip', 1, 1, '2022-11-12 05:26:23', 'yes', '::1', '2015-11-04 15:21:02', 'normal', 'normal', 5, '1O4UV3TR', 'no'),
(5, 'test', 'e+kY9w==', '', 0, '', 1, 4, 4, 40, 'normal', 1, 0, '2022-10-20 05:11:20', NULL, NULL, '2016-07-17 16:17:09', 'normal', 'normal', NULL, 'HQMDEN0L', 'no'),
(51, 'asha', 'eeka8w==', '', 1, '9790885341', 1, 3, 3, 9, 'normal', 1, 0, NULL, NULL, '115.99.26.246', '2022-12-27 09:23:24', 'normal', 'normal', NULL, '1O4UV30O', 'no'),
(117, '00110058', 'e+kY9w==', NULL, 0, '9943725698', 1, 2, 2, 0, 'normal', 1, 0, NULL, NULL, '27.5.216.189', '2023-01-19 06:55:00', 'normal', 'refer', 5, '1O4UV30A', 'no'),
(118, '00110060', NULL, 'lavanya03lav@gmail.com', 1, NULL, 0, 1, 1, 0, 'normal', 1, 0, NULL, NULL, 'Tamil Nadu', '2023-01-19 07:10:10', 'google', 'normal', NULL, 'EZ19JFIK', 'no'),
(119, '00110061', 'e+kY9w==', NULL, 0, '9944916859', 1, 0, 0, 0, 'normal', 1, 0, NULL, NULL, '27.5.216.189', '2023-02-11 11:59:59', 'normal', 'normal', NULL, 'XHNL7IZO', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `users_bonus`
--

DROP TABLE IF EXISTS `users_bonus`;
CREATE TABLE IF NOT EXISTS `users_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `total_bonus` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bonus_used` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bonus_inhand` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_bonus`
--

INSERT INTO `users_bonus` (`id`, `user_id`, `total_bonus`, `bonus_used`, `bonus_inhand`) VALUES
(3, 4, '900.00', '0.00', '900.00');

-- --------------------------------------------------------

--
-- Table structure for table `users_bonus_history`
--

DROP TABLE IF EXISTS `users_bonus_history`;
CREATE TABLE IF NOT EXISTS `users_bonus_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chips` int(11) NOT NULL,
  `action_type` enum('credit','debit') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `action_from` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_from`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_bonus_history`
--

INSERT INTO `users_bonus_history` (`id`, `bonus_id`, `user_id`, `chips`, `action_type`, `action_from`, `created_at`) VALUES
(1, 1, 4, 2999, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:32:53'),
(2, 2, 4, 200, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:32:53'),
(3, 2, 5, 200, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:32:53'),
(4, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:53:01'),
(5, 2, 4, 200, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:53:01'),
(6, 2, 5, 200, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:53:01'),
(7, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:53:55'),
(8, 2, 4, 150, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:53:55'),
(9, 2, 5, 150, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:53:55'),
(10, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:54:34'),
(11, 2, 4, 150, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:54:34'),
(12, 2, 5, 150, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-21 12:54:34'),
(13, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:27:48'),
(14, 2, 4, 150, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:27:48'),
(15, 2, 5, 150, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:27:48'),
(16, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:27:59'),
(17, 2, 4, 150, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:27:59'),
(18, 2, 5, 150, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:27:59'),
(19, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:28:10'),
(20, 2, 4, 200, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:28:10'),
(21, 2, 5, 200, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:28:10'),
(22, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:41:35'),
(23, 2, 4, 150, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:41:35'),
(24, 2, 5, 150, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 09:41:35'),
(25, 1, 4, 1050, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:04:35'),
(26, 2, 4, 0, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:04:35'),
(27, 2, 5, 0, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:04:35'),
(28, 1, 4, 600, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:05:28'),
(29, 2, 4, 0, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:05:28'),
(30, 2, 5, 0, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:05:28'),
(31, 1, 4, 320, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:05:58'),
(32, 2, 4, 0, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:05:58'),
(33, 2, 5, 0, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:05:58'),
(34, 1, 4, 0, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:06:08'),
(35, 2, 4, 0, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:06:08'),
(36, 2, 5, 0, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:06:08'),
(37, 1, 4, 900, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:08:41'),
(38, 2, 4, 0, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:08:41'),
(39, 2, 5, 0, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:08:41'),
(40, 1, 4, 900, 'credit', '{\"credited_by\":\"welcome\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:10:03'),
(41, 2, 4, 150, 'credit', '{\"credited_by\":\"self\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:10:03'),
(42, 2, 5, 150, 'credit', '{\"credited_by\":\"4\",\"transaction_type\":\"deposit\",\"transaction_id\":\"1\"}', '2023-01-23 11:10:03');

-- --------------------------------------------------------

--
-- Table structure for table `users_cash_chips`
--

DROP TABLE IF EXISTS `users_cash_chips`;
CREATE TABLE IF NOT EXISTS `users_cash_chips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `total_deposit_point` decimal(12,2) NOT NULL,
  `total_withdraw_cash` decimal(12,2) NOT NULL,
  `total_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_inplay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_inwin` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_inhand` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_cash_chips`
--

INSERT INTO `users_cash_chips` (`id`, `user_id`, `total_deposit_point`, `total_withdraw_cash`, `total_cash`, `cash_inplay`, `cash_inwin`, `cash_inhand`) VALUES
(11, 4, '500.00', '0.00', '600.00', '0.00', '0.00', '588.00'),
(12, 5, '500.00', '0.00', '600.00', '0.00', '0.00', '600.00'),
(13, 51, '500.00', '0.00', '600.00', '0.00', '0.00', '600.00'),
(14, 117, '500.00', '0.00', '600.00', '0.00', '0.00', '600.00');

-- --------------------------------------------------------

--
-- Table structure for table `users_cash_chips_deposit`
--

DROP TABLE IF EXISTS `users_cash_chips_deposit`;
CREATE TABLE IF NOT EXISTS `users_cash_chips_deposit` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(10) DEFAULT NULL,
  `order_id` varchar(15) NOT NULL,
  `customer_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_amount` float(12,2) DEFAULT NULL,
  `order_currency` varchar(255) NOT NULL DEFAULT 'INR',
  `order_expiry_time` datetime NOT NULL,
  `order_note` varchar(150) NOT NULL,
  `notify_url` varchar(512) NOT NULL,
  `payment_methods` varchar(255) NOT NULL,
  `cf_order_id` varchar(15) DEFAULT NULL,
  `cf_created_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `transaction_status` varchar(32) DEFAULT NULL,
  `payment_session_id` varchar(255) DEFAULT NULL,
  `cf_order_amount` int(11) DEFAULT NULL,
  `cf_order_currency` int(11) DEFAULT NULL,
  `order_status` varchar(255) NOT NULL DEFAULT 'active, paid, expired',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_cash_chips_deposit`
--

INSERT INTO `users_cash_chips_deposit` (`id`, `user_id`, `order_id`, `customer_details`, `transaction_id`, `order_amount`, `order_currency`, `order_expiry_time`, `order_note`, `notify_url`, `payment_methods`, `cf_order_id`, `cf_created_at`, `created_at`, `transaction_status`, `payment_session_id`, `cf_order_amount`, `cf_order_currency`, `order_status`) VALUES
(16, 5, '0', '0', '1', 600.00, 'INR', '0000-00-00 00:00:00', '', '', '', '0', NULL, NULL, NULL, '', 0, 0, 'active, paid, expired'),
(17, 4, '0', '0', '1', 600.00, 'INR', '0000-00-00 00:00:00', '', '', '', '0', NULL, NULL, NULL, '', 0, 0, 'active, paid, expired'),
(18, 5, '1013', 'test', '988765451', 500.00, 'inr', '2023-03-18 15:52:35', 'nothing', 'https:google.com', 'deposit', NULL, NULL, NULL, '3', '15', NULL, NULL, 'active, paid, expired'),
(19, 5, '1014', 'test', '988765451', 500.00, 'inr', '2023-03-18 15:52:35', 'nothing', 'https:google.com', 'deposit', NULL, NULL, NULL, '3', '15', NULL, NULL, 'active, paid, expired'),
(20, 5, '1015', 'test', '988765451', 500.00, 'inr', '2023-03-18 15:52:35', 'nothing', 'https:google.com', 'deposit', NULL, NULL, NULL, '3', '15', NULL, NULL, 'active, paid, expired'),
(21, 4, '1018', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:51:01', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(22, 4, '1019', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:52:14', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(23, 4, '1020', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:52:27', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(24, 4, '1021', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:53:17', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(25, 4, '1022', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:57:45', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(26, 4, '1023', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:58:31', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(27, 4, '1024', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 17:58:54', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(28, 4, '1025', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 18:00:03', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(29, 4, '1026', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 18:00:18', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(30, 4, '1027', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 18:06:18', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(31, 4, '1028', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 18:07:22', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(32, 4, '1029', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-18 18:08:06', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired'),
(33, 4, '1030', '{\"id\":\"4\",\"name\":\"Madhavan\",\"email\":\"barath@vinorastudios.in\",\"phone_no\":\"9944915577\"}', NULL, 2000.00, 'inr', '2023-03-21 14:20:04', 'deposit', 'https:google.com', 'deposit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active, paid, expired');

-- --------------------------------------------------------

--
-- Table structure for table `users_cash_chips_instantcash_history`
--

DROP TABLE IF EXISTS `users_cash_chips_instantcash_history`;
CREATE TABLE IF NOT EXISTS `users_cash_chips_instantcash_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `instantcash` decimal(12,2) NOT NULL,
  `transaction_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_cash_chips_instantcash_history`
--

INSERT INTO `users_cash_chips_instantcash_history` (`id`, `user_id`, `instantcash`, `transaction_id`, `created_at`) VALUES
(13, 4, '60.00', '1', '2023-01-23 11:10:03');

-- --------------------------------------------------------

--
-- Table structure for table `users_cash_chips_play_history`
--

DROP TABLE IF EXISTS `users_cash_chips_play_history`;
CREATE TABLE IF NOT EXISTS `users_cash_chips_play_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `purchase_amount` decimal(10,2) DEFAULT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `real_chips` int(255) DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(10) DEFAULT NULL,
  `transaction_status` varchar(32) DEFAULT NULL,
  `mnet_response_data` text DEFAULT NULL,
  `card_type` varchar(255) DEFAULT NULL,
  `last_digit` int(32) DEFAULT NULL,
  `request_ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_cash_chips_withdraw_request`
--

DROP TABLE IF EXISTS `users_cash_chips_withdraw_request`;
CREATE TABLE IF NOT EXISTS `users_cash_chips_withdraw_request` (
  `order_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(10) DEFAULT NULL,
  `req_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `req_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT 0 COMMENT 'pending(0),complete(1),cancel(2)',
  `status_change_by` int(11) DEFAULT NULL COMMENT 'Admin Id',
  `status_change_by_ip_address` varchar(255) DEFAULT NULL,
  `status_change_date` datetime DEFAULT NULL,
  `haoda_payout_id` varchar(20) DEFAULT NULL,
  `payment_gateway` varchar(16) NOT NULL DEFAULT 'haoda',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_cash_chips_withdraw_request`
--

INSERT INTO `users_cash_chips_withdraw_request` (`order_id`, `user_id`, `req_amount`, `req_date`, `status`, `status_change_by`, `status_change_by_ip_address`, `status_change_date`, `haoda_payout_id`, `payment_gateway`) VALUES
(1, 4, '10.00', '2022-12-16 08:38:59', 2, 74, '115.97.101.184', '2023-03-25 11:45:33', '55', 'haoda'),
(2, 5, '100.00', '2022-12-16 08:38:59', 1, 74, '115.97.101.184', '2023-02-02 15:20:31', '55', 'haoda'),
(3, 5, '100.00', '2022-12-16 08:38:59', 2, 1, '192.168.1.50', '2022-12-16 10:38:42', '55', 'haoda'),
(4, 4, '1.00', '2023-03-25 07:33:46', NULL, NULL, NULL, NULL, '55', 'haoda'),
(5, 4, '1.00', '2023-03-25 07:34:33', NULL, NULL, NULL, NULL, '55', 'haoda'),
(6, 4, '1.00', '2023-03-25 07:36:25', NULL, NULL, NULL, NULL, '55', 'haoda'),
(7, 4, '10.00', '2023-03-25 08:51:17', NULL, NULL, NULL, NULL, '55', 'haoda'),
(8, 4, '10.00', '2023-03-25 08:52:16', NULL, NULL, NULL, NULL, '55', 'haoda'),
(9, 4, '10.00', '2023-03-25 09:12:33', NULL, NULL, NULL, NULL, '55', 'haoda'),
(10, 4, '1.00', '2023-03-25 09:36:36', NULL, NULL, NULL, NULL, '55', 'haoda'),
(11, 4, '1.00', '2023-03-25 09:37:20', NULL, NULL, NULL, NULL, '55', 'haoda'),
(12, 4, '1.00', '2023-03-25 09:37:51', NULL, NULL, NULL, NULL, '55', 'haoda'),
(13, 4, '1.00', '2023-03-25 09:40:12', NULL, NULL, NULL, NULL, '55', 'haoda'),
(14, 4, '1.00', '2023-03-25 09:41:40', NULL, NULL, NULL, NULL, '55', 'haoda'),
(15, 4, '1.00', '2023-03-25 09:44:13', NULL, NULL, NULL, NULL, '55', 'haoda'),
(16, 4, '1.00', '2023-03-25 09:45:38', NULL, NULL, NULL, NULL, '55', 'haoda'),
(17, 4, '1.00', '2023-03-25 09:46:39', NULL, NULL, NULL, NULL, '55', 'haoda'),
(18, 4, '1.00', '2023-03-25 09:47:25', NULL, NULL, NULL, NULL, '55', 'haoda'),
(19, 4, '1.00', '2023-03-25 09:51:27', NULL, NULL, NULL, NULL, '55', 'haoda'),
(20, 4, '1.00', '2023-03-25 09:52:11', NULL, NULL, NULL, NULL, '55', 'haoda'),
(21, 4, '1.00', '2023-03-25 09:53:49', NULL, NULL, NULL, NULL, '55', 'haoda'),
(22, 4, '1.00', '2023-03-25 09:54:55', NULL, NULL, NULL, NULL, '55', 'haoda'),
(23, 4, '1.00', '2023-03-25 09:56:26', NULL, NULL, NULL, NULL, '55', 'haoda'),
(24, 4, '1.00', '2023-03-25 09:57:03', NULL, NULL, NULL, NULL, '55', 'haoda'),
(25, 4, '1.00', '2023-03-25 09:57:41', NULL, NULL, NULL, NULL, '55', 'haoda'),
(26, 4, '1.00', '2023-03-25 09:58:21', NULL, NULL, NULL, NULL, '55', 'haoda'),
(27, 4, '1.00', '2023-03-25 09:58:38', NULL, NULL, NULL, NULL, '55', 'haoda'),
(28, 4, '1.00', '2023-03-25 09:58:55', NULL, NULL, NULL, NULL, '55', 'haoda'),
(29, 4, '1.00', '2023-03-25 10:02:20', NULL, NULL, NULL, NULL, '55', 'haoda'),
(30, 4, '1.00', '2023-03-25 10:02:36', NULL, NULL, NULL, NULL, '55', 'haoda'),
(31, 4, '1.00', '2023-03-25 10:03:06', NULL, NULL, NULL, NULL, '55', 'haoda'),
(32, 4, '1.00', '2023-03-25 10:04:27', NULL, NULL, NULL, NULL, '55', 'haoda'),
(33, 4, '1.00', '2023-03-25 10:05:48', NULL, NULL, NULL, NULL, '55', 'haoda'),
(34, 4, '1.00', '2023-03-25 10:06:53', NULL, NULL, NULL, NULL, '55', 'haoda'),
(35, 4, '1.00', '2023-03-25 10:07:52', NULL, NULL, NULL, NULL, '55', 'haoda'),
(36, 4, '1.00', '2023-03-25 10:08:47', NULL, NULL, NULL, NULL, '55', 'haoda'),
(37, 4, '1.00', '2023-03-25 10:09:11', NULL, NULL, NULL, NULL, '55', 'haoda'),
(38, 4, '1.00', '2023-03-25 10:09:41', NULL, NULL, NULL, NULL, '55', 'haoda'),
(39, 4, '1.00', '2023-03-25 10:10:00', NULL, NULL, NULL, NULL, '55', 'haoda'),
(40, 4, '1.00', '2023-03-25 10:31:59', NULL, NULL, NULL, NULL, '55', 'haoda'),
(41, 4, '1.00', '2023-03-25 10:32:32', NULL, NULL, NULL, NULL, '55', 'haoda'),
(42, 4, '1.00', '2023-03-25 10:33:09', NULL, NULL, NULL, NULL, '55', 'haoda'),
(43, 4, '1.00', '2023-03-25 11:22:45', NULL, NULL, NULL, NULL, '55', 'haoda'),
(44, 4, '1.00', '2023-03-27 05:56:11', NULL, NULL, NULL, NULL, '55', 'haoda'),
(45, 4, '1.00', '2023-03-29 11:08:17', NULL, NULL, NULL, NULL, '55', 'haoda'),
(46, 4, '1.00', '2023-03-30 04:57:43', NULL, NULL, NULL, NULL, '55', 'haoda'),
(47, 4, '1.00', '2023-03-30 04:58:24', NULL, NULL, NULL, NULL, '55', 'haoda'),
(48, 4, '1.00', '2023-03-30 06:41:56', NULL, NULL, NULL, NULL, '55', 'haoda'),
(49, 4, '1.00', '2023-03-30 06:43:35', NULL, NULL, NULL, NULL, '55', 'haoda'),
(50, 4, '1.00', '2023-03-30 06:44:35', NULL, NULL, NULL, NULL, '55', 'haoda'),
(51, 4, '1.00', '2023-03-30 06:44:59', NULL, NULL, NULL, NULL, '55', 'haoda'),
(52, 4, '1.00', '2023-03-30 06:46:59', NULL, NULL, NULL, NULL, '55', 'haoda'),
(53, 4, '1.00', '2023-03-30 06:48:19', NULL, NULL, NULL, NULL, '55', 'haoda'),
(54, 4, '1.00', '2023-03-30 06:48:25', NULL, NULL, NULL, NULL, '55', 'haoda'),
(55, 4, '1.00', '2023-03-30 06:54:44', NULL, NULL, NULL, NULL, '55', 'haoda'),
(56, 4, '5.00', '2023-03-30 06:55:02', NULL, NULL, NULL, NULL, '55', 'haoda'),
(57, 4, '5.00', '2023-03-30 06:55:07', NULL, NULL, NULL, NULL, '55', 'haoda'),
(58, 4, '10.00', '2023-03-30 06:55:31', NULL, NULL, NULL, NULL, '55', 'haoda'),
(59, 4, '6.00', '2023-03-30 07:07:38', NULL, NULL, NULL, NULL, '55', 'haoda'),
(60, 4, '6.00', '2023-03-30 07:08:35', NULL, NULL, NULL, NULL, '56', 'haoda'),
(61, 4, '6.00', '2023-03-30 07:11:49', NULL, NULL, NULL, NULL, '57', 'haoda'),
(62, 4, '6.00', '2023-03-30 07:19:33', NULL, NULL, NULL, NULL, '58', 'haoda'),
(63, 4, '6.00', '2023-03-30 07:20:42', NULL, NULL, NULL, NULL, '59', 'haoda'),
(64, 4, '6.00', '2023-03-30 07:27:48', NULL, NULL, NULL, NULL, '60', 'haoda'),
(65, 4, '6.00', '2023-03-30 07:30:32', NULL, NULL, NULL, NULL, '61', 'haoda'),
(66, 4, '6.00', '2023-03-30 07:34:22', NULL, NULL, NULL, NULL, '62', 'haoda'),
(67, 4, '6.00', '2023-03-30 07:34:40', NULL, NULL, NULL, NULL, '63', 'haoda'),
(68, 4, '6.00', '2023-03-30 07:36:32', NULL, NULL, NULL, NULL, '64', 'haoda'),
(69, 4, '6.00', '2023-03-30 07:36:59', NULL, NULL, NULL, NULL, '65', 'haoda'),
(70, 4, '6.00', '2023-03-30 07:37:13', NULL, NULL, NULL, NULL, '66', 'haoda'),
(71, 4, '6.00', '2023-03-30 07:37:25', NULL, NULL, NULL, NULL, '67', 'haoda'),
(72, 4, '6.00', '2023-03-30 07:37:38', NULL, NULL, NULL, NULL, '68', 'haoda'),
(73, 4, '6.00', '2023-03-30 07:38:50', NULL, NULL, NULL, NULL, '69', 'haoda'),
(74, 4, '6.00', '2023-03-30 08:55:12', NULL, NULL, NULL, NULL, '70', 'haoda'),
(75, 4, '6.00', '2023-03-30 08:55:29', NULL, NULL, NULL, NULL, '71', 'haoda'),
(76, 4, '6.00', '2023-03-30 08:55:46', NULL, NULL, NULL, NULL, '72', 'haoda'),
(77, 4, '6.00', '2023-03-30 08:56:16', NULL, NULL, NULL, NULL, '73', 'haoda'),
(78, 4, '6.00', '2023-03-30 08:56:44', NULL, NULL, NULL, NULL, '74', 'haoda'),
(79, 4, '6.00', '2023-03-30 09:04:54', NULL, NULL, NULL, NULL, '75', 'haoda'),
(80, 4, '6.00', '2023-03-30 09:05:11', NULL, NULL, NULL, NULL, '76', 'haoda'),
(81, 4, '6.00', '2023-03-30 09:50:27', 0, NULL, NULL, NULL, '77', 'haoda'),
(82, 4, '6.00', '2023-03-30 09:54:00', 0, NULL, NULL, NULL, '78', 'haoda'),
(83, 4, '6.00', '2023-03-30 09:58:24', 0, NULL, NULL, NULL, '79', 'haoda'),
(84, 4, '6.00', '2023-03-30 09:59:05', 0, NULL, NULL, NULL, '80', 'haoda'),
(85, 4, '6.00', '2023-03-30 10:02:16', 0, NULL, NULL, NULL, '81', 'haoda'),
(86, 4, '6.00', '2023-03-30 10:06:09', 0, NULL, NULL, NULL, '82', 'haoda'),
(87, 4, '6.00', '2023-03-30 10:06:19', 0, NULL, NULL, NULL, '83', 'haoda'),
(88, 4, '6.00', '2023-03-30 10:08:10', 0, NULL, NULL, NULL, '84', 'haoda'),
(89, 4, '6.00', '2023-03-30 10:09:50', 0, NULL, NULL, NULL, '85', 'haoda'),
(90, 4, '6.00', '2023-03-30 10:11:43', 0, NULL, NULL, NULL, '86', 'haoda'),
(91, 4, '6.00', '2023-03-30 10:11:57', 0, NULL, NULL, NULL, '87', 'haoda'),
(92, 4, '6.00', '2023-03-30 10:13:32', 0, NULL, NULL, NULL, '88', 'haoda'),
(93, 4, '6.00', '2023-03-30 10:16:31', 0, NULL, NULL, NULL, '89', 'haoda'),
(94, 4, '6.00', '2023-03-30 10:18:24', 0, NULL, NULL, NULL, '90', 'haoda'),
(95, 4, '6.00', '2023-03-30 10:19:50', 0, NULL, NULL, NULL, '91', 'haoda'),
(96, 4, '6.00', '2023-03-30 10:25:44', 0, NULL, NULL, NULL, '92', 'haoda'),
(97, 4, '6.00', '2023-03-30 10:25:55', 0, NULL, NULL, NULL, '93', 'haoda'),
(98, 4, '6.00', '2023-03-30 10:31:49', 0, NULL, NULL, NULL, '94', 'haoda'),
(99, 4, '6.00', '2023-03-30 10:33:21', 0, NULL, NULL, NULL, '95', 'haoda'),
(100, 4, '6.00', '2023-03-30 10:34:46', 0, NULL, NULL, NULL, '96', 'haoda'),
(101, 4, '6.00', '2023-03-30 10:36:15', 0, NULL, NULL, NULL, '97', 'haoda'),
(102, 4, '6.00', '2023-03-30 10:42:21', 0, NULL, NULL, NULL, '98', 'haoda'),
(103, 4, '6.00', '2023-03-30 10:43:03', 0, NULL, NULL, NULL, '99', 'haoda'),
(104, 4, '6.00', '2023-03-30 10:43:27', 0, NULL, NULL, NULL, '100', 'haoda'),
(105, 4, '6.00', '2023-03-30 11:06:18', 0, NULL, NULL, NULL, '101', 'haoda'),
(106, 4, '6.00', '2023-03-30 11:08:31', 0, NULL, NULL, NULL, '102', 'haoda'),
(107, 4, '6.00', '2023-03-30 11:09:06', 0, NULL, NULL, NULL, '103', 'haoda'),
(108, 4, '6.00', '2023-03-30 11:18:13', 0, NULL, NULL, NULL, '104', 'haoda'),
(109, 4, '6.00', '2023-03-30 11:18:30', 0, NULL, NULL, NULL, '105', 'haoda'),
(110, 4, '6.00', '2023-03-30 11:20:03', 0, NULL, NULL, NULL, '106', 'haoda'),
(111, 4, '6.00', '2023-03-30 11:20:37', 0, NULL, NULL, NULL, '107', 'haoda'),
(112, 4, '6.00', '2023-03-30 11:22:22', 0, NULL, NULL, NULL, '108', 'haoda'),
(113, 4, '6.00', '2023-03-30 11:23:51', 0, NULL, NULL, NULL, '109', 'haoda'),
(114, 4, '6.00', '2023-03-30 11:24:07', 0, NULL, NULL, NULL, '110', 'haoda'),
(115, 4, '6.00', '2023-03-30 11:25:14', 0, NULL, NULL, NULL, '111', 'haoda'),
(116, 4, '6.00', '2023-03-30 11:25:39', 0, NULL, NULL, NULL, '112', 'haoda'),
(117, 4, '6.00', '2023-03-30 11:31:22', 0, NULL, NULL, NULL, '113', 'haoda'),
(118, 4, '6.00', '2023-03-30 11:40:09', 0, NULL, NULL, NULL, '114', 'haoda'),
(119, 4, '6.00', '2023-03-31 12:29:47', 0, NULL, NULL, NULL, '115', 'haoda'),
(120, 4, '6.00', '2023-03-31 12:35:33', 0, NULL, NULL, NULL, '116', 'haoda'),
(121, 4, '6.00', '2023-03-31 12:36:19', 0, NULL, NULL, NULL, '117', 'haoda'),
(122, 4, '6.00', '2023-03-31 12:36:54', 0, NULL, NULL, NULL, '118', 'haoda'),
(123, 4, '6.00', '2023-03-31 12:37:12', 0, NULL, NULL, NULL, '119', 'haoda'),
(124, 4, '6.00', '2023-03-31 12:38:24', 0, NULL, NULL, NULL, '120', 'haoda'),
(125, 4, '6.00', '2023-03-31 12:38:44', 0, NULL, NULL, NULL, '121', 'haoda'),
(126, 4, '6.00', '2023-03-31 12:38:54', 0, NULL, NULL, NULL, '122', 'haoda'),
(127, 4, '6.00', '2023-03-31 12:39:27', 0, NULL, NULL, NULL, '123', 'haoda'),
(128, 4, '6.00', '2023-03-31 12:40:00', 0, NULL, NULL, NULL, '124', 'haoda'),
(129, 4, '6.00', '2023-03-31 12:43:07', 0, NULL, NULL, NULL, '125', 'haoda'),
(130, 4, '6.00', '2023-03-31 12:43:48', 0, NULL, NULL, NULL, '126', 'haoda'),
(131, 4, '6.00', '2023-03-31 12:52:52', 0, NULL, NULL, NULL, '127', 'haoda'),
(132, 4, '6.00', '2023-03-31 12:54:25', 0, NULL, NULL, NULL, '128', 'haoda'),
(133, 4, '6.00', '2023-03-31 12:55:08', 0, NULL, NULL, NULL, '129', 'haoda'),
(134, 4, '6.00', '2023-04-01 06:48:35', 0, NULL, NULL, NULL, NULL, 'haoda'),
(135, 4, '6.00', '2023-04-01 06:48:56', 0, NULL, NULL, NULL, '1', 'haoda'),
(136, 4, '6.00', '2023-04-01 06:50:44', 0, NULL, NULL, NULL, '1', 'haoda'),
(137, 4, '10.00', '2023-04-01 07:41:55', 0, NULL, NULL, NULL, '2', 'haoda'),
(138, 4, '6.00', '2023-04-01 07:42:25', 0, NULL, NULL, NULL, '3', 'haoda'),
(139, 4, '6.00', '2023-04-01 07:43:10', 0, NULL, NULL, NULL, '4', 'haoda'),
(140, 4, '6.00', '2023-04-01 07:44:04', 0, NULL, NULL, NULL, '5', 'haoda'),
(141, 4, '6.00', '2023-04-26 08:57:02', 0, NULL, NULL, NULL, '6', 'haoda'),
(142, 4, '6.00', '2023-04-27 05:50:09', 0, NULL, NULL, NULL, '7', 'haoda');

-- --------------------------------------------------------

--
-- Table structure for table `users_details_bank`
--

DROP TABLE IF EXISTS `users_details_bank`;
CREATE TABLE IF NOT EXISTS `users_details_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `ac_type` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_details_bank`
--

INSERT INTO `users_details_bank` (`id`, `user_id`, `customer_name`, `bank_name`, `account_no`, `ifsc_code`, `ac_type`, `status`, `created_at`) VALUES
(1, 4, 'Jack', 'SBI', '125545712454', 'SBI01TJHKF', 'Saving', '0', '2023-01-20 09:16:19'),
(2, 5, 'johnto', 'indian overseas bank', '97864651321', 'IDND000T040', 'current', '0', '2023-02-25 06:49:19'),
(3, 6, 'john', 'indian bank', '98764525424', 'IND000T40', 'savings', '0', '2023-02-25 06:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `users_details_kyc`
--

DROP TABLE IF EXISTS `users_details_kyc`;
CREATE TABLE IF NOT EXISTS `users_details_kyc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kyc_verify_status` int(11) NOT NULL DEFAULT 0,
  `pan_no` varchar(255) DEFAULT NULL,
  `pc_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_details_kyc`
--

INSERT INTO `users_details_kyc` (`id`, `user_id`, `kyc_verify_status`, `pan_no`, `pc_file`, `created_at`) VALUES
(1, 4, 0, 'PAN023984508245', '4e13a700340bdef6.jpg', '2023-01-08 09:09:15'),
(2, 5, 0, 'PAN023984508245', '6db9d669ea7fa6f0.jpg', '2023-01-09 10:11:00');

-- --------------------------------------------------------

--
-- Table structure for table `users_details_profile`
--

DROP TABLE IF EXISTS `users_details_profile`;
CREATE TABLE IF NOT EXISTS `users_details_profile` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(120) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dateofbirth` date DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `pin_code` varchar(6) NOT NULL,
  `profile_image` tinytext DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_details_profile`
--

INSERT INTO `users_details_profile` (`id`, `user_id`, `name`, `last_name`, `gender`, `dateofbirth`, `state`, `city`, `pin_code`, `profile_image`, `created_date`) VALUES
(4, 4, 'Madhavan', 'Maddy', 'male', '2001-09-01', 'Tamil Nadu', 'Chennai', '600050', '6db9d669ea7fa6f0.jpg', '2015-11-04 20:51:03'),
(5, 5, 'remo', '', 'F', NULL, '', NULL, '', 'b7c6f6319b99db61.jpg', '2023-02-16 15:06:39'),
(6, 51, 'surya', '', 'M', NULL, '', NULL, '', 'b7c6f6319b99db61.jpg', NULL),
(7, 117, 'rolex', '', 'F', NULL, '', NULL, '', NULL, NULL),
(11, 118, 'vijay', '', NULL, NULL, '', NULL, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_free_chips`
--

DROP TABLE IF EXISTS `users_free_chips`;
CREATE TABLE IF NOT EXISTS `users_free_chips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_chips` decimal(12,2) NOT NULL DEFAULT 0.00,
  `chips_inplay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `chips_inwin` decimal(12,2) NOT NULL DEFAULT 0.00,
  `chips_inhand` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_free_chips`
--

INSERT INTO `users_free_chips` (`id`, `user_id`, `total_chips`, `chips_inplay`, `chips_inwin`, `chips_inhand`) VALUES
(1, 4, '0.00', '0.00', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `users_free_chips_play_history`
--

DROP TABLE IF EXISTS `users_free_chips_play_history`;
CREATE TABLE IF NOT EXISTS `users_free_chips_play_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(10) DEFAULT NULL,
  `game_type` int(11) NOT NULL,
  `real_money_table` bigint(10) NOT NULL,
  `table_id` varchar(255) NOT NULL,
  `bet_amount` int(11) NOT NULL,
  `win_amount` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_game_details`
--

DROP TABLE IF EXISTS `users_game_details`;
CREATE TABLE IF NOT EXISTS `users_game_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_play_games` int(11) NOT NULL,
  `total_loss_ingame` int(11) NOT NULL,
  `total_win_ingame` int(11) NOT NULL,
  `total_play_tourney` int(11) NOT NULL,
  `total_loss_intourney` int(11) NOT NULL,
  `total_win_intourney` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_game_details`
--

INSERT INTO `users_game_details` (`id`, `user_id`, `total_play_games`, `total_loss_ingame`, `total_win_ingame`, `total_play_tourney`, `total_loss_intourney`, `total_win_intourney`) VALUES
(1, 4, 10, 6, 4, 5, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users_id_generate`
--

DROP TABLE IF EXISTS `users_id_generate`;
CREATE TABLE IF NOT EXISTS `users_id_generate` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `assigned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10062 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_id_generate`
--

INSERT INTO `users_id_generate` (`user_id`, `assigned`, `created_at`) VALUES
(10000, 0, '2022-12-27 13:33:45'),
(10001, 0, '2022-12-27 13:34:32'),
(10002, 0, '2022-12-27 13:34:56'),
(10003, 0, '2022-12-27 13:35:37'),
(10004, 0, '2022-12-27 13:36:49'),
(10005, 0, '2022-12-27 13:36:58'),
(10006, 0, '2022-12-27 13:37:55'),
(10007, 0, '2022-12-28 04:11:27'),
(10008, 0, '2022-12-28 04:13:46'),
(10009, 0, '2022-12-28 04:14:21'),
(10010, 0, '2022-12-28 04:14:29'),
(10011, 0, '2022-12-28 04:17:17'),
(10012, 0, '2022-12-28 04:17:24'),
(10013, 0, '2022-12-28 04:17:54'),
(10014, 0, '2022-12-28 04:18:41'),
(10015, 0, '2022-12-28 04:19:00'),
(10016, 0, '2022-12-28 04:19:13'),
(10017, 0, '2022-12-28 04:20:06'),
(10018, 0, '2022-12-28 04:20:29'),
(10019, 0, '2022-12-28 04:34:28'),
(10020, 0, '2022-12-28 04:34:52'),
(10021, 0, '2022-12-28 04:44:20'),
(10022, 0, '2022-12-28 04:48:19'),
(10023, 0, '2022-12-28 04:48:29'),
(10024, 0, '2022-12-28 04:48:51'),
(10025, 0, '2022-12-28 04:49:11'),
(10026, 0, '2022-12-28 04:49:45'),
(10027, 0, '2022-12-28 04:50:43'),
(10028, 0, '2022-12-28 04:51:16'),
(10029, 0, '2022-12-28 04:51:22'),
(10030, 0, '2022-12-28 04:51:45'),
(10031, 0, '2022-12-28 04:52:18'),
(10032, 0, '2022-12-28 04:53:05'),
(10033, 0, '2022-12-28 04:54:59'),
(10034, 0, '2022-12-28 04:57:12'),
(10035, 0, '2022-12-28 11:23:57'),
(10036, 0, '2022-12-28 11:27:06'),
(10037, 0, '2022-12-28 11:29:39'),
(10038, 0, '2022-12-28 11:32:32'),
(10039, 0, '2022-12-28 11:35:58'),
(10040, 0, '2022-12-28 11:36:21'),
(10041, 0, '2022-12-28 13:22:34'),
(10042, 0, '2022-12-29 10:24:47'),
(10043, 0, '2023-01-13 06:55:38'),
(10044, 0, '2023-01-13 07:08:36'),
(10045, 0, '2023-01-19 05:57:19'),
(10046, 0, '2023-01-19 05:58:25'),
(10047, 0, '2023-01-19 05:58:35'),
(10048, 0, '2023-01-19 05:58:41'),
(10049, 0, '2023-01-19 05:59:05'),
(10050, 0, '2023-01-19 05:59:54'),
(10051, 0, '2023-01-19 06:06:00'),
(10052, 0, '2023-01-19 06:06:04'),
(10053, 0, '2023-01-19 06:06:12'),
(10054, 0, '2023-01-19 06:07:37'),
(10055, 0, '2023-01-19 06:08:20'),
(10056, 0, '2023-01-19 06:53:13'),
(10057, 0, '2023-01-19 06:54:06'),
(10058, 0, '2023-01-19 06:55:00'),
(10059, 0, '2023-01-19 07:07:12'),
(10060, 0, '2023-01-19 07:10:10'),
(10061, 0, '2023-02-11 11:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `users_log_history`
--

DROP TABLE IF EXISTS `users_log_history`;
CREATE TABLE IF NOT EXISTS `users_log_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(10) NOT NULL DEFAULT 0,
  `login_device` text NOT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `state_name` varchar(255) DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `location_ip` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_log_history`
--

INSERT INTO `users_log_history` (`id`, `user_id`, `login_device`, `country_name`, `state_name`, `city_name`, `action`, `location_ip`, `created_at`) VALUES
(1, 4, '', NULL, NULL, NULL, 'login', '::1', '2015-11-04 15:21:55'),
(2, 10, '', NULL, NULL, NULL, 'logout', '::1', '2022-10-17 03:09:23'),
(3, 4, '', NULL, NULL, NULL, 'login', '::1', '2022-10-17 03:09:36'),
(4, 4, '', NULL, NULL, NULL, 'logout', '::1', '2022-10-17 06:30:07'),
(5, 4, '', NULL, NULL, NULL, 'login', '192.168.1.22', '2022-10-17 06:45:36'),
(6, 4, '', NULL, NULL, NULL, 'login', '192.168.1.50', '2022-10-19 04:55:43'),
(7, 4, '', NULL, NULL, NULL, 'logout', '192.168.1.50', '2022-10-19 06:36:03'),
(8, 5, '', NULL, NULL, NULL, 'login', '192.168.1.50', '2022-10-19 06:36:49'),
(9, 5, '', NULL, NULL, NULL, 'logout', '192.168.1.50', '2022-10-19 23:41:20'),
(10, 4, '', NULL, NULL, NULL, 'login', '192.168.1.50', '2022-10-20 01:12:29'),
(11, 4, '', NULL, NULL, NULL, 'login', '192.168.1.50', '2022-11-11 23:54:38'),
(12, 4, '', NULL, NULL, NULL, 'logout', '192.168.1.50', '2022-11-11 23:56:05'),
(13, 4, '', NULL, NULL, NULL, 'login', '192.168.1.50', '2022-11-11 23:56:23'),
(14, 5, '', NULL, NULL, NULL, 'Login', '', '2022-12-14 07:05:44'),
(15, 5, 'sql', 'India', 'Tamil Nadu', 'Channai', 'Login', '196.1.23.50', '2022-12-14 07:11:55'),
(16, 5, '', '', '', '', 'Login', '', '2022-12-14 07:19:05'),
(17, 5, '', '', '', '', 'Login', '', '2022-12-14 09:36:00'),
(18, 4, 'PostmanRuntime/7.29.2', 'india', 'tamilnadu', 'chennnai', 'Login', '115.99.26.246', '2022-12-14 09:55:41'),
(19, 4, 'PostmanRuntime/7.29.2', 'india', 'tamilnadu', 'chennnai', 'Login', '115.99.26.246', '2022-12-14 10:01:04'),
(20, 25, 'PostmanRuntime/7.29.2', 'india', 'tamilnadu', 'chennnai', 'Login', '115.99.26.246', '2022-12-14 10:01:19'),
(21, 4, 'PostmanRuntime/7.29.2', 'india', 'tamilnadu', 'chennnai', 'Login', '115.99.26.246', '2022-12-14 10:01:31'),
(22, 5, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:37:35'),
(23, 5, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:37:57'),
(24, 5, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:38:45'),
(25, 5, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:40:12'),
(26, 5, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:41:11'),
(27, 4, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:44:33'),
(28, 4, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:44:49'),
(29, 4, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:45:24'),
(30, 4, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:48:23'),
(31, 4, 'PostmanRuntime/7.30.0', 'India', 'TN', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 05:55:26'),
(32, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 07:01:12'),
(33, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 07:01:19'),
(34, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 07:01:21'),
(35, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 07:01:46'),
(36, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 07:01:48'),
(37, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 10:03:48'),
(38, 5, 'SQl', 'India', 'Tamil Nadu', 'Channai', 'Login', '102.014.012.01', '2022-12-28 11:22:55'),
(39, 106, 'Chennai', 'PostmanRuntime/7.30.0', '27.5.216.189', 'India', 'Login', 'Tamil Nadu', '2022-12-28 11:29:39'),
(40, 106, 'Chennai', 'PostmanRuntime/7.30.0', '27.5.216.189', 'India', 'Login', 'Tamil Nadu', '2022-12-28 11:29:58'),
(41, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-28 13:02:45'),
(42, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Logout', '27.5.216.189', '2022-12-28 13:04:59'),
(43, 110, 'Chennai', 'PostmanRuntime/7.30.0', '27.5.216.189', 'India', 'Login', 'Tamil Nadu', '2022-12-28 13:22:34'),
(44, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2022-12-29 06:03:28'),
(45, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-09 09:47:14'),
(46, 4, 'PostmanRuntime/7.30.0', NULL, NULL, NULL, 'Login', '192.168.1.50', '2023-01-09 10:13:14'),
(47, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-13 05:34:09'),
(48, 5, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-13 06:05:30'),
(49, 5, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Logout', '27.5.216.189', '2023-01-13 06:06:14'),
(50, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-19 06:43:57'),
(51, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-19 06:59:52'),
(52, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-19 07:04:58'),
(53, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Logout', '27.5.216.189', '2023-01-19 07:05:22'),
(54, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:43:31'),
(55, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:47:04'),
(56, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:49:14'),
(57, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:49:44'),
(58, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:51:40'),
(59, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:55:13'),
(60, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 11:55:49'),
(61, 4, 'windows', 'india', 'tamilnadu', 'chennai', 'Login', '192.168.31.102', '2023-01-20 11:58:09'),
(62, 4, 'windows', 'india', 'tamilnadu', 'chennai', 'Login', '192.168.31.102', '2023-01-20 11:59:10'),
(63, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 12:11:01'),
(64, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 12:11:42'),
(65, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-20 12:16:50'),
(66, 118, 'Chennai', 'PostmanRuntime/7.30.0', '27.5.216.189', 'India', 'Login', 'Tamil Nadu', '2023-01-20 12:24:31'),
(67, 118, 'Chennai', 'PostmanRuntime/7.30.0', '27.5.216.189', 'India', 'Login', 'Tamil Nadu', '2023-01-23 07:32:12'),
(68, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-01-24 11:46:45'),
(69, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 11:47:53'),
(70, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 11:57:13'),
(71, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 12:11:09'),
(72, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.29', '2023-01-30 13:00:59'),
(73, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.29', '2023-01-30 13:01:16'),
(74, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 13:12:56'),
(75, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 13:16:04'),
(76, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 13:18:12'),
(77, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 13:19:03'),
(78, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.2', '2023-01-30 13:20:07'),
(79, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.29', '2023-01-30 13:26:16'),
(80, 51, 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)', NULL, NULL, NULL, 'Login', '192.168.1.29', '2023-01-31 05:03:26'),
(81, 4, 'PostmanRuntime/7.30.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-01 09:03:43'),
(82, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-08 05:21:37'),
(83, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-11 05:59:08'),
(84, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-11 07:19:16'),
(85, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-11 11:38:19'),
(86, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Logout', '27.5.216.189', '2023-02-11 11:38:43'),
(87, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-11 12:01:36'),
(88, 119, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-11 12:02:13'),
(89, 4, 'Chennai', 'PostmanRuntime/7.30.1', '27.5.216.189', 'India', 'Login', 'Tamil Nadu', '2023-02-11 12:18:08'),
(90, 4, 'PostmanRuntime/7.30.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-13 05:26:13'),
(91, 4, 'PostmanRuntime/7.31.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-16 07:34:58'),
(92, 4, 'PostmanRuntime/7.30.1', NULL, NULL, NULL, 'Login', '192.168.1.50', '2023-02-16 09:24:04'),
(93, 4, 'PostmanRuntime/7.31.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-18 11:43:36'),
(94, 4, 'PostmanRuntime/7.31.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-18 11:44:00'),
(95, 4, 'PostmanRuntime/7.31.0', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-02-23 11:42:02'),
(96, 4, 'PostmanRuntime/7.31.1', NULL, NULL, NULL, 'Login', '192.168.1.50', '2023-03-03 12:36:42'),
(97, 4, 'PostmanRuntime/7.31.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-03 12:38:58'),
(98, 4, 'PostmanRuntime/7.31.1', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-09 12:10:05'),
(99, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-18 05:29:45'),
(100, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-21 08:49:41'),
(101, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-25 06:42:22'),
(102, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-25 09:56:06'),
(103, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-27 05:55:50'),
(104, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-29 11:07:45'),
(105, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-30 11:08:15'),
(106, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-30 11:08:28'),
(107, 4, 'PostmanRuntime/7.31.3', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-03-31 09:41:02'),
(108, 4, 'PostmanRuntime/7.32.2', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-04-26 08:56:25'),
(109, 4, 'PostmanRuntime/7.32.2', 'India', 'Tamil Nadu', 'Chennai', 'Login', '27.5.216.189', '2023-04-27 10:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `users_payment_order`
--

DROP TABLE IF EXISTS `users_payment_order`;
CREATE TABLE IF NOT EXISTS `users_payment_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_type` enum('withdraw','deposit') NOT NULL,
  `order_status` enum('inprogress','success','cancel','decline','failed') NOT NULL,
  `remarks` varchar(1000) NOT NULL,
  `order_amount` float(12,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1031 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_payment_order`
--

INSERT INTO `users_payment_order` (`id`, `user_id`, `order_type`, `order_status`, `remarks`, `order_amount`) VALUES
(1001, 4, 'withdraw', 'inprogress', 'null', 2000.00),
(1002, 4, 'deposit', 'success', 'nothing remarks', 2000.00),
(1003, 4, 'deposit', 'success', 'nothing remarks', 2000.00),
(1004, 4, 'deposit', 'success', 'nothing remarks', 2000.00),
(1005, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1006, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1007, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1008, 5, 'withdraw', 'success', 'nope', 500.00),
(1009, 5, 'withdraw', 'success', 'nope', 500.00),
(1010, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1011, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1012, 5, 'withdraw', 'success', 'nope', 500.00),
(1013, 5, 'withdraw', 'success', 'nope', 500.00),
(1014, 5, 'withdraw', 'success', 'nope', 500.00),
(1015, 5, 'withdraw', 'success', 'nope', 500.00),
(1016, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1017, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1018, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1019, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1020, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1021, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1022, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1023, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1024, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1025, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1026, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1027, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1028, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1029, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00),
(1030, 4, 'deposit', 'inprogress', 'nothing remarks', 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users_point`
--

DROP TABLE IF EXISTS `users_point`;
CREATE TABLE IF NOT EXISTS `users_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `total_point` decimal(12,2) NOT NULL DEFAULT 0.00,
  `point_decrease` decimal(12,2) NOT NULL DEFAULT 0.00,
  `point_inhand` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_point`
--

INSERT INTO `users_point` (`id`, `user_id`, `total_point`, `point_decrease`, `point_inhand`) VALUES
(1, 4, '1500.00', '500.00', '1000.00'),
(2, 5, '2500.00', '500.00', '2000.00');

-- --------------------------------------------------------

--
-- Table structure for table `users_point_history`
--

DROP TABLE IF EXISTS `users_point_history`;
CREATE TABLE IF NOT EXISTS `users_point_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `game_type` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_real_chips`
--

DROP TABLE IF EXISTS `users_real_chips`;
CREATE TABLE IF NOT EXISTS `users_real_chips` (
  `user_id` int(11) NOT NULL,
  `chips` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paymode` enum('credit','debit') NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT 1,
  `createtime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_real_chips`
--

INSERT INTO `users_real_chips` (`user_id`, `chips`, `paymode`, `remark`, `description`, `status`, `createtime`) VALUES
(1, '10.00', 'debit', '0000000016', 'Join Table', 1, '2023-05-19 02:59:19'),
(1, '10.00', 'debit', '0000000017', 'Join Table', 1, '2023-05-19 03:25:45'),
(1, '10.00', 'debit', '0000000018', 'Join Table', 1, '2023-05-19 03:29:25'),
(2, '10.00', 'debit', '0000000018', 'Join Table', 1, '2023-05-19 03:35:41'),
(1, '10.00', 'debit', '0000000019', 'Join Table', 1, '2023-05-19 13:14:17'),
(2, '10.00', 'debit', '0000000019', 'Join Table', 1, '2023-05-19 13:15:20'),
(1, '10.00', 'debit', '0000000020', 'Join Table', 1, '2023-05-19 15:51:23'),
(2, '10.00', 'debit', '0000000020', 'Join Table', 1, '2023-05-19 15:52:24'),
(1, '10.00', 'debit', '0000000021', 'Join Table', 1, '2023-05-20 04:20:00'),
(1, '10.00', 'debit', '0000000022', 'Join Table', 1, '2023-05-20 05:04:35'),
(1, '10.00', 'debit', '0000000023', 'Join Table', 1, '2023-05-20 05:49:25'),
(1, '290.00', 'debit', '0000000024', 'Join Table', 1, '2023-05-24 23:27:53'),
(2, '874.90', 'debit', '0000000024', 'Join Table', 1, '2023-05-25 00:28:38'),
(1, '290.00', 'credit', NULL, NULL, 1, '2023-05-25 02:06:27'),
(2, '874.90', 'credit', NULL, NULL, 1, '2023-05-25 02:06:27'),
(1, '10.00', 'debit', '0000000025', 'Join Table', 1, '2023-05-25 02:07:16'),
(1, '25.00', 'debit', '0000000026', 'Join Table', 1, '2023-05-25 17:31:08'),
(1, '10.00', 'debit', '0000000027', 'Join Table', 1, '2023-05-26 03:05:59'),
(1, '10.00', 'debit', '0000000028', 'Join Table', 1, '2023-05-26 12:54:28'),
(2, '10.00', 'debit', '0000000028', 'Join Table', 1, '2023-05-27 03:51:52'),
(1, '10.00', 'debit', '0000000029', 'Join Table', 1, '2023-05-27 04:44:51'),
(2, '10.00', 'debit', '0000000029', 'Join Table', 1, '2023-05-27 04:45:16'),
(1, '10.00', 'debit', '0000000030', 'Join Table', 1, '2023-05-27 04:54:32'),
(2, '10.00', 'debit', '0000000030', 'Join Table', 1, '2023-05-27 04:54:59'),
(1, '10.00', 'debit', '0000000031', 'Join Table', 1, '2023-05-27 13:18:48'),
(2, '10.00', 'debit', '0000000031', 'Join Table', 1, '2023-05-27 13:19:48'),
(1, '10.00', 'debit', '0000000032', 'Join Table', 1, '2023-05-27 13:46:55'),
(2, '10.00', 'debit', '0000000032', 'Join Table', 1, '2023-05-27 13:47:29'),
(1, '10.00', 'debit', '0000000033', 'Join Table', 1, '2023-05-27 17:39:48'),
(2, '10.00', 'debit', '0000000033', 'Join Table', 1, '2023-05-27 17:40:18'),
(1, '10.00', 'debit', '0000000034', 'Join Table', 1, '2023-05-27 18:58:18'),
(2, '10.00', 'debit', '0000000034', 'Join Table', 1, '2023-05-27 18:59:07'),
(1, '10.00', 'debit', '0000000035', 'Join Table', 1, '2023-05-31 13:52:00'),
(2, '10.00', 'debit', '0000000035', 'Join Table', 1, '2023-05-31 13:52:44'),
(1, '10.00', 'debit', '0000000036', 'Join Table', 1, '2023-06-01 14:07:20'),
(1, '10.00', 'debit', '0000000037', 'Join Table', 1, '2023-06-14 16:42:16'),
(2, '10.00', 'debit', '0000000037', 'Join Table', 1, '2023-06-14 16:43:00'),
(1, '10.00', 'debit', '0000000038', 'Join Table', 1, '2023-06-14 17:00:26'),
(2, '10.00', 'debit', '0000000038', 'Join Table', 1, '2023-06-14 17:00:45'),
(1, '10.00', 'debit', '0000000039', 'Join Table', 1, '2023-06-14 17:04:25'),
(2, '10.00', 'debit', '0000000039', 'Join Table', 1, '2023-06-14 17:04:45'),
(1, '10.00', 'debit', '0000000040', 'Join Table', 1, '2023-06-14 22:14:19'),
(2, '10.00', 'debit', '0000000040', 'Join Table', 1, '2023-06-14 22:19:42'),
(1, '10.00', 'debit', '0000000041', 'Join Table', 1, '2023-06-14 23:23:58'),
(2, '10.00', 'debit', '0000000041', 'Join Table', 1, '2023-06-14 23:24:19'),
(1, '10.00', 'debit', '0000000042', 'Join Table', 1, '2023-06-14 23:34:32'),
(2, '10.00', 'debit', '0000000042', 'Join Table', 1, '2023-06-14 23:34:52'),
(2, '10.00', 'debit', '0000000043', 'Join Table', 1, '2023-06-14 23:38:04'),
(1, '10.00', 'debit', '0000000043', 'Join Table', 1, '2023-06-14 23:38:27'),
(1, '10.00', 'debit', '0000000044', 'Join Table', 1, '2023-06-15 00:05:49'),
(2, '10.00', 'debit', '0000000044', 'Join Table', 1, '2023-06-15 00:06:10'),
(1, '10.00', 'debit', '0000000045', 'Join Table', 1, '2023-06-15 00:51:58'),
(1, '10.00', 'debit', '0000000046', 'Join Table', 1, '2023-06-15 00:53:38'),
(2, '10.00', 'debit', '0000000046', 'Join Table', 1, '2023-06-15 00:54:00'),
(1, '10.00', 'debit', '0000000047', 'Join Table', 1, '2023-06-15 01:05:54'),
(2, '10.00', 'debit', '0000000047', 'Join Table', 1, '2023-06-15 01:18:11'),
(1, '10.00', 'debit', '0000000048', 'Join Table', 1, '2023-06-15 16:38:09'),
(2, '10.00', 'debit', '0000000048', 'Join Table', 1, '2023-06-15 16:53:45'),
(1, '10.00', 'debit', '0000000049', 'Join Table', 1, '2023-06-15 17:08:46'),
(2, '10.00', 'debit', '0000000049', 'Join Table', 1, '2023-06-15 17:09:56'),
(1, '10.00', 'debit', '0000000050', 'Join Table', 1, '2023-06-15 20:46:06'),
(2, '10.00', 'debit', '0000000050', 'Join Table', 1, '2023-06-15 20:46:33'),
(1, '10.00', 'debit', '0000000051', 'Join Table', 1, '2023-06-16 00:34:51'),
(2, '10.00', 'debit', '0000000051', 'Join Table', 1, '2023-06-16 00:35:10'),
(1, '10000.00', 'credit', NULL, NULL, 1, '2023-06-19 15:34:57'),
(1, '10.00', 'debit', '0000000052', 'Join Table', 1, '2023-06-19 15:39:18'),
(2, '10.00', 'debit', '0000000052', 'Join Table', 1, '2023-06-19 15:39:39'),
(1, '10.00', 'debit', '0000000053', 'Join Table', 1, '2023-06-19 15:52:55'),
(2, '10.00', 'debit', '0000000053', 'Join Table', 1, '2023-06-19 15:53:15'),
(1, '10.00', 'debit', '0000000054', 'Join Table', 1, '2023-06-22 00:24:45'),
(2, '10.00', 'debit', '0000000054', 'Join Table', 1, '2023-06-22 00:35:53'),
(1, '10.00', 'debit', '0000000055', 'Join Table', 1, '2023-06-22 00:45:42'),
(2, '10.00', 'debit', '0000000055', 'Join Table', 1, '2023-06-22 00:46:09'),
(1, '10.00', 'debit', '0000000056', 'Join Table', 1, '2023-06-22 00:59:37'),
(2, '10.00', 'debit', '0000000056', 'Join Table', 1, '2023-06-22 00:59:57'),
(1, '10.00', 'debit', '0000000057', 'Join Table', 1, '2023-06-24 23:25:37'),
(2, '10.00', 'debit', '0000000057', 'Join Table', 1, '2023-06-24 23:26:04'),
(1, '10.00', 'debit', '0000000058', 'Join Table', 1, '2023-06-25 00:11:21'),
(2, '10.00', 'debit', '0000000058', 'Join Table', 1, '2023-06-25 00:11:45'),
(1, '10.00', 'debit', '0000000059', 'Join Table', 1, '2023-06-25 00:51:57'),
(2, '10.00', 'debit', '0000000059', 'Join Table', 1, '2023-06-25 00:52:20'),
(2, '10.00', 'debit', '0000000060', 'Join Table', 1, '2023-06-25 09:24:27'),
(1, '10.00', 'debit', '0000000060', 'Join Table', 1, '2023-06-25 09:24:33'),
(2, '10.00', 'debit', '0000000061', 'Join Table', 1, '2023-06-25 10:29:14'),
(1, '10.00', 'debit', '0000000061', 'Join Table', 1, '2023-06-25 10:29:21'),
(2, '10.00', 'debit', '0000000062', 'Join Table', 1, '2023-06-26 22:05:06'),
(1, '10.00', 'debit', '0000000062', 'Join Table', 1, '2023-06-26 22:05:16');

--
-- Triggers `users_real_chips`
--
DROP TRIGGER IF EXISTS `users_real_chips_before_insert`;
DELIMITER $$
CREATE TRIGGER `users_real_chips_before_insert` BEFORE INSERT ON `users_real_chips` FOR EACH ROW BEGIN	
    IF (NEW.paymode = 'credit') THEN		
		UPDATE users SET real_chips = real_chips + NEW.chips WHERE id = NEW.user_id;
    END IF;
    IF (NEW.paymode = 'debit') THEN		
		UPDATE users SET real_chips = real_chips - NEW.chips  WHERE id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_refer_earn_point`
--

DROP TABLE IF EXISTS `users_refer_earn_point`;
CREATE TABLE IF NOT EXISTS `users_refer_earn_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `total_point` int(11) NOT NULL,
  `point_decrease` int(11) NOT NULL,
  `point_inhand` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_refer_earn_point`
--

INSERT INTO `users_refer_earn_point` (`id`, `user_id`, `total_point`, `point_decrease`, `point_inhand`) VALUES
(1, 4, 1500, 500, 1000),
(2, 5, 2500, 500, 2000),
(3, 4, 9000, 500, 8500),
(4, 5, 8500, 500, 8000),
(5, 4, 10000, 5000, 5000),
(6, 5, 25000, 5000, 20000),
(7, 4, 90000, 50000, 40000),
(8, 5, 85000, 50000, 35000),
(9, 4, 25000, 5000, 20000),
(10, 5, 45000, 5000, 40000),
(11, 4, 9000, 500, 8500),
(12, 5, 8500, 500, 8000),
(13, 4, 10000, 5000, 5000),
(14, 5, 25000, 5000, 20000),
(15, 4, 90000, 50000, 40000),
(16, 5, 85000, 50000, 35000);

-- --------------------------------------------------------

--
-- Table structure for table `users_refer_earn_point_history`
--

DROP TABLE IF EXISTS `users_refer_earn_point_history`;
CREATE TABLE IF NOT EXISTS `users_refer_earn_point_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `game_type` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_signin_otp_verify`
--

DROP TABLE IF EXISTS `users_signin_otp_verify`;
CREATE TABLE IF NOT EXISTS `users_signin_otp_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mobile_country_code` int(5) DEFAULT 91,
  `mobile` varchar(10) DEFAULT NULL,
  `mobile_otp` int(11) NOT NULL,
  `otp_sent_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_verify_on` datetime DEFAULT NULL,
  `otp_expiry_on` datetime NOT NULL,
  `otp_verify_status` int(1) NOT NULL DEFAULT 0,
  `device_id` varchar(32) DEFAULT NULL,
  `device_details` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_signin_otp_verify`
--

INSERT INTO `users_signin_otp_verify` (`id`, `user_id`, `mobile_country_code`, `mobile`, `mobile_otp`, `otp_sent_on`, `otp_verify_on`, `otp_expiry_on`, `otp_verify_status`, `device_id`, `device_details`) VALUES
(81, 51, 91, '9790885341', 857774, '2023-01-31 05:03:06', '2023-01-31 10:33:26', '2023-01-31 10:38:06', 1, '192.168.1.29', 'UnityPlayer/2021.3.16f1 (UnityWebRequest/1.0, libcurl/7.84.0-DEV)'),
(82, 119, 91, '9944916859', 775960, '2023-02-11 12:01:56', '2023-02-11 17:32:13', '2023-02-11 17:36:56', 1, '27.5.216.189', 'PostmanRuntime/7.30.1');

-- --------------------------------------------------------

--
-- Table structure for table `users_signup_otp_verify`
--

DROP TABLE IF EXISTS `users_signup_otp_verify`;
CREATE TABLE IF NOT EXISTS `users_signup_otp_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile_country_code` int(5) DEFAULT 91,
  `mobile` varchar(10) NOT NULL,
  `mobile_otp` int(11) NOT NULL,
  `otp_sent_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_verify_on` datetime DEFAULT NULL,
  `otp_expiry_on` datetime NOT NULL,
  `otp_verify_status` int(1) NOT NULL DEFAULT 0,
  `device_id` varchar(32) DEFAULT NULL,
  `device_details` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_signup_otp_verify`
--

INSERT INTO `users_signup_otp_verify` (`id`, `mobile_country_code`, `mobile`, `mobile_otp`, `otp_sent_on`, `otp_verify_on`, `otp_expiry_on`, `otp_verify_status`, `device_id`, `device_details`) VALUES
(13, 91, '9876543218', 450810, '2022-12-27 10:08:21', NULL, '2022-12-27 15:43:21', 0, NULL, NULL),
(14, 91, '9876543218', 839670, '2022-12-27 10:10:53', NULL, '2022-12-27 16:45:53', 0, NULL, NULL),
(15, 91, '9876543218', 455263, '2022-12-27 11:09:14', NULL, '2022-12-31 16:44:14', 0, NULL, NULL),
(16, 91, '9876543218', 124640, '2022-12-27 12:49:14', '2022-12-28 17:06:21', '2022-12-30 18:24:14', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(17, 91, '9876543218', 220215, '2022-12-28 04:56:57', NULL, '2022-12-28 10:31:57', 0, NULL, NULL),
(18, 91, '987654621', 241656, '2022-12-28 05:09:00', NULL, '2022-12-28 10:44:00', 0, NULL, NULL),
(19, 91, '987654621', 993249, '2022-12-28 11:32:26', NULL, '2022-12-28 17:07:26', 0, NULL, NULL),
(20, 91, '987654621', 106060, '2022-12-28 13:22:10', NULL, '2022-12-28 18:57:10', 0, NULL, NULL),
(21, 91, '9876543220', 239622, '2023-01-13 06:54:51', '2023-01-13 12:25:38', '2023-01-13 12:29:51', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(22, 91, '7708085865', 503142, '2023-01-13 06:58:00', NULL, '2023-01-13 12:33:00', 0, NULL, NULL),
(23, 91, '9943725689', 545732, '2023-01-13 07:04:02', NULL, '2023-01-13 12:39:02', 0, NULL, NULL),
(24, 91, '9943725688', 822636, '2023-01-13 07:04:45', '2023-01-13 12:39:00', '2023-01-13 12:39:45', 1, '192.168.31.102', 'windows'),
(25, 91, '9943725688', 194767, '2023-01-18 12:46:59', NULL, '2023-01-18 18:21:59', 0, NULL, NULL),
(26, 91, '9943725688', 895379, '2023-01-19 05:43:13', NULL, '2023-01-19 11:18:13', 0, NULL, NULL),
(27, 91, '9943725688', 356335, '2023-01-19 05:51:36', NULL, '2023-01-19 11:26:36', 0, NULL, NULL),
(28, 91, '9943725688', 543605, '2023-01-19 05:56:23', '2023-01-19 11:26:23', '2023-01-19 11:31:23', 1, '192.168.31.102', 'windows'),
(29, 91, '9943725688', 983026, '2023-01-19 05:59:43', '2023-01-19 11:29:54', '2023-01-19 11:34:43', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(30, 91, '9943725698', 371383, '2023-01-19 06:07:59', '2023-01-19 11:38:20', '2023-01-19 11:42:59', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(31, 91, '9943725698', 567872, '2023-01-19 06:52:50', '2023-01-19 12:25:00', '2023-01-19 12:27:50', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(32, 91, '9943121327', 969249, '2023-01-20 10:38:52', NULL, '2023-01-20 16:13:52', 0, NULL, NULL),
(33, 91, '9944916854', 194436, '2023-01-28 12:17:50', NULL, '2023-01-28 17:52:50', 0, NULL, NULL),
(34, 91, '9944916859', 645796, '2023-01-28 12:18:19', NULL, '2023-01-28 17:53:19', 0, NULL, NULL),
(35, 91, '9944916859', 387369, '2023-01-28 12:18:45', NULL, '2023-01-28 17:53:45', 0, NULL, NULL),
(36, 91, '9944916859', 802496, '2023-01-30 05:14:50', NULL, '2023-01-30 10:49:50', 0, NULL, NULL),
(37, 91, '9944916859', 178190, '2023-01-30 08:42:19', NULL, '2023-01-30 14:17:19', 0, NULL, NULL),
(38, 91, '9944916859', 568330, '2023-01-30 12:33:08', NULL, '2023-01-30 18:08:08', 0, NULL, NULL),
(39, 91, '9944916859', 802207, '2023-02-11 11:56:29', '2023-02-11 17:29:59', '2023-02-11 17:31:29', 1, '27.5.216.189', 'PostmanRuntime/7.30.1');

-- --------------------------------------------------------

--
-- Table structure for table `users_table`
--

DROP TABLE IF EXISTS `users_table`;
CREATE TABLE IF NOT EXISTS `users_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `table_id` varchar(45) NOT NULL,
  `in_chips` double(7,2) DEFAULT 0.00,
  `win_chips` double(7,2) DEFAULT 0.00,
  `loss_chips` double(7,2) NOT NULL DEFAULT 0.00,
  `deals` int(2) NOT NULL DEFAULT 0,
  `rejoins` int(2) DEFAULT 0,
  `final_possition` int(11) NOT NULL DEFAULT 0,
  `status` enum('register','leave','playing','finished') NOT NULL DEFAULT 'register',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` datetime DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users_table`
--

INSERT INTO `users_table` (`id`, `user_id`, `table_id`, `in_chips`, `win_chips`, `loss_chips`, `deals`, `rejoins`, `final_possition`, `status`, `active`, `createtime`, `end_time`) VALUES
(1, 1, '0000000037', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 16:42:16', '2023-06-14 16:50:30'),
(2, 2, '0000000037', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 16:43:00', '2023-06-14 16:50:30'),
(3, 1, '0000000038', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-14 17:00:26', NULL),
(4, 2, '0000000038', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-14 17:00:45', NULL),
(5, 1, '0000000039', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 17:04:25', '2023-06-14 17:12:14'),
(6, 2, '0000000039', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 17:04:45', '2023-06-14 17:12:14'),
(7, 1, '0000000040', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 22:14:19', '2023-06-14 22:26:28'),
(8, 2, '0000000040', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 22:19:42', '2023-06-14 22:26:28'),
(9, 1, '0000000041', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 23:23:58', '2023-06-14 23:31:33'),
(10, 2, '0000000041', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 23:24:19', '2023-06-14 23:31:33'),
(11, 1, '0000000042', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-14 23:34:32', NULL),
(12, 2, '0000000042', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-14 23:34:52', NULL),
(13, 2, '0000000043', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 23:38:04', '2023-06-14 23:44:15'),
(14, 1, '0000000043', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-14 23:38:27', '2023-06-14 23:44:15'),
(15, 1, '0000000044', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 00:05:49', '2023-06-15 00:13:39'),
(16, 2, '0000000044', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 00:06:10', '2023-06-15 00:13:39'),
(17, 1, '0000000045', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-15 00:51:58', NULL),
(18, 1, '0000000046', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 00:53:38', '2023-06-15 01:01:29'),
(19, 2, '0000000046', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 00:54:00', '2023-06-15 01:01:29'),
(20, 1, '0000000047', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 01:05:54', '2023-06-15 01:25:18'),
(21, 2, '0000000047', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 01:18:11', '2023-06-15 01:25:18'),
(22, 1, '0000000048', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 16:38:09', '2023-06-15 16:59:31'),
(23, 2, '0000000048', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 16:53:45', '2023-06-15 16:59:31'),
(24, 1, '0000000049', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 17:08:46', '2023-06-15 17:14:55'),
(25, 2, '0000000049', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 17:09:56', '2023-06-15 17:14:55'),
(26, 1, '0000000050', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 20:46:06', '2023-06-15 20:51:57'),
(27, 2, '0000000050', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-15 20:46:33', '2023-06-15 20:51:57'),
(28, 1, '0000000051', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-16 00:34:51', '2023-06-16 00:45:05'),
(29, 2, '0000000051', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-16 00:35:10', '2023-06-16 00:45:05'),
(30, 1, '0000000052', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-19 15:39:18', '2023-06-19 15:46:18'),
(31, 2, '0000000052', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-19 15:39:39', '2023-06-19 15:46:18'),
(32, 1, '0000000053', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-19 15:52:55', '2023-06-19 16:00:16'),
(33, 2, '0000000053', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-19 15:53:15', '2023-06-19 16:00:16'),
(34, 1, '0000000054', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-22 00:24:45', '2023-06-22 00:43:21'),
(35, 2, '0000000054', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-22 00:35:53', '2023-06-22 00:43:21'),
(36, 1, '0000000055', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-22 00:45:42', '2023-06-22 00:52:57'),
(37, 2, '0000000055', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-22 00:46:09', '2023-06-22 00:52:57'),
(38, 1, '0000000056', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-22 00:59:37', '2023-06-22 01:05:37'),
(39, 2, '0000000056', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-22 00:59:57', '2023-06-22 01:05:37'),
(40, 1, '0000000057', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-24 23:25:37', '2023-06-24 23:32:26'),
(41, 2, '0000000057', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-24 23:26:04', '2023-06-24 23:32:26'),
(42, 1, '0000000058', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-25 00:11:21', '2023-06-25 00:20:12'),
(43, 2, '0000000058', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-25 00:11:45', '2023-06-25 00:20:12'),
(44, 1, '0000000059', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-25 00:51:57', '2023-06-25 01:01:11'),
(45, 2, '0000000059', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-25 00:52:20', '2023-06-25 01:01:11'),
(46, 2, '0000000060', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-25 09:24:27', '2023-06-25 09:34:53'),
(47, 1, '0000000060', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-25 09:24:33', '2023-06-25 09:34:53'),
(48, 2, '0000000061', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-25 10:29:14', NULL),
(49, 1, '0000000061', 10.00, 0.00, 0.00, 0, 0, 0, 'leave', '1', '2023-06-25 10:29:21', NULL),
(50, 2, '0000000062', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-26 22:05:06', '2023-06-26 22:13:16'),
(51, 1, '0000000062', 10.00, 0.00, 0.00, 0, 0, 0, 'finished', '1', '2023-06-26 22:05:16', '2023-06-26 22:13:16');

--
-- Triggers `users_table`
--
DROP TRIGGER IF EXISTS `users_table_after_insert`;
DELIMITER $$
CREATE TRIGGER `users_table_after_insert` AFTER INSERT ON `users_table` FOR EACH ROW BEGIN
	DECLARE roomid INT;
    DECLARE chiptype ENUM('cash','free');
    
    SELECT master_rummy_main_rooms.room_id, master_rummy_chip_type.name INTO roomid, chiptype 
    FROM master_rummy_main_rooms 
    INNER JOIN master_rummy_chip_type USING (chip_type_id) 
    INNER JOIN create_regular_room_tables ON create_regular_room_tables.room_id = master_rummy_main_rooms.room_id
    WHERE create_regular_room_tables.table_id = NEW.table_id;
    
    IF (chiptype = 'cash') THEN		
		INSERT INTO users_real_chips (user_id, chips, paymode, remark, description) VALUES (NEW.user_id, NEW.in_chips, "debit", NEW.table_id, "Join Table");
    END IF;
    
    IF (chiptype = 'free') THEN		
		INSERT INTO users_free_chips (user_id, chips, paymode, remark, description) VALUES (NEW.user_id, NEW.in_chips, "debit", NEW.table_id, "Join Table");
    END IF; 
    
    UPDATE create_regular_room_tables SET total_players = total_players + 1 WHERE table_id = NEW.table_id;  
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `users_table_before_update`;
DELIMITER $$
CREATE TRIGGER `users_table_before_update` BEFORE UPDATE ON `users_table` FOR EACH ROW BEGIN
	IF (OLD.status = 'join' && NEW.status = 'leave') THEN		
		UPDATE create_regular_room_tables SET total_players = total_players - 1 WHERE table_id = NEW.table_id; 
    END IF;     
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_verification_email`
--

DROP TABLE IF EXISTS `users_verification_email`;
CREATE TABLE IF NOT EXISTS `users_verification_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `otp_no` int(11) NOT NULL,
  `otp_sent_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_verify_on` datetime DEFAULT NULL,
  `otp_expiry_on` datetime NOT NULL,
  `otp_verify_status` int(1) NOT NULL DEFAULT 0,
  `device_id` varchar(32) DEFAULT NULL,
  `device_details` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_verification_email`
--

INSERT INTO `users_verification_email` (`id`, `user_id`, `email`, `otp_no`, `otp_sent_on`, `otp_verify_on`, `otp_expiry_on`, `otp_verify_status`, `device_id`, `device_details`) VALUES
(6, 4, 'barath@gmail.com', 526745, '2022-12-29 07:11:18', NULL, '2022-12-29 12:46:18', 0, NULL, NULL),
(7, 4, 'lavanya@vinorastudios.in', 114106, '2022-12-29 07:11:34', '2022-12-29 12:46:08', '2022-12-29 12:46:34', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(8, 4, 'lavanya@vinorastudios.in', 260713, '2022-12-29 07:12:44', NULL, '2022-12-29 12:47:44', 0, NULL, NULL),
(9, 4, 'lavanya@vinorastudios.in', 871906, '2023-01-19 07:14:40', '2023-01-19 12:45:15', '2023-01-19 12:49:40', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(10, 4, 'lavanya@vinorastudios.in', 219267, '2023-02-01 09:04:02', NULL, '2023-02-01 14:39:02', 0, NULL, NULL),
(11, 4, 'lavanya@vinorastudios.in', 697702, '2023-02-01 09:04:56', NULL, '2023-02-01 14:39:56', 0, NULL, NULL),
(12, 4, 'lavanya@vinorastudios.in', 694351, '2023-02-01 09:05:18', NULL, '2023-02-01 14:40:18', 0, NULL, NULL),
(13, 4, 'lavanya@vinorastudios.in', 980697, '2023-02-01 09:08:49', NULL, '2023-02-01 14:43:49', 0, NULL, NULL),
(14, 4, 'barath@vinorastudios.in', 762822, '2023-02-01 09:10:27', NULL, '2023-02-01 14:45:27', 0, NULL, NULL),
(15, 4, 'barath@vinorastudios.in', 584359, '2023-02-01 09:12:34', NULL, '2023-02-01 14:47:34', 0, NULL, NULL),
(16, 4, 'barath@vinorastudios.in', 388290, '2023-02-01 09:12:54', NULL, '2023-02-01 14:47:54', 0, NULL, NULL),
(17, 4, 'barath@vinorastudios.in', 900639, '2023-02-01 09:13:50', NULL, '2023-02-01 14:48:50', 0, NULL, NULL),
(18, 4, 'barath@vinorastudios.in', 291283, '2023-02-01 09:17:09', NULL, '2023-02-01 14:52:09', 0, NULL, NULL),
(19, 4, 'barath@vinorastudios.in', 785161, '2023-02-01 09:17:28', NULL, '2023-02-01 14:52:28', 0, NULL, NULL),
(20, 4, 'barath@vinorastudios.in', 137774, '2023-02-01 09:18:33', NULL, '2023-02-01 14:53:33', 0, NULL, NULL),
(21, 4, 'barath@vinorastudios.in', 873503, '2023-02-01 09:24:52', NULL, '2023-02-01 14:59:52', 0, NULL, NULL),
(22, 4, 'barath@vinorastudios.in', 361391, '2023-02-01 09:25:43', NULL, '2023-02-01 15:00:43', 0, NULL, NULL),
(23, 4, 'barath@vinorastudios.in', 851433, '2023-02-01 09:27:45', NULL, '2023-02-01 15:02:45', 0, NULL, NULL),
(24, 4, 'barath@vinorastudios.in', 314942, '2023-02-01 09:47:37', NULL, '2023-02-01 15:22:37', 0, NULL, NULL),
(25, 4, 'barath@vinorastudios.in', 448998, '2023-02-01 09:47:44', NULL, '2023-02-01 15:22:44', 0, NULL, NULL),
(26, 4, 'barath@vinorastudios.in', 231183, '2023-02-01 09:47:48', NULL, '2023-02-01 15:22:48', 0, NULL, NULL),
(27, 4, 'barath@vinorastudios.in', 737173, '2023-02-01 09:48:11', NULL, '2023-02-01 15:23:11', 0, NULL, NULL),
(28, 4, 'barath@vinorastudios.in', 424065, '2023-02-01 13:10:10', NULL, '2023-02-01 18:45:10', 0, NULL, NULL),
(29, 4, 'barath@vinorastudios.in', 947613, '2023-02-01 13:11:27', NULL, '2023-02-01 18:46:27', 0, NULL, NULL),
(30, 4, 'barath@vinorastudios.in', 349194, '2023-02-01 13:11:39', NULL, '2023-02-01 18:46:39', 0, NULL, NULL),
(31, 4, 'barath@vinorastudios.in', 766601, '2023-02-01 13:12:45', NULL, '2023-02-01 18:47:45', 0, NULL, NULL),
(32, 4, 'barath@vinorastudios.in', 662214, '2023-02-01 13:14:10', NULL, '2023-02-01 18:49:10', 0, NULL, NULL),
(33, 4, 'barath@vinorastudios.in', 763636, '2023-02-01 13:17:51', NULL, '2023-02-01 18:52:51', 0, NULL, NULL),
(34, 4, 'barath@vinorastudios.in', 281135, '2023-02-01 13:18:08', NULL, '2023-02-01 18:53:08', 0, NULL, NULL),
(35, 4, 'barath@vinorastudios.in', 971079, '2023-02-01 13:20:54', NULL, '2023-02-01 18:55:54', 0, NULL, NULL),
(36, 4, 'barath@vinorastudios.in', 181179, '2023-02-01 13:24:49', NULL, '2023-02-01 18:59:49', 0, NULL, NULL),
(37, 4, 'barath@vinorastudios.in', 558356, '2023-02-01 13:25:15', NULL, '2023-02-01 19:00:15', 0, NULL, NULL),
(38, 4, 'barath@vinorastudios.in', 717569, '2023-02-02 04:49:11', NULL, '2023-02-02 10:24:11', 0, NULL, NULL),
(39, 4, 'barath@vinorastudios.in', 146830, '2023-02-02 05:00:46', NULL, '2023-02-02 10:35:46', 0, NULL, NULL),
(40, 4, 'barath@vinorastudios.in', 238250, '2023-02-02 05:02:02', NULL, '2023-02-02 10:37:02', 0, NULL, NULL),
(41, 4, 'barath@vinorastudios.in', 190874, '2023-02-02 05:03:52', NULL, '2023-02-02 10:38:52', 0, NULL, NULL),
(42, 4, 'barath@vinorastudios.in', 300014, '2023-02-02 05:05:09', NULL, '2023-02-02 10:40:09', 0, NULL, NULL),
(43, 4, 'barath@vinorastudios.in', 868034, '2023-02-02 05:09:20', NULL, '2023-02-02 10:44:20', 0, NULL, NULL),
(44, 4, 'barath@vinorastudios.in', 615851, '2023-02-02 05:09:53', NULL, '2023-02-02 10:44:53', 0, NULL, NULL),
(45, 4, 'barath@vinorastudios.in', 191973, '2023-02-02 05:11:21', NULL, '2023-02-02 10:46:21', 0, NULL, NULL),
(46, 4, 'barath@vinorastudios.in', 902911, '2023-02-02 05:12:30', NULL, '2023-02-02 10:47:30', 0, NULL, NULL),
(47, 4, 'barath@vinorastudios.in', 651416, '2023-02-02 05:13:39', NULL, '2023-02-02 10:48:39', 0, NULL, NULL),
(48, 4, 'barath@vinorastudios.in', 763212, '2023-02-02 06:37:43', NULL, '2023-02-02 12:12:43', 0, NULL, NULL),
(49, 4, 'barath@vinorastudios.in', 245953, '2023-02-02 06:38:00', NULL, '2023-02-02 12:13:00', 0, NULL, NULL),
(50, 4, 'barath@vinorastudios.in', 342027, '2023-02-02 06:39:04', NULL, '2023-02-02 12:14:04', 0, NULL, NULL),
(51, 4, 'barath@vinorastudios.in', 517965, '2023-02-02 06:41:12', NULL, '2023-02-02 12:16:12', 0, NULL, NULL),
(52, 4, 'barath@vinorastudios.in', 426948, '2023-02-02 06:41:27', NULL, '2023-02-02 12:16:27', 0, NULL, NULL),
(53, 4, 'barath@vinorastudios.in', 886377, '2023-02-02 07:12:09', NULL, '2023-02-02 12:47:09', 0, NULL, NULL),
(54, 4, 'barath@vinorastudios.in', 655673, '2023-02-02 07:25:47', NULL, '2023-02-02 13:00:47', 0, NULL, NULL),
(55, 4, 'barath@vinorastudios.in', 347943, '2023-02-02 07:26:07', NULL, '2023-02-02 13:01:07', 0, NULL, NULL),
(56, 4, 'barath@vinorastudios.in', 937747, '2023-02-11 12:24:48', NULL, '2023-02-11 17:59:48', 0, NULL, NULL),
(57, 4, 'barath@vinorastudios.in', 904969, '2023-02-11 12:26:47', NULL, '2023-02-11 18:01:47', 0, NULL, NULL),
(58, 4, 'barath@vinorastudios.in', 689135, '2023-02-11 12:27:21', NULL, '2023-02-11 18:02:21', 0, NULL, NULL),
(59, 4, 'barath@vinorastudios.in', 765186, '2023-02-11 12:30:03', NULL, '2023-02-11 18:05:03', 0, NULL, NULL),
(60, 4, 'barath@vinorastudios.in', 398615, '2023-02-11 12:30:47', NULL, '2023-02-11 18:05:47', 0, NULL, NULL),
(61, 4, 'barath@vinorastudios.in', 351011, '2023-02-11 12:31:14', NULL, '2023-02-11 18:06:14', 0, NULL, NULL),
(62, 4, 'barath@vinorastudios.in', 345938, '2023-02-11 12:31:25', NULL, '2023-02-11 18:06:25', 0, NULL, NULL),
(63, 4, 'barath@vinorastudios.in', 145583, '2023-02-11 12:33:05', '2023-02-11 18:05:21', '2023-02-11 18:08:05', 1, '27.5.216.189', 'PostmanRuntime/7.30.1'),
(64, 4, 'lavanya@vinorastudios.in', 369397, '2023-03-03 12:37:06', NULL, '2023-03-03 18:12:06', 0, NULL, NULL),
(65, 4, 'lavanya@vinorastudios.in', 162229, '2023-03-03 12:39:11', NULL, '2023-03-03 18:14:11', 0, NULL, NULL),
(66, 4, 'lavanya@vinorastudios.in', 703632, '2023-03-03 12:41:05', NULL, '2023-03-03 18:16:05', 0, NULL, NULL),
(67, 4, 'lavanya@vinorastudios.in', 659025, '2023-03-03 12:41:08', NULL, '2023-03-03 18:16:08', 0, NULL, NULL),
(68, 4, 'lavanya@vinorastudios.in', 102644, '2023-03-03 12:41:20', NULL, '2023-03-03 18:16:20', 0, NULL, NULL),
(69, 4, 'barath@vinorastudios.in', 673712, '2023-03-03 12:41:39', NULL, '2023-03-03 18:16:39', 0, NULL, NULL),
(70, 4, 'barath@vinorastudios.in', 227968, '2023-03-03 12:42:06', NULL, '2023-03-03 18:17:06', 0, NULL, NULL),
(71, 4, 'barath@vinorastudios.in', 633487, '2023-03-03 12:42:57', NULL, '2023-03-03 18:17:57', 0, NULL, NULL),
(72, 4, 'lavanya@vinorastudios.in', 424668, '2023-03-03 12:43:16', NULL, '2023-03-03 18:18:16', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_verification_kyc`
--

DROP TABLE IF EXISTS `users_verification_kyc`;
CREATE TABLE IF NOT EXISTS `users_verification_kyc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pan_no` varchar(255) NOT NULL,
  `pc_file` varchar(255) NOT NULL,
  `pc_verify_status` int(11) NOT NULL DEFAULT 0 COMMENT 'incomplete(0), complete(1), \r\ncancel(2)',
  `pc_requested_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `pc_verified_by` int(11) DEFAULT NULL,
  `pc_verify_by_ip_address` varchar(255) DEFAULT NULL,
  `pc_verified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_verification_kyc`
--

INSERT INTO `users_verification_kyc` (`id`, `user_id`, `pan_no`, `pc_file`, `pc_verify_status`, `pc_requested_on`, `pc_verified_by`, `pc_verify_by_ip_address`, `pc_verified_on`) VALUES
(2, 4, 'EJEP0041JP', 'aaadhar.jpg', 2, '2022-12-15 12:25:18', 74, '115.97.101.184', '2023-02-13 14:25:07'),
(4, 4, 'PAN234', '9ef28692db4e6d96.jpg', 1, '2022-12-16 12:21:46', 1, '192.168.1.50', '2022-12-16 13:36:19'),
(3, 5, 'EJEP0041JP', 'frvd.pdf', 2, '2022-12-15 12:25:18', 74, '115.97.101.184', '2023-01-13 15:37:58'),
(5, 5, 'PAN234', '2ca4b7406fb10083.jpg', 0, '2022-12-16 12:22:19', 1, '192.168.1.50', '2022-12-17 10:54:53'),
(6, 5, 'PAN234', '2504a2e706dbf9f8.jpg', 2, '2022-12-16 12:22:52', 1, '192.168.1.50', '2022-12-17 12:33:08'),
(7, 5, 'PAN23478', '6ddd6e25afd85a2c.pdf', 0, '2022-12-27 07:28:39', NULL, '192.168.1.50', NULL),
(8, 5, 'PAN23478', 'cf1299bb72398fa1.pdf', 0, '2022-12-27 07:30:01', NULL, NULL, NULL),
(9, 5, 'PAN23478', '31473d704b28af18.jpg', 0, '2022-12-27 07:34:12', NULL, NULL, NULL),
(10, 5, 'PAN23478', '5775ff58205986da.png', 0, '2022-12-27 07:36:20', NULL, NULL, NULL),
(11, 5, 'PAN23478', '7c1ca82d3f5e24b8.png', 0, '2022-12-27 08:33:16', NULL, NULL, NULL),
(12, 5, 'PAN23478', '529dabaf35e42cb1.png', 0, '2022-12-27 08:36:53', NULL, NULL, NULL),
(13, 5, 'PAN77089', 'bcad64e4b6e2c070.jpg', 0, '2023-01-13 07:26:42', NULL, NULL, NULL),
(14, 5, 'PAN77089', 'bd9943e6508fc20b.jpg', 0, '2023-01-19 07:27:02', NULL, NULL, NULL),
(15, 5, 'PAN77089', '669bd67e91d42887.jpg', 0, '2023-02-13 08:41:13', NULL, NULL, NULL),
(16, 5, 'PAN77089', '077b82d8f3e7a54b.jpg', 0, '2023-02-17 12:18:58', NULL, NULL, NULL),
(17, 5, 'PAN77089', 'ddfddb4851cd30a5.jpg', 0, '2023-02-20 05:45:42', NULL, NULL, NULL),
(18, 5, 'PAN77089', '85fd7b1ae0205a89.jpg', 0, '2023-02-20 05:46:16', NULL, NULL, NULL),
(19, 5, 'PAN77089', '96e9523933a6e9c7.jpg', 0, '2023-02-20 10:16:33', NULL, NULL, NULL),
(20, 5, 'PAN77089', 'd01089336c42eac5.jpg', 0, '2023-02-25 11:53:17', NULL, NULL, NULL),
(21, 4, 'PAN77089', '5e43b51d30b9e37b.jpg', 0, '2023-02-25 11:59:55', NULL, NULL, NULL),
(22, 4, 'PAN77089', '4e13a700340bdef6.jpg', 0, '2023-02-27 10:23:06', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_verification_phone_no`
--

DROP TABLE IF EXISTS `users_verification_phone_no`;
CREATE TABLE IF NOT EXISTS `users_verification_phone_no` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mobile_country_code` int(5) DEFAULT 91,
  `mobile` varchar(10) DEFAULT NULL,
  `mobile_otp` int(11) NOT NULL,
  `otp_sent_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_verify_on` datetime DEFAULT NULL,
  `otp_expiry_on` datetime NOT NULL,
  `otp_verify_status` int(1) NOT NULL DEFAULT 0,
  `device_id` varchar(32) DEFAULT NULL,
  `device_details` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_verification_phone_no`
--

INSERT INTO `users_verification_phone_no` (`id`, `user_id`, `mobile_country_code`, `mobile`, `mobile_otp`, `otp_sent_on`, `otp_verify_on`, `otp_expiry_on`, `otp_verify_status`, `device_id`, `device_details`) VALUES
(25, 4, 91, '9873466210', 921664, '2023-01-19 07:10:58', NULL, '2023-01-19 12:45:58', 0, NULL, NULL),
(3, 51, 91, '7708596754', 348741, '2022-12-17 05:33:24', NULL, '2022-12-17 11:08:24', 0, NULL, NULL),
(4, 51, 91, '7708596754', 867024, '2022-12-17 05:35:11', NULL, '2022-12-17 11:10:11', 0, NULL, NULL),
(5, 51, 91, '9943121327', 534637, '2022-12-17 05:37:19', NULL, '2022-12-17 11:12:19', 0, NULL, NULL),
(6, 51, 91, '9943121327', 731491, '2022-12-17 05:38:12', NULL, '2022-12-17 11:13:12', 0, NULL, NULL),
(7, 51, 91, '9943126327', 505994, '2022-12-17 05:38:20', NULL, '2022-12-17 11:13:20', 0, NULL, NULL),
(8, 51, 91, '9865412307', 642892, '2022-12-17 07:59:59', NULL, '2022-12-17 13:34:59', 0, NULL, NULL),
(9, 51, 91, '9865412307', 779885, '2022-12-17 08:00:17', NULL, '2022-12-17 13:35:17', 0, NULL, NULL),
(10, 51, 91, '9654198787', 666012, '2022-12-19 00:24:20', NULL, '2022-12-19 05:59:20', 0, NULL, NULL),
(11, 51, 91, '9654198787', 974464, '2022-12-19 00:35:57', NULL, '2022-12-19 06:10:57', 0, NULL, NULL),
(12, 51, 91, '9654198787', 441900, '2022-12-19 00:36:10', NULL, '2022-12-19 06:11:10', 0, NULL, NULL),
(13, 51, 91, '9654198787', 186757, '2022-12-19 00:54:28', NULL, '2022-12-19 06:29:28', 0, NULL, NULL),
(14, 51, 91, '9654198787', 373617, '2022-12-19 00:54:56', NULL, '2022-12-19 06:29:56', 0, NULL, NULL),
(15, 51, 91, '9654198787', 742016, '2022-12-19 00:55:11', NULL, '2022-12-19 06:30:11', 0, NULL, NULL),
(16, 51, 91, '9654198787', 759545, '2022-12-19 00:56:41', NULL, '2022-12-19 06:31:41', 0, NULL, NULL),
(17, 51, 91, '9654198787', 673575, '2022-12-19 00:57:54', NULL, '2022-12-19 06:32:54', 0, NULL, NULL),
(18, 51, 91, '9654198787', 344143, '2022-12-19 06:41:53', '2022-12-19 12:54:12', '2022-12-19 12:55:53', 1, '115.97.101.180', 'PostmanRuntime/7.30.0'),
(19, 51, 91, '9654198787', 724349, '2022-12-19 09:32:51', NULL, '2022-12-19 15:07:51', 0, NULL, NULL),
(20, 51, 91, '9654198787', 989477, '2022-12-19 09:57:48', NULL, '2022-12-19 15:32:48', 0, NULL, NULL),
(21, 4, 91, '9654198784', 715119, '2022-12-29 06:34:02', NULL, '2022-12-29 12:09:02', 0, NULL, NULL),
(22, 4, 91, '9654198784', 849895, '2022-12-29 06:34:27', NULL, '2022-12-29 12:09:27', 0, NULL, NULL),
(23, 4, 91, '9500167496', 456924, '2022-12-29 06:49:24', '2022-12-29 12:20:00', '2022-12-29 12:24:24', 1, '98.01.0.12', 'sql'),
(24, 4, 91, '9500167496', 417800, '2022-12-29 06:54:20', '2022-12-29 12:25:33', '2022-12-29 12:29:20', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(26, 4, 91, '9873466210', 815569, '2023-01-19 07:14:13', '2023-01-19 12:44:29', '2023-01-19 12:49:13', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(27, 4, 91, '9873466210', 738102, '2023-02-11 12:18:45', NULL, '2023-02-11 17:53:45', 0, NULL, NULL),
(28, 4, 91, '9873466210', 317406, '2023-02-11 12:24:23', '2023-02-11 17:54:38', '2023-02-11 17:59:23', 1, '27.5.216.189', 'PostmanRuntime/7.30.1');

-- --------------------------------------------------------

--
-- Table structure for table `users_verify_forget_password_email`
--

DROP TABLE IF EXISTS `users_verify_forget_password_email`;
CREATE TABLE IF NOT EXISTS `users_verify_forget_password_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_otp` varchar(255) NOT NULL,
  `otp_sent_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_verify_on` datetime DEFAULT NULL,
  `otp_expiry_on` datetime NOT NULL,
  `otp_verify_status` int(1) NOT NULL DEFAULT 0,
  `device_id` varchar(32) DEFAULT NULL,
  `device_details` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_verify_forget_password_email`
--

INSERT INTO `users_verify_forget_password_email` (`id`, `user_id`, `email`, `email_otp`, `otp_sent_on`, `otp_verify_on`, `otp_expiry_on`, `otp_verify_status`, `device_id`, `device_details`) VALUES
(1, 4, 'barath@vinorastudios.in', '654321', '2022-12-26 08:59:33', '2022-12-26 08:59:33', '2023-01-02 14:29:33', 0, '192.168.15.132', 'sql'),
(2, 4, 'barath@vinorastudios.in', '302065', '2022-12-26 09:00:45', NULL, '2023-01-02 14:30:45', 0, NULL, NULL),
(3, 4, 'barath@vinorastudios.in', '739710', '2022-12-26 09:02:34', '2022-12-26 15:20:00', '2023-01-02 14:32:34', 1, '198.02.0.24', 'sql'),
(4, 4, 'barath@vinorastudios.in', '957527', '2022-12-26 09:39:43', '2022-12-26 15:11:25', '2023-01-02 15:09:43', 1, '115.97.101.180', 'PostmanRuntime/7.30.0'),
(5, 4, 'barath@vinorastudios.in', '643658', '2022-12-26 11:42:50', NULL, '2023-01-02 17:12:50', 0, NULL, NULL),
(6, 105, 'lavanya@gmail.com', '200703', '2022-12-28 10:17:25', NULL, '2022-12-28 15:52:25', 0, NULL, NULL),
(7, 5, 'lavanya@vinorastudios.in', '999184', '2022-12-28 10:27:16', NULL, '2022-12-28 16:02:16', 0, NULL, NULL),
(8, 5, 'lavanya@vinorastudios.in', '879730', '2022-12-28 10:27:45', NULL, '2022-12-28 16:02:45', 0, NULL, NULL),
(9, 5, 'lavanya@vinorastudios.in', '639381', '2022-12-28 10:30:27', NULL, '2022-12-28 16:05:27', 0, NULL, NULL),
(10, 5, 'lavanya@vinorastudios.in', '225418', '2022-12-28 10:42:45', NULL, '2022-12-28 16:17:45', 0, NULL, NULL),
(11, 5, 'lavanya@vinorastudios.in', '415894', '2022-12-28 12:21:23', '2022-12-28 17:53:25', '2022-12-28 17:56:23', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(12, 4, 'barath@vinorastudios.in', '347147', '2023-01-13 05:38:28', NULL, '2023-01-13 11:13:28', 0, NULL, NULL),
(13, 4, 'barath@vinorastudios.in', '240312', '2023-01-13 05:38:43', NULL, '2023-01-13 11:13:43', 0, NULL, NULL),
(14, 4, 'barath@vinorastudios.in', '385051', '2023-01-13 05:39:05', NULL, '2023-01-13 11:14:05', 0, NULL, NULL),
(15, 4, 'barath@vinorastudios.in', '519254', '2023-01-13 05:55:42', NULL, '2023-01-13 11:30:42', 0, NULL, NULL),
(16, 5, 'lavanya@vinorastudios.in', '261339', '2023-01-13 05:57:16', NULL, '2023-01-13 11:32:16', 0, NULL, NULL),
(17, 4, 'barath@vinorastudios.in', '579354', '2023-01-19 07:01:30', NULL, '2023-01-19 12:36:30', 0, NULL, NULL),
(18, 4, 'barath@vinorastudios.in', '256317', '2023-02-11 12:05:50', '2023-02-11 17:38:36', '2023-02-11 17:40:50', 1, '27.5.216.189', 'PostmanRuntime/7.30.1');

-- --------------------------------------------------------

--
-- Table structure for table `users_verify_forget_password_mobile`
--

DROP TABLE IF EXISTS `users_verify_forget_password_mobile`;
CREATE TABLE IF NOT EXISTS `users_verify_forget_password_mobile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `phone_no` varchar(10) DEFAULT NULL,
  `mobile_otp` varchar(255) NOT NULL,
  `otp_sent_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_verify_on` datetime DEFAULT NULL,
  `otp_expiry_on` datetime NOT NULL,
  `otp_verify_status` int(1) NOT NULL DEFAULT 0,
  `device_id` varchar(32) DEFAULT NULL,
  `device_details` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_verify_forget_password_mobile`
--

INSERT INTO `users_verify_forget_password_mobile` (`id`, `user_id`, `phone_no`, `mobile_otp`, `otp_sent_on`, `otp_verify_on`, `otp_expiry_on`, `otp_verify_status`, `device_id`, `device_details`) VALUES
(1, 4, '7708585967', '987654', '2022-12-26 11:37:01', '2022-12-26 11:37:01', '2022-12-26 17:12:01', 0, '192.168.15.132', 'sql'),
(2, 4, '9876543210', '559425', '2022-12-28 12:38:45', '2022-12-28 18:13:02', '2022-12-28 18:13:45', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(3, 4, '9876543210', '260767', '2023-01-13 06:01:37', '2023-01-13 11:32:09', '2023-01-13 11:36:37', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(4, 4, '9876543210', '940027', '2023-01-19 07:02:32', NULL, '2023-01-19 12:37:32', 0, NULL, NULL),
(5, 4, '9876543210', '675502', '2023-01-19 07:03:30', '2023-01-19 12:34:30', '2023-01-19 12:38:30', 1, '27.5.216.189', 'PostmanRuntime/7.30.0'),
(6, 119, '9944916859', '602205', '2023-02-11 12:12:05', '2023-02-11 17:43:26', '2023-02-11 17:47:05', 1, '27.5.216.189', 'PostmanRuntime/7.30.1');

-- --------------------------------------------------------

--
-- Table structure for table `ws_faq`
--

DROP TABLE IF EXISTS `ws_faq`;
CREATE TABLE IF NOT EXISTS `ws_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `answer` longtext NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ws_faq`
--

INSERT INTO `ws_faq` (`id`, `title`, `answer`, `status`, `created_at`) VALUES
(1, 'Is it legal to play rummy in India?', 'Yes, in a selected few states it is legal to play.', '1', '2023-02-04 00:18:33'),
(2, 'Do I have to give my mobile number and email ID?', 'Yes, you have to give your mobile number, to receive OTP’s and email is optional, but giving your email ID is considered safe for all rummy games.', '1', '2023-02-04 00:18:33'),
(3, 'Can I play Rummy without paying Cash?', 'Yes, you can play as much as practice games you like, and move on to cash games, when you feel you are ready.', '1', '2023-02-04 00:19:11'),
(4, 'Is my Money safe on 7S Rummy?', 'Absolutely, 7S Rummy is very secure and safe to play.', '1', '2023-02-04 00:19:11'),
(5, 'When do I get my winnings from the game?', 'Once you win a game, you get your winnings transferred to your 7S Rummy account instantly. From your 7S Rummy account, you can get your winnings transferred to your bank account within seconds.', '1', '2023-02-04 00:19:30'),
(6, 'How do I play rummy games on 7s Rummy?', 'It’s very simple. Just download the app from <a href=\"#\">https://www.7srummy.com </a>and install it. Register\r\nfor free and start playing your favorite rummy games.', '1', '2023-02-04 00:45:15'),
(7, 'Do I have to give my mobile number and email address?', 'The mobile number is used to verify your identity and you also get all your OTPs and important messages\r\non your number. Sharing the email ID is optional but highly recommended to get all the rummy related\r\noffers.', '1', '2023-02-04 00:45:15'),
(8, 'How can I add cash to my 7s Rummy account?', 'You can use your credit card, debit card, net banking, and UPI IDs to add cash on 7s Rummy.', '1', '2023-02-04 00:45:15'),
(9, 'Can I play rummy online without adding any cash?', 'Yes, you can play as many practice games as you want on 7s Rummy. You can move to the cash games when\r\nyou feel you’re ready for it.\r\n', '1', '2023-02-04 00:45:15'),
(10, 'Is my money safe on 7s Rummy?', 'Absolutely! 7s Rummy uses a Thawte SSL Secured platform which ensures your money, data, and identity are\n safe and secure.', '1', '2023-02-04 00:45:15'),
(11, 'When do I get my winning amount in my account or wallet?', 'Once you win a game, you get your winnings transferred to your 7s Rummy account instantly. From your 7s\r\n    Rummy account, you can get your winnings transferred to your bank account within seconds.', '1', '2023-02-04 00:45:15'),
(12, 'My deposit attempt failed but the amount has been deducted from my bank account?', 'Absolutely ! 7s Rummy is a Thawte SSL Secured platform which ensures that your money, data and identity\r\n    are absolutely safe and secure.', '1', '2023-02-04 00:45:15'),
(13, 'My internet is working fine but shows a network issues?', 'We do understand your problem that you faced a disconnection issue while playing.Online gaming\r\n      requires constant and stable internet connection. As a user, we feel that our internet is stable and\r\n      working fine. Also, when we browse another website it seems to be running inflow. But even a slight\r\n      fluctuation in your internet leads to disconnection of games at 7s Rummy.\r\n    \r\n      For a smooth experience, make sure that your device is connected to a reliable network and that your\r\n      device\'s signal is strong. We recommend playing over WiFi, or at least a solid 3G/4G connection.', '1', '2023-02-04 00:45:15'),
(14, 'How to update KYC Document?', 'To update you KYC document, visit \"Profile\" Section on your Lobby page, under the same you would\r\n      have an option called as \"KYC\", In the same option you can update your KYC details.\r\n\r\n      Once your KYC details have been updated it would take 2 Business days to verify. You would receive a\r\n      Mail & an SMS on your KYC approval or disapproval status.', '1', '2023-02-04 00:46:53'),
(15, 'Why is my KYC Document Rejected?', 'To know the reason for document rejection, visit your KYC tab under the “Profile” option', '1', '2023-02-04 00:46:53'),
(16, 'How to Refer a Friend?', 'You can refer a friend with the \"Refer & earn\" option which is available in the main 7s Rummy page\r\n    through Whats app and various social platforms.', '1', '2023-02-04 00:46:53'),
(17, 'How can I join a tournament?', 'You can access the Tournaments which you can participate in by clicking on the Tournament icon on the\r\n    Lobby page of our website/App.\r\n', '1', '2023-02-04 00:46:53'),
(18, 'What is TDS and how does it work?', 'TDS is the “Tax Deducted at Source,” which we are legally obliged to deduct on all winnings of more than\r\n    Rs.10,000/- in a single tournament, or a single Pool Rummy game or Points Rummy game.', '1', '2023-02-04 00:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `ws_news`
--

DROP TABLE IF EXISTS `ws_news`;
CREATE TABLE IF NOT EXISTS `ws_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `sub_description` varchar(500) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ws_news`
--

INSERT INTO `ws_news` (`id`, `title`, `image`, `content`, `sub_description`, `link`, `status`, `created_at`) VALUES
(1, 'WELCOME BONUS AND MORE', 'd816013af15882fb.png', 'Welcome Bonus\nTERMS AND CONDITIONS\nThis offer is valid for all players who are depositing cash at 7SRummy.com\nThis offer is valid for only 1 transaction per player\nMinimum Cash Deposit of 25 is required to avail 100% bonus upto 15000\nBonus must be claimed within 30 days of depositing cash in the offer period.\nNo part of the welcome bonus will be added to a player\'s account after the 30-day offer period.\nOne player can have only one account on 7SRummy.com. If multiple accounts are detected, the entire bonus amount on all accounts will be fortified\nThe bonus offer may not be claimed in conjunction with any other bonus offers currently offered in 7S Rummy. Once you activate any bonus offere, any previous bonuses will expire automatically. You will receive disbursements from the active bonus\nThe Welcome bonus offer may be withdrawn by 7S rummy at any time if you violate any conditions of the terms of service on www.7srummy.com\nThe decision of the 7S rummy management is final in case of any disputes.', 'Start off your Rummy journey with 7S with exciting bonus offer, Add cash today and get upto 15000 + 1000 instant cash as Welcome Bonus. Refer to terms & conditions for more info.\n', 'welcome_bonus', '0', '2023-02-03 23:31:05'),
(2, 'PRIZE INCREASED BY 200 000 INR', '7e00023da8422ede.png', 'How to Get Welcome Bonus', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod', '', '1', '2023-02-03 23:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `ws_our_games`
--

DROP TABLE IF EXISTS `ws_our_games`;
CREATE TABLE IF NOT EXISTS `ws_our_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type_heading` varchar(255) NOT NULL,
  `game_heading` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ws_our_games`
--

INSERT INTO `ws_our_games` (`id`, `title`, `type_heading`, `game_heading`, `image`, `status`, `created_at`) VALUES
(1, 'tournament', '80 pool', 'rummy', 'rummy.png', '1', '2023-01-25 06:35:37'),
(5, 'tournament', '240 pool', 'rummy', '7d235b6864f86153.jpg', '1', '2023-01-26 23:06:03'),
(6, 'tournament', '80 pool', 'rummy', 'rummy.png', '1', '2023-01-25 06:35:37'),
(7, 'tournament', '240 pool', 'rummy', '7d235b6864f86153.jpg', '1', '2023-01-26 23:06:03'),
(8, 'tournament', '80 pool', 'rummy', 'rummy.png', '1', '2023-01-25 06:35:37'),
(9, 'tournament', '240 pool', 'rummy', '7d235b6864f86153.jpg', '1', '2023-01-26 23:06:03'),
(10, 'rummy', 'types of rummy', 'jin rummy', 'b6965236a8a38a06.jpeg', '1', '2023-02-20 00:43:37');

-- --------------------------------------------------------

--
-- Table structure for table `ws_settings`
--

DROP TABLE IF EXISTS `ws_settings`;
CREATE TABLE IF NOT EXISTS `ws_settings` (
  `logo_image` varchar(500) NOT NULL,
  `footer` varchar(5000) NOT NULL,
  `banner_title` varchar(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ws_settings`
--

INSERT INTO `ws_settings` (`logo_image`, `footer`, `banner_title`) VALUES
('logo.png', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n</head>\r\n<body>\r\n<p>Copyright @2023 7s Rummy</p>\r\n</body>\r\n</html>', 'WELCOME BONUS UP TO 15000 INR');

-- --------------------------------------------------------

--
-- Table structure for table `ws_social_media`
--

DROP TABLE IF EXISTS `ws_social_media`;
CREATE TABLE IF NOT EXISTS `ws_social_media` (
  `facebook` varchar(2048) NOT NULL,
  `google` varchar(2048) NOT NULL,
  `playstore` varchar(2048) NOT NULL,
  `android` varchar(2048) NOT NULL,
  `ios` varchar(2048) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ws_social_media`
--

INSERT INTO `ws_social_media` (`facebook`, `google`, `playstore`, `android`, `ios`) VALUES
('https://www.facebook.com/', 'https://www.google.com/', 'https://www.playstore.com/', 'https://www.android.com/', 'https://www.ios.com/');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
