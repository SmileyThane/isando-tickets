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

/**
 * Check user has one of provided permissions to manage knowledge base
 * @param kbType {string} - type of knowledge base
 * @param permissionType {string} - type of permission
 * @returns {boolean} - user has the permission(s)
 */
export const checkKbPermissionsByType = (kbType, permissionType) => {
    let permissionExists = false;
    let permissions = store.getters['getPermissions'];
    let kbTypes = store.getters['getKnowledgeBaseTypes'];

    if (Array.isArray(permissions) && Array.isArray(kbTypes)) {
        if (permissionExists === false) {
            let id = kbTypes
                .find(item => item.alias === kbType)
                ?.permissions
                .find(item => item.type === permissionType)
                ?.value;
            permissionExists = permissions.includes(id);
        }
    }

    return permissionExists;
}
