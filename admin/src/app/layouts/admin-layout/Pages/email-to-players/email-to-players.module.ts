
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

import { EmailToPlayersRoutes } from './email-to-players.routing';

import { SendEmailComponent } from './send-email/send-email.component';
import { SendEmailListComponent } from './send-email-list/send-email-list.component';
import { EmailTemplateComponent } from './email-template/email-template.component';

import { NgxTinymceModule } from 'ngx-tinymce';
import { EditEmailTemplateComponent } from './email-template/edit-email-template/edit-email-template.component';


@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(EmailToPlayersRoutes),
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    NgbModule,
    ClipboardModule,
    ModalModule.forRoot(),
    NgxDatatableModule,
    NgxPaginationModule,
    NgxTinymceModule
  ],
  declarations: [

    SendEmailComponent,
    SendEmailListComponent,
    EmailTemplateComponent,
    EditEmailTemplateComponent

  ],providers:[AuthGuardService]
})
export class EmailToPlayersModule {}