import { Component } from '@angular/core';
import { NgScrollbarModule } from 'ngx-scrollbar';
import { MatExpansionModule } from '@angular/material/expansion';
import { RouterLink, RouterLinkActive, RouterModule } from '@angular/router';
import { SidebarService } from './sidebar.service';
import { NgClass } from '@angular/common';
import { CustomizerSettingsService } from '../../../shared/components/customizer-settings/customizer-settings.service';
import { SidebarItems } from '../../../shared/types/commom.types';
@Component({
    selector: 'app-sidebar',
    standalone: true,
    imports: [NgScrollbarModule, MatExpansionModule, RouterLinkActive, RouterModule, RouterLink, NgClass],
    templateUrl: './sidebar.component.html',
    styleUrl: './sidebar.component.scss'
})
export class SidebarComponent {
  // isSidebarToggled
  isSidebarToggled = false;

  // isToggled
  isToggled = false;

  // Mat Expansion
  panelOpenState = false;

  menuItems: SidebarItems[] = [
    {
      title: 'PAGES',
      children: [
        { title: 'Dashboard', icon: 'dashboard', route: '/panel' },
        { title: 'Forms', icon: 'forum', route: '/panel/forms' }
      ]
    },
  ];

  constructor(
    private SidebarService: SidebarService,
    public themeService: CustomizerSettingsService
  ) {
    this.SidebarService.isSidebarToggled$.subscribe(isSidebarToggled => {
      this.isSidebarToggled = isSidebarToggled;
    });
    this.themeService.isToggled$.subscribe(isToggled => {
      this.isToggled = isToggled;
    });
  }

  // Burger Menu Toggle
  toggle() {
    this.SidebarService.toggle();
  }
}
