export const storeKey = (key, value = null, moduleName = 'tracking') => {
    let data = loadStore(moduleName);
    data[key] = value;
    localStorage.setItem(moduleName, JSON.stringify(data));
}

export const getKey = (key, moduleName = 'tracking') => {
    const data = loadStore(moduleName);
    return data[key] ?? null;
}

const loadStore = (moduleName = 'tracking') => {
    const data = localStorage.getItem(moduleName);
    return data ? JSON.parse(data) : {};
}

export const invertColor = (hex, bw = true) => {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
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
    return "#" + padZero(r) + padZero(g) + padZero(b);
}

const padZero = (str, len) => {
    len = len || 2;
    let zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}

export const genRandomColor = () => {
    return '#' + Math.floor(Math.random() * 16777215).toString(16).substr(0, 6);
}
