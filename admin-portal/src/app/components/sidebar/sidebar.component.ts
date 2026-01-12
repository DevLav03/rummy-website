import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient ,HttpHeaders, HttpBackend} from '@angular/common/http';
import { DomSanitizer } from '@angular/platform-browser';

import { environment } from '../../../environments/environment';

import { TokenStorageService } from '../../services/token.service';
import { AuthService } from '../../services/auth.service';

declare interface RouteInfo {
  
  path: string;
  title: string;
  id:string;
  icon: string;
  class: string;
  sub_menu:  any; 
  

}

export const ROUTES: RouteInfo[] = [

  { path: '/dashboard', title: 'Dashboard', id:'get_dashboard', icon:'ni ni-planet',  class: '', sub_menu: []  },

  { path: '/admin', title: 'Admin', id:'get_admin', icon:'ni ni-single-02', class: '', sub_menu: [] },

  { path: '/users', title: 'Player`s', id:'get_user', icon:'fa fa-users', class: '', sub_menu: []},

  { path: '/users-kyc-verify', title: 'User KYC Verify', id:'get_user_kyc_Verify', icon:'fa fa-circle',  class: '', sub_menu: []  },

  { path: '/role', title: 'Role Management', id:'get_role', icon:'fa fa-users', class: '', sub_menu: [] },

  { path: '/', title: 'Game Management', id:'get_match_type', icon:'fa fa-gamepad', class: '',
    sub_menu: [
      { path: '/game-match-type', title: 'Rummy Format', id:'get_match_type', icon:'fa fa-circle'},
      // { path: '/game-types', title: 'Game Types', id:'get_game_type', icon:'fa fa-circle'},
      { path: '/game-tables', title: 'Game Tables', id:'get_game_table', icon:'fa fa-circle'},
    ] 
  },

  //{ path: '/real-money-table', title: 'Rummy', id:'get_cash_table', icon:'fa fa-cube', class: '', sub_menu: [] },

  { path: '/tournaments', title: 'Tournaments', id:'get_tourney', icon:'fa fa-trophy', class: '', sub_menu: [] },

  { path: '/withdrawal-request', title: 'Withdrawal Request', id:'get_withdraw_req', icon:'fa fa-credit-card', class: '',sub_menu: [] },

  { path: '/config', title: 'configuration', id:'get_config', icon:'ni ni-settings', class: '', sub_menu: []},

  
  { path: '/', title: 'Website', id:'get_social_media', icon:'ni ni-world-2', class: '',
    sub_menu: [

      { path: '/social-media', title: 'Social Media', id:'get_social_media', icon:'fa fa-circle'},

      { path: '/faq-list', title: 'FAQ', id:'get_faq', icon:'fa fa-circle'},

      // { path: '/news-list', title: 'News', id:'get_news_list', icon:'fa fa-circle'},

      { path: '/web-setting', title: 'Settings', id:'get_web_setting', icon:'fa fa-circle'}
  
    ] 
  },

  { path: '/mail-template', title: 'Email Templates', id:'get_mail_templates', icon:'fa fa-envelope', class: '', sub_menu: [] },

  { path: '/sms-template', title: 'Send SMS', id:'get_sms_templates', icon:'ni ni-chat-round', class: '', sub_menu: [] },
  
];

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss']
})

export class SidebarComponent implements OnInit, OnDestroy {

  public apiURL: any;
  public logo: any;

  public name: any;
  public role: any;

  public interval: any;

  public sub;

  public role_id: number;
  public menu_list: any=[];
  public menuItems: any[];
  public isCollapsed = true;

  constructor(private router: Router, private httpClient : HttpClient,private handler: HttpBackend,  private authService: AuthService, private tokenStorageService: TokenStorageService, public sanitizer:DomSanitizer) {

    this.apiURL = environment.apiURL;

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

  }

  ngOnInit() {

    //this.getCurrentUser();
    
    let auth_token = this.tokenStorageService.getToken(); 

    let headerOptions = new HttpHeaders({
      'Content-Type':'application/json; charset=utf-8',
      'Access-Control-Allow-Origin':'*',
      'Authorization':`${auth_token}`
    });

    this.httpClient.get(this.apiURL+'settings/get-settings',{headers: headerOptions}).subscribe((data:any= {}) => {
      this.logo = data.data.settings[0].logo_image;
    });
    this.menuItems = ROUTES.filter(menuItem => menuItem);
    // this.interval = interval(10000).subscribe(x => {
    //   if(auth_token){ 
    //     this.httpClient.post(this.apiURL+'admin-auth/time-out',{},{headers: headerOptions}).subscribe((date:any= {}) => {});
    //   }else{
    //    console.log('No Token');
    //   }

    // });

    //this.getMenuList();

  }

