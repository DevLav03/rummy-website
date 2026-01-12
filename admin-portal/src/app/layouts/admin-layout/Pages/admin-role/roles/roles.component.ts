import { Component, OnInit,ViewChild } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Router,ActivatedRoute } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { DeleteFormComponent } from '../../../../../forms/delete-form/delete-form.component';


@Component({
  selector: 'app-roles',
  templateUrl: './roles.component.html',
  styleUrls: ['./roles.component.scss']
})

export class RolesComponent implements OnInit {

  public getData : any= []; //Data

  public name: any;
  public role: any;

  public msg: any; //Alert Msg
  public temp : any= []; //Search

  bsModalRef: BsModalRef; //Modal

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

  constructor(private httpClient: HttpClient,private modalService: BsModalService,private _router: Router, private authService: AuthService,
    private notifyService : NotificationService, private route: ActivatedRoute) { 

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
   
    this.httpClient.get('role/get-roles').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getData =  data.data.roles;
        //console.log(this.getData); 
        this.temp = [...data.data.roles];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public roleScope(role_id: number){
    this._router.navigate(['scope-permission', role_id])
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
        if(d.name !== null){
        return ((d.name !==null && d.name.toLowerCase().indexOf(val) !== -1) ||  (d.email !==null && d.email.toLowerCase().indexOf(val) !== -1) || (d.username !==null && d.username.toLowerCase().indexOf(val) !== -1) ) || !val ;
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
 
  // public deleteData(id: number){
  //   const initialState = {
  //     config: {
  //       title: "Role",
  //       id: id
  //     },
  //     dataInfo: {
  //       url: 'role/delete-admin-role/' + id
  //     }
  //   };
  //   this.bsModalRef = this.modalService.show(DeleteFormComponent, {
  //     initialState,
  //     animated: true,
  //     backdrop: 'static',
  //     class: 'modal-md'
  //   });
  //   this.bsModalRef.content.event.subscribe(res => {

  //     //this.removeId = res.delete_id;

  //     const removeItinerary = (removeId: number) => {

  //       const data = this.temp.filter(obj => obj.id !== removeId); 

  //       //console.log('obj', obj)

  //       //console.log('remove function', data);

  //       return data;
  //     }

  //     //let data = removeItinerary(this.removeId);

  //     // this.temp=[...data];
  //     // this.getData=[...data];
  //     this.table.recalculate();
  //   });
  // }

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


