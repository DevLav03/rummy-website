import { Routes } from '@angular/router';

import { UsersKycVerifyComponent } from './users-kyc-verify.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const UsersKycVerifyRoute: Routes = [

    { path: 'users-kyc-verify',           component: UsersKycVerifyComponent, canActivate : [AuthGuardService], data:{id:'get_user_kyc_Verify'} },

];