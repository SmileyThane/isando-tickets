import store from './../store';

/**
 * Checks user has one of provided permissions
 * @param ids {Array} - permissions ids for check
 * @param check {string} - check method of permissions
 * @returns {boolean} - user has the permission(s)
 */
export const checkPermissionByIds = (ids, check = 'some') => {
  let permissionExists = false;
  let permissions = store.getters['getPermissions'];
  try {
    ids.forEach((id) => {
      if (check === 'some') {
        if (permissionExists === false) {
          permissionExists =
            Array.isArray(permissions) && permissions.includes(id);
        }
      }

      if (check === 'all') {
        permissionExists =
          Array.isArray(permissions) && permissions.includes(id);
      }
    });
  } catch (e) {
    console.log(e.message);
  }
  return permissionExists;
};

/**
 * Check user has one of provided permissions to manage knowledge base
 * @param kbType {string} - type of knowledge base
 * @param permissionType {string} - type of permission
 * @returns {boolean} - user has the permission(s)
 */
export const checkKbPermissionsByType = (kbType, permissionType) => {
  let permissionExists = false;
  let permissions = store.getters['getPermissions'];
  let kbTypes = store.getters['KbTypes/getKbTypes'];

  try {
    if (Array.isArray(permissions) && Array.isArray(kbTypes)) {
      if (permissionExists === false) {
        let id = kbTypes
          .find((item) => item.alias === kbType)
          ?.permissions?.find((item) => item.type === permissionType)?.value;
        permissionExists = permissions.includes(id);
      }
    }
  } catch (e) {
    console.log(e.message);
  }

  return permissionExists;
};
