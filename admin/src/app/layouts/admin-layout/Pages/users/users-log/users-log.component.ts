import { Component, OnInit, ViewChild  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { HttpClient } from '@angular/common/http';
import * as moment from 'moment';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../../services/notification.service';


@Component({
  selector: 'app-users-log',
  templateUrl: './users-log.component.html',
  styleUrls: ['./users-log.component.scss']
})
export class UsersLogComponent implements OnInit {

  public userLogData : any= []; //Data

  public user_id: number;
  public current_date : Date;
  public start_date : any ;
  public end_date: any ;


  public userLogForm: FormGroup;
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
 

  @ViewChild(DatatableComponent) public userLogTable: DatatableComponent; //ngx table
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
    this.user_id = this.route.snapshot.params['user_id'];

    this.userLogForm = this.fb.group({
      start_date: [this.start_date, [Validators.required]],
      end_date: [this.end_date, [Validators.required]]
    });   

  }
  ngOnInit() {

    this.getUserlog();
  }

  public getUserlog(){

    let jsonInput :any = { 
      "start_date" :  this.start_date,
      "end_date" :  this.end_date,
      "offset": 0,
      "limit": 10,
      "search_val": ''
    };

    this.httpClient.post('users/user-log-history/' + this.user_id, jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data[1].total;
        this.userLogData =  data.data[0].user_log;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.userLogData = [];
      }else{
        this.error();
      } 
    });
  }

  public onSubmit(){
    this.submit = true;
    this.userLogTable.offset = 0;
    this.pageCallback({ offset: 0, limit: this.userLogTable.limit });
  }

  pageCallback(evnt) {
    //console.log("Event",evnt);
    let offset = evnt.offset*evnt.limit;
    let jsonInput :any = { 
      "start_date" :  this.userLogForm.controls['start_date'].value,
      "end_date" :  this.userLogForm.controls['end_date'].value,
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
    this.subscription=this.httpClient.post('users/user-log-history/' + this.user_id, jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data[1].total;
        this.userLogData =  data.data[0].user_log;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.userLogData = [];
      }else{
        this.error();
      } 
    });
  }

 public onLimitChange(limit: any): void {
    this.currentPageLimit=limit;
    this.userLogTable.limit = limit;
    this.userLogTable.offset = 0;
    this.pageCallback({ offset: 0,limit:this.userLogTable.limit });
  }

  updateFilter(event) {
    this.userLogTable.offset = 0;
    this.searchVal = event.target.value.toLowerCase();
    this.pageCallback({ offset: 0,limit:this.userLogTable.limit });
  }

  //Notification
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}