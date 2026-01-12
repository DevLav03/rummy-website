import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GameTypesComponent } from './game-types.component';

describe('GameTypesComponent', () => {
  let component: GameTypesComponent;
  let fixture: ComponentFixture<GameTypesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ GameTypesComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GameTypesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
