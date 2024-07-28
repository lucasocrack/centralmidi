import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class SidebarService {

    private isSidebarToggled = new BehaviorSubject<boolean>(false);
    get isSidebarToggled$() {
        return this.isSidebarToggled.asObservable();
    }
    toggle() {
        this.isSidebarToggled.next(!this.isSidebarToggled.value);
    }

}