import store from '../../store'

import { Validator } from '../easy-validator';

import { lang } from '../extraLogics';

export function validateGeneralSettings(data) {

    const { google_secret_key, google_site_key, recaptcha_status, agora_invoicing_url, timezone, date_format, time_format, storage_disk, s3_bucket, s3_region, s3_access_key, s3_secret_key, s3_endpoint_url, archives_path, query_path, s3_path_style_endpoint } = data

    let validatingData = {

        agora_invoicing_url: [agora_invoicing_url,'isRequired'],

        timezone: [timezone, 'isRequired'],

        date_format: [date_format, 'isRequired' ],

        time_format: [time_format,'isRequired'],

        storage_disk: [storage_disk, 'isRequired']

    };

    if(recaptcha_status) {
        validatingData.google_site_key = [google_site_key, 'isRequired'];
        validatingData.google_secret_key = [google_secret_key, 'isRequired'];
    }

    if(storage_disk === 's3') {
        validatingData.s3_bucket = [s3_bucket, 'isRequired'];
        validatingData.s3_region = [s3_region, 'isRequired'];
        validatingData.s3_access_key = [s3_access_key, 'isRequired'];
        validatingData.s3_secret_key = [s3_secret_key, 'isRequired'];
        validatingData.s3_endpoint_url = [s3_endpoint_url, 'isRequired'];
        validatingData.s3_path_style_endpoint = [s3_path_style_endpoint, 'isRequired'];
    }

    if(storage_disk === 'system') {
        validatingData.archives_path = [archives_path, 'isRequired'];
        validatingData.query_path = [query_path, 'isRequired'];
    }

    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
