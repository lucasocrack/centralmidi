import { Routes } from '@angular/router';
import { HomeComponent } from './site/home/home.component';
import { AboutComponent } from './site/about/about.component';
import { ContactComponent } from './site/contact/contact.component';
import {LoginComponent} from "./site/login/login.component";

export const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  { path: 'home', component: HomeComponent},
  { path: 'about', component: AboutComponent},
  { path: 'contact', component: ContactComponent},
  { path: 'login', component: LoginComponent},
  { path: '**', redirectTo: 'not-found' }
];
