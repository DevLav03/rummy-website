
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

//Service
import { AuthGuardService } from '../../../../services/authguard.service';

//Route
import { GameSettingsRoutes } from './game-settings.routing';

//Form
import { GameMatchTypeFormComponent } from './game-match-type-form/game-match-type-form.component';
import { GameTypesFormComponent } from './game-types-form/game-types-form.component';

//Page
import { GameMatchTypeComponent } from './game-match-type/game-match-type.component';
import { GameTypesComponent } from './game-types/game-types.component';
import { GameTablesComponent } from './game-tables/game-tables.component';
import { GameTablesFormComponent } from './game-tables-form/game-tables-form.component';
import { BonusEntryComponent } from './bonus-entry/bonus-entry.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(GameSettingsRoutes),
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
  
    GameMatchTypeComponent, GameMatchTypeFormComponent,
    GameTypesComponent, GameTypesFormComponent,
    GameTablesComponent,  GameTablesFormComponent,
    BonusEntryComponent,
   
    
  ],providers:[AuthGuardService]
})
export class GameSettingsModule {}