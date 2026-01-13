import { Component, OnInit, EventEmitter  } from '@angular/core';
import { BsModalRef } from 'ngx-bootstrap';
import { NotificationService } from '../../services/notification.service';
//import { RestApiService } from '../../services/rest-api.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
@Component({
  selector: 'app-block-form',
  templateUrl: './block-form.component.html',
  styleUrls: ['./block-form.component.scss']
})
export class BlockFormComponent implements OnInit {
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
  public blockData(){

    this.httpClient.get(this.dataInfo.url).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.bsModalRef.hide();
        this.triggerEvent(this.config.value);
      }else{
        this.Error();
      }
    });

  }

  triggerEvent(status: any) { 
    this.event.emit({ active: status });
  }

  public Error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
}
