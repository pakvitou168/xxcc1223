// import Vue from 'vue'

// const eventBus = new Vue()

// export default eventBus

// const eventBus = Vue.createApp({})
// export default eventBus
import emitter from 'tiny-emitter/instance'

export default {
  $on: (...args) => emitter.on(...args),
  $once: (...args) => emitter.once(...args),
  $off: (...args) => emitter.off(...args),
  $emit: (...args) => emitter.emit(...args)
}