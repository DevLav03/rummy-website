import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ActivatedRoute } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';

import { NotificationService } from '../../../../../services/notification.service';


@Component({
  selector: 'app-user-detail',
  templateUrl: './user-detail.component.html',
  styleUrls: ['./user-detail.component.scss']
})
export class UserDetailComponent implements OnInit {

  public userData : any= [];
  public userBankData: any = [];
  public userKycData: any = [];
  public userCashData: any = [];
  public userPracticeData: any = [];
  public userBonusData: any = [];
  public userPointsData: any = [];
  public userGamedetailsData: any = [];


  public user_id: number;

  public msg: any;

  constructor(private httpClient : HttpClient, public sanitizer:DomSanitizer, private route: ActivatedRoute, private notifyService : NotificationService) { }

  ngOnInit(): void {

    this.user_id = this.route.snapshot.params['user_id'];

    this.getUserData(this.user_id);

    this.getUserBankData(this.user_id);

    this.getUserKycData(this.user_id);

    this.getUserCashData(this.user_id);

    this.getUserPracticeData(this.user_id);

    this.getUserBonusData(this.user_id);

    this.getUserPointsData(this.user_id);

    this.getUserGamedetailsData(this.user_id);


  }


  public getUserData(id: number){
    this.httpClient.get('users/get-user/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userData = data.data.users[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserBankData(id: number){
    this.httpClient.get('user-bank-details/get-details/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userBankData = data.data[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserKycData(id: number){
    this.httpClient.get('kyc-verify/get-kycverify/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userKycData = data.data.kyc_verify[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserCashData(id: number){
    this.httpClient.get('users/get-cash-chips/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userCashData = data.data[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserPracticeData(id: number){
    this.httpClient.get('users/get-free-chips/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userPracticeData = data.data[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserBonusData(id: number){
    this.httpClient.get('users/get-bonus/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userBonusData = data.data[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserPointsData(id: number){
    this.httpClient.get('users/get-points/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userPointsData = data.data[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public getUserGamedetailsData(id: number){
    this.httpClient.get('users/user-game-details/' + id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.userGamedetailsData = data.data[0];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  // Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success")
  }
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}
