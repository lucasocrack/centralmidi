import { Component, OnInit } from '@angular/core';
import { Event, NavigationEnd, Router, RouterOutlet } from '@angular/router';
import { SidebarService } from '../shared/components/sidebar/sidebar.service';
import { CommonModule, NgClass, ViewportScroller } from '@angular/common';
import { CustomizerSettingsService } from '../shared/components/customizer-settings/customizer-settings.service';
import { SidebarComponent } from '../shared/components/sidebar/sidebar.component';
import { CustomizerSettingsComponent } from '../shared/components/customizer-settings/customizer-settings.component';
import { FooterComponent } from '../shared/components/footer/footer.component';
import { HeaderComponent } from './components/header/header.component';
import { FormControl, ReactiveFormsModule } from '@angular/forms';

type Question = {
  name: string;
  key: string;
  type: string;
}

type DynamicFormControls<T extends Question[]> = {
  [K in T[number]['key']]: FormControl<string | null>;
};

@Component({
  selector: 'app-private',
  standalone: true,
  imports: [
    RouterOutlet,
    CommonModule,
    SidebarComponent,
    HeaderComponent,
    FooterComponent,
    CustomizerSettingsComponent,
    NgClass,
    ReactiveFormsModule
  ],
  templateUrl: './private.component.html',
  styleUrl: './private.component.scss'
})
export class PrivateComponent {
  // Title
  title = 'Default - Angular 18 Material Design Admin Dashboard Template';

  // isSidebarToggled
  isSidebarToggled = false;

  // isToggled
  isToggled = false;

  constructor(
      public router: Router,
      private SidebarService: SidebarService,
      private viewportScroller: ViewportScroller,
      public themeService: CustomizerSettingsService,
  ) {
    this.router.events.subscribe((event: Event) => {
      if (event instanceof NavigationEnd) {
        // Scroll to the top after each navigation end
        this.viewportScroller.scrollToPosition([0, 0]);
      }
    });
    this.SidebarService.isSidebarToggled$.subscribe(isSidebarToggled => {
      this.isSidebarToggled = isSidebarToggled;
    });
    this.themeService.isToggled$.subscribe(isToggled => {
      this.isToggled = isToggled;
    });
  }
}
