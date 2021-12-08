import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateLicenseSettings(data) {

  const { product_id, license_code, client_id } = data

  var validatingData = {

    product_id: [product_id, 'isRequired']

  };

  if(!data.client_id){
       
    validatingData['license_code'] = [data.license_code,'isRequired'];
       
  }

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
