/**
 * Get shorten text
 * @returns {string}
 */
export const shortenText = (text, length = 100) => {
    return text.length > length ? text.slice(0, length) + '...' : text;
}
