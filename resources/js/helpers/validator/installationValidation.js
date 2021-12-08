import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateInstallationSettings(data) {

  const { installation_ip } = data

  var validatingData = {

    installation_ip: [installation_ip, 'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
