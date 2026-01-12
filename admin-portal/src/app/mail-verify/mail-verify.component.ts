import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import {HttpBackend, HttpClient} from '@angular/common/http';

import { environment } from '../../environments/environment';

@Component({
  selector: 'app-mail-verify',
  templateUrl: './mail-verify.component.html',
  styleUrls: ['./mail-verify.component.scss']
})


export class MailVerifyComponent implements OnInit {

  public msg: string;
  private httpClient: HttpClient;

  constructor(private handler: HttpBackend,private route: ActivatedRoute) { 
   
    this.httpClient = new HttpClient(handler);

  }

  ngOnInit(): void {

    this.verifyMail();

  }

  private verifyMail(){

    let apiURL = environment.apiURL;

    const params=this.route.snapshot.queryParamMap;

    let email_id=params.get('email');
    let verify_guid=params.get('email_verify'); 
    if((email_id !=null && email_id !='')  && (verify_guid !=null && verify_guid !='')){
      let jsonInput :any = { 
        "email_verify" : verify_guid,
        "email" : email_id
      };
      this.httpClient.post(apiURL+'player/profile/email/verify', jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
  
          this.msg = 'Verify Successfully';
          
        }else if(data.response == 'failure'){
          
          this.msg = 'Verify Failed';
  
        }else{
  
          this.msg = 'Something Wrong';
          
        } 
      });
    }else{
      this.msg = 'Something Wrong'; 
    }

  }

}
