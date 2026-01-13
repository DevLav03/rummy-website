import { Component, OnInit  } from '@angular/core';
import { Validators, FormGroup, FormBuilder, FormControl } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';

import { NotificationService } from '../../../../../../services/notification.service';

@Component({
  selector: 'app-edit-email-template',
  templateUrl: './edit-email-template.component.html',
  styleUrls: ['./edit-email-template.component.scss']
})

export class EditEmailTemplateComponent implements OnInit {

  public getData: any; 

  public name: any;
  public role: any;

  public tem_id : number;

  public msg: any; //Alert Msg
  
  form: FormGroup;
  submit = false;

  public config: any = {
    height: 450,
    // theme: "modern",
    // powerpaste advcode toc tinymcespellchecker a11ychecker mediaembed linkchecker help
    plugins:
      'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image imagetools link media template codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists textcolor wordcount contextmenu colorpicker textpattern',
    toolbar:
      'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
    image_advtab: true,
    imagetools_toolbar:
      'rotateleft rotateright | flipv fliph | editimage imageoptions',
    templates: [
      { title: 'Test template 1', content: 'Test 1' },
      { title: 'Test template 2', content: 'Test 2' },
    ],
    content_css: [
      '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
      '//www.tinymce.com/css/codepen.min.css',
    ],
    setup: (editor) => {
      //console.log(editor.ui);
    },
  };

  public content:string='';

  fileHolder: File | null;

  constructor(private fb: FormBuilder, private route: ActivatedRoute,  private notifyService : NotificationService,  private httpClient : HttpClient,  public sanitizer:DomSanitizer) {
    
    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

    this.tem_id = this.route.snapshot.params['mail_tem_id'];
  }

  ngOnInit(): void {

    this.form = new FormGroup({
      name: new FormControl('', [Validators.required]),
      subject: new FormControl('', [Validators.required]),
      message: new FormControl('', [Validators.required])
    });

    this.getFormData();
  }

  public getFormData(){

    this.httpClient.get('template-mail/get-template/'+this.tem_id).subscribe((data:any= {}) => {
      if(data.response == 'success'){

        this.getData = data.data[0];
  
        this.form = new FormGroup({
          name: new FormControl(this.getData.name, [Validators.required]),
          subject: new FormControl(this.getData.subject, [Validators.required]),
          message: new FormControl(this.getData.message, [Validators.required])
        });

      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    },error => {
      this.error();
    });

  }

 
  public onSubmit(){ 

    this.submit = true;
    
    if (this.form.valid) {

      let jsonInput :any = {
        "name" : this.form.controls['name'].value, 
        "subject" : this.form.controls['subject'].value, 
        "message" : this.form.controls['message'].value
      }; 
      
      this.httpClient.post('template-mail/update-default-mail/'+this.tem_id,jsonInput).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          this.success('Update Successfully!')
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      });

    }

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



