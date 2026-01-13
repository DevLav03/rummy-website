import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule } from  '@angular/forms';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ToastrModule } from 'ngx-toastr';
import { NgxTinymceModule } from 'ngx-tinymce';

import { AppRoutingModule } from './app.routing';

import { AppComponent } from './app.component';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { AuthLayoutComponent } from './layouts/auth-layout/auth-layout.component';
//import { AngularFileUploadComponent } from './angular-file-upload/angular-file-upload.component';
import { ComponentsModule } from './components/components.module';
import { MailVerifyComponent } from './mail-verify/mail-verify.component';

@NgModule({
  imports: [
    BrowserAnimationsModule,
    FormsModule,
    HttpClientModule, 
    ComponentsModule,
    NgbModule,
    RouterModule,
    AppRoutingModule,
    ReactiveFormsModule,
    ToastrModule.forRoot(),
    NgxTinymceModule.forRoot({ baseURL: '//cdnjs.cloudflare.com/ajax/libs/tinymce/5.7.1/' })
  ],
  declarations: [
    AppComponent,
    AdminLayoutComponent,
    AuthLayoutComponent,
    MailVerifyComponent,
    //AngularFileUploadComponent
  ],
  providers: [], bootstrap: [AppComponent]
})
export class AppModule { }
