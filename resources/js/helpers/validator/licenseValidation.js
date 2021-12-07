import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateLicenseSettings(data) {

  const { product_id } = data

  var validatingData = {

    product_id: [product_id, 'isRequired']

  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
