import { Component, OnInit, EventEmitter  } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import * as moment from 'moment';

import { BsModalRef } from 'ngx-bootstrap';

@Component({
  selector: 'app-admin-detail-form',
  templateUrl: './admin-detail-form.component.html',
  styleUrls: ['./admin-detail-form.component.scss']
})
export class AdminDetailFormComponent implements OnInit {

  public adminData : any= [];

  public date : any;
  public current_date = new Date();

  public status: number;
  public active: string;
  dataInfo: any;
  config: any;

  public event: EventEmitter<any> = new EventEmitter();

  constructor(private bsModalRef: BsModalRef, private httpClient : HttpClient) {

    this.date = moment(this.current_date).format('yyyy-MM-DD hh:mm:ss');

  }

  ngOnInit(): void {
    this.getAdminDetail();
  } 

  onClose() {
    this.bsModalRef.hide();
  }

  public getAdminDetail(){
    this.httpClient.get(this.dataInfo.url).subscribe((data:any= {}) => {
      this.adminData = data.data.admins[0];
      this.status = this.adminData.active;
      if(this.status == 0){
        this.active = 'Block';
      }else if(this.status == 1){
        this.active = 'Active';
      }
    });
  }

}
