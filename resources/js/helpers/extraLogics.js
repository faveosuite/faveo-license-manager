
import moment from "moment";
import 'moment-timezone'

/**
 * gets the last integer from a given url(string)
 * @param  {string} url  url with/without Id
 * @return {integer}     id
 */
export const getIdFromUrl = (url) => {

    let urlArray = url.split("/");

    let idArray = urlArray.filter(function (item) {
        return (parseInt(item) == item);
    });

    return idArray[idArray.length - 1];
};

/**
 * converts english string into language string
 * NOTE: global lang() method is only available in vue components, so it is required to declare again
 *
 * @param {string}  string      english string
 * @return {string}             language string
 */
export const lang = (string) => {
    if (typeof translator !== 'undefined') {
        return (translator.lang[string] ? translator.lang[string] : string);
    }
    return string;
};

/**
 * Converts given string into boolean based on php rules
 * for eg. `0` means false, '1' means true, null means false
 * @return {any}
 */
export const boolean = (value) => {

    //for checking if variable is an empty array
    if (Array.isArray(value) && value.length === 0) {
        return false;
    }

    switch (value) {
        case 0:
            return false;

        case '0':
            return false;

        case null:
            return false;

        case "":
            return false;

        case undefined:
            return false;

        case false:
            return false;

        default:
            return true;
    }
};

/**
 * gets the substring value of a given string
 * @param  {string} name
 * @param  {count} number of letters
 * @return {string}     string
 */
export const getSubStringValue = (name, count) => {
    if (name) {
        if (name.length > count) {
            return name.substring(0, count) + '...';
        } else {
            return name;
        }
    }
};

/**
 * gets the substring value of a given string
 * @param  {number} length
 * @return {string}     string
 */
export const generateRandomString = (length = 16) => {
    var a = ''

    var n = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    for (var e = 1; e <= length; e++) {

        a += n.charAt(Math.floor(Math.random() * n.length));

        if (e % 4 == 0 && e != length) {

            a += ''
        }
    }

    return a;
};

/**
 * Checks if a given date-time string is in ISO 8601 format.
 * @param {string} dateTime - The date-time string to check.
 * @returns {boolean} True if the string contains 'T' and 'Z', false otherwise.
 */
const isIsoFormat = (dateTime) => dateTime.includes('T') && dateTime.includes('Z');

/**
 * Checks if a given date-time string contains a time component.
 * @param {string} dateTime - The date-time string to check.
 * @returns {boolean} True if the string has a time component, false otherwise.
 */
const hasTimeComponent = (dateTime) => dateTime.split(' ').length === 2;

const hasTimeShift = (timezone) => {

    switch (timezone) {
        case 'Asia/Magadan':
            return 1;

        case 'Asia/Krasnoyarsk' :
            return 1;

        case 'Europe/Volgograd' :
            return 1;

        case 'Europe/Minsk' :
            return -1;

        case 'Europe/Moscow' :
            return 1;

        default:
            return 0;
    }
}

/**
 * Formats a date-time string for a given timezone, adjusting for DST if necessary.
 * @param {moment.Moment} utcDate - The parsed UTC date.
 * @param {string} timezone - The target timezone.
 * @param {string} dateFormat - The desired date format.
 * @param {string} timeFormat - The desired time format.
 * @returns {string} The formatted date-time string.
 */
const formatDateTimeWithTimezone = (utcDate, timezone, dateFormat, timeFormat) => {
    const localizedDate = moment(utcDate.format()).tz(timezone);

    if (localizedDate.isDST()) {
        // Adjust for Daylight Saving Time
        return localizedDate.subtract(1, 'hour').format(`${dateFormat} ${timeFormat}`);

    } else if(hasTimeShift(timezone)) {

        const value = hasTimeShift(timezone)
        return localizedDate.add(value, 'hour').format(`${dateFormat} ${timeFormat}`);
    }

    else {
        return localizedDate.format(`${dateFormat} ${timeFormat}`);
    }
};

/**
 * Formats a date-time string based on the given parameters.
 * @param {string} dateTime - The date-time string to format.
 * @param {string} timezone - The target timezone.
 * @param {string} dateFormat - The desired date format.
 * @param {string} timeFormat - The desired time format.
 * @param {string} [format='YYYY-MM-DD HH:mm:ss'] - The expected input date-time format.
 * @returns {string} The formatted date-time string.
 */
export const formatDateTime = (dateTime, timezone, dateFormat, timeFormat, format = 'YYYY-MM-DD HH:mm:ss') => {
    if (!dateTime) return '----';

    const isIso = isIsoFormat(dateTime);
    const hasTime = hasTimeComponent(dateTime);

    if (hasTime || isIso) {
        const utcDate = moment.utc(dateTime, format);
        return formatDateTimeWithTimezone(utcDate, timezone, dateFormat, timeFormat);
    }
    return moment(dateTime).format(dateFormat);
};


