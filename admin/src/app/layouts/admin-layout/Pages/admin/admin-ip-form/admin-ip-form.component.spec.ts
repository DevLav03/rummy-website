import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminIpFormComponent } from './admin-ip-form.component';

describe('AdminIpFormComponent', () => {
  let component: AdminIpFormComponent;
  let fixture: ComponentFixture<AdminIpFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AdminIpFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminIpFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
