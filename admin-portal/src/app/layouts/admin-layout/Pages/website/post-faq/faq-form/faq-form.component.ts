import { Component, OnInit, EventEmitter  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { BsModalRef, ModalOptions } from 'ngx-bootstrap/modal';

import { CustomvalidationService } from '../../../../../../services/customvalidation.service';
import { NotificationService } from '../../../../../../services/notification.service';

@Component({
  selector: 'app-faq-form',
  templateUrl: './faq-form.component.html',
  styleUrls: ['./faq-form.component.scss']
})

export class FaqFormComponent implements OnInit {

  public msg: any; //Alert Msg
  
  dataInfo: any;
  options:any;
  config: any;

  public event: EventEmitter<any> = new EventEmitter(); //Form
  form: FormGroup;
  submit = false;

  constructor(private bsModalRef: BsModalRef, private fb: FormBuilder,  private notifyService : NotificationService,  private httpClient : HttpClient,  public modalOptions: ModalOptions, private customValidation: CustomvalidationService) {    
    
    this.options = modalOptions;

    if(this.options.initialState.dataInfo !== null)
    {
      this.dataInfo= {
        id: 0,
        title: '',
        answer: ''
      }
    }else{
      this.dataInfo= {
        id: this.options.initialState.dataInfo.id,
        title: this.options.initialState.dataInfo.title,
        answer: this.options.initialState.dataInfo.answer
      }
    }

  }

  ngOnInit(): void {

    this.form = this.fb.group(
    {
      title: [this.dataInfo.title, [Validators.required]],
      answer: [this.dataInfo.answer, [Validators.required]]
    });
  
  } 

  onClose() {
    this.bsModalRef.hide();
  }

  public onSubmit(){ 

    this.submit = true;
    
    if (this.form.valid) {

      let jsonInput :any = {
        "title" : this.form.controls['title'].value, 
        "answer" : this.form.controls['answer'].value
      }; 

      if(this.dataInfo.id == 0){ 
  
        this.httpClient.post('faq/insert-faq',jsonInput).subscribe((data:any= {}) => {
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEvent(jsonInput, data);
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
        
        this.httpClient.post('faq/update-faq/'+ this.dataInfo.id, jsonInput).subscribe((data:any= {}) => {
          
          if(data.response == 'success'){
            this.bsModalRef.hide();
            this.triggerEvent(jsonInput, data);
          }else if(data.response == 'failure'){
            this.msg = data.message;
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

  triggerEvent(data: any, res: any) { 
    this.event.emit({ data: data, res: res});
  }

  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  
}

