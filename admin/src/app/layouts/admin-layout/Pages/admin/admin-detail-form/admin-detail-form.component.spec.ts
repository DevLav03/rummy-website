import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminDetailFormComponent } from './admin-detail-form.component';

describe('AdminDetailFormComponent', () => {
  let component: AdminDetailFormComponent;
  let fixture: ComponentFixture<AdminDetailFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AdminDetailFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminDetailFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
