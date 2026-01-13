import { Component, OnInit, EventEmitter, AfterViewInit  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-game-tables-form',
  templateUrl: './game-tables-form.component.html',
  styleUrls: ['./game-tables-form.component.scss']
})

export class GameTablesFormComponent implements OnInit, AfterViewInit {

  public msg: any; //Alert Msg
  
  dataInfo: any;
  options:any;
  config: any;

  public matchTypeData : any = [];
  public gameTypesData : any = [];
  public max_seats : any = [];

  public event: EventEmitter<any> = new EventEmitter(); //Form
  form: FormGroup;
  submit = false;
  private formType='add_form';

  constructor(private bsModalRef: BsModalRef, private fb: FormBuilder,  private notifyService : NotificationService,  private httpClient : HttpClient,  public modalOptions: ModalOptions) {    

    //console.log('config', this.config);
    
    this.options = modalOptions;
    //console.log('dataInfo', this.dataInfo,this.options.initialState);

    //console.log('options', this.options);

    if(this.options.initialState.dataInfo !== null)
    {
      if(this.options.initialState.dataInfo.id == '0'){
        this.formType='add_form';
        this.dataInfo= {
          id: 0,
          //match_type: '',
          game_type: '',
          max_player: '',
          entry_fees: '',
          comm_per:''
        }
      }else{
        this.formType='edit_form';
        this.dataInfo= {
          id: this.options.initialState.dataInfo.id,
          //match_type: this.options.initialState.dataInfo.match_type,
          game_type: this.options.initialState.dataInfo.game_type,
          max_player: this.options.initialState.dataInfo.max_player,
          entry_fees: this.options.initialState.dataInfo.entry_fees,
          comm_per: this.options.initialState.dataInfo.comm_per
        }
      }
      
    }else{
      this.dataInfo= {
        id: 0,
        //match_type: '',
        game_type: '',
        max_player: '',
        entry_fees: '',
        comm_per:''
      }
    }


   

    //condtructer la endha component use panna kudadhu. oninit la thaan use pannanum
    // if(this.dataInfo.id != null && this.dataInfo.id !='' && this.dataInfo.id != 0){ 

    //   let match_type: number = +this.dataInfo.match_type;
    //   let game_type: number = +this.dataInfo.game_type;
    //   let max_player: number = +this.dataInfo.max_player;

    //   this.form.controls['match_type'].patchValue(match_type);
    //   this.form.controls['game_type'].patchValue(game_type);
    //   this.form.controls['max_player'].patchValue(max_player);

    // }

  }

  ngAfterViewInit():void{
    //console.log(this.gameTypesData);
  }


  ngOnInit(): void {

    this.form = this.fb.group(
    {
      //match_type: ['', [Validators.required]],
      game_type: ['', [Validators.required]],
      max_player: ['', [Validators.required]],
      entry_fees: ['', [Validators.required]],
      comm_per: ['', [Validators.required]],
    });  

    this.getGameMatchType();

    this.get_max_seats();

    //this.getGameTypes();

    // this.form = this.fb.group(
    // {
    //   match_type: [this.dataInfo.match_type, [Validators.required]],
    //   game_type: [this.dataInfo.game_type, [Validators.required]],
    //   max_player: [this.dataInfo.max_player, [Validators.required]],
    //   entry_fees: [this.dataInfo.entry_fees, [Validators.required]],
    // });


  } 

