import { Component, OnInit  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { CustomvalidationService } from '../../../../services/customvalidation.service';
import { NotificationService } from '../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-configuration',
  templateUrl: './configuration.component.html',
  styleUrls: ['./configuration.component.scss']
})
export class ConfigurationComponent implements OnInit {

  public name: any;
  public role: any;

  public msg: any; //Alert Msg

  public getEmail: any; 
  emailForm: FormGroup;
  emailSubmit = false;

  public getSMS: any; 
  smsForm: FormGroup;
  smsSubmit = false;


  constructor(private fb: FormBuilder,  private notifyService : NotificationService, private authService: AuthService,  private httpClient : HttpClient, private customValidation: CustomvalidationService) { 

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

  }

  ngOnInit(): void {

    this.getEmailFormData();
    this.getSmsFormData();

    this.emailForm = this.fb.group(
    {
      sender_mail: ['', [Validators.required]],
      from_name: ['', [Validators.required]],
      smtp_host: ['', [Validators.required]],
      smtp_type: ['', [Validators.required]],
      smtp_port: ['', [Validators.required]],
      smtp_auth: ['', [Validators.required]],
      smtp_username: ['', [Validators.required]],
      smtp_password: ['', [Validators.required]],
    });

    this.smsForm = this.fb.group(
    {
      username: ['', [Validators.required]],
      password: ['', [Validators.required]],
      sender_id: ['', [Validators.required]],
      auth_key: ['', [Validators.required]]
    });

  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public getEmailFormData() {

    this.httpClient.get('mail-config/get-mail-config').subscribe((data:any= {}) => {
      this.getEmail = data.data;
      this.emailForm = this.fb.group(
        {
          sender_mail: [this.getEmail.sender_mail, [Validators.required]],
          from_name: [this.getEmail.from_name, [Validators.required]],
          smtp_host: [this.getEmail.smtp_host, [Validators.required]],
          smtp_type: [this.getEmail.smtp_type, [Validators.required]],
          smtp_port: [this.getEmail.smtp_port, [Validators.required]],
          smtp_auth: [this.getEmail.smtp_auth, [Validators.required]],
          smtp_username: [this.getEmail.smtp_username, [Validators.required]],
          smtp_password: [this.getEmail.smtp_password, [Validators.required]],
        });
    
    });

  }

  public getSmsFormData() {

    this.httpClient.get('sms-config/get-sms-config').subscribe((data:any= {}) => {
      this.getSMS = data.data.sms_config[0];
      this.smsForm = this.fb.group(
        {
          username: [this.getSMS.username, [Validators.required]],
          password: [this.getSMS.password, [Validators.required]],
          sender_id: [this.getSMS.sender_id, [Validators.required]],
          auth_key: [this.getSMS.auth_key, [Validators.required]]
        });
    
    });

  }

  public emailConfigSubmit(){ 

    this.emailSubmit = true;
    
    if (this.emailForm.valid) {

      let jsonInput :any = 
      {
        "sender_mail" : this.emailForm.controls['sender_mail'].value, 
        "from_name" : this.emailForm.controls['from_name'].value, 
        "smtp_host" : this.emailForm.controls['smtp_host'].value, 
        "smtp_type" : this.emailForm.controls['smtp_type'].value, 
        "smtp_port" : this.emailForm.controls['smtp_port'].value, 
        "smtp_auth" : this.emailForm.controls['smtp_auth'].value,
        "smtp_username" : this.emailForm.controls['smtp_username'].value, 
        "smtp_password" : this.emailForm.controls['smtp_password'].value
      }; 
      
  
      this.httpClient.post('mail-config/update-mail-config',jsonInput).subscribe((data:any= {}) => {
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

  public smsConfigSubmit(){ 

    this.smsSubmit = true;
    
    if (this.smsForm.valid) {

      let jsonInput :any = 
      {
        "username" : this.smsForm.controls['username'].value, 
        "password" : this.smsForm.controls['password'].value, 
        "sender_id" : this.smsForm.controls['sender_id'].value, 
        "auth_key" : this.smsForm.controls['auth_key'].value, 
      }; 
      
  
      this.httpClient.post('sms-config/update-sms-config',jsonInput).subscribe((data:any= {}) => {
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
