import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor, HttpResponse, HttpErrorResponse, HttpHeaders } from '@angular/common/http';


import {Router} from '@angular/router';
import {Observable, throwError} from 'rxjs';
import {tap,catchError,switchMap} from "rxjs/operators";

import { AuthService } from '../services/auth.service';
import { TokenStorageService } from '../services/token.service'
import { HttpClient } from "@angular/common/http";
import { environment } from '../../environments/environment';


@Injectable()
export class JwtInterceptor implements HttpInterceptor {
    private isRefreshing = false;

    constructor(private router: Router, private authService : AuthService, private tokenStorageService: TokenStorageService, private httpClient:HttpClient){ 
        
    }
    
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        
        let apiURL = environment.apiURL;

    //    if(request.url.indexOf("/admin-auth/admin-login")>0){ 
    //     request = request.clone({
    //         setHeaders: {
    //             'Content-Type':'application/json; charset=utf-8',
    //             'Access-Control-Allow-Origin':'*'
    //         }
    //     });
    //     return next.handle(request);
    //    }else{
        let auth_token = this.tokenStorageService.getToken();  
    
        if(auth_token){
            request = request.clone({ url: apiURL+request.url });
            //console.log("token",auth_token,request,request.headers.get('Content-Type'));
            request = request.clone({
                setHeaders: {
                    //'Content-Type':'application/json; charset=utf-8',
                    'Access-Control-Allow-Origin':'*',
                    'Authorization':`${auth_token}`
                }
            });
            
        
        
            return next.handle(request).pipe(
                catchError((error) => {
                if (error instanceof HttpErrorResponse  &&  error.status === 401) {
                    if (!this.isRefreshing) {
                        this.isRefreshing = true;
                        let refresh_token = this.tokenStorageService.getRefreshToken();
                        if (refresh_token) {
                        return this.authService.refreshToken().pipe(
                            switchMap(() => {
                            this.isRefreshing = false;
                            let auth_token = this.tokenStorageService.getToken();
                            request = request.clone({
                                    setHeaders: {
                                        'Content-Type':'application/json; charset=utf-8',
                                        'Access-Control-Allow-Origin':'*',
                                        'Authorization':`${auth_token}`
                                    }
                                });
                            return next.handle(request);
                            }),
                            catchError((error) => {
                            this.isRefreshing = false;
                
                            if (error instanceof HttpErrorResponse  &&  error.status === 401) {
                                this.tokenStorageService.signOut(); 
                            }
                
                            return throwError(() => error);
                            })
                        );
                        }
                    }else{
                        this.authService.logout();
                    }
                }
                return throwError(() => error);
                })
            );
        
        }else{
            this.authService.logout();
            //return throwError(() => error);
        }

        // }//else
    }
}
