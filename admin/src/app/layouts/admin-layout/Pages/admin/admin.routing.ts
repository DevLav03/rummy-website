import { Routes } from '@angular/router';

import { AdminComponent } from './admin.component';
import { AdminLogComponent } from './admin-log/admin-log.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const AdminRoutes: Routes = [

    { path: 'admin',           component: AdminComponent, canActivate : [AuthGuardService], data:{id:'get_admin'} },
    { path: 'admin-log/:admin_id',  component: AdminLogComponent,canActivate : [AuthGuardService], data:{id:'admin_log'} },

];