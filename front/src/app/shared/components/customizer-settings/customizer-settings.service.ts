import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class CustomizerSettingsService {

    constructor() {
        // Dark Mode
        this.isDarkTheme = JSON.parse(localStorage.getItem('isDarkTheme')!) || false;
        this.updateDarkBodyClass();

        // Sidebar Dark Mode
        this.isSidebarDarkTheme = JSON.parse(localStorage.getItem('isSidebarDarkTheme')!);

        // Right Sidebar
        this.isRightSidebarTheme = JSON.parse(localStorage.getItem('isRightSidebarTheme')!);

        // Hide Sidebar
        this.isHideSidebarTheme = JSON.parse(localStorage.getItem('isHideSidebarTheme')!);

        // Header Dark
        this.isHeaderDarkTheme = JSON.parse(localStorage.getItem('isHeaderDarkTheme')!);

        // Card Border
        this.isCardBorderTheme = JSON.parse(localStorage.getItem('isCardBorderTheme')!);

        // Card Border Radius
        this.isCardBorderRadiusTheme = JSON.parse(localStorage.getItem('isCardBorderRadiusTheme')!);

        // RTL Mode
        this.isRTLEnabledTheme = JSON.parse(localStorage.getItem('isRTLEnabledTheme')!) || false;
        this.updateRTLBodyClass();
    }

    // Dark Mode
    private isDarkTheme: boolean;
    toggleTheme() {
        this.isDarkTheme = !this.isDarkTheme;
        localStorage.setItem('isDarkTheme', JSON.stringify(this.isDarkTheme));
        this.updateDarkBodyClass();
    }
    isDark() {
        return this.isDarkTheme;
    }
    private updateDarkBodyClass() {
        if (this.isDarkTheme) {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
    }

    // Sidebar Dark
    private isSidebarDarkTheme: boolean;
    toggleSidebarTheme() {
        this.isSidebarDarkTheme = !this.isSidebarDarkTheme;
        localStorage.setItem('isSidebarDarkTheme', JSON.stringify(this.isSidebarDarkTheme));
    }
    isSidebarDark() {
        return this.isSidebarDarkTheme;
    }

    // Right Sidebar
    private isRightSidebarTheme: boolean;
    toggleRightSidebarTheme() {
        this.isRightSidebarTheme = !this.isRightSidebarTheme;
        localStorage.setItem('isRightSidebarTheme', JSON.stringify(this.isRightSidebarTheme));
    }
    isRightSidebar() {
        return this.isRightSidebarTheme;
    }

    // Hide Sidebar
    private isHideSidebarTheme: boolean;
    toggleHideSidebarTheme() {
        this.isHideSidebarTheme = !this.isHideSidebarTheme;
        localStorage.setItem('isHideSidebarTheme', JSON.stringify(this.isHideSidebarTheme));
    }
    isHideSidebar() {
        return this.isHideSidebarTheme;
    }

    // Header Dark Mode
    private isHeaderDarkTheme: boolean;
    toggleHeaderTheme() {
        this.isHeaderDarkTheme = !this.isHeaderDarkTheme;
        localStorage.setItem('isHeaderDarkTheme', JSON.stringify(this.isHeaderDarkTheme));
    }
    isHeaderDark() {
        return this.isHeaderDarkTheme;
    }

    // Card Border
    private isCardBorderTheme: boolean;
    toggleCardBorderTheme() {
        this.isCardBorderTheme = !this.isCardBorderTheme;
        localStorage.setItem('isCardBorderTheme', JSON.stringify(this.isCardBorderTheme));
    }
    isCardBorder() {
        return this.isCardBorderTheme;
    }

    // Card Border Radius
    private isCardBorderRadiusTheme: boolean;
    toggleCardBorderRadiusTheme() {
        this.isCardBorderRadiusTheme = !this.isCardBorderRadiusTheme;
        localStorage.setItem('isCardBorderRadiusTheme', JSON.stringify(this.isCardBorderRadiusTheme));
    }
    isCardBorderRadius() {
        return this.isCardBorderRadiusTheme;
    }

    // RTL Mode
    private isRTLEnabledTheme: boolean;
    toggleRTLEnabledTheme() {
        this.isRTLEnabledTheme = !this.isRTLEnabledTheme;
        localStorage.setItem('isRTLEnabledTheme', JSON.stringify(this.isRTLEnabledTheme));
        this.updateRTLBodyClass();
    }
    isRTLEnabled() {
        return this.isRTLEnabledTheme;
    }
    private updateRTLBodyClass() {
        if (this.isRTLEnabledTheme) {
            document.body.classList.add('rtl-enabled');
        } else {
            document.body.classList.remove('rtl-enabled');
        }
    }

    // isToggled
    private isToggled = new BehaviorSubject<boolean>(false);
    get isToggled$() {
        return this.isToggled.asObservable();
    }
    toggle() {
        this.isToggled.next(!this.isToggled.value);
    }
    
}