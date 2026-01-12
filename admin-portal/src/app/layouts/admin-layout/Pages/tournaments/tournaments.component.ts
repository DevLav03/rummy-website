import { Component, OnInit, ViewChild  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { Router } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { HttpClient } from '@angular/common/http';
import * as moment from 'moment';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { DeleteFormComponent } from '../../../../forms/delete-form/delete-form.component';
import { BlockFormComponent } from '../../../../forms/block-form/block-form.component';

import { TournamentsFormComponent } from './tournaments-form/tournaments-form.component';

@Component({
  selector: 'app-tournaments',
  templateUrl: './tournaments.component.html',
  styleUrls: ['./tournaments.component.scss']
})

export class TournamentsComponent implements OnInit {

  public getData : any= []; //Data

  public name: any;
  public role: any;

  public gameTypesData: any;
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

  bsModalRef: BsModalRef; //Modal

  @ViewChild(DatatableComponent) public table: DatatableComponent; //ngx table
  public currentPageLimit: number = 10;
  public readonly pageLimitOptions = [
    {value: 5},
    {value: 10},
    {value: 25},
    {value: 50},
    {value: 100},
  ];

  constructor(private fb: FormBuilder,private modalService: BsModalService,  private authService: AuthService, private httpClient : HttpClient,private _router: Router, private route: ActivatedRoute,  private notifyService : NotificationService) {
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

    this.current_date = new Date();
    this.start_date=moment(this.current_date).format('yyyy-MM-DD');
    this.end_date=moment(this.current_date).format('yyyy-MM-DD');

    this.form = this.fb.group({
      start_date: [this.start_date, [Validators.required]],
      end_date: [this.end_date, [Validators.required]],
      game_type: [''],
      active_status: [''],
      tourney_status: [''],
    });   

  }
  ngOnInit() {

    this.getAllData();

    this.getGameTypedata();

  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public getGameTypedata(){

    this.httpClient.get('game-type/get-game-types').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTypesData = data.data.gameTypes;
      }else{
        this.error();
      } 
    });

  }

  public getAllData(){

    let jsonInput :any = { 
      "start_date" :  this.start_date,
      "end_date" :  this.end_date,
      "game_type" :  '',
      "active_status" : '',
      "table_status" :  '',
      "offset": 0,
      "limit": 10,
      "search_val": ''
    };

    this.httpClient.post('game-room-tourney/get-game-room-tourney', jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.tourney_room;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.getData = [];
      }else{
        this.error();
      } 
    });
  }

  public onSubmit(){
    this.submit = true;
    this.table.offset = 0;
    this.pageCallback({ offset: 0, limit: this.table.limit });
  }

  pageCallback(evnt) {
    let offset = evnt.offset*evnt.limit;
    let jsonInput :any = { 
      "start_date" :  this.form.controls['start_date'].value,
      "end_date" :  this.form.controls['end_date'].value,
      "game_type" :  this.form.controls['game_type'].value,
      "active_status" : this.form.controls['active_status'].value,
      "table_status" :  this.form.controls['tourney_status'].value,
      "offset":offset,
      "limit":evnt.limit,
      "search_val":this.searchVal
    };
    this.reloadTable(jsonInput);
  }

  reloadTable(jsonObj:any) {
    if (this.subscription) {
      this.subscription.unsubscribe();
    }
    this.subscription=this.httpClient.post('game-room-tourney/get-game-room-tourney', jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.tourney_room;
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

  public addData(){
    const initialState = { 
      config:{
        title: "Add Tournaments",
      },
      dataInfo: {
        id: 0,
        name: '',
        start_date: '',
        start_time: '',
        reg_start_date: '',
        reg_start_time: '',
        reg_end_date: '',
        reg_end_time: '',
        price_amount: 0,
        game_id: 0,
        max_player: 0,
        entry_fees: 0
      }
    };
    this.bsModalRef = this.modalService.show(TournamentsFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {

      //console.log(res)

      const obj = {
        id: res.res.data['admin-id'],
        title: res.data.title,
        start_date: res.data.start_date,
        start_time: res.data.start_time,
        reg_start_date: res.data.reg_start_date,
        reg_start_time: res.data.reg_start_time,
        reg_end_date: res.data.reg_end_date,
        reg_end_time: res.data.reg_end_time,
        price_amount: res.data.price_amount,
        game_type: res.game_type,
        status: 0,
        ip_restrict: 0 
      }
   
      this.getData.push(obj);
      this.table.recalculate();
   
      this.success('Tourney Create Successfully!');
    });
  }

  public changeActive(item: any){
    if(item.active == 0){
      const initialState = {
        config: {
          title: "Tournament",
          type: "Active",
          value: 1,
          icon: "fa fa-unlock"
        },
        dataInfo: {
          url: 'game-room-tourney/game-room-active/' + item.id + '/' + 1
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.getData.map(obj => {
          if (obj.id == item.id) {
            obj.active= res.active;
          }
        });
        //this.adminData=[...this.temp];
        this.table.recalculate();
        this.success('Update Successfully');
      });
    }else if (item.active == 1){
      const initialState = {
        config: {
          title: "Tournament",
          type: "InActive",
          value: 0,
          icon: "fa fa-ban"
        },
        dataInfo: {
          url: 'game-room-tourney/game-room-active/' + item.id + '/' + 0
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.getData.map(obj => {
          if (obj.id == item.id) {
            obj.active= res.active;
          }
        });
        //this.adminData=[...this.temp];
        this.table.recalculate();
        this.success('Update Successfully');
      });
    }
  }

  //Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}
