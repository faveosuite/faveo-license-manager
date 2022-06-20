import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateLicenseSettings(data) {

  const { license_comments } = data

  var validatingData = {

    license_comments:[license_comments, { 'max(250)' : 'The comments limit should be less than 250 characters.'}]
   
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
