import { TestBed } from '@angular/core/testing';

import { CustomizerSettingsService } from './customizer-settings.service';

describe('CustomizerSettingsService', () => {
    let service: CustomizerSettingsService;

    beforeEach(() => {
        TestBed.configureTestingModule({});
        service = TestBed.inject(CustomizerSettingsService);
    });

    it('should be created', () => {
        expect(service).toBeTruthy();
    });
});