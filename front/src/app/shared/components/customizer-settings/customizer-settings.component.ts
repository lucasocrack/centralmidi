import { Component } from '@angular/core';
import { CustomizerSettingsService } from './customizer-settings.service';
import { RouterLink } from '@angular/router';
import { NgClass } from '@angular/common';
import { MatDividerModule } from '@angular/material/divider';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { NgScrollbarModule } from 'ngx-scrollbar';

@Component({
    selector: 'app-customizer-settings',
    standalone: true,
    imports: [RouterLink, NgClass, MatDividerModule, MatIconModule, MatButtonModule, NgScrollbarModule],
    templateUrl: './customizer-settings.component.html',
    styleUrl: './customizer-settings.component.scss'
})
export class CustomizerSettingsComponent {

    // isToggled
    isToggled = false;
    
    constructor(
        public themeService: CustomizerSettingsService
    ) {
        this.themeService.isToggled$.subscribe(isToggled => {
            this.isToggled = isToggled;
        });
    }

    // Dark Mode
    toggleTheme() {
        this.themeService.toggleTheme();
    }

    // Sidebar Dark
    toggleSidebarTheme() {
        this.themeService.toggleSidebarTheme();
    }

    // Right Sidebar
    toggleRightSidebarTheme() {
        this.themeService.toggleRightSidebarTheme();
    }

    // Hide Sidebar
    toggleHideSidebarTheme() {
        this.themeService.toggleHideSidebarTheme();
    }

    // Header Dark Mode
    toggleHeaderTheme() {
        this.themeService.toggleHeaderTheme();
    }

    // Card Border
    toggleCardBorderTheme() {
        this.themeService.toggleCardBorderTheme();
    }

    // Card Border Radius
    toggleCardBorderRadiusTheme() {
        this.themeService.toggleCardBorderRadiusTheme();
    }

    // RTL Mode
    toggleRTLEnabledTheme() {
        this.themeService.toggleRTLEnabledTheme();
    }

    // Settings Button Toggle
    toggle() {
        this.themeService.toggle();
    }

}