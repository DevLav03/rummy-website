import { Component, OnInit, ViewChild  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { HttpClient } from '@angular/common/http';
import * as moment from 'moment';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-user-transaction-report',
  templateUrl: './user-transaction-report.component.html',
  styleUrls: ['./user-transaction-report.component.scss']
})

export class UserTransactionReportComponent implements OnInit {

  public getData : any= []; //Data

  public current_date : Date;
  public start_date : any ;
  public end_date: any ;


  public form: FormGroup;
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
 

  @ViewChild(DatatableComponent) public table: DatatableComponent; //ngx table
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

    this.form = this.fb.group({
      start_date: [this.start_date],
      end_date: [this.end_date],
      game_type: [1],
      chips_type: [1],
      status_type: [1]
    });   

  }



  ngOnInit() {

    this.getData = [{ "created_at" : "2023-01-19 12:01:12", "table_id" :"454", "round_id" : "2", "table_name" :"Table Name", "name" :"Barath", "username" :"1000247", "game_type" :"80 Pool", "amount" :"454.00"},
    { "created_at" : "2023-01-19 12:01:12", "table_id" :"454", "round_id" : "2", "table_name" :"Table Name", "name" :"Lav", "username" :"1000246", "game_type" :"Deal Rummy", "amount" :"500.00"}]
    
  }

 
  public onSubmit(){
    this.submit = true;
    this.table.offset = 0;
    this.pageCallback({ offset: 0, limit: this.table.limit });
  }

  pageCallback(evnt) {
    //console.log("Event",evnt);
    let offset = evnt.offset*evnt.limit;
    let jsonInput :any = { 
      "start_date" :  this.form.controls['start_date'].value,
      "end_date" :  this.form.controls['end_date'].value,
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
    this.subscription=this.httpClient.post('admin-auth/admin-log-history', jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.admin_log;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.getData = [];
      }else{
        this.error();
      } 
    });
  }

 public onLimitChange(limit: any): void {
    this.currentPageLimit=limit;
    this.table.limit = limit;
    this.table.offset = 0;
    this.pageCallback({ offset: 0,limit:this.table.limit });
  }

  updateFilter(event) {
    this.table.offset = 0;
    this.searchVal = event.target.value.toLowerCase();
    this.pageCallback({ offset: 0,limit:this.table.limit });
  }

  //Notification
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}





