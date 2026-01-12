import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SmsTemFormComponent } from './sms-tem-form.component';

describe('SmsTemFormComponent', () => {
  let component: SmsTemFormComponent;
  let fixture: ComponentFixture<SmsTemFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SmsTemFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SmsTemFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
