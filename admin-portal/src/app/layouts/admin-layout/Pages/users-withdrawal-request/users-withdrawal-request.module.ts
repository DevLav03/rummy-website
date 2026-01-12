
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ClipboardModule } from 'ngx-clipboard';


import { UsersWithdrawalRoutes } from './users-withdrawal-request.routing';


import { UsersWithdrawalRequestComponent } from './users-withdrawal-request.component';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
// import { HttpClient, HttpHeaders } from '@angular/common/http';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import { JwtInterceptor } from '../../../../helpers/jwt.interceptor';
import {NgxPaginationModule} from 'ngx-pagination';
import { AuthGuardService } from '../../../../services/authguard.service'

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(UsersWithdrawalRoutes),
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

    UsersWithdrawalRequestComponent

  ],providers:[AuthGuardService]
})

export class UsersWithdrawalModule {}