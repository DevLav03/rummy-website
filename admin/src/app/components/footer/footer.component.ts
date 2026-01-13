import { Component, OnInit } from '@angular/core';
import { HttpClient ,HttpHeaders} from '@angular/common/http';

import { environment } from '../../../environments/environment';

import { TokenStorageService } from '../../services/token.service';

@Component({
  selector: 'app-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.scss']
})
export class FooterComponent implements OnInit {

  public apiURL: any;
  public copyright: any;

  constructor(private httpClient: HttpClient, private tokenStorageService: TokenStorageService) {
    
    this.apiURL = environment.apiURL;

  }

  ngOnInit() {

    let auth_token = this.tokenStorageService.getToken(); 

    let headerOptions = new HttpHeaders({
      'Content-Type':'application/json; charset=utf-8',
      'Access-Control-Allow-Origin':'*',
      'Authorization':`${auth_token}`
    });

   
    if(auth_token){ 
      this.httpClient.get(this.apiURL+'settings/get-settings',{headers: headerOptions}).subscribe((data:any= {}) => {
        this.copyright = data.data.settings[0].footer;
      });
    }else{
      console.log('No Token');
    }
 
  }

}
