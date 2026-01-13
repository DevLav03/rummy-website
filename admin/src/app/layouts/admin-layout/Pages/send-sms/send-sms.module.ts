
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';

import { SendSMSRoutes } from './send-sms.routing';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';

import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';
import { AuthGuardService } from '../../../../services/authguard.service';

import { SendNewSmsComponent } from './send-new-sms/send-new-sms.component';
import { SendSmsDetailsComponent } from './send-sms-details/send-sms-details.component';
import { SmsTemplatesComponent } from './sms-templates/sms-templates.component';

import { SmsTemFormComponent } from './sms-templates/sms-tem-form/sms-tem-form.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(SendSMSRoutes),
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

  
    SendNewSmsComponent, SendSmsDetailsComponent, SmsTemplatesComponent,
    SmsTemFormComponent

  ],providers:[AuthGuardService]
})
export class SendSMSModule {}