import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GameTypesFormComponent } from './game-types-form.component';

describe('GameTypesFormComponent', () => {
  let component: GameTypesFormComponent;
  let fixture: ComponentFixture<GameTypesFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ GameTypesFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GameTypesFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
