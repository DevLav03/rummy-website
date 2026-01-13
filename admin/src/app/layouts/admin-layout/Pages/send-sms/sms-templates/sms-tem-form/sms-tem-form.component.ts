import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { CustomvalidationService } from '../../../../../../services/customvalidation.service';
import { NotificationService } from '../../../../../../services/notification.service';

@Component({
  selector: 'app-sms-tem-form',
  templateUrl: './sms-tem-form.component.html',
  styleUrls: ['./sms-tem-form.component.scss']
})

export class SmsTemFormComponent implements OnInit {


  public msg: any; //Alert Msg
  
  dataInfo: any;
  options:any;
  config: any;

  public event: EventEmitter<any> = new EventEmitter(); //Form
  form: FormGroup;
  submit = false;

  constructor(private bsModalRef: BsModalRef, private fb: FormBuilder,  private notifyService : NotificationService,  private httpClient : HttpClient,  public modalOptions: ModalOptions, private customValidation: CustomvalidationService) {    
    
    this.options = modalOptions;

    if(this.options.initialState.dataInfo !== null)
    {
      this.dataInfo= {
        id: 0,
        name: '',
        subject: '',
        message: ''
      }
    }else{
      this.dataInfo= {
        id: this.options.initialState.dataInfo.id,
        name: this.options.initialState.dataInfo.name,
        subject: this.options.initialState.dataInfo.subject,
        message: this.options.initialState.dataInfo.message
      }
    }

  }

  ngOnInit(): void {

    this.form = this.fb.group(
    {
      name: [this.dataInfo.name, [Validators.required,Validators.minLength(3),Validators.maxLength(26)]],
      subject: [this.dataInfo.subject, [Validators.required]],
      message: [this.dataInfo.message, [Validators.required]],
    });
  } 

  onClose() {
    this.bsModalRef.hide();
  }


  public onSubmit(){ 

    this.submit = true;
    
    if (this.form.valid) {

      let jsonInput :any = {
        "name" : this.form.controls['name'].value, 
        "subject" : this.form.controls['subject'].value, 
        "message" : this.form.controls['message'].value
      }; 
      
     
      this.httpClient.post('template-sms/update-default-sms/'+ this.dataInfo.id, jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          this.bsModalRef.hide();
          this.triggerEvent(jsonInput);
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      },error => {
        this.bsModalRef.hide();
      });  
   
    }
  }

  triggerEvent(data: any) { 
    this.event.emit({ data: data});
  }

  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}

