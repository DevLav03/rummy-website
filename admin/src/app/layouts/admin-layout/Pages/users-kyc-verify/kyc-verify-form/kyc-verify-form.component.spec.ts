import { ComponentFixture, TestBed } from '@angular/core/testing';

import { KycVerifyFormComponent } from './kyc-verify-form.component';

describe('KycVerifyFormComponent', () => {
  let component: KycVerifyFormComponent;
  let fixture: ComponentFixture<KycVerifyFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ KycVerifyFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(KycVerifyFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
