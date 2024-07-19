import { Component } from '@angular/core';
import {CarouselHomeComponent} from "../components/carousel-home/carousel-home.component";
import {FooterComponent} from "../components/footer/footer.component";

@Component({
  selector: 'app-index',
  standalone: true,
  imports: [
    CarouselHomeComponent,
    FooterComponent
  ],
  templateUrl: './home.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {

}
