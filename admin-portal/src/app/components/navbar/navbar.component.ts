import { Component, OnDestroy, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { HttpClient } from '@angular/common/http';

import { AuthService } from '../../services/auth.service';

import { ROUTES } from '../sidebar/sidebar.component';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent implements OnInit {

  public focus;
  public listTitles: any[];
  public location: Location;
  public name:string;

  constructor(location: Location, private authService: AuthService, private httpClient: HttpClient) {
    this.location = location;
  }
 
  ngOnInit() {

    this.listTitles = ROUTES.filter(listTitle => listTitle);
    this.name = sessionStorage.getItem("name");

  }

  getTitle(){

    var titlee = this.location.prepareExternalUrl(this.location.path());

    if(titlee.charAt(0) === '#'){
      titlee = titlee.slice( 1 );
    }

    for(var item = 0; item < this.listTitles.length; item++){
      if(this.listTitles[item].path === titlee){
        return this.listTitles[item].title;
      }
    }
    
    return 'Dashboard';

  }

  logout(){
    this.authService.logout();
  }

 

}