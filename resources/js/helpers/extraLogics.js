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
}