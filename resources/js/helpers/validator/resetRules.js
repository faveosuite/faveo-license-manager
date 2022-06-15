import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateResetSettings(data){

    const { email, password, confirm } = data;

    let validatingData = {

        email: [email, 'isRequired'],

        password: [password, 'isRequired', 'max(50)', 'min(2)'],

        confirm: [confirm, 'isRequired', 'max(50)', 'min(2)']

    };
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return {errors, isValid};
};
