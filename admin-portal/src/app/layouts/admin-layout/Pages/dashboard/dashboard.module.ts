
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

import { DashboardRoutes } from './dashboard.routing';

import { DashboardComponent } from './dashboard.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(DashboardRoutes),
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
    
    DashboardComponent,

  ],providers:[AuthGuardService]
})
//providers: [{ provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true } ]
export class DashboardModule {}