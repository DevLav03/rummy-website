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

import { NgxTinymceModule } from 'ngx-tinymce';

import { AuthGuardService } from '../../../../services/authguard.service';

import { WebsiteRoutes } from './website.routing';

import { WebSettingComponent } from './web-setting/web-setting.component';
import { SocialMediaComponent } from './social-media/social-media.component';
import { PostFaqComponent } from './post-faq/post-faq.component';
import { PostNewsComponent } from './post-news/post-news.component';
import { FaqFormComponent } from './post-faq/faq-form/faq-form.component';

import { AddNewsComponent } from './post-news/add-news/add-news.component';
import { EditNewsComponent } from './post-news/edit-news/edit-news.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(WebsiteRoutes),
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
  
    WebSettingComponent,
    SocialMediaComponent,
    PostFaqComponent,
    PostNewsComponent,
    FaqFormComponent,
    AddNewsComponent,
    EditNewsComponent

  ],providers:[AuthGuardService]
})
export class WebsiteModule {}