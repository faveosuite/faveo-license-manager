import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateEmailSettings(data) {

    const { EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS,EMAIL_CC_SENDER,EMAIL_EXPIRING_LICENSE_DAYS, EMAIL_EXPIRING_UPDATES_DAYS, EMAIL_EXPIRING_SUPPORT_DAYS} = data

    var validatingData = {

        EMAIL_FROM_NAME: [EMAIL_FROM_NAME,'isRequired'],

        EMAIL_FROM_ADDRESS: [EMAIL_FROM_ADDRESS,'isRequired'],

        EMAIL_CC_SENDER: [EMAIL_CC_SENDER, 'isRequired'],

        EMAIL_EXPIRING_LICENSE_DAYS: [EMAIL_EXPIRING_LICENSE_DAYS,'isRequired'],

        EMAIL_EXPIRING_UPDATES_DAYS: [EMAIL_EXPIRING_UPDATES_DAYS, 'isRequired'],

        EMAIL_EXPIRING_SUPPORT_DAYS: [EMAIL_EXPIRING_SUPPORT_DAYS, 'isRequired' ],

    };

    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
