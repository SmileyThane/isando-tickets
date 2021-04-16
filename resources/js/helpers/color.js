import * as Numbers from './numbers';

/**
 * Invert color to opposite
 * @param: hex: string - e.g. #FFFFFF or #000
 * @param: bw: boolean
 * @returns {string}
 */
export const invertColor = (hex, bw = true) => {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    hex = hex.substr(0, 6);
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length < 6) {
        hex = Numbers.addZeroBefore(hex, 6);
    }
    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.');
    }
    var r = parseInt(hex.slice(0, 2), 16),
        g = parseInt(hex.slice(2, 4), 16),
        b = parseInt(hex.slice(4, 6), 16);
    if (bw) {
        // http://stackoverflow.com/a/3943023/112731
        return (r * 0.299 + g * 0.587 + b * 0.114) > 186
            ? '#000000'
            : '#FFFFFF';
    }
    // invert color components
    r = (255 - r).toString(16);
    g = (255 - g).toString(16);
    b = (255 - b).toString(16);
    // pad each with zeros and return
    return "#" + Numbers.addZeroBefore(r) + Numbers.addZeroBefore(g) + Numbers.addZeroBefore(b);
}

/**
 * Get random color
 * @returns {string} - #FFF000
 */
export const genRandomColor = () => {
    return '#' + Math.floor(Math.random()*16777215).toString(16).substr(0, 6);
}
