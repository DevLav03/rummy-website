
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ClipboardModule } from 'ngx-clipboard';


import { UsersKycVerifyRoute } from './users-kyc-verify.routing';
import { UsersKycVerifyComponent } from './users-kyc-verify.component';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
// import { HttpClient, HttpHeaders } from '@angular/common/http';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import { JwtInterceptor } from '../../../../helpers/jwt.interceptor';
import {NgxPaginationModule} from 'ngx-pagination';
import { AuthGuardService } from '../../../../services/authguard.service';
//import { KycCancelFormComponent } from './kyc-cancel-form/kyc-cancel-form.component';
//import { KycVerifyFormComponent } from './kyc-verify-form/kyc-verify-form.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(UsersKycVerifyRoute),
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    NgbModule,
    ClipboardModule,
    ModalModule.forRoot(),
    NgxDatatableModule,
    NgxPaginationModule
    
  ],
  declarations: [

    UsersKycVerifyComponent,
    //KycCancelFormComponent,
    //KycVerifyFormComponent 

  ],providers:[AuthGuardService]
})
//providers: [{ provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true } ]
export class UserKycVerifyModule {}