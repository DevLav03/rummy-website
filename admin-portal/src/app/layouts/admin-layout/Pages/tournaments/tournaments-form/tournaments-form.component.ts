import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { CustomvalidationService } from '../../../../../services/customvalidation.service';
import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-tournaments-form',
  templateUrl: './tournaments-form.component.html',
  styleUrls: ['./tournaments-form.component.scss']
})

export class TournamentsFormComponent implements OnInit {

  public roles: any; //Role

  public msg: any; //Alert Msg

  public gameTypesData: any;
  public gameTourneyPlayer: any;
  public gameTourneyFees: any;
  
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
      
      title : ['', [Validators.required, Validators.minLength(3),Validators.maxLength(26)]],
      start_date: ['', [Validators.required]],
      start_time: ['', [Validators.required]],
      reg_start_date:['', [Validators.required]],
      reg_start_time: ['', [Validators.required]],
      reg_end_date: ['', [Validators.required]],
      reg_end_time: ['', [Validators.required]],
      price_amount: [0, [Validators.required]],
      game_id: [0, [Validators.required]],
      max_player:[0, [Validators.required]],
      entry_fees: [0, [Validators.required]]
    
    });
  
  } 

  gameTypeChange(game_id:number){
    this.gameId=game_id;//console.log(this.gameId);
    this.getTourneyMaxPlayer();
  }
  playerTypeChange(playerType:number){
    this.maxPlayerId=playerType;//console.log(this.maxPlayerId);
    this.getTourneyEntryFees();
  }
  entryEntryChange(entryType:number){
    this.entryFeeId=entryType;
    //this.getTourneyEntryFees();
  }

  getGameTypes(){
    this.httpClient.get('game-room-tourney/get-tourney-game-type').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTypesData = data.data;
        this.form.controls['game_id'].patchValue(this.gameTypesData[0].id);  
      }else{
        this.error();
      } 
    });

  }

  getTourneyMaxPlayer(){

    this.httpClient.get('game-room-tourney/get-tourney-max-player/'+this.gameId).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTourneyPlayer = data.data;
        this.form.controls['max_player'].patchValue(this.gameTourneyPlayer[0].id);  
      }else{
        this.error();
      } 
    });


  }

  getTourneyEntryFees(){

    this.httpClient.get('game-room-tourney/get-tourney-entry-fees/'+this.gameId+'/'+this.maxPlayerId).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.gameTourneyFees = data.data;
        this.form.controls['entry_fees'].patchValue(this.gameTourneyFees[0].entry_fees);  
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
        "title" : this.form.controls['title'].value, 
        "start_date" : this.form.controls['start_date'].value, 
        "start_time" : this.form.controls['start_time'].value, 
        "reg_start_date" : this.form.controls['reg_start_date'].value, 
        "reg_start_time" : this.form.controls['reg_start_time'].value, 
        "reg_end_date" : this.form.controls['reg_end_date'].value,
        "reg_end_time" : this.form.controls['reg_end_time'].value,
        "price_amount" : this.form.controls['price_amount'].value,
        "game_id" : this.form.controls['game_id'].value,
        "max_player" : this.form.controls['max_player'].value,
        "entry_fees" : this.form.controls['entry_fees'].value
      };       
  
      this.httpClient.post('game-room-tourney/insert-game-room-tourney',jsonInput).subscribe((data:any= {}) => {
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
