import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot,RouterStateSnapshot } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { TokenStorageService } from '../services/token.service';

@Injectable()
export class AuthGuardService implements CanActivate { 
    public menu_list: any=[];
    constructor(private _router:Router, private httpClient : HttpClient, private tokenStorageService: TokenStorageService) {
    
    }  

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
        let scopeList  = this.tokenStorageService.getScope();
        let menu_id = route.data.id;
        //console.log(scopeList,menu_id)

        if(scopeList != null){
            let scope : any=[];
            scope = JSON.parse(scopeList).filter((scope_name) => scope_name==menu_id);
        
            if(scope.length >= 1){
              return true;
            }else{
              return false;
            } 
        }else{
            return false;
        }
        /** 
        console.log("scopeList",scopeList);
        this.httpClient.get('admin-auth/get-current-user').subscribe((data:any= {}) => {
            //console.log(data.data.admins[0].role_id);
            if(data.response == 'success'){
                let role_id = data.data.admins[0].role_id;
                this.httpClient.get('role/get-role/'+ role_id).subscribe((menuData:any= {}) => {
                    if(menuData.response == 'success'){
                        //console.log(menuData.data);
                        this.menu_list = menuData.data.roles[0].menu_list;
                        const obj = JSON.parse(this.menu_list);
                    // console.log(this.menu_list);
                        //console.log(route,obj,obj.includes(route.data.id));
                        //return true;
                        let menu_id = route.data.id;
                        if(obj.includes(menu_id)===true){
                            //console.log("true");
                            return true;
                        }else{
                            //console.log("false");
                            this._router.navigate(['login']) //403 page 
                            return false;
                        }
                    }
                
                });
            }
            
        });
        return true;
        */
    
    }
 
}