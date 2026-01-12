import { Component, OnInit, ViewChild  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { HttpClient } from '@angular/common/http';
import * as moment from 'moment';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-send-sms-details',
  templateUrl: './send-sms-details.component.html',
  styleUrls: ['./send-sms-details.component.scss']
})

export class SendSmsDetailsComponent implements OnInit {

  public adminLogData : any= []; //Data

  public admin_id: number;
  public current_date : Date;
  public start_date : any ;
  public end_date: any ;


  public TransactionForm: FormGroup;
  submit = false;
  
  subscription: SubscriptionLike;
  page = {
    limit: 10,
    count: 0,
    offset: 0,
    orderBy: 'created_date',
    orderDir: 'desc'
  };
  searchVal:string='';
 

  @ViewChild(DatatableComponent) public adminLogTable: DatatableComponent; //ngx table
  public currentPageLimit: number = 10;
  public readonly pageLimitOptions = [
    {value: 5},
    {value: 10},
    {value: 25},
    {value: 50},
    {value: 100},
  ];


  constructor(private fb: FormBuilder, private httpClient : HttpClient, private route: ActivatedRoute,  private notifyService : NotificationService) {
    
    this.current_date = new Date();
    this.start_date=moment(this.current_date).format('yyyy-MM-DD');
    this.end_date=moment(this.current_date).format('yyyy-MM-DD');
    this.admin_id = this.route.snapshot.params['admin_id'];

    this.TransactionForm = this.fb.group({
      start_date: [this.start_date, [Validators.required]],
      end_date: [this.end_date, [Validators.required]]
    });   

  }



  ngOnInit() {

    //this.getAdminlog();
  }

  public getAdminlog(){

    let jsonInput :any = { 
      "start_date" :  this.start_date,
      "end_date" :  this.end_date,
      "offset": 0,
      "limit": 10,
      "search_val": ''
    };

    this.httpClient.post('admin-auth/admin-log-history/' + this.admin_id, jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.adminLogData =  data.data.admin_log;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.adminLogData = [];
      }else{
        this.error();
      } 
    });
  }

  public onSubmit(){
    this.submit = true;
    this.adminLogTable.offset = 0;
    this.pageCallback({ offset: 0, limit: this.adminLogTable.limit });
  }

  pageCallback(evnt) {
    //console.log("Event",evnt);
    let offset = evnt.offset*evnt.limit;
    let jsonInput :any = { 
      "start_date" :  this.TransactionForm.controls['start_date'].value,
      "end_date" :  this.TransactionForm.controls['end_date'].value,
      "offset":offset,
      "limit":evnt.limit,
      "search_val":this.searchVal
    };
    this.reloadTable(jsonInput);
  }

  // sortCallback(sortInfo: { sorts: { dir: string, prop: string }[], column: {}, prevValue: string, newValue: string }) {
  //   // there will always be one "sort" object if "sortType" is set to "single"
  //   this.page.orderDir = sortInfo.sorts[0].dir;
  //   this.page.orderBy = sortInfo.sorts[0].prop;
  //   //this.reloadTable(jsonInput);
  // }

  reloadTable(jsonObj:any) {
    //console.log(this.start_date,this.end_date);
    if (this.subscription) {
      this.subscription.unsubscribe();
    }
    this.subscription=this.httpClient.post('admin-auth/admin-log-history/' + this.admin_id, jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.adminLogData =  data.data.admin_log;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.adminLogData = [];
      }else{
        this.error();
      } 
    });
  }

 public onLimitChange(limit: any): void {
    this.currentPageLimit=limit;
    this.adminLogTable.limit = limit;
    this.adminLogTable.offset = 0;
    this.pageCallback({ offset: 0,limit:this.adminLogTable.limit });
  }

  updateFilter(event) {
    this.adminLogTable.offset = 0;
    this.searchVal = event.target.value.toLowerCase();
    this.pageCallback({ offset: 0,limit:this.adminLogTable.limit });
  }

  //Notification
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}








