
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';

import { AuthGuardService } from '../../../../services/authguard.service';

//Routes
import { GameTableRoutes } from './game-table.routing';

//Forms
//import { AddRealMoneyTableComponent } from './forms/add-real-money-table/add-real-money-table.component';

//Pages
import { RealMoneyTableComponent } from './real-money-table/real-money-table.component';
import { PracticeTableComponent } from './practice-table/practice-table.component';
import { RealMoneyTableFormComponent } from './real-money-table-form/real-money-table-form.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(GameTableRoutes),
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

    //AddRealMoneyTableComponent,
    RealMoneyTableComponent,
    PracticeTableComponent,
    RealMoneyTableFormComponent

  ],providers:[AuthGuardService]
})
export class GameTableModule {}