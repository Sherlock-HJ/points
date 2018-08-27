import spinner from './spinner'
import methods from './methods'
import nettool from './nettool'
import cookie from 'js-cookie'
import bus from 'vue-bus'


export default {
  install: function (Vue, options) {
    Vue.prototype.$spinner = spinner
    Vue.prototype.$net = nettool
    Vue.prototype.$m = methods
    Vue.prototype.$cookie = cookie
    Vue.prototype.$bus = bus

  }
}