import { Component, OnInit,ViewChild } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Router } from '@angular/router';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';


@Component({
  selector: 'app-email-template',
  templateUrl: './email-template.component.html',
  styleUrls: ['./email-template.component.scss']
})

export class EmailTemplateComponent implements OnInit {

  public getData : any= []; //Data
  public date : any;

  public name: any;
  public role: any;

  public current_date = new Date();

  public removeId: number;

  public status: number;

  public msg: any; //Alert Msg
  
  public temp : any= []; //Search

  @ViewChild(DatatableComponent) public table: DatatableComponent; //ngx table
  public currentPageLimit: number = 10;
  public currentVisible: number = 3; 
  public readonly pageLimitOptions = [
    {value: 5},
    {value: 10},
    {value: 25},
    {value: 50},
    {value: 100},
  ];
  public readonly visibleOptions = [
    {value: 1},
    {value: 3},
    {value: 5},
    {value: 10},
  ];

  constructor(private httpClient: HttpClient, private _router: Router, private notifyService : NotificationService, private authService: AuthService) { 

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

  }

  ngOnInit() {

    this.getAllData();

  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);
    return result;

  }


  //Get Data
  public getAllData() {
   
    this.httpClient.get('template-mail/get-template').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getData =  data.data.default_mail;
        this.temp = [...data.data.default_mail];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  //Edit Data
  public editData(id: number){
    this._router.navigate(['edit-mail-template', id])
  }

  //ngx Table
  public onLimitChange(limit: any): void {
    this.changePageLimit(limit);
    this.table.limit = this.currentPageLimit;
    this.table.recalculate();
    setTimeout(() => {
      if (this.table.bodyComponent.temp.length <= 0) {
        this.table.offset = Math.floor((this.table.rowCount - 1) / this.table.limit);
      }
    });
  }
  public onVisibleChange(visible: any): void {
    this.currentVisible = parseInt(visible, 10);
  }
  private changePageLimit(limit: any): void {
    this.currentPageLimit = parseInt(limit, 10);
  }
  updateFilter(event) {
    const val = event.target.value.toLowerCase();
    if(val !== null && val.toString().trim() !=''){  
      const temp = this.temp.filter(function (d) {
        if(d.type_mail !== null){
        return ((d.type_mail !==null && d.type_mail.toLowerCase().indexOf(val) !== -1) ||  (d.name !==null && d.name.toLowerCase().indexOf(val) !== -1)) || !val ;
      }      
    });  
    this.getData = temp;  // update the rows
    this.table.offset = 0;
    }else{
      this.getData = [...this.temp];
      this.table.offset = 0;
    }
  }
  //End

 

  // Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success")
  }
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
    
}










