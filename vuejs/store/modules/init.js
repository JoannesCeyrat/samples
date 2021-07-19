import { couleurs } from './couleurs'

export const init = {
	state: {
		environnement: "dev", // dev | prod
		baseUrl: {
			dev: 'https://dev.innov-home.fr',
			prod: 'https://www.innov-home.fr'
		},
		hauteur_iframe: "10px",
		banner_title: "Compte conseiller : ACCUEIL",
		main_dialog: {
			open: false,
			component: '',
			data: {}
		},
		main_loading: false,
		btn_traiter: {
			is_sending: false,
		},
		snackbar: {
			open: false,
			color: "#e2007a",
			text: '',
			icon: ''
		},
		list_villes: {
			bien: [],
			user: []
		},
		selected_ville_id: {
			bien: null,
			user: null
		},
		callbackSejour: undefined,
		containerSejour: undefined,
		panel_ec: undefined,
		mess_coll: '',
		search_dialog: {
			open: false,
			component: '',
			data: {}
		},
		data_loaded_once: false,
		main_confirm: {
			open: false,
			component: '', // stocke le component qui va ecouter la confirmation
			message: '', // voulez vous supprimer cette période ?
			action: '', // action qui sera appelé par les écouteurs d'events root
		},
		couleurs: couleurs,
		calendrierDroits: {
			cons: ["BlocageCons", "BlocageProp"],
			prop: ["BlocageProp"]
		},
		search: '',
		parametre_recherche: 'none',
		admins: [1, 2],
		taux_taxe_sejour: 0.03,
	},

	mutations: {
		set_hauteur_iframe(state, n) {
			state.hauteur_iframe= String(Math.round(n)) + "px"
		},
		set_search(state, s) {
			state.search = s
		},
		set_banner_title(state, s) {
			state.banner_title= s
		},
		set_main_dialog(state, obj) {
			state.main_dialog[obj.cle]= obj.value
		},
		set_main_loading(state, b) {
			state.main_loading= b
		},
		set_main_confirm(state, obj) {
			state.main_confirm= obj
		},
		set_btn_traiter(state, obj) {
			state.btn_traiter = obj
		},
		set_snackbar(state, obj) {
			state.snackbar = obj
		},
		/**
		 * @param {cible: 'bien|user', arr: []} 
		 */
		set_list_villes(state, obj) {
			state.list_villes[obj.cible] = obj.arr
		},
		/**
		 * @param {cible: 'bien|user', id: int}
		 */
		set_selected_ville_id(state, obj) {
			state.selected_ville_id[obj.cible] = obj.id
		},
		set_callbackSejour(state, s) {
			state.callbackSejour= s
		},
		set_containerSejour(state, s) {
			state.containerSejour= s
		},
		set_panel_ec(state, i) {
			state.panel_ec= i
		},
		set_mess_coll(state, s) {
			state.mess_coll= s
		},
		set_search_dialog(state, obj) {
			state.search_dialog[obj.cle] = obj.value
		},
		set_data_loaded_once(state, b) {
			state.data_loaded_once= b
		},
		set_parametre_recherche(state, s) {
			state.parametre_recherche = s
		},
		init_search(state) {
			state.search = ""
			state.parametre_recherche= 'none'
		}
	},

	getters: {
		laratoken: () => {
			return window.Laravel.csrfToken
		},
		environnement: (state) => {
			return state.environnement
		},
		axios: (state) => {
			return axios.create ({
				baseURL: state.baseUrl[state.environnement] ,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': window.Laravel.csrfToken
				}
			})
		},
		axios_config: (state) => {
			return {
				baseURL: state.baseUrl[state.environnement] + '/api/',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': window.Laravel.csrfToken
				}
			}
		},
		hauteur_iframe: (state) => {
			return state.hauteur_iframe
		},
		banner_title: (state) => {
			return state.banner_title
		},
		main_dialog: (state) => {
			return state.main_dialog
		},
		initiales: () => {
			return window.user.initiales
		},
		prenom: () => {
			return window.user.prenom
		},
		uid: () => {
			return parseInt(window.user.uid)
		},
		admins: (state) => {
			return state.admins
		},
		is_SA(state, getters) {
			return state.admins.indexOf(getters.uid) >= 0 ? true : false;
		},
		backold: () => {
			return "https://www.innov-home.fr/etmoi/" + window.user.uid
		},
		main_loading: (state) => {
			return state.main_loading
		},
		url_base: (state) => {
			return state.baseUrl[state.environnement]
		},
		btn_traiter(state) {
			return state.btn_traiter
		},
		snackbar(state) {
			return state.snackbar
		},
		current_ville(state) { // return object
			let cibles = Object.keys(state.list_villes)
			let obj_r= {}
			for (const c of cibles) {
				obj_r[c] = { commune: '', id: 0}
				if (!state.list_villes[c] || state.list_villes[c].length == 0) {
					continue
				}
				for (let V of state.list_villes[c]) {
					if (V.id == state.selected_ville_id[c]) {
						obj_r[c] = V
					}
				}
			}
			return obj_r
			/** 
			if (!state.list_villes || state.list_villes.length == 0) {
				return { commune: "ici", id: 1 }//null
			}
			for (let V of state.list_villes) {
				if (V.id == state.selected_ville_id) {
					return V
				}
			}
			return { commune: "là", id: 1 }//null
			**/
		},
		list_villes(state) { // return object
			return state.list_villes
		},
		selected_ville_id(state) { // return object
			return state.selected_ville_id
		},
		callbackSejour(state) {
			return state.callbackSejour
		},
		containerSejour(state) {
			return state.containerSejour
		},
		panel_ec(state) {
			return state.panel_ec
		},
		mess_coll(state) {
			return state.mess_coll
		},
		search_dialog(state) {
			return state.search_dialog
		},
		isMobile() {
			return parseInt(window.innerWidth) < 500 ? true : false
		},
		couleurs() {
			return couleurs
		},
		data_loaded_once(state) {
			return state.data_loaded_once
		},
		main_confirm(state) {
			return state.main_confirm
		},
		couleurs_cal(state) {
			return {
				Devis: state.couleurs.primaire,
				AirBnB: state.couleurs.bloqueTiers,
				tiers: state.couleurs.bloqueTiers,
				BlocageProp: state.couleurs.bloqueProprio,
				BlocageCons: state.couleurs.bloqueCons,
				BlocageUndef: state.couleurs.bloqueTiers,
				DemandeResas: state.couleurs.primaire
			}
		},
		calendrierDroits(state) {
			return state.calendrierDroits
		},
		search(state) {
			return state.search
		},
		parametre_recherche(state) {
			return state.parametre_recherche
		},
		conseillers() {
			return JSON.parse(document.getElementById('conseillers').innerHTML)
		},
		taux_taxe_sejour(state) {
			return state.taux_taxe_sejour
		}
	},

	actions: {
		setHauteurIframe({ commit }) {
			let el = document.getElementById('etapes')
			let h = window.innerHeight
			if (!el) {
				h-= 140
			} else {
				let entete = document.getElementById('entete')
				let rect = el.getBoundingClientRect();
				h -= (rect.top + rect.height + 200)
			}
			commit('set_hauteur_iframe', h)
		},
		setBannerTitle({ commit }, s) {
			commit('set_banner_title', s)
		},
		setMainDialog({ commit }, obj) {
			commit('set_main_dialog', obj)
		},
		setSearchDialog({ commit }, obj) {
			commit('set_search_dialog', obj)	
		},
		setMainLoading({ commit }, b) {
			commit('set_main_loading', b)
		},
		setBtnTraiter({ commit }, obj) {
			commit('set_btn_traiter', obj)
		},
		/**
		 * @param {cible: 'bien|user', val: 'string|null'} 
		 */
		getSuggestIntra({ commit, getters, dispatch }, obj) { 
			const regCp = /[0-9]{4,7}$/i; // literal notation
			if (!obj.val) {
				return
			}
			// val too short
			if (obj.val.length < 3) return
			// val is a selected city
			if (regCp.test(obj.val)) {
				return
			}

			// Items have already been loaded
			//if (this.items.length > 0) return
			if (getters.list_villes[obj.cible].length ==1) return

			if (getters.environnement == "prod") {
				
				// Items have already been requested
				if (getters.btn_traiter.is_sending) {
					return
				}
			
				let adr = getters.url_base + '/api/communes?s=' + obj.val
				
				commit('set_btn_traiter', { is_sending: true })

				getters.axios.get(adr)
					.then((response) => {
						commit('set_btn_traiter', { is_sending: false })
						if (typeof response.data == "string") {
							dispatch('snackError', response.data)
						} else {
							commit('set_list_villes', { cible: obj.cible, arr: response.data.results })
						}
					})
					.catch((error) => {
						commit('set_btn_traiter', { is_sending: false })
						dispatch('snackBug', error)
					})
			} else {
				let s = '[{"commune":"Albi\u00e8s - 09310","id":191},{"commune":"Albigny-sur-Sa\u00f4ne - 69250","id":2582},{"commune":"Albiez-Montrond - 73300","id":4410},{"commune":"Albias - 82350","id":10030},{"commune":"Albiez-le-Jeune - 73300","id":11389},{"commune":"Albi - 81000","id":15681},{"commune":"Albi\u00e8res - 11330","id":16401},{"commune":"Albignac - 19190","id":21269},{"commune":"Albiac - 46500","id":23123},{"commune":"Albitreccia - 20128","id":23958},{"commune":"Albiac - 31460","id":24049},{"commune":"Albine - 81240","id":33288}]'
				commit('set_list_villes', { cible: obj.cible, arr: JSON.parse(s) })
			}
		},
		resetSuggestVille({ commit }, target) {
			commit('set_list_villes', { cible: target, arr: [] })
			commit('set_selected_ville_id', { cible: target, id: null })
			
		},
		setCallbackSejour({ commit }, s) {
			commit('set_callbackSejour', s)
		},
		setContainerSejour({ commit }, s) {
			commit('set_containerSejour', s)
		},
		initSuggestVille({ commit }, objOfobj) {
			commit('set_selected_ville_id', { cible: objOfobj.cible, id: objOfobj.obj.id })
			commit('set_list_villes', { cible: objOfobj.cible, arr: [objOfobj.obj] })
		},
		setPanelEc({ commit }, i) {
			commit('set_panel_ec', i)
		},
		setMessColl({ commit }, texte) {
			commit('set_mess_coll', texte)
		},
		sendMessColl({ commit, getters, dispatch }) {
			commit('set_btn_traiter', { is_sending: true })
			let adr = getters.url_base + "/send-email-collectifs"
			getters.axios.post(adr, { message: getters.mess_coll})
				.then((response) => {
					commit('set_btn_traiter', { is_sending: false })
					if (response.data.ok) {
						dispatch('snackOk', response.data.ok)
						// fermer la fenetre
						commit('')
					} else {
						dispatch('snackAlert', "Ah je n'ai pas eu le bon code retour... Vérifiez dans votre email si vous l'avez reçu !",)
					}
				})
				.catch((error) => {
					commit('set_btn_traiter', { is_sending: false })
					dispatch('snackBug', error)
				})
		},
		snackOk({ commit, getters }, mess) {
			commit('set_snackbar', {
				open: true,
				color: getters.couleurs.ok,
				text: mess,
				icon: 'mdi-check'
			})
		},
		snackAlert({ commit, getters }, mess) {
			commit('set_snackbar', {
				open: true,
				color: getters.couleurs.alert,
				text: mess,
				icon: 'mdi-alert'
			})
		},
		snackBug({ commit, getters }, error) {
			if (error.response && parseInt(error.response.status) == 401) {
				document.location.href = getters.url_base
			} else {
				commit('set_snackbar', {
					open: true,
					color: getters.couleurs.bug,
					text: error,
					icon: 'mdi-ladybug'
				})
				this._vm.$rollbar.error("snackBug conseiller", error);
			}
		},
		snackError({ commit, getters }, mess) {
			let reg = /^<!DOCTYPE html>/i
			if (reg.test(mess)) {
				mess = "Je pense que votre session est terminée, je vous redirige vers la page d'accueil"
				setTimeout(() => {
					window.location.href="./moncompte"
				}, 3000);
			}
			commit('set_snackbar', {
				open: true,
				color: getters.couleurs.primaire,
				text: mess,
				icon: 'mdi-bomb'
			})
			this._vm.$rollbar.error("snackError conseiller", mess);
		},
		callSetSearch({ commit }, val) {
			if (!val) {
				commit('set_search', '')
			} else {
				commit('set_search', val)
			}
		},
		setParametreRecherche({ commit }, s) {
			commit('set_parametre_recherche', s)
		},
		initSearch({ commit }) {
			commit('init_search')
		},
	},

}

import axios from 'axios'

