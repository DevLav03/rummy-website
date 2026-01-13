import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GameTablesFormComponent } from './game-tables-form.component';

describe('GameTablesFormComponent', () => {
  let component: GameTablesFormComponent;
  let fixture: ComponentFixture<GameTablesFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ GameTablesFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GameTablesFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
