import { ComponentFixture, TestBed } from '@angular/core/testing';

import { KycCancelFormComponent } from './kyc-cancel-form.component';

describe('KycCancelFormComponent', () => {
  let component: KycCancelFormComponent;
  let fixture: ComponentFixture<KycCancelFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ KycCancelFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(KycCancelFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
