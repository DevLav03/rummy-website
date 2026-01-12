import { Component, OnInit, ViewChild  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { Router } from '@angular/router';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { HttpClient } from '@angular/common/http';
import * as moment from 'moment';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';


@Component({
  selector: 'app-withdrawal-request',
  templateUrl: './users-withdrawal-request.component.html',
  styleUrls: ['./users-withdrawal-request.component.scss']
})

export class UsersWithdrawalRequestComponent implements OnInit {

  public withdrawData : any= []; //Data

  public name: any;
  public role: any;

  public admin_id: number;
  public current_date : Date;
  public start_date : any ;
  public end_date: any ;


  public withdrawForm: FormGroup;
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
 

  @ViewChild(DatatableComponent) public withdrawTable: DatatableComponent; //ngx table
  public currentPageLimit: number = 10;
  public readonly pageLimitOptions = [
    {value: 5},
    {value: 10},
    {value: 25},
    {value: 50},
    {value: 100},
  ];

  constructor(private fb: FormBuilder, private httpClient : HttpClient,private _router: Router, private route: ActivatedRoute,  private authService: AuthService, private notifyService : NotificationService) {
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");
    
    this.current_date = new Date();
    this.start_date=moment(this.current_date).format('yyyy-MM-DD');
    this.end_date=moment(this.current_date).format('yyyy-MM-DD');
    this.admin_id = this.route.snapshot.params['admin_id'];

    this.withdrawForm = this.fb.group({
      start_date: [this.start_date, [Validators.required]],
      end_date: [this.end_date, [Validators.required]]
    });   

  }
  ngOnInit() {

    this.getTournaments();
  }

  
  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public addTournaments(){
    this._router.navigate(['add-tournament'])
  }

  public changeStatus(id: number, status: number){
   
    this.httpClient.get('withdraw/status-withdraw/' + id + '/' + status).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.success('Update Successfully!');
        this.getTournaments();
      }else if(data.response == 'failure'){
        this.error();
      }else{
        this.error();
      } 
    });

  }

  public getTournaments(){

    let jsonInput :any = { 
      "start_date" :  this.start_date,
      "end_date" :  this.end_date,
      "offset": 0,
      "limit": 10
    };

    this.httpClient.post('withdraw-request/get-withdraw-request', jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data[1].total;
        this.withdrawData =  data.data[0].withdraw_req;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.withdrawData = [];
      }else{
        this.error();
      } 
    }); 
  }

  public onSubmit(){
    this.submit = true;
    this.withdrawTable.offset = 0;
    this.pageCallback({ offset: 0, limit: this.withdrawTable.limit });
  }

  pageCallback(evnt) {
    let offset = evnt.offset*evnt.limit;
    let jsonInput :any = { 
      "start_date" :  this.withdrawForm.controls['start_date'].value,
      "end_date" :  this.withdrawForm.controls['end_date'].value,
      "offset":offset,
      "limit":evnt.limit,
      // "search_val":this.searchVal
    };
    this.reloadTable(jsonInput);
  }

  reloadTable(jsonObj:any) {
    if (this.subscription) {
      this.subscription.unsubscribe();
    }
    this.subscription=this.httpClient.post('withdraw-request/get-withdraw-request', jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data[1].total;
        this.withdrawData =  data.data[0].withdraw_req;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.withdrawData = [];
      }else{
        this.error();
      } 
    });
  }

 public onLimitChange(limit: any): void {
    this.currentPageLimit=limit;
    this.withdrawTable.limit = limit;
    this.withdrawTable.offset = 0;
    this.pageCallback({ offset: 0,limit:this.withdrawTable.limit });
  }

  updateFilter(event) {
    this.withdrawTable.offset = 0;
    this.searchVal = event.target.value.toLowerCase();
    this.pageCallback({ offset: 0,limit:this.withdrawTable.limit });
  }

  //Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}