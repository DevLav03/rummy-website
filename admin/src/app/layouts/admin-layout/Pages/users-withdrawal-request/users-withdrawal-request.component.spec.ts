import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UsersWithdrawalRequestComponent } from './users-withdrawal-request.component';

describe('UsersWithdrawalRequestComponent', () => {
  let component: UsersWithdrawalRequestComponent;
  let fixture: ComponentFixture<UsersWithdrawalRequestComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UsersWithdrawalRequestComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UsersWithdrawalRequestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
