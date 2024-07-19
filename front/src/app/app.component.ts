import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import {HeaderComponent} from "./site/components/header/header.component";
import {FooterComponent} from "./site/components/footer/footer.component";
import {CopywriterComponent} from "./site/components/copywriter/copywriter.component";

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, HeaderComponent, FooterComponent, CopywriterComponent],
  template: `
    <app-header />
    <router-outlet />
    <app-copywriter />
  `,
})
export class AppComponent {
  title = 'cds-gestor';
}
