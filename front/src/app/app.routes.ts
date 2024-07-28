import { Routes } from '@angular/router';
import { PublicComponent } from './public/public.component';
import { publicRoutes } from './public/public.routes';
import { PrivateComponent } from './private/private.component';
import { privateRoutes } from './private/private.routes';
import { AuthenticationComponent } from './core/pages/authentication/authentication.component';
import { authenticationRoutes } from './core/pages/authentication/authentication.routes';

export const routes: Routes = [
  {
    path: 'public',
    component: PublicComponent,
    children: publicRoutes,
  },
  {
    path: '',
    component: PrivateComponent,
    children: privateRoutes,
  },
  {
    path: 'authentication',
    component: AuthenticationComponent,
    children: authenticationRoutes
  },
  { path: '**', redirectTo: '' },
];