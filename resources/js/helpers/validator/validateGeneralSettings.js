import store from '../../store'

import { Validator } from '../easy-validator';

import { lang } from '../extraLogics';

export function validateGeneralSettings(data) {

    const { google_site_key, google_secret_key, agora_invoicing_url, timezone, date_format, time_format } = data

    let validatingData = {

        google_site_key: [google_site_key,'isRequired'],

        google_secret_key: [google_secret_key, 'isRequired'],

        agora_invoicing_url: [agora_invoicing_url,'isRequired'],

        timezone: [timezone, 'isRequired'],

        date_format: [date_format, 'isRequired' ],

        time_format: [time_format,'isRequired'],

    };

    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
