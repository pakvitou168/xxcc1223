const authorizedFunctionsElement = document.querySelector('#authorized-functions')
const authorizedFunctions = authorizedFunctionsElement ? JSON.parse(authorizedFunctionsElement.value) : []

const getFunctions = functionCode => authorizedFunctions.filter(item => item.code === functionCode)
const hasCode = functionCode => getFunctions(functionCode).length > 0

const hasPermission = (functionCode, permission) => {
    // return hasCode(functionCode) ? getFunctions(functionCode)[0].permission.includes(permission) : false
    return functionCode && permission ? authorizedFunctions.some(item => item.code === `${functionCode+'.'+permission}`) : true
}

export { hasPermission }