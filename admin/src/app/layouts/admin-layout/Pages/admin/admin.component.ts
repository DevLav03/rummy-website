import { Component, OnInit,ViewChild } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Router,ActivatedRoute } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';

import * as moment from 'moment';

import { NotificationService } from '../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { AdminFormComponent } from './admin-form/admin-form.component';
import { AdminDetailFormComponent } from './admin-detail-form/admin-detail-form.component';
import { AdminIpFormComponent } from './admin-ip-form/admin-ip-form.component';
import { AdminLogComponent } from './admin-log/admin-log.component';

import { DeleteFormComponent } from '../../../../forms/delete-form/delete-form.component';
import { BlockFormComponent } from '../../../../forms/block-form/block-form.component';


@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.scss']
})

export class AdminComponent implements OnInit {

  public getData : any= []; //Data
  
  public name: any; // Scope
  public role: any;

  public date : any;
  public current_date = new Date();

  public removeId: number;

  public status: number;

  public msg: any; //Alert Msg
  
  public temp : any= []; //Search
  
  bsModalRef: BsModalRef; //Modal

  @ViewChild(DatatableComponent) public adminTable: DatatableComponent; //ngx table
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

  constructor(private httpClient: HttpClient,private modalService: BsModalService,private _router: Router, 
    private notifyService : NotificationService, private route: ActivatedRoute, private authService: AuthService) { 

    this.date = moment(this.current_date).format('yyyy-MM-DD hh:mm:ss');
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
   
    this.httpClient.get('admin/get-admins').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getData =  data.data.admins;
        //console.log(this.getData); 
        this.temp = [...data.data.admins];
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
    this.adminTable.limit = this.currentPageLimit;
    this.adminTable.recalculate();
    setTimeout(() => {
      if (this.adminTable.bodyComponent.temp.length <= 0) {
        this.adminTable.offset = Math.floor((this.adminTable.rowCount - 1) / this.adminTable.limit);
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
        //console.log(d);
        if(d.name !== null){
        return ((d.name !==null && d.name.toLowerCase().indexOf(val) !== -1) ||  (d.role_name !==null && d.role_name.toLowerCase().indexOf(val) !== -1) || (d.username !==null && d.username.toLowerCase().indexOf(val) !== -1) ) || !val ;
      }      
    });  
    this.getData = temp;  // update the rows
    this.adminTable.offset = 0;
    }else{
      this.getData = [...this.temp];
      this.adminTable.offset = 0;
    }
  }
  //End

  public addData(){
    const initialState = { 
      config:{
        title: "Add Admin",
      },
      dataInfo: {
        id: 0,
        name: '',
        username: '',
        password: '',
        email: '', 
        phone_no: '',
        role_id: ''
      }
    };
    this.bsModalRef = this.modalService.show(AdminFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {

      //console.log(res);

      const obj = {
        id: res.res.data['admin-id'],
        name: res.admin.name,
        username: res.admin.username,
        email: res.admin.email,
        phone_no: res.admin.phone_no,
        role_id: res.admin.role_id,
        role_name: res.role,
        status: 0,
        ip_restrict: 0 
      }
   
      this.temp.push(obj);
      //console.log('temp', this.temp);

      this.getData=[...this.temp];
      //console.log('getData', this.getData);
      this.adminTable.recalculate();
   
      this.success('Admin Create Successfully!');
    });
  }

  public editData(item: any){
    //console.log("item",item);
    const initialState = {
      config:{
        title: "Edit Admin",
      },
      dataInfo: {
        id: item.id,
        name: item.name,
        username: item.username,
        email: item.email,
        phone_no: item.phone_no,
        role_id: item.role_id
      }
    };
    this.bsModalRef = this.modalService.show(AdminFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {
      this.temp.map(obj => {
        if (obj.id == item.id) {
          obj.name= res.admin.name;
          obj.role_id = res.admin.role_id,
          obj.role_name = res.role
        }
      });
    
      this.getData=[...this.temp];
      this.adminTable.recalculate();
      this.success('Admin Update Successfully!');
      
    });
  }

  public viewDetailAdmin(id: number){
    const initialState = {
      config: {
        title: "Admin",
      },
      dataInfo: {
        url: 'admin/get-admin/' + id
      }
    };
    this.bsModalRef = this.modalService.show(AdminDetailFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
  }
  
  public changeActive(item: any){
    if(item.active == 0){
      const initialState = {
        config: {
          title: "Admin",
          type: "Unblock",
          value: 1,
          icon: "fa fa-unlock"
        },
        dataInfo: {
          url: 'admin/block-admin/' + item.id + '/' + 1
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
        this.getData=[...this.temp];
        this.adminTable.recalculate();
        this.success('unblocked Successfully');
      });
    }else if (item.active == 1){
      const initialState = {
        config: {
          title: "Admin",
          type: "Block",
          value: 0,
          icon: "fa fa-ban"
        },
        dataInfo: {
          url: 'admin/block-admin/' + item.id + '/' + 0
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
        this.getData=[...this.temp];
        this.adminTable.recalculate();
        this.success('Blocked Successfully');
      });
    }
  }

  public deleteData(id: number){
    const initialState = {
      config: {
        title: "Admin",
        id: id
      },
      dataInfo: {
        url: 'admin/delete-admin/' + id
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

        //console.log('obj', obj)

        //console.log('remove function', data);

        return data;
      }

      let data = removeItinerary(this.removeId);

      //console.log('delete array', data);
      this.temp=[...data];
      this.getData=[...data];
      this.adminTable.recalculate();
    });
  }

  public adminLoginHistory(id: number){
    this._router.navigate(['admin-log', id])
  }

  public ChangeIPRestrict(item: any){

    this.httpClient.get('admin-ip/admin-ip-status/' + item.id + '/' + 1).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        const initialState = {
          config: {
            title: "IP Restrict",
          },
          dataInfo: {
            id: item.id
          }
        };
        this.bsModalRef = this.modalService.show(AdminIpFormComponent, {
          initialState,
          animated: true,
          backdrop: 'static',
          class: 'modal-md'
        });
        this.bsModalRef.content.event.subscribe(res => {
          this.getAllData();
        });
      }else{
        this.error();
      } 
    });    
  }

  public removeIpRestrict(item: any){
    this.httpClient.get('admin-ip/admin-ip-status/' + item.id + '/' + 0).subscribe((data:any= {}) => {
      if(data.response == 'success'){

        this.temp.map(obj => {
          if (obj.id == item.id) {
            obj.ip_restrict = 0;
          }
        });
        this.getData=[...this.temp];
        this.adminTable.recalculate();

        //this.getAllData();

        this.success('Update Successfully!');

      }else{
        this.error();
      } 
    });
  }

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

