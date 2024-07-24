import { Component } from '@angular/core';
import { MatToolbarModule } from '@angular/material/toolbar';
import { BoardComponent } from './board/board.component';
import { CommonModule } from '@angular/common';
import { MatNativeDateModule } from '@angular/material/core';
import { MatDatepickerModule } from '@angular/material/datepicker';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    MatNativeDateModule,
    MatDatepickerModule,
    MatToolbarModule,
    BoardComponent
  ],
  template: `
    <app-board></app-board>
  `,
  styles: []
})
export class AppComponent {}
