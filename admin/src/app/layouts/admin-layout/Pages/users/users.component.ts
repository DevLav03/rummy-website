import { Component, OnInit,ViewChild } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { DomSanitizer } from '@angular/platform-browser';

import { Router } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';

import { ExcelService } from '../../../../services/excel.service';
import { NotificationService } from '../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { DeleteFormComponent } from '../../../../forms/delete-form/delete-form.component';
import { BlockFormComponent } from '../../../../forms/block-form/block-form.component';


@Component({
  selector: 'app-users',
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.scss']
})


export class UsersComponent implements OnInit {

  public userData : any= []; //Data
  public getBankData: any;

  public removeId: number;

  public name: any;
  public role: any;

  public msg: any; //Alert Msg
  
  public temp : any= []; //Search

  bsModalRef: BsModalRef; //Modal

  @ViewChild(DatatableComponent) public usersTable: DatatableComponent; //ngx table
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

  constructor(private httpClient: HttpClient,private _router: Router, public sanitizer:DomSanitizer,  private authService: AuthService,
    private notifyService : NotificationService, private excelService: ExcelService, private modalService: BsModalService) { 
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

  }

  ngOnInit() {

    this.getusersData();

    this.getUserBankDetails();

  }

  //Get Data
  public getusersData() {
    this.httpClient.get('users/get-user').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userData =  data.data.users;
        this.temp = [...data.data.users];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  //Bank Detail
  public getUserBankDetails(){

    this.httpClient.get('user-bank-details/get-details').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getBankData =  data.data[0].bankdetails;
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });

  }

  //ngx Table
  public onLimitChange(limit: any): void {
    this.changePageLimit(limit);
    this.usersTable.limit = this.currentPageLimit;
    this.usersTable.recalculate();
    setTimeout(() => {
      if (this.usersTable.bodyComponent.temp.length <= 0) {
        this.usersTable.offset = Math.floor((this.usersTable.rowCount - 1) / this.usersTable.limit);
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
        if(d.name !== null){
        return ((d.name !==null && d.name.toLowerCase().indexOf(val) !== -1) ||  (d.email !==null && d.email.toLowerCase().indexOf(val) !== -1) || (d.username !==null && d.username.toLowerCase().indexOf(val) !== -1) ) || !val ;
      }      
    });  
    this.userData = temp;  // update the rows
    this.usersTable.offset = 0;
    }else{
      this.userData = [...this.temp];
      this.usersTable.offset = 0;
    }
  }
  //End

  
  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }


  public userDetails(id: number){
    this._router.navigate(['user-details', id])
  }
  
  public userLoginHistory(id: number){
    this._router.navigate(['users-log', id])
  }

  public blockUser(item: any){
    if(item.active == 0){
      const initialState = {
        config: {
          title: "Users",
          type: "Unblock",
          value: 1,
          icon: "fa fa-unlock"
        },
        dataInfo: {
          url: 'users/active-user/' + item.id + '/' + 1
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.temp.map(obj => {
          if (obj.id == item.id) {
            obj.active= res.active;
          }
        });
        this.userData=[...this.temp];
        this.usersTable.recalculate();
        this.blockSuccess();
      });
    }else if (item.active == 1){
      const initialState = {
        config: {
          title: "Users",
          type: "Block",
          value: 0,
          icon: "fa fa-ban"
        },
        dataInfo: {
          url: 'users/active-user/' + item.id + '/' + 0
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.temp.map(obj => {
          if (obj.id == item.id) {
            obj.active= res.active;
          }
        });
        this.userData=[...this.temp];
        this.usersTable.recalculate();
        this.blockSuccess();
      });
    }
  }

  public deleteUserData(id: number){
    const initialState = {
      config: {
        title: "Player",
        id: id
      },
      dataInfo: {
        url: 'users/delete-user/' + id
      }
    };
    this.bsModalRef = this.modalService.show(DeleteFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-md'
    });
    this.bsModalRef.content.event.subscribe(res => {

      this.removeId = res.delete_id;

      const removeItinerary = (removeId: number) => {

        const data = this.temp.filter(obj => obj.id !== removeId); 

        return data;
      }

      let data = removeItinerary(this.removeId);

      //console.log('delete array', data);
      this.temp=[...data];
      this.userData=[...data];
      this.usersTable.recalculate();
    });
  }

  public exportAsXLSX():void {
    //console.log('test');
    this.excelService.exportAsExcelFile(this.getBankData, 'Game_players_bank_details');
  }

  // Notification
  public createSuccess(){
    this.notifyService.showSuccess("Create Successfully!", "Success")
  }
  public updateSuccess(){
    this.notifyService.showSuccess("Update Successfully!", "Success")
  }
  public UnblockSuccess(){
    this.notifyService.showSuccess("Unblock Successfully!", "Success"); 
  }
  public blockSuccess(){
    this.notifyService.showSuccess("Block Successfully!", "Success"); 
  }
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
    
}

