import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef } from 'ngx-bootstrap';

import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-ip-form',
  templateUrl: './admin-ip-form.component.html',
  styleUrls: ['./admin-ip-form.component.scss']
})
export class AdminIpFormComponent implements OnInit {

  public msg: any; //Alert Msg
  dataInfo: any;
  config: any;

  public ip_id: number;
  public slug: string = 'add';
  public ip_address: any = '';
  public ip_address_data: any;

  public event: EventEmitter<any> = new EventEmitter();
  public ipForm: FormGroup;
  submit = false;

  constructor(private fb: FormBuilder, private bsModalRef: BsModalRef,private notifyService : NotificationService, private httpClient : HttpClient) {
    
  }

  ngOnInit(): void {

    this.getIpAdress();

    this.ipForm = this.fb.group({
      ip_address: [this.ip_address, [Validators.required,Validators.pattern('(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)')]],
    });
   
  } 

  public getIpAdress(){
    this.httpClient.get('admin-ip/get-admin-ip/' + this.dataInfo.id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.ip_address_data = data.data.admin_ip;
      }else{
        this.error();
      } 
    });
  }

  onClose() {
    this.bsModalRef.hide();
    this.triggerEvent();
  }

  triggerEvent() { 
    this.event.emit({});
  }

  public onSubmit(){

    this.submit = true;
    
    if (this.ipForm.valid) {

      let jsonInput :any = 
      {
        "ip_address" : this.ipForm.controls['ip_address'].value,
        "admin_id" : this.dataInfo.id
      }; 

      if(this.slug == 'add'){

        this.httpClient.post('admin-ip/insert-admin-ip',jsonInput).subscribe((data:any= {}) => {
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.insertSuccess();
          }else if(data.response == 'failure'){
            this.msg = data.message;
            this.failed(this.msg);
          }else{
            this.error();
          } 
        },error => {
          this.bsModalRef.hide();
        });

      }else if(this.slug == 'edit'){

        this.httpClient.post('admin-ip/update-admin-ip/' + this.ip_id, jsonInput).subscribe((data:any= {}) => {
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.updateSuccess();
          }else if(data.response == 'failure'){
            this.msg = data.message;
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

  public deleteAdminIP(id: number){
    this.httpClient.get('admin-ip/delete-ip-address/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getIpAdress();
        this.deleteSuccess();
      }else{
        this.error();
      } 
    });
  }

  public editAdminIP(item: any, slug: string){
    this.ip_id = item.id;
    this.ip_address = item.ip_address;
    this.slug = slug;
    this.ipForm.controls['ip_address'].patchValue(this.ip_address);
  }

  public insertSuccess(){
    this.notifyService.showSuccess("Insert Successfully!", "Success") 
  }
  public deleteSuccess(){
    this.notifyService.showSuccess("Delete Successfully!", "Success") 
  }
  public updateSuccess(){
    this.notifyService.showSuccess("Update Successfully!", "Success") 
  }
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }
  public error(){
    this.notifyService.showWarning("Something Wrong!", "Warning") 
  }
}