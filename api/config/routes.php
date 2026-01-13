<?php

// Define app routes
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AuthMiddleware;

return function (App $app) {

    //Home
    $app->get('/', \App\Action\Home\HomeAction::class)->setName('home');

    /**********************************************************/
    /********************  MASTER TABLES API  *****************/
    /**********************************************************/


    //........... Admin Portal Master Tables ............//

    //Master Admin Roles @
    $app->group(
        '/role',
        function (RouteCollectorProxy $app) { 

            $app->get('/get-roles', \App\Action\Master_Table\Master_Role\MasterRoleAction::class. ':getRoles')->add(AuthMiddleware::class)->setArgument('scope', 'get_role'); 
            $app->get('/get-role/{role-id}', \App\Action\Master_Table\Master_Role\MasterRoleAction::class. ':getOneRole')->add(AuthMiddleware::class)->setArgument('scope', 'get_role');
            // $app->post('/insert-admin-role', \App\Action\Master_Table\Master_Role\MasterRoleAction::class. ':insertRole')->add(AuthMiddleware::class)->setArgument('scope', 'add_role');
            // $app->post('/update-admin-role/{id}', \App\Action\Master_Table\Master_Role\MasterRoleAction::class. ':updateRole')->add(AuthMiddleware::class)->setArgument('scope', 'edit_role');
            // $app->get('/delete-admin-role/{role-id}', \App\Action\Master_Table\Master_Role\MasterRoleAction::class. ':deleteRoles')->add(AuthMiddleware::class)->setArgument('scope', 'delete_role');       
  
        }
    );

    //Master Admin Menu
    // $app->group(
    //     '/admin-menu',
    //     function (RouteCollectorProxy $app) { 
    //         $app->get('/get-all-menu', \App\Action\Master_Table\Admin_Menu\AdminMenuAction::class. ':getAllMenu')->add(AuthMiddleware::class)->setArgument('scope', 'get_menu'); 
    //         $app->get('/get-menu', \App\Action\Master_Table\Admin_Menu\AdminMenuAction::class. ':getMenu')->add(AuthMiddleware::class); 
    //         $app->post('/update-admin-menu/{menu-id}', \App\Action\Master_Table\Admin_Menu\AdminMenuAction::class. ':updateMenu')->add(AuthMiddleware::class)->setArgument('scope', 'edit_menu');
    //         $app->get('/menu-status/{menu-id}/{status}', \App\Action\Master_Table\Admin_Menu\AdminMenuAction::class. ':menuStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_menu');
           
    //     }
    // );

    //Master Admin Scope @
    $app->group(
        '/admin-scope',
        function (RouteCollectorProxy $app) { 
            $app->get('/get-all-scope', \App\Action\Master_Table\Admin_Scope\AdminScopeAction::class. ':getAllScope')->add(AuthMiddleware::class);       
            $app->post('/update-role-scope/{role-id}', \App\Action\Master_Table\Admin_Scope\AdminScopeAction::class. ':updateRoleScope')->add(AuthMiddleware::class)->setArgument('scope', 'edit_scope');      
        }
    );

    //........... Games Master Tables ............//

    //Master Rummy Variants 

    //add scope (get_rummy_variants, add_rummy_variants, edit_rummy_variants, status_rummy_variants, delete_rummy_variants)
    // $app->group(
    //     '/rummy-variants',
    //     function (RouteCollectorProxy $app) {
    //         $app->get('/get-all-rummy-variants', \App\Action\Master_Table\Rummy_Variants\RummyVariantsAction::class. ':getRummyVariants')->add(AuthMiddleware::class);
    //         $app->get('/get-variants-active-status', \App\Action\Master_Table\Rummy_Variants\RummyVariantsAction::class. ':getActiveRummyVariants')->add(AuthMiddleware::class);
    //         $app->post('/insert-rummy-variants', \App\Action\Master_Table\Rummy_Variants\RummyVariantsAction::class. ':insertRummyVariants')->add(AuthMiddleware::class);
    //         $app->post('/update-rummy-variants/{variants-id}', \App\Action\Master_Table\Rummy_Variants\RummyVariantsAction::class. ':updateRummyVariants')->add(AuthMiddleware::class);
    //         $app->get('/variants-status/{variants-id}/{status}', \App\Action\Master_Table\Rummy_Variants\RummyVariantsAction::class. ':rummyVariantStatus')->add(AuthMiddleware::class);
    //         $app->get('/delete-rummy-variants/{variants-id}', \App\Action\Master_Table\Rummy_Variants\RummyVariantsAction::class. ':deleteRummyVariants')->add(AuthMiddleware::class);
    //     }
    // );

    //add scope
    $app->group(
        '/rummy-format',
        function (RouteCollectorProxy $app) {
            $app->get('/get-rummy-format', \App\Action\Master_Table\Rummy_Format\RummyFormatAction::class. ':getRummyFormat')->add(AuthMiddleware::class);
            $app->get('/get-active-rummy-format', \App\Action\Master_Table\Rummy_Format\RummyFormatAction::class. ':getActiveRummyFormat')->add(AuthMiddleware::class); 
            $app->post('/insert-rummy-format', \App\Action\Master_Table\Rummy_Format\RummyFormatAction::class. ':insertRummyFormat')->add(AuthMiddleware::class);
            $app->post('/update-rummy-fromat/{format-id}', \App\Action\Master_Table\Rummy_Format\RummyFormatAction::class. ':updateRummyFormat')->add(AuthMiddleware::class);
            $app->get('/format-status/{format-id}/{status}', \App\Action\Master_Table\Rummy_Format\RummyFormatAction::class. ':rummyFormatStatus')->add(AuthMiddleware::class);
            //$app->get('/delete-rummy-format/{format-id}', \App\Action\Master_Table\Rummy_Format\RummyFormatAction::class. ':deleteRummyFormat')->add(AuthMiddleware::class);

        }
    );

    $app->group(
        '/rummy-format-types',
        function (RouteCollectorProxy $app) {
            $app->get('/get-rummy-format-types/{format-id}', \App\Action\Master_Table\Rummy_Format_Types\RummyFormatTypesAction::class. ':getRummyFormatTypes')->add(AuthMiddleware::class);
            $app->get('/get-active-rummy-format-types', \App\Action\Master_Table\Rummy_Format_Types\RummyFormatTypesAction::class. ':getActiveRummyFormatTypes')->add(AuthMiddleware::class); 
            $app->post('/insert-rummy-format-types', \App\Action\Master_Table\Rummy_Format_Types\RummyFormatTypesAction::class. ':insertRummyFormatTypes')->add(AuthMiddleware::class);
            $app->post('/update-rummy-fromat-types/{format-type-id}', \App\Action\Master_Table\Rummy_Format_Types\RummyFormatTypesAction::class. ':updateRummyFormatTypes')->add(AuthMiddleware::class);
            $app->get('/format-types-status/{format-type-id}/{status}', \App\Action\Master_Table\Rummy_Format_Types\RummyFormatTypesAction::class. ':rummyFormatTypeStatus')->add(AuthMiddleware::class);
            //$app->get('/delete-rummy-format-types/{format-type-id}', \App\Action\Master_Table\Rummy_Format_Types\RummyFormatTypesAction::class. ':deleteRummyFormatTypes')->add(AuthMiddleware::class);

        }
    );

    $app->group(
        '/rummy-max-seats',
        function (RouteCollectorProxy $app) {
            $app->get('/get-all-max-seats', \App\Action\Master_Table\Rummy_Max_Seats\RummyMaxSeatsAction::class. ':getMaxSeats')->add(AuthMiddleware::class);
            $app->get('/get-active-max-seats', \App\Action\Master_Table\Rummy_Max_Seats\RummyMaxSeatsAction::class. ':getActiveMaxSeats')->add(AuthMiddleware::class);
            $app->post('/insert-max-seats', \App\Action\Master_Table\Rummy_Max_Seats\RummyMaxSeatsAction::class. ':insertMaxSeats')->add(AuthMiddleware::class);
            $app->post('/update-max-seats/{seats-id}', \App\Action\Master_Table\Rummy_Max_Seats\RummyMaxSeatsAction::class. ':updateMaxSeats')->add(AuthMiddleware::class);
            $app->get('/max-seats-status/{seats-id}/{status}', \App\Action\Master_Table\Rummy_Max_Seats\RummyMaxSeatsAction::class. ':maxSeatsStatus')->add(AuthMiddleware::class);
            $app->get('/delete-max-seats/{seats-id}', \App\Action\Master_Table\Rummy_Max_Seats\RummyMaxSeatsAction::class. ':deleteMaxSeats')->add(AuthMiddleware::class);
        }
    );

    // $app->group(
    //     '/rummy-main-list',
    //     function (RouteCollectorProxy $app) {
    //         $app->get('/get-all-main-list', \App\Action\Master_Table\Rummy_Main_List\RummyMainListAction::class. ':getMainList')->add(AuthMiddleware::class);
    //         $app->get('/get-active-main-list', \App\Action\Master_Table\Rummy_Main_List\RummyMainListAction::class. ':getActiveMainList')->add(AuthMiddleware::class);
    //         $app->post('/insert-main-list', \App\Action\Master_Table\Rummy_Main_List\RummyMainListAction::class. ':insertMainList')->add(AuthMiddleware::class);
    //         $app->post('/update-main-list/{main-list-id}', \App\Action\Master_Table\Rummy_Main_List\RummyMainListAction::class. ':updateMainList')->add(AuthMiddleware::class);
    //         $app->get('/max-main-list/{main-list-id}/{status}', \App\Action\Master_Table\Rummy_Main_List\RummyMainListAction::class. ':mainListStatus')->add(AuthMiddleware::class);
    //         $app->get('/delete-main-list/{main-list-id}', \App\Action\Master_Table\Rummy_Main_List\RummyMainListAction::class. ':deleteMainList')->add(AuthMiddleware::class);
    //     }
    // );

    //deleted
    $app->group(
        '/match-type',
        function (RouteCollectorProxy $app) {
            $app->get('/get-match-types', \App\Action\Master_Table\Master_Game_Match_Types\MatchTypesAction::class. ':getMatchType')->add(AuthMiddleware::class)->setArgument('scope', 'get_match_type');
            $app->post('/insert-match', \App\Action\Master_Table\Master_Game_Match_Types\MatchTypesAction::class. ':insertMatchType')->add(AuthMiddleware::class)->setArgument('scope', 'add_match_type');
            $app->post('/update-match/{matchtype-id}', \App\Action\Master_Table\Master_Game_Match_Types\MatchTypesAction::class. ':updateMatchType')->add(AuthMiddleware::class)->setArgument('scope', 'edit_match_type');
            $app->get('/match-status/{matchtype-id}/{status}', \App\Action\Master_Table\Master_Game_Match_Types\MatchTypesAction::class. ':matchStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_match_type');
            $app->get('/delete-match/{matchtype-id}', \App\Action\Master_Table\Master_Game_Match_Types\MatchTypesAction::class. ':deleteMatchType')->add(AuthMiddleware::class)->setArgument('scope', 'delete_match_type');
        }
    );

    //deleted
    $app->group(
        '/game-type',
        function (RouteCollectorProxy $app) {
            $app->get('/get-game-types', \App\Action\Master_Table\Master_Game_Types\GameTypesAction::class. ':getGameTypes')->add(AuthMiddleware::class)->setArgument('scope', 'get_game_type'); //
            $app->post('/insert-game', \App\Action\Master_Table\Master_Game_Types\GameTypesAction::class. ':insertGameType')->add(AuthMiddleware::class)->setArgument('scope', 'add_game_type');
            $app->post('/update-game/{game-id}', \App\Action\Master_Table\Master_Game_Types\GameTypesAction::class. ':updateGameType')->add(AuthMiddleware::class)->setArgument('scope', 'edit_game_type');
            $app->get('/game-status/{game-id}/{status}', \App\Action\Master_Table\Master_Game_Types\GameTypesAction::class. ':gameStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_game_type');
            $app->get('/delete-game/{game-id}', \App\Action\Master_Table\Master_Game_Types\GameTypesAction::class. ':deleteGameType')->add(AuthMiddleware::class)->setArgument('scope', 'delete_game_type');

        }
    );

    //Master Game Table
    $app->group(
        '/game-table',
        function (RouteCollectorProxy $app) {
            $app->post('/get-game-table', \App\Action\Master_Table\Master_Game_Table\GameTableAction::class. ':getGameTable')->add(AuthMiddleware::class)->setArgument('scope', 'get_game_table');
            $app->post('/insert-game-table', \App\Action\Master_Table\Master_Game_Table\GameTableAction::class. ':insertGameTable')->add(AuthMiddleware::class)->setArgument('scope', 'add_game_table');
            $app->post('/update-game-table/{gametable-id}', \App\Action\Master_Table\Master_Game_Table\GameTableAction::class. ':updateGameTable')->add(AuthMiddleware::class)->setArgument('scope', 'edit_game_table');
            $app->get('/game-table-status/{gametable-id}/{status}', \App\Action\Master_Table\Master_Game_Table\GameTableAction::class. ':gametableStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_game_table');
            //$app->get('/delete-game-table/{gametable-id}', \App\Action\Master_Table\Master_Game_Table\GameTableAction::class. ':deleteGameTable')->add(AuthMiddleware::class)->setArgument('scope', 'delete_game_table');
        }
    );

    //Master Game Country
    // $app->group(
    //     '/country',
    //     function (RouteCollectorProxy $app) {
    //         $app->get('/get-country', \App\Action\Master_Table\Master_Game_Country\CountryAction::class. ':getCountry')->add(AuthMiddleware::class)->setArgument('scope', 'get_country');
    //         //$app->get('/get-country/{country-id}', \App\Action\Master_Table\Master_Game_Country\CountryAction::class. ':getOneCountry')->add(AuthMiddleware::class);
    //         //$app->post('/insert-country', \App\Action\Master_Table\Master_Game_Country\CountryAction::class. ':insertCountry')->add(AuthMiddleware::class);
    //         $app->post('/update-country/{country-id}', \App\Action\Master_Table\Master_Game_Country\CountryAction::class. ':updateCountry')->add(AuthMiddleware::class)->setArgument('scope', 'update_country');
    //         //$app->get('/delete-country/{country-id}', \App\Action\Master_Table\Master_Game_Country\CountryAction::class. ':deleteCountry')->add(AuthMiddleware::class);
    //     }
    // );

    //Master Game States
    $app->group(
        '/states',
        function (RouteCollectorProxy $app) {
            $app->get('/get-states', \App\Action\Master_Table\Master_Game_State\StateAction::class. ':getStates')->add(AuthMiddleware::class)->setArgument('scope', 'get_states');
            // $app->get('/get-state/{state-id}', \App\Action\Master_Table\Master_Game_State\StateAction::class. ':getOneState')->add(AuthMiddleware::class);
            $app->post('/update-state/{state-id}', \App\Action\Master_Table\Master_Game_State\StateAction::class. ':updateState')->add(AuthMiddleware::class)->setArgument('scope', 'edit_states');
            $app->get('/state-change-status/{state-id}/{status}', \App\Action\Master_Table\Master_Game_State\StateAction::class. ':ChangestateStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_states');
        }
    );


    //Master Game Bonus Types
    $app->group(
        '/bonus-type',
        function (RouteCollectorProxy $app) {
            $app->post('/get-bonus-type', \App\Action\Master_Table\Master_Game_Bonus\BonusTypeAction::class. ':getBonusType')->add(AuthMiddleware::class)->setArgument('scope', 'get_bonus_type');
            $app->post('/insert-bonus-type', \App\Action\Master_Table\Master_Game_Bonus\BonusTypeAction::class. ':insertBonusType')->add(AuthMiddleware::class)->setArgument('scope', 'add_bonus_type');
            $app->post('/update-bonus-type/{bonustype-id}', \App\Action\Master_Table\Master_Game_Bonus\BonusTypeAction::class. ':updateBonusType')->add(AuthMiddleware::class)->setArgument('scope', 'edit_bonus_type');
            $app->get('/game-bonus-status/{bonustype-id}/{status}', \App\Action\Master_Table\Master_Game_Bonus\BonusTypeAction::class. ':bonustypeStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_bonus_type');
            
        }
    );

    //Master Game Bonus(Welcome Bonus) 
    $app->group(
        '/welcome-bonus',
        function (RouteCollectorProxy $app) {
            $app->get('/get-welcome-bonus', \App\Action\Master_Table\Game_Welcome_Bonus\WelcomeBonusAction::class. ':getWelcomeBonus')->add(AuthMiddleware::class)->setArgument('scope', 'get_welcome_bonus');
            $app->post('/insert-welcome-bonus', \App\Action\Master_Table\Game_Welcome_Bonus\WelcomeBonusAction::class. ':insertWelcomeBonus')->add(AuthMiddleware::class)->setArgument('scope', 'add_welcome_bonus');
            $app->post('/update-welcome-bonus/{welcomebonus-id}', \App\Action\Master_Table\Game_Welcome_Bonus\WelcomeBonusAction::class. ':updateWelcomeBonus')->add(AuthMiddleware::class)->setArgument('scope', 'edit_welcome_bonus');
            $app->get('/delete-welcome-bonus/{welcomebonus-id}', \App\Action\Master_Table\Game_Welcome_Bonus\WelcomeBonusAction::class. ':deleteWelcomeBonus')->add(AuthMiddleware::class)->setArgument('scope', 'delete_welcome_bonus');
        }
    );

    //Master Softwares
    $app->group(
        '/user-software',
        function (RouteCollectorProxy $app) {
            $app->get('/get-android-user', \App\Action\Master_Table\Master_Software\SoftwareAction::class. ':getAndroid')->add(AuthMiddleware::class);
            $app->get('/get-ios-user', \App\Action\Master_Table\Master_Software\SoftwareAction::class. ':getIos')->add(AuthMiddleware::class);
            $app->post('/insert-software-version', \App\Action\Master_Table\Master_Software\SoftwareAction::class. ':insertSoftwareversion')->add(AuthMiddleware::class);
           
        }
    );

    //........... Common Master Tables ............//
   
    //Master Mail Config @
    $app->group(
        '/mail-config',
        function (RouteCollectorProxy $app) {
            $app->get('/get-mail-config', \App\Action\Master_Table\Mail_Config\MailConfigAction::class. ':getMailConfig')->add(AuthMiddleware::class)->setArgument('scope', 'get_config');
            $app->post('/update-mail-config', \App\Action\Master_Table\Mail_Config\MailConfigAction::class. ':updateMailConfig')->add(AuthMiddleware::class)->setArgument('scope', 'edit_config');
        }
    );

    //Master Sms Config @
    $app->group(
        '/sms-config',
        function (RouteCollectorProxy $app) {
            $app->get('/get-sms-config', \App\Action\Master_Table\Sms_Config\SmsConfigAction::class. ':getSmsConfig')->add(AuthMiddleware::class)->setArgument('scope', 'get_config');
            $app->post('/update-sms-config', \App\Action\Master_Table\Sms_Config\SmsConfigAction::class. ':updateSmsConfig')->add(AuthMiddleware::class)->setArgument('scope', 'edit_config');
        }
    );

    //Master Ip Config
    // $app->group(
    //     '/ip-config',
    //     function (RouteCollectorProxy $app) {
    //         $app->get('/get-ip-config', \App\Action\Master_Table\Ip_Config\IpConfigAction::class. ':getIpConfig')->add(AuthMiddleware::class)->setArgument('scope', 'get_config');
    //         $app->post('/update-ip-config', \App\Action\Master_Table\Ip_Config\IpConfigAction::class. ':updateIpConfig')->add(AuthMiddleware::class)->setArgument('scope', 'edit_config');
    //     }
    // );

    //Master Social Login Config
    // $app->group(
    //     '/social-config',
    //     function (RouteCollectorProxy $app) {
    //         $app->get('/get-social-config', \App\Action\Master_Table\Social_Config\SocialConfigAction::class. ':getSocialConfig')->add(AuthMiddleware::class)->setArgument('scope', 'get_config');
    //         $app->post('/update-social-config', \App\Action\Master_Table\Social_Config\SocialConfigAction::class. ':updateSocialConfig')->add(AuthMiddleware::class)->setArgument('scope', 'edit_config');
    //     }
    // );

    //Master Default Mail Template @
    $app->group(
        '/template-mail',
        function (RouteCollectorProxy $app) { 
            $app->get('/get-template', \App\Action\Master_Table\Default_Mail\DefaultMailAction::class. ':getAllDefaultMail')->add(AuthMiddleware::class);
            $app->get('/get-template/{mail-id}', \App\Action\Master_Table\Default_Mail\DefaultMailAction::class. ':getOneDefaultMail')->add(AuthMiddleware::class)->setArgument('scope', 'get_mail_templates');   
            $app->post('/update-default-mail/{mail-id}', \App\Action\Master_Table\Default_Mail\DefaultMailAction::class. ':updateDefaultMail')->add(AuthMiddleware::class)->setArgument('scope', 'edit_mail_templates');   

        }
    );

    //Master Default SMS Template @
    $app->group(
        '/template-sms',
        function (RouteCollectorProxy $app) { 
            $app->get('/get-sms-template', \App\Action\Master_Table\Default_Sms\DefaultSmsAction::class. ':getAllDefaultSms')->add(AuthMiddleware::class);
            $app->get('/get-sms-template/{sms-id}', \App\Action\Master_Table\Default_Sms\DefaultSmsAction::class. ':getOneDefaultSms')->add(AuthMiddleware::class)->setArgument('scope', 'get_mail_templates');   
            $app->post('/update-default-sms/{sms-id}', \App\Action\Master_Table\Default_Sms\DefaultSmsAction::class. ':updateDefaultSms')->add(AuthMiddleware::class)->setArgument('scope', 'edit_mail_templates');   

        }
    );

    /**********************************************************/
    /*****   ADMIN PANEL API  *****/
    /**********************************************************/

    //Admin Panel Authentication
    $app->group(
        '/admin-auth',
        function (RouteCollectorProxy $app) {  
            $app->post('/admin-login', \App\Action\Admin_Panel\Login\LoginAction::class. ':adminLogin');  
            $app->post('/refresh-token', \App\Action\Auth\AuthAction::class. ':generateTokens');  //Authetication
            $app->get('/get-current-user', \App\Action\Admin_Panel\Login\LoginAction::class. ':getCurrentUser')->add(AuthMiddleware::class);
            $app->get('/admin-logout', \App\Action\Admin_Panel\Login\LoginAction::class. ':adminLogout')->add(AuthMiddleware::class);
            $app->post('/admin-log-history/{admin-id}', \App\Action\Admin_Panel\Login\LoginAction::class. ':logAdminHistory')->add(AuthMiddleware::class)->setArgument('scope', 'admin_log');
            $app->get('/time-in/{admin-id}', \App\Action\Admin_Panel\Login\LoginAction::class. ':timeinAdmin');
            $app->post('/time-out', \App\Action\Admin_Panel\Login\LoginAction::class. ':timeoutAdmin')->add(AuthMiddleware::class);

        }
    );

    //Admin Profile @
    $app->group(
        '/admin-profile',
        function (RouteCollectorProxy $app) {  
            $app->post('/update-profile', \App\Action\Admin_Panel\Profile\ProfileAction::class. ':updateProfile')->add(AuthMiddleware::class);
            $app->post('/change-password', \App\Action\Admin_Panel\Profile\ProfileAction::class. ':updatePassword')->add(AuthMiddleware::class);
        }
    );
    
    //Admin @
    $app->group(
        '/admin',
        function (RouteCollectorProxy $app) {  
            $app->get('/get-admins', \App\Action\Admin_Panel\Admin\AdminAction::class. ':getAdmins')->add(AuthMiddleware::class)->setArgument('scope', 'get_admin');
            $app->get('/get-admin/{admin-id}', \App\Action\Admin_Panel\Admin\AdminAction::class. ':getOneAdmin')->add(AuthMiddleware::class)->setArgument('scope', 'get_admin'); 
            $app->post('/insert-admin', \App\Action\Admin_Panel\Admin\AdminAction::class. ':insertAdmin')->add(AuthMiddleware::class)->setArgument('scope', 'add_admin');
            $app->post('/update-admin/{admin-id}', \App\Action\Admin_Panel\Admin\AdminAction::class. ':updateAdmin')->add(AuthMiddleware::class)->setArgument('scope', 'edit_admin');
            $app->get('/block-admin/{admin-id}/{active}', \App\Action\Admin_Panel\Admin\AdminAction::class. ':blockAdmin')->add(AuthMiddleware::class)->setArgument('scope', 'status_admin');
            $app->get('/delete-admin/{admin-id}', \App\Action\Admin_Panel\Admin\AdminAction::class. ':deleteAdmin')->add(AuthMiddleware::class)->setArgument('scope', 'delete_admin');    
        }
    );

    //Admin IP Restrict @
    $app->group(
        '/admin-ip',
        function (RouteCollectorProxy $app) {     
            
            $app->get('/admin-ip-status/{admin-id}/{status}', \App\Action\Admin_Panel\Admin\AdminIpAction::class. ':adminIpStatus')->add(AuthMiddleware::class)->setArgument('scope', 'ip_status_admin');  

            $app->get('/get-admin-ip/{admin-id}', \App\Action\Admin_Panel\Admin\AdminIpAction::class. ':getOneAdminIp')->add(AuthMiddleware::class)->setArgument('scope', 'ip_status_admin');
            $app->post('/insert-admin-ip', \App\Action\Admin_Panel\Admin\AdminIpAction::class. ':insertAdminIp')->add(AuthMiddleware::class)->setArgument('scope', 'ip_status_admin'); 
            $app->post('/update-admin-ip/{id}', \App\Action\Admin_Panel\Admin\AdminIpAction::class. ':updateAdminIp')->add(AuthMiddleware::class)->setArgument('scope', 'ip_status_admin');    
            $app->get('/delete-ip-address/{id}', \App\Action\Admin_Panel\Admin\AdminIpAction::class. ':deleteAdminIp')->add(AuthMiddleware::class)->setArgument('scope', 'ip_status_admin');    
        }
    );

    //Users(Player's)
    $app->group(
        '/users',
        function (RouteCollectorProxy $app) {
            $app->get('/get-user', \App\Action\Admin_Panel\Users\UsersAction::class. ':getUsers')->add(AuthMiddleware::class)->setArgument('scope', 'get_user');
            $app->get('/get-user/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':getOneUser')->add(AuthMiddleware::class)->setArgument('scope', 'get_user');
            $app->get('/active-user/{user-id}/{active}', \App\Action\Admin_Panel\Users\UsersAction::class. ':activeUser')->add(AuthMiddleware::class)->setArgument('scope', 'status_user');
            $app->post('/user-log-history/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':userLogHistory')->add(AuthMiddleware::class)->setArgument('scope', 'user_log');

            //chips, free chips, bonus, points
            $app->get('/get-cash-chips/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':getCashChips')->add(AuthMiddleware::class)->setArgument('scope', 'user_bank_detail');
            $app->get('/get-free-chips/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':getFreeChips')->add(AuthMiddleware::class)->setArgument('scope', 'user_bank_detail');
            $app->get('/get-bonus/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':getBonus')->add(AuthMiddleware::class)->setArgument('scope', 'user_bank_detail');
            $app->get('/get-points/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':getPoints')->add(AuthMiddleware::class)->setArgument('scope', 'user_bank_detail');
            $app->get('/user-game-details/{user-id}', \App\Action\Admin_Panel\Users\UsersAction::class. ':usersGameDetails')->add(AuthMiddleware::class)->setArgument('scope', 'user_bank_detail');
            
            
        }
    ); 
 
    //Tournaments
    $app->group(
        '/tournament',
        function (RouteCollectorProxy $app) {
            $app->post('/get-tournament', \App\Action\Admin_Panel\Tournament\TournamentAction::class. ':getTournaments')->add(AuthMiddleware::class)->setArgument('scope', 'get_tourney');
            $app->get('/get-tournament/{tournament-id}', \App\Action\Admin_Panel\Tournament\TournamentAction::class. ':getOneTournament')->add(AuthMiddleware::class)->setArgument('scope', 'get_tourney');
            $app->post('/insert-tournament', \App\Action\Admin_Panel\Tournament\TournamentAction::class. ':insertTournament')->add(AuthMiddleware::class)->setArgument('scope', 'add_tourney');
            // $app->post('/update-tournament/{tournament-id}', \App\Action\Admin_Panel\Tournament\TournamentAction::class. ':updateTournament')->add(AuthMiddleware::class)->setArgument('scope', 'edit_tourney');
            $app->get('/status-tournament/{tournament-id}/{tourney_status}', \App\Action\Admin_Panel\Tournament\TournamentAction::class. ':blockTournament')->add(AuthMiddleware::class)->setArgument('scope', 'status_tourney');
        }
    );

    //Withdraw cash
    $app->group(
        '/withdraw-request',
        function (RouteCollectorProxy $app) {
            $app->post('/get-withdraw-request', \App\Action\Admin_Panel\Withdraw_Request\WithdrawReqAction::class. ':getWithdrawsReq')->add(AuthMiddleware::class);//->setArgument('scope', 'get_withdraw_req')
            $app->get('/status-withdraw/{withdraw-id}/{status}', \App\Action\Admin_Panel\Withdraw_Request\WithdrawReqAction::class. ':statusWithdrawReq')->add(AuthMiddleware::class)->setArgument('scope', 'status_withdraw');

            //Insert Query - payment withdraw request

            $app->get('/debit/{user-id}/{in-amount}', \App\Action\Admin_Panel\Withdraw_Request\WithdrawReqAction::class. ':debitWithdrawReqTest'); //test

            $app->post('/debit', \App\Action\Admin_Panel\Withdraw_Request\WithdrawReqAction::class. ':debitWithdrawReq')->add(AuthMiddleware::class);
            $app->post('/payout-webhook', \App\Action\Admin_Panel\Withdraw_Request\WithdrawReqAction::class. ':webhookPayoutReq');
            $app->post('/payout-status-check', \App\Action\Admin_Panel\Withdraw_Request\WithdrawReqAction::class. ':payoutStatusCheck');
            
        }
    );

    //Rummy Game Table doubt
    $app->group(
        '/rummy-table',
        function (RouteCollectorProxy $app) {

            $app->post('/insert-rummy-table', \App\Action\Admin_Panel\Rummy_Game_Table\RummyGameAction::class. ':insertRummytable')->add(AuthMiddleware::class)->setArgument('scope', 'get_game_table');
            $app->get('/table-status/{id}/{status}', \App\Action\Admin_Panel\Rummy_Game_Table\RummyGameAction::class. ':changeStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_game_table');
            $app->get('/is-deleted/{id}', \App\Action\Admin_Panel\Rummy_Game_Table\RummyGameAction::class. ':deleteTable')->add(AuthMiddleware::class)->setArgument('scope', 'delete_game_table');

        }
    );

    /**********************************************************/
    /*****   RUMMY GAME API  *****/
    /**********************************************************/

    //Users Login
    $app->group(
        '/users-auth', 
        function (RouteCollectorProxy $app) {  

            $app->post('/login-with-password', \App\Action\Rummy_Game\Login\LoginAction::class. ':loginPassword');
            $app->post('/login-with-otp', \App\Action\Rummy_Game\Login\LoginAction::class. ':otpLogin'); 
            $app->post('/login-with-otp-verify', \App\Action\Rummy_Game\Login\LoginAction::class. ':otpLoginVerify'); 
            $app->post('/login-with-google', \App\Action\Rummy_Game\Login\GoogleLoginAction::class. ':googleLogin'); 
            $app->get('/users-logout', \App\Action\Rummy_Game\Login\GoogleLoginAction::class. ':userLogout')->add(AuthMiddleware::class);  

            $app->post('/users-register', \App\Action\Rummy_Game\Login\SignupAction::class. ':usersRegsiter');  
            $app->post('/users-register-verify', \App\Action\Rummy_Game\Login\SignupAction::class. ':usersRegsiterVerify');   

            $app->post('/forget-password-email', \App\Action\Rummy_Game\Login\ForgetAction::class. ':forgetEmailPassword'); 
            $app->post('/forget-password-email-otp-verify', \App\Action\Rummy_Game\Login\ForgetAction::class. ':forgetEmailOTPVerify'); 

            $app->post('/forget-password-mobile', \App\Action\Rummy_Game\Login\ForgetAction::class. ':forgetMobilePassword'); 
            $app->post('/forget-password-mobile-otp-verify', \App\Action\Rummy_Game\Login\ForgetAction::class. ':forgetMobileOTPVerify');

            $app->post('/reset-password', \App\Action\Rummy_Game\Login\ForgetAction::class. ':resetPassword');  

        }
    );

    //users Profile
    $app->group(
        '/users-profile',
        function (RouteCollectorProxy $app) {  

            $app->post('/update-profile', \App\Action\Rummy_Game\Profile\ProfileAction::class. ':updateUserProfile')->add(AuthMiddleware::class);
            $app->post('/change-password', \App\Action\Rummy_Game\Profile\ProfileAction::class. ':changePassword')->add(AuthMiddleware::class);
            $app->post('/upload-profile-image', \App\Action\Rummy_Game\Profile\ProfileAction::class. ':uploadProfileImage')->add(AuthMiddleware::class);

            //verify
            $app->post('/mobile-verity/send-otp', \App\Action\Rummy_Game\Profile\ProfileVerifyAction::class. ':mobileSendOTP')->add(AuthMiddleware::class);
            $app->post('/mobile-verify/verify-otp', \App\Action\Rummy_Game\Profile\ProfileVerifyAction::class. ':mobileOTPVerify')->add(AuthMiddleware::class);

            $app->post('/email-verify/send-otp', \App\Action\Rummy_Game\Profile\ProfileVerifyAction::class. ':emailSendOTP')->add(AuthMiddleware::class);
            $app->post('/email-verify/verify-otp', \App\Action\Rummy_Game\Profile\ProfileVerifyAction::class. ':emailOTPVerify')->add(AuthMiddleware::class);

        }
    );
  
    //Kyc verify
    $app->group(
        '/kyc-verify',
        function (RouteCollectorProxy $app) {
            $app->post('/get-kycverify', \App\Action\Rummy_Game\Kyc_Verify\KycVerifyAction::class. ':getKycVerifys')->add(AuthMiddleware::class)->setArgument('scope', 'get_user_kyc_Verify');//scope
            $app->get('/get-kycverify/{user-id}', \App\Action\Rummy_Game\Kyc_Verify\KycVerifyAction::class. ':getUserKycVerify')->add(AuthMiddleware::class);  
            $app->post('/kyc-verify-insert', \App\Action\Rummy_Game\Kyc_Verify\KycVerifyAction::class. ':insertKycVerify')->add(AuthMiddleware::class);
            $app->get('/status-kyc-verify/{kycverify-id}/{verify_status}', \App\Action\Rummy_Game\Kyc_Verify\KycVerifyAction::class. ':statusKycVerify')->add(AuthMiddleware::class)->setArgument('scope', 'status_kyc'); //scope
            $app->get('/file-download/{kycverify-id}', \App\Action\Rummy_Game\Kyc_Verify\KycVerifyAction::class. ':kycFileDownload'); //scope   
        }
    );  

    //Bank Details
    $app->group(
        '/user-bank-details',
        function (RouteCollectorProxy $app) {
            $app->get('/get-details', \App\Action\Rummy_Game\User_Bank_Details\BankdetailsAction::class. ':getBankDetails')->add(AuthMiddleware::class)->setArgument('scope', 'user_bank_detail'); //scope
            $app->get('/get-all-details', \App\Action\Rummy_Game\User_Bank_Details\BankdetailsAction::class. ':getAllBankDetails');
            $app->get('/get-details/{user-id}', \App\Action\Rummy_Game\User_Bank_Details\BankdetailsAction::class. ':getUserBankDetails')->add(AuthMiddleware::class);
            $app->post('/insert-details', \App\Action\Rummy_Game\User_Bank_Details\BankdetailsAction::class. ':insertBankDetails')->add(AuthMiddleware::class);
            $app->post('/update-details/{bankdetails-id}', \App\Action\Rummy_Game\User_Bank_Details\BankdetailsAction::class. ':updateBankDetails')->add(AuthMiddleware::class);
           
        }
    );


    // //User 
    // $app->group(
    //     '/user-withdraw',
    //     function (RouteCollectorProxy $app) {
    //         $app->post('/insert', \App\Action\Rummy_Game\Withdraw\BankdetailsAction::class. ':getBankDetails')->add(AuthMiddleware::class); //scope        
    //     }
    // );

    //Game Room Cash
    $app->group(
        '/game-room-cash',
        function (RouteCollectorProxy $app) {

            $app->get('/get-cash-game-type', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':getCashGameType')->add(AuthMiddleware::class);
            $app->get('/get-cash-max-player/{game-id}', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':getCashMaxPlayer')->add(AuthMiddleware::class);
            $app->get('/get-cash-entry-fees/{game-id}/{max-player}', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':getCashEntryFees')->add(AuthMiddleware::class);

            $app->post('/get-game-room-cash', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':getGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'get_cash_table'); //scope
            $app->post('/insert-game-room-cash', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':insertGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'add_cash_table'); //scope
            $app->post('/update-game-room-cash/{gameroom-id}', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':updateGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'edit_cash_table'); //scope
            $app->get('/game-room-status/{gameroom-id}/{status}', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':gameroomStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_cash_room'); //scope
            $app->get('/delete-game-room-cash/{gameroom-id}', \App\Action\Rummy_Game\GameRoom_Cash\GameRoomAction::class. ':deleteGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'delete_cash_table'); //scope
        }
    ); 

    //Game Room Free
    $app->group(
        '/game-room-free',
        function (RouteCollectorProxy $app) {
            $app->post('/get-game-room-free', \App\Action\Rummy_Game\GameRoom_Free\GameRoomFreeAction::class. ':getGamefreeroom')->add(AuthMiddleware::class)->setArgument('scope', 'get_free_table'); //scope
            $app->post('/insert-game-room-free', \App\Action\Rummy_Game\GameRoom_Free\GameRoomFreeAction::class. ':insertfreeGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'add_free_table'); //scope
            $app->post('/update-game-room-free/{freegame-id}', \App\Action\Rummy_Game\GameRoom_Free\GameRoomFreeAction::class. ':updatefreeGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'edit_free_table'); //scope
            $app->get('/game-room-status/{freegame-id}/{status}', \App\Action\Rummy_Game\GameRoom_Free\GameRoomFreeAction::class. ':gamefreeroomStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_cash_room'); //scope
            $app->get('/delete-game-room-free/{freegame-id}', \App\Action\Rummy_Game\GameRoom_Free\GameRoomFreeAction::class. ':deletefreeGameroom')->add(AuthMiddleware::class)->setArgument('scope', 'delete_free_table'); //scope
        }
    ); 

    
    //Game Room Torunament
    $app->group(
        '/game-room-tourney',
        function (RouteCollectorProxy $app) {

            $app->get('/get-tourney-game-type', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':getTourneyGameType')->add(AuthMiddleware::class);
            $app->get('/get-tourney-max-player/{game-id}', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':getTourneyMaxPlayer')->add(AuthMiddleware::class);
            $app->get('/get-tourney-entry-fees/{game-id}/{max-player}', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':getTourneyEntryFees')->add(AuthMiddleware::class);
           
            $app->post('/get-game-room-tourney', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':getTourneyroom')->add(AuthMiddleware::class)->setArgument('scope', 'get_tourney'); //Scope
            $app->post('/insert-game-room-tourney', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':insertTourneyroom')->add(AuthMiddleware::class)->setArgument('scope', 'add_tourney'); //Scope
            $app->get('/game-room-active/{tourneygame-id}/{active}', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':TourneyroomActive')->add(AuthMiddleware::class)->setArgument('scope', 'status_tourney'); //Scope
            $app->get('/delete-game-room-tourney/{tourneygame-id}', \App\Action\Rummy_Game\GameRoom_Tournament\GameRoomTournamentAction::class. ':deleteTourneyroom')->add(AuthMiddleware::class)->setArgument('scope', 'delete_tourney'); //Scope
        }
    ); 

    //Game Room Private Table
    $app->group(
        '/game-private-table',
        function (RouteCollectorProxy $app) {
            $app->post('/insert-game-private-table', \App\Action\Rummy_Game\Gameroom_PrivateTable\GameRoomPrivateTableAction::class. ':insertPrivateTable')->add(AuthMiddleware::class);
            $app->post('/enter-game-private-table', \App\Action\Rummy_Game\Gameroom_PrivateTable\GameRoomPrivateTableAction::class. ':enterPrivateTable')->add(AuthMiddleware::class);

        }
    ); 

    //Game Room LeaderBoard
    $app->group(
        '/game-leader-board',
        function (RouteCollectorProxy $app) {
            $app->get('/get-top-15-players', \App\Action\Rummy_Game\Leaderboard\LeaderboardAction::class. ':getTop15Players')->add(AuthMiddleware::class);
            $app->get('/get-top-15-refer-earners', \App\Action\Rummy_Game\Leaderboard\LeaderboardAction::class. ':referearnTop15Players')->add(AuthMiddleware::class);
            
        }
    );


    //Game Point Convert to Bonus
    $app->group(
        '/game-point-claim-bonus',
        function (RouteCollectorProxy $app) {
            $app->post('/point-claim-bonus', \App\Action\Rummy_Game\Point_Claim_Bonus\PointClaimBonusAction::class. ':PointClaimBonus')->add(AuthMiddleware::class);
           
            
        }
    ); 

    //Security 
    $app->group(
        '/secure',
        function (RouteCollectorProxy $app) {
            $app->get('/get-last-login', \App\Action\Rummy_Game\Login\LoginAction::class. ':LastLogin');
           
            
        }
    ); 


    //payment gateway
    $app->group(
        '/deposit-order',
        function (RouteCollectorProxy $app) {
            $app->post('/create-order', \App\Action\Rummy_Game\Deposit\DepositAction::class. ':createOrder')->add(AuthMiddleware::class); 
            $app->post('/deposit-order', \App\Action\Rummy_Game\Deposit\DepositAction::class. ':createDepositOrder'); 

            

        }
    ); 


    /**********************************************************/
    /*****   RUMMY WEBSITE API  *****/
    /**********************************************************/   

    //Website Settings
    $app->group(
        '/settings',
        function (RouteCollectorProxy $app) {
            $app->get('/get-settings', \App\Action\Rummy_Website\Settings\SettingsAction::class. ':getLogoSettings')->setArgument('scope', 'get_web_setting'); 
            $app->post('/update-settings', \App\Action\Rummy_Website\Settings\SettingsAction::class. ':updateLogoSettings')->add(AuthMiddleware::class)->setArgument('scope', 'edit_web_setting'); 
        }
    );

    //Social Media @
    $app->group(
        '/social-media',
        function (RouteCollectorProxy $app) {
            $app->get('/get-social-medias', \App\Action\Rummy_Website\SocialMedia\SocialMediaAction::class. ':getSocialMedia')->setArgument('scope', 'get_social_media');
            $app->post('/update-social-media', \App\Action\Rummy_Website\SocialMedia\SocialMediaAction::class. ':updateSocialMedia')->add(AuthMiddleware::class)->setArgument('scope', 'edit_social_media');
        }
    );

    //FAQ's @
    $app->group(
        '/faq',
        function (RouteCollectorProxy $app) {

            //Global
            $app->get('/get-faq', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':getFaq');
            $app->get('/get-latest-faq', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':getLatestFaq');

            $app->get('/get-all-faq', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':getAllFaq')->add(AuthMiddleware::class)->setArgument('scope', 'get_faq'); //Scope
            $app->post('/insert-faq', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':insertFaq')->add(AuthMiddleware::class)->setArgument('scope', 'add_faq'); //Scope
            $app->post('/update-faq/{faq-id}', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':updateFaq')->add(AuthMiddleware::class)->setArgument('scope', 'edit_faq'); //Scope
            $app->get('/status/{faq-id}/{status}', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':changeStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_faq'); //Scope
            $app->get('/delete-faq/{faq-id}', \App\Action\Rummy_Website\FAQ\FaqAction::class. ':deleteFaq')->add(AuthMiddleware::class)->setArgument('scope', 'delete_faq'); //Scope
        }
    );

    //News //feature used
    // $app->group(
    //     '/news',
    //     function (RouteCollectorProxy $app) {

    //         //Global
    //         $app->get('/get-news', \App\Action\Rummy_Website\News\NewsAction::class. ':getNews');
    //         $app->get('/get-latest-news', \App\Action\Rummy_Website\News\NewsAction::class. ':getLatestNews');

    //         $app->get('/get-all-news', \App\Action\Rummy_Website\News\NewsAction::class. ':getAllNews')->add(AuthMiddleware::class)->setArgument('scope', 'get_news_list'); //Scope
    //         $app->get('/get-news/{news-id}', \App\Action\Rummy_Website\News\NewsAction::class. ':getOneNews')->setArgument('scope', 'get_news_list'); //Scope
    //         $app->post('/insert-news', \App\Action\Rummy_Website\News\NewsAction::class. ':insertNews')->add(AuthMiddleware::class)->setArgument('scope', 'add_news_list'); //Scope
    //         $app->post('/update-news/{news-id}', \App\Action\Rummy_Website\News\NewsAction::class. ':updateNews')->add(AuthMiddleware::class)->setArgument('scope', 'edit_news_list'); //Scope
    //         $app->get('/status/{news-id}/{status}', \App\Action\Rummy_Website\News\NewsAction::class. ':changeStatus')->add(AuthMiddleware::class)->setArgument('scope', 'status_news'); //Scope
    //         $app->get('/delete-news/{news-id}', \App\Action\Rummy_Website\News\NewsAction::class. ':deleteNews')->add(AuthMiddleware::class)->setArgument('scope', 'delete_news_list'); //Scope
    //     }
    // );

    //Our Games
    // $app->group(
    //     '/our-games',
    //     function (RouteCollectorProxy $app) {

    //         $app->get('/get-games', \App\Action\Rummy_Website\Our_Games\OurGamesAction::class. ':getGames');

    //         $app->get('/get-all-games', \App\Action\Rummy_Website\Our_Games\OurGamesAction::class. ':getAllGames')->add(AuthMiddleware::class)->setArgument('scope', 'get_news_list'); //Scope     
    //         $app->post('/insert-games', \App\Action\Rummy_Website\Our_Games\OurGamesAction::class. ':insertGames')->add(AuthMiddleware::class)->setArgument('scope', 'get_news_list'); //Scope
    //         $app->post('/update-games/{games-id}', \App\Action\Rummy_Website\Our_Games\OurGamesAction::class. ':updateGames')->add(AuthMiddleware::class)->setArgument('scope', 'get_news_list'); //Scope
    //         $app->get('/status/{games-id}/{status}', \App\Action\Rummy_Website\Our_Games\OurGamesAction::class. ':changeStatus')->add(AuthMiddleware::class)->setArgument('scope', 'get_news_list'); //Scope
    //         $app->get('/delete-games/{games-id}', \App\Action\Rummy_Website\Our_Games\OurGamesAction::class. ':deleteGames')->add(AuthMiddleware::class)->setArgument('scope', 'get_news_list'); //Scope
    //     }
    // );


    /**********************************************************/
    /*************************SpreadSheet Demo*****************/
    /**********************************************************/

       //Spreadsheet
    //    $app->group(
    //     '/spreadsheet',
    //     function (RouteCollectorProxy $app) {
    //         $app->get('/get-spread-sheet', \App\Action\Spreadsheet\SpreadSheetAction::class. ':getSpreadsheet'); 
            

    //     }
    // ); 

    
};

    

