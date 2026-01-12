import { Routes } from '@angular/router';

import { UsersComponent } from './users.component';
import { UsersLogComponent } from './users-log/users-log.component';
import { UserDetailComponent } from './user-detail/user-detail.component';
import { UserTransactionReportComponent } from './user-transaction-report/user-transaction-report.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const UsersRoutes: Routes = [

    { path: 'users',           component: UsersComponent, canActivate : [AuthGuardService], data:{id:'get_user'} },
    { path: 'users-log/:user_id',           component: UsersLogComponent, canActivate : [AuthGuardService], data:{id:'user_log'}},
    { path: 'user-details/:user_id',           component: UserDetailComponent, canActivate : [AuthGuardService], data:{id:'user_detail'}},
    // { path: 'user-transaction',           component: UserTransactionReportComponent, canActivate : [AuthGuardService], data:{id:'user_transaction'} },
];