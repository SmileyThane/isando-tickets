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
