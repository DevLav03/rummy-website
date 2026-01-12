import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserTransactionReportComponent } from './user-transaction-report.component';

describe('UserTransactionReportComponent', () => {
  let component: UserTransactionReportComponent;
  let fixture: ComponentFixture<UserTransactionReportComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UserTransactionReportComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UserTransactionReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
