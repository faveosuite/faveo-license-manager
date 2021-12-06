import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateProductSettings(data) {

  const { product_title, product_sku,product_description } = data

  var validatingData = {

    product_title: [product_title, 'isRequired'],

    product_sku: [product_sku, 'isRequired'],

    product_description: [product_description, { 'max(250)' : 'The description should be less than 250 characters.'} ]


  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
