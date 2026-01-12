import { Component, OnInit, ViewChild  } from '@angular/core';
import { FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { GameTablesFormComponent } from '../game-tables-form/game-tables-form.component';

import { BlockFormComponent } from '../../../../../forms/block-form/block-form.component';
import { DeleteFormComponent } from '../../../../../forms/delete-form/delete-form.component';

@Component({
  selector: 'app-game-tables',
  templateUrl: './game-tables.component.html',
  styleUrls: ['./game-tables.component.scss']
})

export class GameTablesComponent implements OnInit {

  public getData : any= []; //Data

  public name: any;
  public role: any;

  public removeId: number;

  public matchTypeData : any = [];
  public gameTypesData : any = [];
  public max_seats : any = [];

  public form: FormGroup;
  
  subscription: SubscriptionLike;
  page = {
    limit: 10,
    count: 0,
    offset: 0,
    orderBy: 'created_date',
    orderDir: 'desc'
  };

  match_type: '';
  game_type: '';
  sitting_capacity: '';
  bet_value: '';
  table_status: '';
  game_deck: '';
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

  constructor(private fb: FormBuilder, private httpClient : HttpClient,private authService: AuthService, private modalService: BsModalService, private notifyService : NotificationService) {
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");
    
    this.form = this.fb.group({
      match_type: [''],
      game_type: [''],
      sitting_capacity: [''],
      bet_value: [''],
      table_status: ['']
    });   

  }
  ngOnInit() {

    this.getGameMatchType();

    this.get_max_seats();

    this.getGameTypes();

    this.getAllData();
  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public getGameTypes(){

    this.httpClient.get('match-type/get-match-types').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.matchTypeData = data.data.matchtype;        
      }else{
        this.error();
      } 
    });

  }

  public getGameMatchType(){

    this.httpClient.get('rummy-format-types/get-active-rummy-format-types').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTypesData = data.data.format_types;        
      }else{
        this.error();
      } 
    });
    
  }

  public get_max_seats(){
    this.httpClient.get('rummy-max-seats/get-active-max-seats').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.max_seats = data.data.max_seats;    
      }else{
        this.error();
      } 
    });
  }

  public getAllData(){

    let jsonInput :any = { 
      "match_type": '',
      "game_type": '',
      "sitting_capacity": '',
      "bet_value": '',
      "table_status": '',
      "offset": 0,
      "limit": 10,
      "search_val": ''
    };

    this.httpClient.post('game-table/get-game-table', jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.game_tables;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.getData = [];
      }else{
        this.error();
      }  
    });

  }

  public onSubmit(){
    this.table.offset = 0;
    this.pageCallback({ offset: 0, limit: this.table.limit });
  }

  pageCallback(evnt) {
    let offset = evnt.offset*evnt.limit;

    let jsonInput :any = { 
      "match_type": this.form.controls['match_type'].value,
      "game_type": this.form.controls['game_type'].value,
      "sitting_capacity": this.form.controls['sitting_capacity'].value,
      "bet_value": this.form.controls['bet_value'].value,
      "table_status": this.form.controls['table_status'].value,
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
    this.subscription=this.httpClient.post('game-table/get-game-table', jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.game_tables;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.getData = [];
        this.failed('No Data Found');
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
        title: "Add Game Table",
      },
      dataInfo: {
        id: 0,
        game_type: '',
        max_player: '',
        entry_fees: '',
        comm_per: '',       
      }
    };
    this.bsModalRef = this.modalService.show(GameTablesFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {
      const obj = {
        total: this.page.count++,
        id: res.id,
        format_id: res.format_id,
        format_name: res.format_name,
        format_type_id: res.format_type_id,
        format_type_name: res.game_match_name,
        max_seat: res.game_type_name,
        entry_chips: res.data.game_id,
        comm_per: res.data.match_id,
        live_players: 0,
        active: 1,
      }
   
      this.getData.push(obj);
      //console.log(this.getData,this.getData);
      this.table.recalculate();
      this.success('Game Table Create Successfully!');
    });
  }

  public editData(item: any){
    const initialState = {
      config:{
        title: "Edit Game Table",
      },
      dataInfo: {
        id: item.id,
        game_type: item.format_type_id,
        max_player: item.max_seat,
        entry_fees: item.entry_chips,
        comm_per: item.comm_per        
      }
    };
    this.bsModalRef = this.modalService.show(GameTablesFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {
      this.getData.map(obj => {
        if (obj.id == item.id) {
          obj.total= this.page.count;
          obj.game_match_name = res.game_match_name,
          obj.game_type_name = res.game_type_name,
          obj.game_id= res.data.game_id;
          obj.match_id = res.data.match_id,
          obj.max_player = res.data.max_player,
          obj.entry_fees= res.data.entry_fees;
          obj.active = item.active
        }
      });
      this.table.recalculate();

      this.success('Game Table Update Successfully!');
      
    });
  }

  public changeStatus(item: any){
    if(item.active == 0){
      const initialState = {
        config: {
          title: "Game Table",
          type: "Active",
          value: 1,
          icon: "fa fa-unlock"
        },
        dataInfo: {
          url: 'game-table/game-table-status/' + item.id + '/' + 1
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
        //this.getData=[...this.temp];
        this.table.recalculate();
        this.success('Update Successfully');
      });
    }else if (item.active == 1){
      const initialState = {
        config: {
          title: "Game Table",
          type: "InActive",
          value: 0,
          icon: "fa fa-ban"
        },
        dataInfo: {
          url: 'game-table/game-table-status/' + item.id + '/' + 0
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
        //this.getData=[...this.temp];
        this.table.recalculate();
        this.success('Update Successfully');
      });
    }
  }


  public deleteData(id: number){

    const initialState = {
      config: {
        title: "Game Table",
        id: id
      },
      dataInfo: {
        url: 'game-table/delete-game-table/' + id
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
        const data = this.getData.filter(obj => obj.id !== removeId); 
        return data;
      }
      let data = removeItinerary(this.removeId);
      this.getData=[...data];
      this.page.count--;
      this.table.recalculate();
    });
  }

  //Notification
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
