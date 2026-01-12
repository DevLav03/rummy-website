import { Routes } from '@angular/router';

import { UsersWithdrawalRequestComponent } from './users-withdrawal-request.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const UsersWithdrawalRoutes: Routes = [

    { path: 'withdrawal-request',  component: UsersWithdrawalRequestComponent, canActivate : [AuthGuardService], data:{id:'get_withdraw_req'} },

];