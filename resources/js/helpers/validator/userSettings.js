import store from '../../store'

import { Validator } from '../easy-validator';

import { lang } from '../../helpers/extraLogics';


export function validateUserSettings(data) {

    const { user_fname, user_lname, user_email } = data

    var validatingData = {

        user_fname: [user_fname, 'isRequired'],

        user_lname: [user_lname, 'isRequired'],

        user_email: [user_email, 'isRequired', 'isEmail']

    };

    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
