let timer = {}
const debounce = (func, timeout = 500) => {
  clearTimeout(timer)

  timer = setTimeout(() => {
    func()
  }, timeout)
}
const formatCurrency = (number) => {
  if (!number) return ''

  if (typeof number === 'string' && !number.includes(",")) {
    number = parseFloat(number);
  }
  return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
export {
  debounce,
  formatCurrency
}