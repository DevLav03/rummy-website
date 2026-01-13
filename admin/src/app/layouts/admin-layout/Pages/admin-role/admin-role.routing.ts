import { Routes } from '@angular/router';

import { RolesComponent } from './roles/roles.component';
import { ScopePermissionComponent } from './scope-permission/scope-permission.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const AdminRoleRoutes: Routes = [

    { path: 'role', component: RolesComponent, canActivate : [AuthGuardService], data:{id:'get_role'} },
    { path: 'scope-permission/:role_id',  component: ScopePermissionComponent, canActivate : [AuthGuardService], data:{id:'get_scope'} },

];