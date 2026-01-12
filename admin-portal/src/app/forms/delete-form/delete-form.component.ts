import { Component, OnInit, EventEmitter  } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { BsModalRef } from 'ngx-bootstrap';

import { NotificationService } from '../../services/notification.service';

@Component({
  selector: 'app-delete-form',
  templateUrl: './delete-form.component.html',
  styleUrls: ['./delete-form.component.scss']
})
export class DeleteFormComponent implements OnInit {

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

  public deleteData(){

    this.httpClient.get(this.dataInfo.url).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.bsModalRef.hide();
        this.triggerEvent(data);
        this.deleteSuccess();
      }else{
        this.Error();
      }
    });

  }

  triggerEvent(data: any) { 
    this.event.emit({ delete_id: this.config.id, data: data });
  }

  public deleteSuccess(){
    this.notifyService.showSuccess("Delete Successfully!", "Success"); 
  }

  public Error(){
    this.notifyService.showWarning("Something Wrong!", "Warning") 
  }

}
