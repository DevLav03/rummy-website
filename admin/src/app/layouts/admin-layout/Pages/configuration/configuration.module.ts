
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';

import { ConfigurationRoutes } from './configuration.routing';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';

import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';
import { AuthGuardService } from '../../../../services/authguard.service';

import { ConfigurationComponent } from './configuration.component'

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(ConfigurationRoutes),
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
    ConfigurationComponent
  ],providers:[AuthGuardService]
})
export class ConfigurationModule {}