import { Component, OnInit } from '@angular/core';
import { Validators, FormGroup, FormBuilder, FormControl } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

import { catchError, map } from 'rxjs/operators';
import { throwError } from 'rxjs';

import { DomSanitizer } from '@angular/platform-browser';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-web-setting',
  templateUrl: './web-setting.component.html',
  styleUrls: ['./web-setting.component.scss']
})

export class WebSettingComponent implements OnInit {

  public getData: any;

  public name: any;
  public role: any;

  public logo: any;

  public msg: any; //Alert Msg

  form: FormGroup;
  submit = false;

  public config: any = {
    height: 250,
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
  public html: string = '';
  fileHolder: File | null;

  constructor(private notifyService: NotificationService, private httpClient: HttpClient, public sanitizer: DomSanitizer, private authService: AuthService) {

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");

    this.fileHolder = null;
  }

  ngOnInit(): void {

    this.html = '';

    this.form = new FormGroup({
      logo_image: new FormControl(''),
      footer: new FormControl('', [Validators.required]),
      banner_title: new FormControl('', [Validators.required])

    });
    this.getFormData();
  }

  public checkScope(scopeName: string) {

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public getFormData() {

    this.httpClient.get('settings/get-settings').subscribe((data: any = {}) => {
      this.getData = data.data.settings[0];
      this.logo = this.getData.logo_image;
      this.form.get('footer').patchValue(this.getData.footer);
      this.form.get('banner_title').patchValue(this.getData.banner_title);
    });

  }

  public logoFileImg(e) {

    let extensionAllowed = { "png": true, "jpeg": true, "jpg": true };
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
    this.fileHolder = file;
  }

  public onSubmit() {

    //console.log(this.form,this.form.value.footer,this.form.value.banner_title);
    if (this.form.valid) {

      const form = document.querySelector('form')!;
      const formData = new FormData(form);

      formData.append('footer', this.form.value.footer);
      formData.append('banner_title', this.form.value.banner_title);
      if (this.fileHolder != null) {
        formData.append('logo_image', this.fileHolder, this.fileHolder.name);
      }

      // use the formDat here
      //console.log(formData);
      this.httpClient.post('settings/update-settings', formData).subscribe((data: any = {}) => {
        if (data.response == 'success') {
          this.getFormData();
          this.success('Update Successfully!');
        } else if (data.response == 'failure') {
          this.msg = data.err_message;
          this.failed(this.msg);
        } else {
          this.error();
        }
      }, error => {
        console.log("erorr", error);
      });

    }

  }


  //Notification
  public success(msg: string) {
    this.notifyService.showSuccess(msg, "Success")
  }

  public failed(msg: string) {
    this.notifyService.showError(msg, "Error")
  }

  public error() {
    this.notifyService.showWarning("Something Went Wrong!", "Warning")
  }

}

