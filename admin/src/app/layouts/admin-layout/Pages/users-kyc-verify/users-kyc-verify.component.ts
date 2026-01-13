import { Component, OnInit, ViewChild  } from '@angular/core';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { Router } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';

import { HttpClient } from '@angular/common/http';
import * as moment from 'moment';
import { SubscriptionLike } from 'rxjs';

import { NotificationService } from '../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { KycCancelFormComponent } from './kyc-cancel-form/kyc-cancel-form.component';
import { KycVerifyFormComponent } from './kyc-verify-form/kyc-verify-form.component';


@Component({
  selector: 'app-users-kyc-verify',
  templateUrl: './users-kyc-verify.component.html',
  styleUrls: ['./users-kyc-verify.component.scss']
})

export class UsersKycVerifyComponent implements OnInit {

  public kycVerifyData : any= []; //Data

  public temp: any;

  public admin_id: number;
  public current_date : Date;
  public start_date : any ;
  public end_date: any ;


  bsModalRef: BsModalRef; //Modal

  public kycVerifyForm: FormGroup;
  submit = false;
  
  subscription: SubscriptionLike;
  page = {
    limit: 10,
    count: 0,
    offset: 0,
    orderBy: 'created_date',
    orderDir: 'desc'
  };
  searchVal:string='';
 

  @ViewChild(DatatableComponent) public kycVerifyTable: DatatableComponent; //ngx table
  public currentPageLimit: number = 10;
  public readonly pageLimitOptions = [
    {value: 5},
    {value: 10},
    {value: 25},
    {value: 50},
    {value: 100},
  ];

  constructor(private fb: FormBuilder, private httpClient : HttpClient,private modalService: BsModalService, private authService: AuthService,
    private _router: Router, private route: ActivatedRoute,  private notifyService : NotificationService) {
    
    this.current_date = new Date();
    this.start_date=moment(this.current_date).format('yyyy-MM-DD');
    this.end_date=moment(this.current_date).format('yyyy-MM-DD');
    this.admin_id = this.route.snapshot.params['admin_id'];

    this.kycVerifyForm = this.fb.group({
      start_date: [this.start_date, [Validators.required]],
      end_date: [this.end_date, [Validators.required]]
    });   

  }
  ngOnInit() {

    this.getTournaments();
  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }

  public addTournaments(){
    this._router.navigate(['add-tournament'])
  }

  public verityForm(item: any){
    const initialState = {
      config:{
        title: "Verify KYC Status",
      },
      dataInfo: {
        id: item.id
      }
    };
    this.bsModalRef = this.modalService.show(KycVerifyFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
     this.bsModalRef.content.event.subscribe(res => {

       //console.log('res', res);

      this.kycVerifyData.map(obj => {
        if (obj.id == item.id) {
          obj.pc_verify_status= res.pc_verify_status;
        }
      });
    
      this.kycVerifyTable.recalculate();
      this.success('Update Successfully!');
      
     });
  }

  public rejectForm(item: any){
    const initialState = {
      config:{
        title: "Verify KYC Status",
      },
      dataInfo: {
        id: item.id
      }
    };
    this.bsModalRef = this.modalService.show(KycCancelFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-sm'
    });
     this.bsModalRef.content.event.subscribe(res => {

       //console.log('res', res);

      this.kycVerifyData.map(obj => {
        if (obj.id == item.id) {
          obj.pc_verify_status= res.pc_verify_status;
        }
      });
    
      this.kycVerifyTable.recalculate();
      this.success('Update Successfully!');
      
     });
  }

  public downloadFile(id: number){
    window.open('kyc-verify/file-download/' + id); 
  }

  public getTournaments(){

    let jsonInput :any = { 
      "start_date" :  this.start_date,
      "end_date" :  this.end_date,
      "offset": 0,
      "limit": 10,
      //"search_val": ''
    };

    this.httpClient.post('kyc-verify/get-kycverify', jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data[1].total;
        this.kycVerifyData =  data.data[0].kyc_verify;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.kycVerifyData = [];
      }else{
        this.error();
      } 
    }); 
  }

  public onSubmit(){
    this.submit = true;
    this.kycVerifyTable.offset = 0;
    this.pageCallback({ offset: 0, limit: this.kycVerifyTable.limit });
  }

  pageCallback(evnt) {
    let offset = evnt.offset*evnt.limit;
    let jsonInput :any = { 
      "start_date" :  this.kycVerifyForm.controls['start_date'].value,
      "end_date" :  this.kycVerifyForm.controls['end_date'].value,
      "offset":offset,
      "limit":evnt.limit,
      //"search_val":this.searchVal
    };
    this.reloadTable(jsonInput);
  }

  reloadTable(jsonObj:any) {
    if (this.subscription) {
      this.subscription.unsubscribe();
    }
    this.subscription=this.httpClient.post('kyc-verify/get-kycverify', jsonObj).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.page.count = data.data[1].total;
        this.kycVerifyData =  data.data[0].kyc_verify;
      }else if(data.response == 'failure'){
        this.page.count = 0;
        this.kycVerifyData = [];
      }else{
        this.error();
      } 
    });
  }

 public onLimitChange(limit: any): void {
    this.currentPageLimit=limit;
    this.kycVerifyTable.limit = limit;
    this.kycVerifyTable.offset = 0;
    this.pageCallback({ offset: 0,limit:this.kycVerifyTable.limit });
  }

  updateFilter(event) {
    this.kycVerifyTable.offset = 0;
    this.searchVal = event.target.value.toLowerCase();
    this.pageCallback({ offset: 0,limit:this.kycVerifyTable.limit });
  }

  //Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success") 
  }

  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }

}