  public get_max_seats(){
    this.httpClient.get('rummy-max-seats/get-active-max-seats').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.max_seats = data.data.max_seats;    
        console.log(this.max_seats);
      }else{
        this.error();
      } 
    });
  }

  public getGameMatchType(){

    this.httpClient.get('rummy-format-types/get-active-rummy-format-types').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTypesData = data.data.format_types;

        if(this.formType =='add_form'){
          this.form.controls['game_type'].patchValue(this.gameTypesData[0].id);  
          this.form.controls['max_player'].patchValue(this.max_seats[0].id);
          // this.form.controls['max_player'].patchValue("2"); 
        }else{
          let game_type: number = +this.dataInfo.game_type;
          let max_player: number = +this.dataInfo.max_player;
          let entry_fee: number = +this.dataInfo.entry_fees;
          let comm_per : any =this.dataInfo.comm_per;
          const game_type_val = this.gameTypesData.filter(obj => obj.id == this.dataInfo.game_type); 
    
          this.form.controls['game_type'].patchValue(game_type_val.length>0?game_type_val[0].id:'');
          this.form.controls['comm_per'].patchValue(comm_per);
          this.form.controls['max_player'].patchValue(max_player);
          this.form.controls['entry_fees'].patchValue(entry_fee);
        }    

        //this.getGameTypes();    
      }else{
        this.error();
      } 
    });
    
  }

  // public getGameTypes(){

  //   this.httpClient.get('match-type/get-match-types').subscribe((data:any= {}) => {
  //     if(data.response == 'success'){
  //       this.matchTypeData = data.data.matchtype;
  //       if(this.formType =='add_form'){
  //         this.form.controls['game_type'].patchValue(this.gameTypesData[0].id);  
  //         this.form.controls['match_type'].patchValue(this.matchTypeData[0].id);
  //         this.form.controls['max_player'].patchValue("2"); 
  //       }else{
  //         let match_type: number = +this.dataInfo.match_type;
  //         let game_type: number = +this.dataInfo.game_type;
  //         let max_player: number = +this.dataInfo.max_player;
  //         let entry_fee : any =this.dataInfo.entry_fees;
  //         const game_type_val = this.gameTypesData.filter(obj => obj.id == this.dataInfo.game_type); 
    
  //         this.form.controls['game_type'].patchValue(game_type_val.length>0?game_type_val[0].id:'');
  //         this.form.controls['match_type'].patchValue(match_type);
  //         this.form.controls['max_player'].patchValue(max_player);
  //         this.form.controls['entry_fees'].patchValue(entry_fee);
  //       }    
       
  //     }else{
  //       this.error();
  //     } 
  //   });

  // }

  onClose() {
    this.bsModalRef.hide();
  }

  public onSubmit(){ 

    //let selectedItem1 =  this.matchTypeData.find((item)=>item.id == this.form.controls['match_type'].value);
    //console.log('test', selectedItem1);
    //let seletctedText1 = selectedItem1.name;
    let selectedItem = this.gameTypesData.find((item)=>item.id == this.form.controls['game_type'].value);
    let seletctedText = selectedItem.name;

    this.submit = true;
    
    if (this.form.valid) {

      let jsonInput :any = {
        "format_type_id" : this.form.controls['game_type'].value, 
        "max_seat" : this.form.controls['max_player'].value,
        "entry_fees" : this.form.controls['entry_fees'].value, 
        "comm_per" : this.form.controls['comm_per'].value
      };  
      
      if(this.dataInfo.id == 0){ 
  
        this.httpClient.post('game-table/insert-game-table',jsonInput).subscribe((data:any= {}) => {
          //console.log(data);
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerAddEvent(jsonInput, data, seletctedText);
          }else if(data.response == 'failure'){
            this.msg = data.err_message;
            this.failed(this.msg);
          }else{
            this.error();
          } 
        },error => {
          this.bsModalRef.hide();
        });
        
      }else if(this.dataInfo.id > 0){
        
        this.httpClient.post('game-table/update-game-table/'+ this.dataInfo.id, jsonInput).subscribe((data:any= {}) => {
          
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEditEvent(jsonInput, seletctedText);
          }else if(data.response == 'failure'){
            this.msg = data.err_message;
            this.failed(this.msg);
          }else{
            this.error();
          } 
        },error => {
          this.bsModalRef.hide();
        });  
      }else{
        this.error();
      }
    }
  }

  triggerAddEvent(jsonInput: any, data: any,game_type_name: string) { 
    let id = data.data.gametable_id;
    this.event.emit({ data: jsonInput, id: id, game_type_name: game_type_name});
  }

  triggerEditEvent(jsonInput: any, game_type_name: string) { 
    this.event.emit({ data: jsonInput, game_type_name: game_type_name});
  }

  //Notification
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}


