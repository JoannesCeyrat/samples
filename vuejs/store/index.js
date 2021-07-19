import Vue from 'vue'
import Vuex from 'vuex'

import { init } from './modules/init'
import { mandats } from './modules/mandats'
import { baux } from './modules/baux'
import { biens } from './modules/biens'
import { devis } from './modules/devis'
import { user } from './modules/user'
import { entreprise } from './modules/entreprise'
import { semaines } from './modules/semaines'
import { remboursements } from './modules/remboursements'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    init,
    mandats,
    baux,
    biens,
    devis,
    user,
    entreprise,
    semaines,
    remboursements
  },

})