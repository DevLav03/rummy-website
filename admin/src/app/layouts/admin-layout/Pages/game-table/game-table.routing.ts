import { Routes } from '@angular/router';

import { AuthGuardService } from '../../../../services/authguard.service';

import { RealMoneyTableComponent } from './real-money-table/real-money-table.component';
import { PracticeTableComponent } from './practice-table/practice-table.component';


export const GameTableRoutes: Routes = [

    { path: 'real-money-table', component: RealMoneyTableComponent, canActivate : [AuthGuardService], data:{id:'get_cash_table'} },
    { path: 'practice-table', component: PracticeTableComponent, canActivate : [AuthGuardService], data:{id:'get_free_table'} },

];