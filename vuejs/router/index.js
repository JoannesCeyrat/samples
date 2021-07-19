import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
  /** home */
  {
    path: '/',
    name: 'home',
    component: Home
  },
  /** mandats */
  {
    path: '/mandats',
    name: 'mandats',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/mandats/Mandats.vue')
  },
  {
    path: '/mandats/nouveau-mandat',
    name: 'MandatAjout',
    component: () => import(/* webpackChunkName: "about" */ '../views/mandats/MandatAjout.vue')
  },
  {
    path: '/mandats/record-mandat',
    name: 'MandatRecord',
    component: () => import(/* webpackChunkName: "about" */ '../views/mandats/MandatRecord.vue')
  },
  /** Demandes Résa */
  {
    path: '/demandes-resas/',
    name: 'DemandesResa',
    component: () => import(/* webpackChunkName: "about" */ '../views/demandes/DemandesResa.vue')
  },
  /** Baux */
  {
    path: '/baux/',
    name: 'Baux',
    component: () => import(/* webpackChunkName: "about" */ '../views/baux/Baux.vue')
  },
  {
    path: '/baux/traiter',
    name: 'Traiter',
    component: () => import(/* webpackChunkName: "about" */ '../views/baux/Traiter.vue')
  },

  /** Users */
  {
    path: '/users/',
    name: 'Users',
    component: () => import(/* webpackChunkName: "about" */ '../views/users/Users.vue')
  },
  /** Biens */
  {
    path: '/biens/',
    name: 'Biens',
    component: () => import(/* webpackChunkName: "about" */ '../views/biens/Biens.vue')
  },
  /** Actualiser */
  {
    path: '/actualiser/',
    name: 'Actualiser',
    component: () => import(/* webpackChunkName: "about" */ '../views/Actualiser.vue')
  },
  /** Devis */
  {
    path: '/devis/',
    name: 'Devis',
    component: () => import(/* webpackChunkName: "about" */ '../views/devis/Devis.vue')
  },
  /** Remboursements */
  {
    path: '/remboursements/',
    name: 'Remboursement',
    component: () => import(/* webpackChunkName: "about" */ '../views/remboursements/Remboursements.vue')
  },

]

const router = new VueRouter({
  base: '/moncompte/',
  routes
})

export default router
