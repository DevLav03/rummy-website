import { Injectable } from "@angular/core";
import { HttpClient, HttpResponse, HttpHeaders } from "@angular/common/http";
import { BehaviorSubject, Observable, of, timer } from "rxjs";
//import { environment } from "../../../environments/environment";
import { map, switchMap, tap, shareReplay, catchError } from "rxjs/operators";
//import { PersistanceService } from "./persistance.service";
import { Router } from "@angular/router";
import { TokenStorageService } from '../services/token.service';

import { environment } from '../../environments/environment';

@Injectable({
  providedIn: "root",
})

export class AuthService {
  readonly baseUrl;
  private readonly JWT_TOKEN = "token";
  private readonly REFRESH_TOKEN = "refresh_token";
  private loggedUser: string;
  constructor(
    private httpClient: HttpClient,
    private router: Router, private tokenStorageService: TokenStorageService
  ) {

    let apiURL = environment.apiURL;

    this.baseUrl = apiURL;

  }
  login(data): Observable<boolean> {
    return this.httpClient.post<any>(`${this.baseUrl}auth/user`, data).pipe(
      tap((tokens) => this.doLoginUser(data.email, tokens)),
      map((res) => {
        return res;
      }),
      catchError((error) => {
        alert(error.error);
        return of(false);
      })
    );
  }

  logout() {
    let auth_token = this.tokenStorageService.getToken(); 
       if(auth_token){
          let headerOptions = new HttpHeaders({
              'Content-Type':'application/json; charset=utf-8',
              'Access-Control-Allow-Origin':'*',
              'Authorization':`${auth_token}`
          });

          this.httpClient.get( this.baseUrl+'admin-auth/admin-logout',{headers:headerOptions}).subscribe((data:any= {}) => {
            if(data.response == 'success'){
              this.doLogoutUser();
            }
          });
        }else{
          this.doLogoutUser();
        }
    
  }
  
  refreshToken() {
    return this.httpClient
      .post<any>(`${this.baseUrl}/admin-auth/refresh-token`, {
        refresh_token: this.tokenStorageService.getRefreshToken(),
      })
      .pipe(
        tap((tokens) => {
          this.storeTokens(tokens);
        }),
        catchError((error) => {
          this.doLogoutUser();
          return of(false);
        })
      );
  }


  private doLoginUser(username?: string, tokens?) {
    this.loggedUser = username;
    this.storeTokens(tokens);
  }
  private doLogoutUser() {
    this.tokenStorageService.signOut()
    this.router.navigate(["/"]);
  }

  private storeTokens(data) {
    //console.log(data);
    this.tokenStorageService.saveToken(data.data.token);
    this.tokenStorageService.saveRefreshToken(data.data.refresh_token)
  }

  public checkScopeExists(scopeName) {

    let scopeList  = this.tokenStorageService.getScope();
    //console.log(scopeList,scopeName,JSON.parse(scopeList).filter((scope_name) => scope_name==scopeName).length)

    if(scopeList != null){
      let scope : any=[];
      scope = JSON.parse(scopeList).filter((scope_name) => scope_name==scopeName);
  
      if(scope.length >= 1){
        return true;
      }else{
        return false;
      } 
    }else{
      return false;
    }
  }
  public checkSubScopeExists(subMenu:any) {
    
    let scopeList  = this.tokenStorageService.getScope();
    
    //console.log(scopeList,scopeName,JSON.parse(scopeList).filter((scope_name) => scope_name==scopeName).length)
    let scopeExists:boolean=false;
    if(scopeList != null){
      subMenu.forEach(element => {
        let scopeName=element.id;
        let scope : any=[];
        scope = JSON.parse(scopeList).filter((scope_name) => scope_name==scopeName);
        //console.log(scopeName, scope.length);
        if(scope.length >= 1){
          scopeExists=true;
        }
      });
      return scopeExists;
    }else{
      return false;
    }
  }

  
}
