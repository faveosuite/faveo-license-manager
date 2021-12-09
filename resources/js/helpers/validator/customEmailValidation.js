import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateCustomEmailSettings(data) {

  const { 
    email_expiring_license_subject,
    email_expiring_license_text,
    email_expiring_updates_subject,
    email_expiring_updates_text,
    email_expiring_support_subject,
    email_expiring_support_text,
  } = data

  var validatingData = {

    email_expiring_license_subject: [email_expiring_license_subject, 'isRequired'],
    email_expiring_license_text: [email_expiring_license_text, 'isRequired'],
    email_expiring_updates_subject: [email_expiring_updates_subject, 'isRequired'],
    email_expiring_updates_text: [email_expiring_updates_text, 'isRequired'],
    email_expiring_support_subject: [email_expiring_support_subject, 'isRequired'],
    email_expiring_support_text: [email_expiring_support_text, 'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
