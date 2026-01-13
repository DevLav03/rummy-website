import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { NotificationService } from '../../../../../services/notification.service';

@Component({
  selector: 'app-game-match-type-form',
  templateUrl: './game-match-type-form.component.html',
  styleUrls: ['./game-match-type-form.component.scss']
})

export class GameMatchTypeFormComponent implements OnInit {

  public msg: any; //Alert Msg
  
  dataInfo: any;
  options:any;
  config: any;

  public event: EventEmitter<any> = new EventEmitter(); //Form
  MatchTypeForm: FormGroup;
  submit = false;

  constructor(private bsModalRef: BsModalRef, private fb: FormBuilder,  private notifyService : NotificationService,  private httpClient : HttpClient,  public modalOptions: ModalOptions) {    
    
    this.options = modalOptions;

    if(this.options.initialState.dataInfo !== null)
    {
      this.dataInfo= {
        id: 0,
        name: '',
        description: ''
      }
    }else{
      this.dataInfo= {
        id: this.options.initialState.dataInfo.id,
        name: this.options.initialState.dataInfo.name,
        description: this.options.initialState.dataInfo.description,
      }
    }

  }

  ngOnInit(): void {

    this.MatchTypeForm = this.fb.group(
    {
      name: [this.dataInfo.name, [Validators.required,Validators.minLength(3),Validators.maxLength(26)]],
      description: [this.dataInfo.description, [Validators.required,Validators.minLength(8),Validators.maxLength(200)]],
    });

  } 

  onClose() {
    this.bsModalRef.hide();
  }

 
  public onSubmit(){ 

    this.submit = true;
    
    if (this.MatchTypeForm.valid) {

      let jsonInput :any = {
        "name" : this.MatchTypeForm.controls['name'].value, 
        "description" : this.MatchTypeForm.controls['description'].value
      }; 
      
      
      if(this.dataInfo.id == 0){ 
  
        this.httpClient.post('rummy-format/insert-rummy-format',jsonInput).subscribe((data:any= {}) => {
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEvent(jsonInput);
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
        
        this.httpClient.post('rummy-format/update-rummy-fromat/'+ this.dataInfo.id, jsonInput).subscribe((data:any= {}) => {
          
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEvent(jsonInput);
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

  triggerEvent(data: any) { 
    this.event.emit({ data: data});
  }

  //Notification
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}

