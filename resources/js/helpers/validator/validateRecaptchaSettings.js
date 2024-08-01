import store from '../../store'

import { Validator } from '../easy-validator';

import { lang } from '../extraLogics';

export function validateRecaptchaSettings(data) {

    const { google_site_key, google_secret_key } = data

    let validatingData = {

        google_site_key: [google_site_key,'isRequired'],

        google_secret_key: [google_secret_key, 'isRequired'],

    };

    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
