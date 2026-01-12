import { Routes } from '@angular/router';

import { AuthGuardService } from '../../../../services/authguard.service';

import { TournamentsComponent } from './tournaments.component';
import { TournamentsTransactionsComponent } from './tournaments-transactions/tournaments-transactions.component';

export const TournamentRoutes: Routes = [

    { path: 'tournaments',           component: TournamentsComponent, canActivate : [AuthGuardService], data:{id:'get_tourney'} },
    // { path: 'tournament-transaction',           component: TournamentsTransactionsComponent, canActivate : [AuthGuardService], data:{id:'tournament_transaction'} },

];