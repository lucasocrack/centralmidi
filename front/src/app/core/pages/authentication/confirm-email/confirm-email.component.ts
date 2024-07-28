import { Component } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { RouterLink } from '@angular/router';
import { CustomizerSettingsService } from '../../../../shared/components/customizer-settings/customizer-settings.service';

@Component({
    selector: 'app-confirm-email',
    standalone: true,
    imports: [RouterLink, MatButtonModule],
    templateUrl: './confirm-email.component.html',
    styleUrl: './confirm-email.component.scss'
})
export class ConfirmEmailComponent {

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