import { Component, OnInit  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-social-media',
  templateUrl: './social-media.component.html',
  styleUrls: ['./social-media.component.scss']
})

export class SocialMediaComponent implements OnInit {

  public getData: any; 

  public name: any;
  public role: any;

  public msg: any; //Alert Msg
  
  form: FormGroup;
  submit = false;

  constructor(private fb: FormBuilder,  private notifyService : NotificationService, private authService: AuthService, private httpClient : HttpClient) {
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

  }

  ngOnInit(): void {

    this.getFormData();

    this.form = this.fb.group(
      {
        facebook: ['', [Validators.required]],
        google: ['', [Validators.required]],
        playstore: ['', [Validators.required]],
        android: ['', [Validators.required]],
        ios: ['', [Validators.required]]
      });
    
  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public getFormData() {

    this.httpClient.get('social-media/get-social-medias').subscribe((data:any= {}) => {
      this.getData = data.data.socialmedia[0];
      this.form = this.fb.group(
        {
          facebook: [this.getData.facebook, [Validators.required]],
          google: [this.getData.google, [Validators.required]],
          playstore: [this.getData.playstore, [Validators.required]],
          android: [this.getData.android, [Validators.required]],
          ios: [this.getData.ios, [Validators.required]],
        });
    
    });

  }

  public onSubmit(){ 

    this.submit = true;
    
    if (this.form.valid) {

      let jsonInput :any = 
      {
        "facebook" : this.form.controls['facebook'].value, 
        "google" : this.form.controls['google'].value, 
        "playstore" : this.form.controls['playstore'].value, 
        "android" : this.form.controls['android'].value, 
        "ios" : this.form.controls['ios'].value
      }; 
      
  
      this.httpClient.post('social-media/update-social-media',jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          this.success('Update Successfully!');
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      },error => {
        this.error();
      });
        
      
    }
  }

  //Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success") 
  }

  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}
