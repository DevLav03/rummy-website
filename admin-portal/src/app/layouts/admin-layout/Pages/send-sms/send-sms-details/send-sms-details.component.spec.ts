import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SendSmsDetailsComponent } from './send-sms-details.component';

describe('SendSmsDetailsComponent', () => {
  let component: SendSmsDetailsComponent;
  let fixture: ComponentFixture<SendSmsDetailsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SendSmsDetailsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SendSmsDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
