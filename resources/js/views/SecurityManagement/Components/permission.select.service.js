const listTreePermissionOptions = permissions => {
  return permissions.map((app, appIndex) => {
    let functionNodes = app.functions.map((fnc, fncIndex) => {

      let permissionNodes = fnc.permissions.map((permission, permissionIndex) => {
        const metaData = {
          appId: app?.id,
          appCode: app?.code,
          appName: app?.name,
        };
        
        return getNode(`${appIndex}-${fncIndex}-${permissionIndex}`, permission?.name, permission?.id, [], metaData)
      })

      return getNode(`${appIndex}-${fncIndex}`, fnc?.name, fnc?.id, permissionNodes)
    })

    return getNode(appIndex, app?.name, app?.id, functionNodes)
  }) 
}

const getNode = (key, label, data, children = [], meta = {}) => {
  return {
    key: key,
    label: label,
    data: data,
    children: children,
    meta: meta,
  }
}

const getSelectedPermissions = (permissionOptions, selectedPermissions) => {
  let flatPermissionOptions = listFlatPermissionOptions(permissionOptions)
  
  let selectedOptionKeys = Object.keys(selectedPermissions).filter(item => item.split('-').length === 3)
  return selectedOptionKeys.map(item => {
    let selectedOption = flatPermissionOptions.find(i => i?.key === item)
    
    return {
      id: selectedOption.data,
      name: selectedOption.label,
      app_id: selectedOption.meta?.appId
    }
  })
}

const listFlatPermissionOptions = permissionOptions => {
  let flatPermissionOptions = [];

  Object.keys(permissionOptions).forEach(appId => {
    let app = permissionOptions[appId]
    app?.children.forEach(fnc => {
      fnc?.children.forEach(permission => {
        flatPermissionOptions.push(permission)
      })
    })
  })
  return flatPermissionOptions
}

const listTreeSelectedPermissions = ((permissions, permissionOptions) => {
  let selectedPermissions = {}

  let permissionIds = permissions.map(item => item.id)

  Object.keys(permissionOptions).forEach(appId => {
    let app = permissionOptions[appId]

    app?.children.forEach(fnc => {

      fnc?.children.forEach(permission => {
        if (permissionIds.includes(permission?.data)) {
          selectedPermissions[permission?.key] = renderTreeCheckedState(true)
        }
      })

      let fncPermissionIds = fnc.children.map(item => item.data)

      // If yet to assign function in selectedPermissions, and at least one of function's permissions exists in selectedPermissions
      if (selectedPermissions[fnc?.key] === undefined && fncPermissionIds.some(item => permissionIds.includes(item))) {
        if (fncPermissionIds.every(item => permissionIds.includes(item))) {
          selectedPermissions[fnc?.key] = renderTreeCheckedState(true)
        } else {
          selectedPermissions[fnc?.key] = renderTreeCheckedState(false)
        }
      }
    })
    
    let appFunctionKeys = app.children.map(item => item.key)
    
    // If yet to assign app in selectedPermissions, and at least one of app's functions exists in selectedPermissions
    if (selectedPermissions[app?.key] === undefined && appFunctionKeys.some(item => Object.keys(selectedPermissions).includes(item))) {
      // If every functions in app is checked
      if (appFunctionKeys.every(item => Object.keys(selectedPermissions).includes(item) && selectedPermissions[item]?.checked === true)) {
        selectedPermissions[app?.key] = renderTreeCheckedState(true)
      } else {
        selectedPermissions[app?.key] = renderTreeCheckedState(false)
      }
    }
  })
  return selectedPermissions
})

const getExpandedKeys = (selectedPermissions) => {
  let expandedKeys = {}

  Object.keys(selectedPermissions).filter(item => item.split('-').length === 1).forEach(item => {
    expandedKeys[item] = true
  })

  return expandedKeys
}

const renderTreeCheckedState = isSelected => {
  return {
    checked: isSelected,
    partialChecked: !isSelected
  }
}

export {
  listTreePermissionOptions,
  listTreeSelectedPermissions,
  getSelectedPermissions,
  getExpandedKeys,
}