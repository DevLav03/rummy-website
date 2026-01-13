import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { environment } from '../../../../../environments/environment';

import { NotificationService } from '../../../../services/notification.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  
  public getData : any; //Data
  public msg : string; //Msg

  loginForm: FormGroup; //Form
  submitted = false;

  constructor(private router: Router, private fb: FormBuilder, private notifyService : NotificationService, private httpClient: HttpClient) {}

  ngOnInit() {
    
    this.loginForm = this.fb.group(
      {
      username: ['', [Validators.required]],
      password: ['', [Validators.required]]
    });

  }

  public onSubmit(){

    let apiURL = environment.apiURL;

    this.submitted = true;

    if (this.loginForm.valid) {

      let jsonInput :any = 
      {
        "username" : this.loginForm.controls['username'].value, 
        "password" : this.loginForm.controls['password'].value
      }; 
       
      this.httpClient.post(apiURL + 'admin-auth/admin-login', jsonInput).subscribe((data:any= {}) => {

        this.getData = data.data;

        if(data.response == "success"){

          sessionStorage.setItem('name', this.getData.name);
          sessionStorage.setItem('role', this.getData.role_name);
          sessionStorage.setItem('scope', this.getData.scope_list);
          sessionStorage.setItem('token', this.getData.token);
          sessionStorage.setItem('refresh_token', this.getData.refresh_token);

          this.router.navigate(['dashboard']);

          this.success("Login Success!");

          this.loginTimeIn();

        }else if(data.response == "failure"){
          this.failed(data.err_message);
        }else{ 
          this.error();
        }
      });   

    }

  }

  loginTimeIn(){
    let apiURL = environment.apiURL;

    this.httpClient.get(apiURL + 'admin-auth/time-in/' + this.getData.id).subscribe((data:any= {}) => {
      if(data.response == "success"){
      }else{ 
        this.error();
      }
    });   
  }

  public success(msg :string){
    this.notifyService.showSuccess(msg, "Success")
  }

  public failed(msg :string){
    this.notifyService.showError(msg, "Error")
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
 
}