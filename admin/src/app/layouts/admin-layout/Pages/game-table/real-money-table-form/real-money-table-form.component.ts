import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { CustomvalidationService } from '../../../../../services/customvalidation.service';
import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-real-money-table-form',
  templateUrl: './real-money-table-form.component.html',
  styleUrls: ['./real-money-table-form.component.scss']
})

export class RealMoneyTableFormComponent implements OnInit {

  public roles: any; //Role

  public msg: any; //Alert Msg

  public gameTypesData: any;
  public gameMaxPlayer: any;
  public gameEntryFees: any;
  
  dataInfo: any;
  options:any;
  config: any;

  public event: EventEmitter<any> = new EventEmitter(); //Form
  form: FormGroup;
  submit = false;

  gameId:number=0;
  maxPlayerId:number=0;
  entryFeeId:number=0;

  constructor(private bsModalRef: BsModalRef, private fb: FormBuilder,  private notifyService : NotificationService,  private httpClient : HttpClient,  public modalOptions: ModalOptions, private customValidation: CustomvalidationService) {    
    
  }

  ngOnInit(): void {

    this.getGameTypes();

    this.form = this.fb.group(
    {
      
      game_id : ['', [Validators.required]],
      joker_type: ['joker', [Validators.required]],
      deck: ['2', [Validators.required]],
      max_player:['', [Validators.required]],
      entry_fees: ['', [Validators.required]]
    
    });
  
  } 

  gameTypeChange(game_id:number){
    this.gameId=game_id;//console.log(this.gameId);
    this.getMaxPlayer();
  }
  playerTypeChange(playerType:number){
    this.maxPlayerId=playerType;//console.log(this.maxPlayerId);
    this.getEntryFees();
  }
  entryEntryChange(entryType:number){
    this.entryFeeId=entryType;
    //this.getTourneyEntryFees();
  }

  getGameTypes(){
    this.httpClient.get('game-room-cash/get-cash-game-type').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTypesData = data.data;
        this.form.controls['game_id'].patchValue(this.gameTypesData[0].id);  
      }else{
        this.error();
      } 
    });

  }

  getMaxPlayer(){

    this.httpClient.get('game-room-cash/get-cash-max-player/'+this.gameId).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameMaxPlayer = data.data;
        this.form.controls['max_player'].patchValue(this.gameMaxPlayer[0].id);  
      }else{
        this.error();
      } 
    });


  }

  getEntryFees(){

    this.httpClient.get('game-room-cash/get-cash-entry-fees/'+this.gameId+'/'+this.maxPlayerId).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameEntryFees = data.data;
        this.form.controls['entry_fees'].patchValue(this.gameEntryFees[0].entry_fees);  
      }else{
        this.error();
      } 
    });

  }

  onClose() {
    this.bsModalRef.hide();
  }


  public onSubmit(){ 

  
    this.submit = true;
    
    if (this.form.valid) {

      let selectedItem = this.gameTypesData.find((item)=>item.game_id == this.form.controls['game_id'].value);
      let seletctedText = selectedItem.game_type;
  

      let jsonInput :any = {
        "game_id" : this.form.controls['game_id'].value, 
        "joker_type" : this.form.controls['joker_type'].value, 
        "deck" : this.form.controls['deck'].value, 
        "max_player" : this.form.controls['max_player'].value,
        "entry_fees" : this.form.controls['entry_fees'].value
      };       
  
      this.httpClient.post('game-room-cash/insert-game-room-cash',jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          this.bsModalRef.hide();
          this.triggerEvent(jsonInput, data, seletctedText);
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      },error => {
        this.bsModalRef.hide();
      }); 
     
    }
  }

  triggerEvent(data: any, res: any, game_type: any) { 
    this.event.emit({ admin: data, res: res, game_type: game_type});
  }

  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}

