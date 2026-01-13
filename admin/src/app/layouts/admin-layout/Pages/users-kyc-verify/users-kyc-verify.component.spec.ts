import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UsersKycVerifyComponent } from './users-kyc-verify.component';

describe('UsersKycVerifyComponent', () => {
  let component: UsersKycVerifyComponent;
  let fixture: ComponentFixture<UsersKycVerifyComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UsersKycVerifyComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UsersKycVerifyComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
