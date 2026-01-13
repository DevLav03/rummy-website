import { Component, OnInit, EventEmitter  } from '@angular/core';
import { BsModalRef } from 'ngx-bootstrap';

import { HttpClient } from '@angular/common/http';

import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-kyc-verify-form',
  templateUrl: './kyc-verify-form.component.html',
  styleUrls: ['./kyc-verify-form.component.scss']
})

export class KycVerifyFormComponent implements OnInit {
  dataInfo: any;
  config: any;
  public event: EventEmitter<any> = new EventEmitter();

  constructor(private http: HttpClient, private bsModalRef: BsModalRef,private notifyService : NotificationService, private httpClient : HttpClient) {
  }
  ngOnInit(): void {
   
  } 
  onClose() {
    this.bsModalRef.hide();
  }
  public changeStatus(){

    this.httpClient.get('kyc/status-kycverify/' + this.dataInfo.id + '/' + 1).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.bsModalRef.hide();
        this.triggerEvent();
      }else if(data.response == 'failure'){
        this.error();
      }else{
        this.error();
      } 
    });
  }

  triggerEvent() { 
    this.event.emit({ pc_verify_status: 1 });
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
}

