import { Component, OnInit  } from '@angular/core';
import { Validators, FormGroup, FormBuilder, FormControl } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { NotificationService } from '../../../../../../services/notification.service';

@Component({
  selector: 'app-add-news',
  templateUrl: './add-news.component.html',
  styleUrls: ['./add-news.component.scss']
})

export class AddNewsComponent implements OnInit {

  public getData: any; 

  public name: any;
  public role: any;

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

  constructor(private notifyService : NotificationService,  private httpClient : HttpClient) {

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");
    
    this.fileHolder=null;

  }

  ngOnInit(): void {

    this.content = '';

    this.form = new FormGroup({
      title: new FormControl('', [Validators.required]),
      image: new FormControl('', [Validators.required]),
      sub_desc: new FormControl('', [Validators.required]),
      content: new FormControl('', [Validators.required])
    });

  }

  public uploadFileImg(e){
    let extensionAllowed = {"png":true,"jpeg":true,"jpg":true};
    //console.log("File:::",e.target.files,e.target.files[0].size / 1024 / 1024,extensionAllowed,e.target.files[0].name.split('.').pop(),!extensionAllowed[nam]);
    if (e.target.files[0].size / 1024 / 1024 > 20) { 
      return;
    }
    if (extensionAllowed) {
      var nam = e.target.files[0].name.split('.').pop();
      if (!extensionAllowed[nam]) {
        return;
      }
    }
     
    const file = (e.target.files[0] as File);
    this.fileHolder=file;
  }

  public onSubmit(){ 

    this.submit = true;

    if (this.form.valid) {

      const form = document.querySelector('form')!;
      const formData = new FormData(form);

      formData.append('title', this.form.value.title);
      formData.append('sub_description', this.form.value.sub_desc);
      formData.append('content', this.form.value.content);

      if(this.fileHolder != null){
        formData.append('image', this.fileHolder, this.fileHolder.name);
      }
     
     
      this.httpClient.post('news/insert-news',formData).subscribe((data:any= {}) => {
        if(data.response == 'success'){
          this.success('Add Successfully!');
          this.form = new FormGroup({
            title: new FormControl(''),
            image: new FormControl(''),
            sub_desc: new FormControl(''),
            content: new FormControl('')
          });
        }else if(data.response == 'failure'){
          this.msg = data.err_message;
          this.failed(this.msg);
        }else{
          this.error();
        } 
      },error => {
        console.log("erorr",error);
      
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

