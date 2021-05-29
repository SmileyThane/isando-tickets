/**
 * Save value to localStorage
 * @param key: string
 * @param value: any
 * @param moduleName - name of module which using localStorage
 */
export const storeKey = (key, value = null, moduleName = 'global') => {
    let data = loadStore(moduleName);
    data[key] = value;
    localStorage.setItem(moduleName, JSON.stringify(data));
}

/**
 * Get value from localStorage by key
 * @param key
 * @param moduleName
 * @returns {*|null}
 */
export const getKey = (key, moduleName = 'global') => {
    const data = loadStore(moduleName);
    return data[key] ?? null;
}

/**
 * Get data from localStorage. Private method
 * @param moduleName
 * @returns {any}
 */
const loadStore = (moduleName = 'global') => {
    const data = localStorage.getItem(moduleName);
    return data ? JSON.parse(data) : {};
}
