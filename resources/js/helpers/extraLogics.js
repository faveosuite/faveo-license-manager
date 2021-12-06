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
	if( typeof translator !== 'undefined'){
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
export const getSubStringValue = (name,count) => {
    if(name){
        if(name.length>count){
            return name.substring(0,count) + '...';
        } else {
            return name;
        }
    }
};