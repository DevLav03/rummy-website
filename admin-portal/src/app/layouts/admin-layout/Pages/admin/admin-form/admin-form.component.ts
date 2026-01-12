import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { CustomvalidationService } from '../../../../../services/customvalidation.service';
import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-admin-form',
  templateUrl: './admin-form.component.html',
  styleUrls: ['./admin-form.component.scss']
})

export class AdminFormComponent implements OnInit {

  public roles: any; //Role

  public msg: any; //Alert Msg

  public role_name: any;
  
  dataInfo: any;
  options:any;
  config: any;

  public event: EventEmitter<any> = new EventEmitter(); //Form
  adminForm: FormGroup;
  submit = false;

  constructor(private bsModalRef: BsModalRef, private fb: FormBuilder,  private notifyService : NotificationService,  private httpClient : HttpClient,  public modalOptions: ModalOptions, private customValidation: CustomvalidationService) {    
    
    this.options = modalOptions;

    if(this.options.initialState.dataInfo !== null)
    {
      this.dataInfo= {
        id: 0,
        name: '',
        username: '',
        password: '',
        email: '',
        phone_no: '',
        role_id: 1
      }
    }else{
      this.dataInfo= {
        id: this.options.initialState.dataInfo.id,
        name: this.options.initialState.dataInfo.name,
        username: this.options.initialState.dataInfo.username,
        password: this.options.initialState.dataInfo.password,
        email: this.options.initialState.dataInfo.email,
        phone_no: this.options.initialState.dataInfo.phone_no,
        role_id: this.options.initialState.dataInfo.role_id
      }
    }

  }

  ngOnInit(): void {

    this.role_type_list();

    this.adminForm = this.fb.group(
    {
      name: [this.dataInfo.name, [Validators.required,Validators.minLength(3),Validators.maxLength(26)]],
      username: [this.dataInfo.username, [Validators.required,Validators.minLength(5),Validators.maxLength(16)]],
      password: [this.dataInfo.password, [Validators.required, this.customValidation.patternValidator()]],
      email: [this.dataInfo.email, [Validators.required, Validators.email]],
      phone_no: [this.dataInfo.phone_no, [Validators.required, Validators.pattern("[0-9]{10}")]],
      role_id: [1, [Validators.required]]
    });

    if(this.dataInfo.id != null && this.dataInfo.id !='' && this.dataInfo.id != 0){ 
      let role_id: number = +this.dataInfo.role_id;
      this.adminForm.controls['role_id'].patchValue(role_id);

      if(this.adminForm.controls['password'].value==null || this.adminForm.controls['password'].value==''){ 
        
        this.adminForm.get('password').clearValidators(); 
        this.adminForm.get('password').setValidators(this.customValidation.patternValidator()); 
        this.adminForm.get('password').updateValueAndValidity();
        
      }
    }
  
  } 

  onClose() {
    this.bsModalRef.hide();
  }

  public role_type_list() {
    this.httpClient.get('role/get-roles').subscribe((data:any= {}) => {
      this.roles = data.data.roles;
    });
  }

  public onSubmit(){ 

    let selectedItem = this.roles.find((item)=>item.role_id == this.adminForm.controls['role_id'].value);
    let seletctedText = selectedItem.role_name;

    this.submit = true;
    
    if (this.adminForm.valid) {

      let jsonInput :any = {
        "name" : this.adminForm.controls['name'].value, 
        "username" : this.adminForm.controls['username'].value, 
        "password" : this.adminForm.controls['password'].value, 
        "email" : this.adminForm.controls['email'].value, 
        "phone_no" : this.adminForm.controls['phone_no'].value, 
        "role_id" : this.adminForm.controls['role_id'].value
      }; 
      
      this.role_name = this.options.initialState.dataInfo.role_name;

      if(this.dataInfo.id == 0){ 
  
        this.httpClient.post('admin/insert-admin',jsonInput).subscribe((data:any= {}) => {
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEvent(jsonInput, data, seletctedText);
          }else if(data.response == 'failure'){
            this.msg = data.err_message;
            this.failed(this.msg);
          }else{
            this.error();
          } 
        },error => {
          this.bsModalRef.hide();
        });
        
      }else if(this.dataInfo.id > 0){
        
        this.httpClient.post('admin/update-admin/'+ this.dataInfo.id, jsonInput).subscribe((data:any= {}) => {
          
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEvent(jsonInput, data, seletctedText);
          }else if(data.response == 'failure'){
            this.msg = data.err_message;
            this.failed(this.msg);
          }else{
            this.error();
          } 
        },error => {
          this.bsModalRef.hide();
        });  
      }else{
        this.error();
      }
    }
  }

  triggerEvent(data: any, res: any, role: any) { 
    this.event.emit({ admin: data, role: role, res: res});
  }

  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}
