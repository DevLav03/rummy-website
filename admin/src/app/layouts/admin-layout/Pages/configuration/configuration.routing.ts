import { Routes } from '@angular/router';

import { ConfigurationComponent } from './configuration.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const ConfigurationRoutes: Routes = [

    { path: 'config', component: ConfigurationComponent, canActivate : [AuthGuardService], data:{id:'get_config'} },

];