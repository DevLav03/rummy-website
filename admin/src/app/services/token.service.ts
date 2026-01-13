import { Injectable } from '@angular/core';

const TOKEN_KEY = 'token';
const REFRESHTOKEN_KEY = 'refresh_token';
const SCOPE = 'scope';
const USER_NAME = 'name';

@Injectable({
  providedIn: 'root'
})
export class TokenStorageService {
  constructor() { }

  public signOut(): void {
    window.sessionStorage.clear(); 
  }

  public getToken(): string | null {
    return window.sessionStorage.getItem(TOKEN_KEY);
  }

  public getScope(): string | null {
    return window.sessionStorage.getItem(SCOPE);
  }

  public saveToken(token: string): void {
    window.sessionStorage.removeItem(TOKEN_KEY);
    window.sessionStorage.setItem(TOKEN_KEY, token);
  }
  
 
  public getRefreshToken(): string | null {
    return window.sessionStorage.getItem(REFRESHTOKEN_KEY);
  }
  public saveRefreshToken(token: string): void {
    window.sessionStorage.removeItem(REFRESHTOKEN_KEY);
    window.sessionStorage.setItem(REFRESHTOKEN_KEY, token);
  } 

  //save username
  public getUserName(): string | null {
    return window.sessionStorage.getItem(USER_NAME);
  }
  public saveUserName(user: string): void {
    window.sessionStorage.removeItem(USER_NAME);
    window.sessionStorage.setItem(USER_NAME, user);
  }

}