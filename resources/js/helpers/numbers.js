/**
 * Adds zeros in front of the number
 * @param str: string | number
 * @param len: number - count of zeros
 * @returns {string}
 */
export const addZeroBefore = (str, len = 2) => {
    if (str.toString().length >= len) return str;
    let zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}

/**
 * Format a number with grouped thousands
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 * @returns {string}
 */
export const numberFormat = (number, decimals = 2, dec_point = ".", thousands_sep = ",") => {
    let i, j, kw, kd, km;

    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ".";
    }
    if (thousands_sep == undefined) {
        thousands_sep = ",";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

    return km + kw + kd;
}
