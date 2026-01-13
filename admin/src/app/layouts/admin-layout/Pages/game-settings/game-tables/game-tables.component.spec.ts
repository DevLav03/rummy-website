import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GameTablesComponent } from './game-tables.component';

describe('GameTablesComponent', () => {
  let component: GameTablesComponent;
  let fixture: ComponentFixture<GameTablesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ GameTablesComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GameTablesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
