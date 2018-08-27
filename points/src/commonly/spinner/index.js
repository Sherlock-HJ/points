import spinner from './spinner';
import Vue from 'vue'
const Instance = new Vue({
  render (h) {
    return h(spinner);
  }
});

const component = Instance.$mount();
document.body.appendChild(component.$el);
const spin = Instance.$children[0];

export default {
  show () {
    spin.isShow = true
  },
  hide () {
    spin.isShow =false

  }
};
