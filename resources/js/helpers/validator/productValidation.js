import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateProductSettings(data) {

  const { product_title, product_sku } = data

  var validatingData = {

    product_title: [product_title, 'isRequired'],

    product_sku: [product_sku, 'isRequired']

  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
