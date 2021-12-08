import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateCustomNoteSettings(data) {

  const { 
    notification_product_not_found,
    notification_product_inactive,
    notification_license_ok,
    notification_license_not_found,
    notification_invalid_ip,
    notification_invalid_domain,
    notification_domain_required,
    notification_domain_in_use,
    notification_license_suspended,
    notification_license_expired,
    notification_updates_expired,
    notification_support_expired,
    notification_license_cancelled,
    notification_license_limit,
    notification_installation_not_found,
    notification_invalid_signature,
    notification_host_banned,
    notification_unknown_error
  } = data

  var validatingData = {

    notification_product_not_found: [notification_product_not_found, 'isRequired'],
    notification_product_inactive: [notification_product_inactive, 'isRequired'],
    notification_license_ok: [notification_license_ok, 'isRequired'],
    notification_license_not_found: [notification_license_not_found, 'isRequired'],
    notification_invalid_ip: [notification_invalid_ip, 'isRequired'],
    notification_invalid_domain: [notification_invalid_domain, 'isRequired'],
    notification_domain_required: [notification_domain_required, 'isRequired'],
    notification_domain_in_use: [notification_domain_in_use, 'isRequired'],
    notification_license_suspended: [notification_license_suspended, 'isRequired'],
    notification_license_expired: [notification_license_expired, 'isRequired'],
    notification_updates_expired: [notification_updates_expired, 'isRequired'],
    notification_support_expired: [notification_support_expired, 'isRequired'],
    notification_license_cancelled: [notification_license_cancelled, 'isRequired'],
    notification_license_limit: [notification_license_limit, 'isRequired'],
    notification_installation_not_found: [notification_installation_not_found, 'isRequired'],
    notification_invalid_signature: [notification_invalid_signature, 'isRequired'],
    notification_host_banned: [notification_host_banned, 'isRequired'],
    notification_unknown_error: [notification_unknown_error, 'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