  ngOnDestroy(): void {
    if (this.interval) {
      this.interval.unsubscribe();
   }
  }
  getMenuIndex(menu_id){
    return true;
  }
  
  scopeExists(scopeName){
    let result = this.authService.checkScopeExists(scopeName);
    return result;
  }

  subScopeExists(sub_menu){
    let result = this.authService.checkSubScopeExists(sub_menu);
    return result;
  }

  logout(){
    this.authService.logout();
  }

  showSubmenu(itemEl: HTMLElement) {
    itemEl.classList.toggle("showMenu");   
  }
  // public getMenuList(){ 
  //   let auth_token = this.tokenStorageService.getToken(); 
  //   if(auth_token){
  //     let headerOptions = new HttpHeaders({
  //         'Content-Type':'application/json; charset=utf-8',
  //         'Access-Control-Allow-Origin':'*',
  //         'Authorization':`${auth_token}`
  //     });
  //     this.menuItems = ROUTES.filter(menuItem => menuItem);
  //     // this.httpClient.get(this.apiURL+'admin-menu/get-menu',{headers: headerOptions}).subscribe((menuData:any= {}) => {
  //     //     if(menuData.response == 'success'){
  //     //       //console.log(menuData);
  //     //       //this.menuItems = menuData.data.menu_list;
  //     //       //console.log("this.menu_list",this.menuItems);
  //     //       this.menuItems = ROUTES.filter(menuItem => menuItem);
  //     //       //console.log("this.menuItems1",this.menuItems);
  //     //       //ROUTES=this.menu_list;
  //     //       //console.log(this.menu_list);
  //     //       // this.menuItems = ROUTES.filter(menuItem => menuItem);
  //     //       // this.router.events.subscribe((event) => {
  //     //       //   this.isCollapsed = true;
  //     //       // });
  //     //     }
  //     //   });
  //   }else{
  //     let menu_list:any=[];
  //     return menu_list;
  //   }
  // }

  // public getCurrentUser(){

  //   let auth_token = this.tokenStorageService.getToken(); 

  //   if(auth_token){
  //     let headerOptions = new HttpHeaders({
  //         'Content-Type':'application/json; charset=utf-8',
  //         'Access-Control-Allow-Origin':'*',
  //         'Authorization':`${auth_token}`
  //     });

  //     // this.httpClient.get(this.apiURL+'admin-auth/get-current-user',{headers: headerOptions}).subscribe((data:any= {}) => {
  //     //   //console.log("sidebar");
  //     //   if(data.response == 'success'){
  //     //     // console.log(data);
  //     //     let role_id = data.data.admins[0].role_id;
  //     //     this.httpClient.get(this.apiURL+'role/get-role/'+role_id,{headers: headerOptions}).subscribe((menuRoleData:any= {}) => {
  //     //         if(menuRoleData.response == 'success'){
  //     //           //console.log(menuData.data);
  //     //           this.menu_list = menuRoleData.data.roles[0].menu_list;
  //     //          // this.menuItems = ROUTES.filter(menuItem => menuItem);
  //     //           //console.log(this.menuItems);
  //     //           //console.log(this.menu_list);
  //     //           // this.httpClient.get(this.apiURL+'admin-menu/get-menu',{headers: headerOptions}).subscribe((menuData:any= {}) => {
  //     //           //   if(menuData.response == 'success'){
  //     //           //     console.log(menuData);
  //     //           //     this.menuItems = menuData.data.menu_list;
  //     //           //     console.log(this.menuItems);
  //     //           //     this.menuItems = ROUTES.filter(menuItem => menuItem);
                    
  //     //           //     // this.router.events.subscribe((event) => {
  //     //           //     //   this.isCollapsed = true;
  //     //           //     // });
  //     //           //     // console.log(ROUTES);
  //     //           //     // console.log(this.menuItems);
                    
  //     //           //     // this.menuItems.forEach(element => {
  //     //           //     //   menuItems_db.forEach(element_db => {
  //     //           //     //     if(element.id == element_db.menu_key){
  //     //           //     //       element.title=element_db.menu_name;
  //     //           //     //       element.icon=element_db.icons;
                          
  //     //           //     //     }
                        
  //     //           //     //   });
  //     //           //     // });
  //     //           //   }
  //     //           // });
                
  //     //         }
  //     //       });
  //     //   }else{
  //     //     //this.doLogoutUser();
  //     //   }
  //     // });
  //   }else{
  //     //this.doLogoutUser();
  //   }
  // }



 
  
}
