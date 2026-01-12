import { Routes } from '@angular/router';

import { AuthGuardService } from '../../../../services/authguard.service';

import { GameMatchTypeComponent } from './game-match-type/game-match-type.component';
import { GameTypesComponent } from './game-types/game-types.component';
import { GameTablesComponent } from './game-tables/game-tables.component';
import { BonusEntryComponent } from './bonus-entry/bonus-entry.component';

export const GameSettingsRoutes: Routes = [

    { path: 'game-match-type', component: GameMatchTypeComponent, canActivate : [AuthGuardService], data:{id:'get_match_type'} },
    { path: 'game-types/:format_id', component: GameTypesComponent, canActivate : [AuthGuardService], data:{id:'get_game_type'} },
    { path: 'game-tables', component: GameTablesComponent, canActivate : [AuthGuardService], data:{id:'get_game_table'} },
    // { path: 'bonus-entry', component: BonusEntryComponent, canActivate : [AuthGuardService], data:{id:'bonus_entry'} },

];