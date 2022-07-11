import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateGeneralSettings(data) {

    const { TIMEZONE, RECORDE_ON_ADMIN_PAGE,RECORDE_ON_INDEX_PAGE,RECORDE_ON_SEARCH_PAGE, SMART_REPORTS, SMART_TABLES, RECORDE_ARCHIVE_DAYS } = data

    var validatingData = {

        SMART_REPORTS: [SMART_REPORTS,'isRequired'],

        SMART_TABLES: [SMART_TABLES,'isRequired'],

        TIMEZONE: [TIMEZONE, 'isRequired'],

        RECORDE_ARCHIVE_DAYS: [RECORDE_ARCHIVE_DAYS,'isRequired'],

        RECORDE_ON_ADMIN_PAGE: [RECORDE_ON_ADMIN_PAGE, 'isRequired'],

        RECORDE_ON_INDEX_PAGE: [RECORDE_ON_INDEX_PAGE, 'isRequired' ],

        RECORDE_ON_SEARCH_PAGE: [RECORDE_ON_SEARCH_PAGE,'isRequired'],

    };

    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
