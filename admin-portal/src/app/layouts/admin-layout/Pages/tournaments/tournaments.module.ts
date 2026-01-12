
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

import { TournamentRoutes } from './tournaments.routing';

import { TournamentsComponent } from './tournaments.component';
import { TournamentsFormComponent } from './tournaments-form/tournaments-form.component';
import { TournamentsTransactionsComponent } from './tournaments-transactions/tournaments-transactions.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(TournamentRoutes),
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

    TournamentsComponent,
    TournamentsFormComponent,
    TournamentsTransactionsComponent

  ],providers:[AuthGuardService]
})
export class TournamentModule {}