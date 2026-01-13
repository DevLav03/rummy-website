import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';

import { AuthService } from '../../../../services/auth.service';
import { NotificationService } from '../../../../services/notification.service';
import { CustomvalidationService } from '../../../../services/customvalidation.service';

@Component({
  selector: 'app-user-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})

export class ProfileComponent implements OnInit {

  public profileData : any={};

  public old_password : string = '';
  public new_password : string = '';
  public re_new_password : string = '';

  public name: any;
  public role: any;

  public msg: any; //Alert Msg

  profileForm: FormGroup; //Form
  submitProfile =  false;

  changePasswordForm: FormGroup;
  submitPassword = false;
 
  options:any;

  constructor(private httpClient: HttpClient, private router: Router,private authService: AuthService, private fb: FormBuilder, private notifyService : NotificationService, private customValidation: CustomvalidationService) { 
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

  }

  ngOnInit() {
    this.getData(); 
    this.changePasswordForm = this.fb.group(
      {
        old_password: ['', [Validators.required]],
        new_password: ['', [Validators.required, this.customValidation.patternValidator()]],
        re_new_password: ['', [Validators.required]]
    });
    this.profileForm = this.fb.group(
      {
        name: ['', [Validators.required,Validators.minLength(3),Validators.maxLength(26)]],
        username: ['', [Validators.required,Validators.minLength(5),Validators.maxLength(16)]],
        email: ['', [Validators.required, Validators.email]],
        phone_no: ['', [Validators.required, Validators.pattern("[0-9]{10}")]],
      }
    );
  }

  public getData(){
    this.httpClient.get('admin-auth/get-current-user').subscribe((data:any= {}) => {
     
      this.profileData = data.data.admins[0]; 

      //console.log(this.profileData);

      this.profileForm = this.fb.group(
        {
          name: [this.profileData.name, [Validators.required,Validators.minLength(3),Validators.maxLength(26)]],
          username: [this.profileData.username, [Validators.required,Validators.minLength(5),Validators.maxLength(16)]],
          email: [this.profileData.email, [Validators.required, Validators.email]],
          phone_no: [this.profileData.phone_no, [Validators.required, Validators.pattern("[0-9]{10}")]],
        }
      );

      //console.log(this.profileForm);

    });
  }

  public onSubmit(){
    this.submitPassword = true;

    if (this.changePasswordForm.valid) {

      let jsonInput :any = 
      {
        "old_password" : this.changePasswordForm.controls['old_password'].value, 
        "new_password" : this.changePasswordForm.controls['new_password'].value, 
        "re_new_password" : this.changePasswordForm.controls['re_new_password'].value
      };

      this.httpClient.post('admin-profile/change-password', jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          this.updateSuccess();
          this.router.navigate(['login']);
          this.authService.logout();
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      });

    }
  }

  public onProfileSubmit(){ 
    this.submitProfile = true;

    if (this.profileForm.valid) {

      let jsonInput :any = 
      {
        "name" : this.profileForm.controls['name'].value, 
        "username" : this.profileForm.controls['username'].value, 
        "email" : this.profileForm.controls['email'].value,
        "phone_no" : this.profileForm.controls['phone_no'].value
      };

      //console.log(jsonInput);
      
      this.httpClient.post('admin-profile/update-profile', jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          sessionStorage.setItem('name', this.profileForm.controls['name'].value);
          this.updateSuccess();
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      });

    }
  }

  public updateSuccess(){
    this.notifyService.showSuccess("Update Successfully!", "Success")
  }
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
}