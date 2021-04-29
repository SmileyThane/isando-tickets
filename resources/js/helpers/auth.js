import store from './../store';

/**
 * Checks user has one of provided permissions
 * @param ids {Array} - permissions ids for check
 * @returns {boolean} - user has the permission(s)
 */
export const checkPermissionByIds = (ids) => {
    let permissionExists = false;
    let permissions = store.getters['getPermissions'];
    ids.forEach(id => {
        if (permissionExists === false) {
            permissionExists = Array.isArray(permissions) && permissions.includes(id)
        }
    });
    return permissionExists;
}
