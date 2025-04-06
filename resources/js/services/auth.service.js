const authorizedFunctions = JSON.parse(document.querySelector('#authorized-functions')?.value ?? "[]");

const getPermission = (code) => {
  let permissionArr = authorizedFunctions.filter(item => item.code.includes(code+'.'))
  return permissionArr;
}

const hasPermission = (code, permission) => {
  return authorizedFunctions.some(item => item.code === `${code+'.'+permission}`)
}
const can = (code) => {
  return authorizedFunctions.some(item => item.code === code);
}
export {
  authorizedFunctions,
  getPermission,
  hasPermission,
  can
}