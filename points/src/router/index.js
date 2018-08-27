import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'orglist',
      component: () => import('@/components/orglist')
    },
    {
      path: '/cionlist',
      name: 'cionlist',
      component: () => import('@/components/cionlist')
    }
  ]
})
