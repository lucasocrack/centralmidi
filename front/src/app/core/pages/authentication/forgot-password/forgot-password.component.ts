import { Component } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { RouterLink } from '@angular/router';
import { ReactiveFormsModule } from '@angular/forms';
import { CustomizerSettingsService } from '../../../../shared/components/customizer-settings/customizer-settings.service';

@Component({
    selector: 'app-forgot-password',
    standalone: true,
    imports: [RouterLink, MatFormFieldModule, MatInputModule, MatButtonModule, MatCheckboxModule, ReactiveFormsModule],
    templateUrl: './forgot-password.component.html',
    styleUrl: './forgot-password.component.scss'
})
export class ForgotPasswordComponent {

    // isToggled
    isToggled = false;

    constructor(
        public themeService: CustomizerSettingsService
    ) {
        this.themeService.isToggled$.subscribe(isToggled => {
            this.isToggled = isToggled;
        });
    }

}