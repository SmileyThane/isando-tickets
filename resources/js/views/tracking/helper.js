export const storeKey = (key, value, moduleName = 'tracking') => {
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
