
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';

import { AuthGuardService } from '../../../../services/authguard.service';
import { ExcelService } from '../../../../services/excel.service';

import { UsersRoutes } from './users.routing';

import { UsersComponent } from './users.component';
import { UserDetailComponent } from './user-detail/user-detail.component';
import { UsersLogComponent } from './users-log/users-log.component';
import { UserTransactionReportComponent } from './user-transaction-report/user-transaction-report.component';


@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(UsersRoutes),
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

    UsersComponent,
    UsersLogComponent,
    UserDetailComponent,
    UserTransactionReportComponent

  ],providers:[AuthGuardService, ExcelService]
})
//providers: [{ provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true } ]
export class UsersModule {}