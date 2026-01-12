import { Component, OnInit,ViewChild } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Router,ActivatedRoute } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { GameMatchTypeFormComponent } from '../game-match-type-form/game-match-type-form.component';
import { DeleteFormComponent } from '../../../../../forms/delete-form/delete-form.component';
import { BlockFormComponent } from '../../../../../forms/block-form/block-form.component';

@Component({
  selector: 'app-game-match-type',
  templateUrl: './game-match-type.component.html',
  styleUrls: ['./game-match-type.component.scss']
})

export class GameMatchTypeComponent implements OnInit {

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

  constructor(private httpClient: HttpClient,private modalService: BsModalService,private _router: Router, private notifyService : NotificationService, private authService: AuthService) { 

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
   
    this.httpClient.get('rummy-format/get-rummy-format').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getData =  data.data.format;
        this.temp = [...data.data.format];
        console.log(this.getData);
      }else if(data.response == 'failure'){
        this.failed(data.err_message);
      }else{
        this.error();
      } 
    });

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
        return ((d.name !==null && d.name.toLowerCase().indexOf(val) !== -1) || (d.description !==null && d.description.toLowerCase().indexOf(val) !== -1) ) || !val ;
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

  public addData(){
    const initialState = { 
      config:{
        title: "Add Rummy Format",
      },
      dataInfo: {
        id: 0,
        name: '',
        description: '',
      }
    };
    this.bsModalRef = this.modalService.show(GameMatchTypeFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {
      this.getAllData();   
      this.success('Create Successfully!');
    });
  }

  public updateData(item: any){
    const initialState = {
      config:{
        title: "Edit Rummy Format",
      },
      dataInfo: {
        id: item.id,
        name: item.name,
        description: item.description,
      }
    };
    this.bsModalRef = this.modalService.show(GameMatchTypeFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {
      this.getAllData();   
      this.success('Update Successfully!');      
    });
  }
  
  public changeStatus(item: any){
    if(item.active == 0){
      const initialState = {
        config: {
          title: "Change Status",
          type: "InActive",
          value: 1,
          icon: "fa fa-unlock"
        },
        dataInfo: {
          url: '/rummy-format/format-status/' + item.id + '/' + 1
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
        this.table.recalculate();
        this.success('Status Update Successfully');
      });
    }else if (item.active == 1){
      const initialState = {
        config: {
          title: "Change Status",
          type: "Active",
          value: 0,
          icon: "fa fa-ban"
        },
        dataInfo: {
          url: '/rummy-format/format-status/' + item.id + '/' + 0
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.getAllData();
        this.success('Status Update Successfully');
      });
    }
  }

  public deleteData(id: number){
    const initialState = {
      config: {
        title: "Game Match Type",
        id: id
      },
      dataInfo: {
        url: 'match-type/delete-match/' + id
      }
    };
    this.bsModalRef = this.modalService.show(DeleteFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-md'
    });
    this.bsModalRef.content.event.subscribe(res => {

      this.getAllData();

    });
  }

  public rummyFormatTypes(id: number){
    console.log(id);
    this._router.navigate(['game-types', id])
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

