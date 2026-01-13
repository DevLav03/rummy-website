import { Component, OnInit, ViewChild  } from '@angular/core';
import { FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router,ActivatedRoute } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { RealMoneyTableFormComponent } from '../real-money-table-form/real-money-table-form.component';

import { BlockFormComponent } from '../../../../../forms/block-form/block-form.component';
import { DeleteFormComponent } from '../../../../../forms/delete-form/delete-form.component';

@Component({
  selector: 'app-real-money-table',
  templateUrl: './real-money-table.component.html',
  styleUrls: ['./real-money-table.component.scss']
})


export class RealMoneyTableComponent implements OnInit {

  public getData : any= []; //Data

  public name: any;
  public role: any;

  public removeId: number;
  public gameTypesData : any = [];

  public form: FormGroup;
  
  subscription: SubscriptionLike;
  page = {
    limit: 10,
    count: 0,
    offset: 0,
    orderBy: 'created_date',
    orderDir: 'desc'
  };

  game_type: '';
  sitting_capacity: '';
  bet_value: '';
  joker_type: '';
  deck: '';
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

  constructor(private fb: FormBuilder, private httpClient : HttpClient,private modalService: BsModalService, private notifyService : NotificationService, private _router: Router, private authService: AuthService) {
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

    this.form = this.fb.group({
      game_type: [''],
      sitting_capacity: [''],
      joker_type: [''],
      deck: [''],
      bet_value: [''],
      table_status: ['']
    });   

  }

  ngOnInit() {


    this.getGameTypes();

    this.getAllData();
  }

  public checkScope(scopeName: string){
    let result = this.authService.checkScopeExists(scopeName);
    return result;
  }

  public getGameTypes(){

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
      "game_type": '',
      "sitting_capacity": '',
      "joker_type": '',
      "deck": '',
      "bet_value": '',
      "table_status": '',
      "offset": 0,
      "limit": 10,
      "search_val": ''
    };

    this.httpClient.post('game-room-cash/get-game-room-cash', jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.real_money_table;
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
      "game_type": this.form.controls['game_type'].value,
      "sitting_capacity": this.form.controls['sitting_capacity'].value,
      "bet_value": this.form.controls['bet_value'].value,
      "joker_type": this.form.controls['joker_type'].value,
      "deck": this.form.controls['deck'].value,
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

    this.subscription = this.httpClient.post('game-room-cash/get-game-room-cash', jsonObj).subscribe((data:any= {}) => {

      if(data.response == 'success'){
        this.page.count = data.data.total;
        this.getData =  data.data.real_money_table;
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
        match_id: '',
        match_type: '',
        game_id: '',
        game_type: '',
        joker_type: '',
        max_player: '',
        entry_fees: '',
        deck: '',       
      }
    };
    this.bsModalRef = this.modalService.show(RealMoneyTableFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
    this.bsModalRef.content.event.subscribe(res => {
      const obj = {
        total: this.page.count++,
        id: res.id,
        match_id: res.data.match_id,
        match_type: res.data.match_type,
        game_id: res.data.game_id,
        game_type: res.data.game_type,
        joker_type: res.data.joker_type,
        max_player: res.data.max_player,
        entry_fees: res.data.entry_fees,
        deck: res.data.deck,
        active: 1,
      }
   
      this.getData.push(obj);
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
        match_type: item.match_id,
        game_type: item.game_id,
        max_player: item.max_player,
        entry_fees: item.entry_fees        
      }
    };
    // this.bsModalRef = this.modalService.show(GameTablesFormComponent, {
    //   initialState,
    //   animated: true,
    //   backdrop: 'static',
    //   class: 'modal-sm'
    // });
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
          title: "Real Money Table",
          type: "Active",
          value: 1,
          icon: "fa fa-unlock"
        },
        dataInfo: {
          url: 'game-room-cash/game-room-status/' + item.id + '/' + 1
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
          title: "Real Money Table",
          type: "InActive",
          value: 0,
          icon: "fa fa-ban"
        },
        dataInfo: {
          url: 'game-room-cash/game-room-status/' + item.id + '/' + 0
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
        title: "Real Money Table",
        id: id
      },
      dataInfo: {
        url: 'game-room-cash/delete-game-room-cash/' + id
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

  public privateTable(){
    this._router.navigate(['private-tables'])
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
