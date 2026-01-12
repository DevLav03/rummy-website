
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';

import { AdminRoutes } from './admin.routing';

import { AdminComponent } from './admin.component'; 
import { AdminLogComponent } from './admin-log/admin-log.component';
import { AdminFormComponent } from './admin-form/admin-form.component'; 
import { AdminIpFormComponent } from './admin-ip-form/admin-ip-form.component';
import { AdminDetailFormComponent } from './admin-detail-form/admin-detail-form.component';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';
import { AuthGuardService } from '../../../../services/authguard.service'

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(AdminRoutes),
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
  
    AdminComponent,
    AdminFormComponent,
    AdminIpFormComponent,
    AdminLogComponent,
    AdminDetailFormComponent

  ],providers:[AuthGuardService]
})
export class AdminModule {}