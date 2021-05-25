import store from './../store';


/**
 * Get current locale
 * @returns {string} - en
 */
export const getCurrentLocale = () => {
    return store.getters['getLang'];
}

/**
 * Returns localized item value
 * @param item {Object} | {Array} - item to localize
 * @param field {string} - item field fo localize (by default - name)
 * @returns {string} - localized field, eq. value of "name" for English language, value of "name_de" for German
 */
export const localized = (item, field = 'name') => {
    let locale = getCurrentLocale();
    return item[field + '_' + locale] ? item[field + '_' + locale] : item[field] ? item[field] : field;
}

/**
 * Returns substitute params into message
 * @param msg {string} - message
 * @param params {Object} - optional params to substitute
 * @returns {string} - message including corresponded params
 */
export const message = (msg, params = {}) => {
    if (typeof params !== 'object') {
        return msg;
    }
    for (const [key, value] of Object.entries(params)) {
        msg = msg.replaceAll('%{'+key+'}', value);
    }
    return msg;
}

/**
 * Returns 'field is required' message
 * @param field {string} - field name
 * @returns {string} - message including corresponded field name
 */
export const required = (field) => {
    return message(getCurrentLocale().lang_map.exceptions.field_is_required, {field: field});
}
