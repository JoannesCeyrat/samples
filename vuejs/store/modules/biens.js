//import { compileToFunctions } from "vue-template-compiler"
import { couleurs } from './couleurs'

const objIniBiens = JSON.parse(document.getElementById('initBiens').innerHTML)
export const biens = {
	state: {
        allBiens: [],
        stepsBiens: 1,
        stepBienEc: 1,
        maxBiens: 100, // nombre de biens affichés par le pagination
        // bien en cours edition, set via init_editEc set_editEc
        editEc: {},
        gestionnaires: [],
        typesBiens: [],
        typesContrats: [],
        equipements: [],
        has_uploaded_once: false,
        upload_ec: false,
        documents_upload: [],
        images_upload: [],
        indexPanelEditBien: undefined,
        periode: {
            sejours: [],
            mois: 0,
            annee: 0,
            //semainesLouees: []
        },
	},

	mutations: {
        set_allBiens(state, arr) {
            state.allBiens= arr
        },
        set_stepsBiens(state, i) {
            state.stepsBiens= i
        },
        set_stepBienEc(state, i) {
            state.stepBienEc= i
        },
        init_editEc(state, obj) {
            if (obj.newId) {
                state.editEc = {
                    bid: obj.newId,
                    commune_id: 0,
                    typeBien_id: 0,
                    typeContrat_id: 0,
                    proprio_id: 0,
                    proprio_type: undefined,
                    gestionnaire_id: null,
                    mandat_id: null,
                    destination: 'L',
                    nombrePersonnes: 0,
                    nombreChambres: 0,
                    descFR: 0,
                    titreFR: '',
                    loyer: 0,
                    loyer2: null,
                    loyer3: null,
                    loyer4: null,
                    loyer5: null,
                    calme: '3',
                    confort: '3',
                    sorties: '3',
                    parking: '3',
                    valide: '0',
                    cdc: 0,
                    images: [],
                    justificatifs: [],
                    lit2P: 0,
                    lit1P: 0,
                    convert1P: 0,
                    convert2P: 0,
                    douche: 0,
                    baignoire: 0,
                    wc: 0,
                    equipements: [],
                    ideal: '',
                    proche: '',
                    adresse: '',
                    abo_id: 0,
                    abo: 0,
                    sinistre: 0,
                    cdc: 0,
                    
                }
            } else {
                state.editEc = obj
            }
        },
        set_editEc_property(state, obj) {
            state.editEc[obj.prop]= obj.value
        },
        set_gestionnaires(state, arr) {
            state.gestionnaires= arr
        },
        set_typesBiens(state, arr) {
            state.typesBiens= arr
        },
        set_typesContrats(state, arr) {
            state.typesContrats= arr
        },
        set_equipements(state, arr) {
            state.equipements= arr
        },
        set_upload_ec(state, b) {
            state.upload_ec= b
        },
        set_has_uploaded_once(state, b) {
            state.has_uploaded_once= b
        },
        set_documents_upload(state, a) {
            state.documents_upload= a
        },
        set_images_upload(state, a) {
            state.images_upload = a
        },
        set_editEc_synchro(state, obj) {
            state.editEc["synchro"][obj.index]["url"] = obj.value
        },
        set_Bien(state, obj) {
            //state.allBiens[obj.index] = obj.data
            state.allBiens.splice(obj.index, 1, obj.data)
        },
        set_indexPanelEditBien(state, i) {
            state.indexPanelEditBien= i
        },
        add_sejour_periode(state, obj) {
            state.periode.sejours.push(obj)
        },
        set_sejour_periode(state, obj) {
            if (obj.index === undefined) {
                window.alert('Mince2, pas d\index blocage / déblocage pour travailler')
            } else {
                let O = state.periode.sejours[obj.index]
                O[obj.propriete] = obj.valeur
                state.periode.sejours.splice(obj.index, 1, O)
                //state.edit_bail_encours.sejours[obj.index]['action']= 'edit'
            }
        },
        remove_sejour_periode(state, i) {
            state.periode.sejours.splice(i, 1)
        },
        remove_all_sejour_periode(state) {
            state.periode.sejours= []
        },
        remove_all_semainesLouees(state) {
            state.periode.semainesLouees = []
        },
        add_semaineLouee(state, i) {
            state.periode.semainesLouees.push(i)
        },
        set_periode_property(state, obj) {
            state.periode[obj.cle]= obj.value
        },
        remove_bien_from_bid(state, bid) {
            let index= -1
            for (let i= 0; i < state.allBiens.length; i++) {
                if (state.allBiens[i]['ref'] == bid) {
                    index = i
                    break
                }
            }
            if (index >= 0) {
                state.allBiens.splice(index, 1)
            }
        },
        add_bien(state, obj) {
            state.allBiens.unshift(obj)
        },
        add_url__synchro(state) {
            state.editEc.synchro.push({
                id: 0,
                url: ""
            })
        },
        

	},
	getters: {
        allBiens(state) {
            return state.allBiens
        },
        stepBienEc(state) {
            return state.stepBienEc
        },
        stepsBiens(state) {
            return state.stepsBiens
        },
        infosValide() {
            return {
                "0": {
                    text: "à valider",
                    color: couleurs.alert,
                    icon: "mdi-timer-sand"
                },
                "1": {
                    text: "Publié",
                    color: couleurs.ok,
                    icon: "mdi-check",
                },
                "-1": {
                    text: "Dépublié",
                    color: "#777",
                    icon: "mdi-eye-off",
                },
                "-6": {
                    text: "Souci propriétaire",
                    color: "#ccc",
                    icon: "mdi-account-remove",
                },
                "AP": {
                    text: "Appeler propriétaire",
                    color: couleurs.primaire,
                    icon: "mdi-phone-alert",
                }
            }
        },
        biensDpl(state, getters) {
            let biensFiltered= []
            // on filtre
            if (getters.search.length > 1) {
                let exp = new RegExp(getters.search, 'i')
                let expDebut = new RegExp('^' + getters.search, 'i')
                let f = getters.parametre_recherche

                biensFiltered = state.allBiens.filter((bien) => {
                    if (f == "commune") {
                        return (exp.test(bien.adresse))
                    } else if (f == "prop") {
                        return (exp.test(bien.proprio))
                    } else if (f == "bid") {
                        return (expDebut.test(bien.ref))
                    } else if (f == "cp") {
                        return (expDebut.test(bien.adresse))
                    } else if (f == "gest") {
                        return (exp.test(bien.gestionnaire))
                    }
                })
            } else {
                biensFiltered = state.allBiens
            }
            return biensFiltered
            // on pagine
            //let from = (state.stepBienEc - 1) * state.maxBiens
            //let to = state.stepBienEc * state.maxBiens
            //return biensFiltered.slice(from, to)
        },
        editEc(state) {
            return state.editEc
        },
        gestionnaires(state) {
            return state.gestionnaires
        },
        typesBiens(state) {
            return state.typesBiens
        },
        typesContrats(state) {
            return state.typesContrats
        },
        equipements(state) {
            return state.equipements
        },
        upload_ec(state) {
            return state.upload_ec
        },
        has_uploaded_once(state) {
            return state.has_uploaded_once
        },
        documents_upload(state) {
            return state.documents_upload
        },
        images_upload(state) {
            return state.images_upload
        },
        indexEditEc(state) {
            for (let i = 0; i < state.allBiens.length; i++) {
                if (state.allBiens[i].ref == state.editEc.bid) {
                    return i
                }
            }
            return undefined
        },
        indexPanelEditBien(state) {
            return state.indexPanelEditBien
        },
        biensMenuAlertCount(state) {
            let tot = 0
            const regAppel = /appel/i;
            for (let B of state.allBiens) {
                if (regAppel.test(B.contrat)) {
                    tot++
                }
            }
            return tot
        },
        periode(state) {
            return state.periode
        },
        semainesLouees(state) {
            let arr_s= []
            for (let S of state.periode.sejours) {
                for (let D of S.detail) {
                    arr_s.push(D.semaine)
                }
            }
            return arr_s
        },

	},

	actions: {
        getAllBiens({ commit, getters, dispatch }) {
            
        /** prod online */
            if (getters.environnement == "prod") {
                commit('set_main_loading', true)
                getters.axios.get('./api/biens/get-biens')
                    .then((response) => {
                        commit('set_main_loading', false)
                        if (typeof response.data == "string") {
                            dispatch('snackError', response.data)
                        } else {
                            let O = response.data
                            if (O.steps > 1) {
                                commit('set_stepsBiens', O.steps)
                            }
                            commit('set_allBiens', O.biens)
                        }
                    })
                    .catch((error) => {
                        commit('set_main_loading', false)
                        dispatch('snackBug', error)
                    })
            } else {
                /** off line */
                if (objIniBiens.steps > 1) {
                    commit('set_stepsBiens', objIniBiens.steps)
                }
                commit('set_allBiens', objIniBiens.biens)
            }     
        },
        resetBiens({ commit }) {
            commit('set_allBiens', [])
        },
        setEditEcProperty({ commit }, obj) {
            commit('set_editEc_property', obj)
        },
        setEditEcSynchro({ commit }, obj) {
            commit('set_editEc_synchro', obj)
        },
        getBienDatas({ commit, getters, dispatch }, bid) {
            let O= {}
            /** prod online */
            if (getters.environnement == "prod") {
                commit('set_main_loading', true)
                getters.axios.get(('./api/biens/get-bien?bid='+bid))
                    .then((response) => {
                        commit('set_main_loading', false)
                        if (typeof response.data == "string") {
                            dispatch('snackError', response.data)
                        } else {
                            O = response.data
                            commit('init_editEc', O)
                            dispatch('dispatcherGetDetails', O)
                        }
                    })
                    .catch((error) => {
                        commit('set_main_loading', false)
                        dispatch('snackBug', error)
                    })
            } else {
            /** offline */
                O = JSON.parse(document.getElementById('bienDatas').innerHTML)
                commit('init_editEc', O)
                dispatch('dispatcherGetDetails', O)
            }
            
            
        },
        dispatcherGetDetails({ commit, dispatch }, obj) {
            if (obj.proprio_id > 0) {
                if (obj.proprio_type == "App\\Entreprise") {
                    commit('set_type_user', 'E')
                    dispatch('getEntreprise', obj.proprio_id)
                } else {
                    commit('set_type_user', 'U')
                    dispatch('getUser', obj.proprio_id)
                }
            }
            // init de la ville pour ce bien
            dispatch('initSuggestVille', { cible: 'bien', obj: { commune: obj.commune, id: obj.commune_id } })
            

        },
        getGestionnaires({ commit, getters, dispatch }) {
            /** prod online */
            if (getters.environnement == "prod") {
                getters.axios.get('./api/biens/get-gestionnaires')
                    .then((response) => {
                        if (typeof response.data == "string") {
                            window.alert("Erreur getGestionnaires : " + response.data)
                        } else {
                            commit('set_gestionnaires', response.data)
                        }
                    })
                    .catch((error) => {
                        dispatch('snackBug', error)
                    })
            } else {
                /** offline */
                let arr = [{ "libelle": "Automatique", "value": 0 }, { "libelle": "ADLER Sylvie", "value": 983 }, { "libelle": "AHKOON Laurent", "value": 4101 }, { "libelle": "AHKOON Sonia", "value": 2722 }]
                commit('set_gestionnaires', arr)
            }
        },
        getTypesBiens({ commit, getters, dispatch }) {
            /** prod online */
            if (getters.environnement == "prod") {
                getters.axios.get('./api/biens/get-typesBiens')
                    .then((response) => {
                        if (typeof response.data == "string") {
                            dispatch('snackError', "Erreur getTypesBiens : " + response.data)
                        } else {
                            commit('set_typesBiens', response.data)
                        }
                    })
                    .catch((error) => {
                        dispatch('snackBug', error)
                       
                    })
            } else {
                /** offline */
                let arr = [{ "libelle": "appartement", "value": 3 }, { "libelle": "atypique roulotte", "value": 2 }, { "libelle": "chambre", "value": 5 }, { "libelle": "colocation", "value": 7 }, { "libelle": "h\u00f4tel", "value": 6 }, { "libelle": "maison", "value": 1 }, { "libelle": "mobil-home", "value": 8 }, { "libelle": "petit appartement", "value": 9 }, { "libelle": "studio", "value": 4 }]
                commit('set_typesBiens', arr)
            }
        },
        getTypesContrats({ commit, getters, dispatch }) {
            /** prod online */
            if (getters.environnement == "prod") {
                getters.axios.get('./api/biens/get-typesContrats')
                    .then((response) => {
                        if (typeof response.data == "string") {
                            dispatch('snackError', "Erreur getTypesContrats : " + response.data)
                        } else {
                            commit('set_typesContrats', response.data)
                        }
                    })
                    .catch((error) => {
                        dispatch('snackBug', error)
                    })
            } else {
                /** offline */
                let arr = [{ "libelle": "ATTENTE APPEL", "value": 4 }, { "libelle": "ANNONCEUR", "value": 1 }, { "libelle": "R\u00e9gisseur PLUS", "value": 2 }, { "libelle": "S\u00c9R\u00c9NIT\u00c9", "value": 3 }]
                commit('set_typesContrats', arr)
            }
        },
        getEquipements({ commit, getters, dispatch }) {
            /** prod online */
            if (getters.environnement == "prod") {
                getters.axios.get('./api/biens/get-equipements')
                    .then((response) => {
                        if (typeof response.data == "string") {
                            dispatch('snackError', "Erreur getEquipements : " + response.data)
                        } else {
                            commit('set_equipements', response.data)
                        }
                    })
                    .catch((error) => {
                        dispatch('snackBug', error)
                    })
            } else {
                /** offline */
                let arr = [{ "id": 1, "name": "Les essentiels", "equipements": [{ "id": 2, "name": "g\u00eete ind\u00e9pendant" }, { "id": 1, "name": "internet" }, { "id": 8, "name": "wifi" }, { "id": 7, "name": "animaux accept\u00e9s" }, { "id": 6, "name": "fumeurs accept\u00e9s" }, { "id": 11, "name": "acc\u00e8s handicap\u00e9" }, { "id": 9, "name": "produits de base" }, { "id": 3, "name": "t\u00e9l\u00e9vision" }, { "id": 5, "name": "satellite" }, { "id": 4, "name": "cable" }, { "id": 25, "name": "parking gratuit" }] }, { "id": 2, "name": "Travail", "equipements": [{ "id": 10, "name": "bureau \u00e9quip\u00e9" }, { "id": 13, "name": "tablette" }, { "id": 12, "name": "ordinateur" }] }, { "id": 3, "name": "Pratiques", "equipements": [{ "id": 35, "name": "plaques de cuisson" }, { "id": 17, "name": "four" }, { "id": 24, "name": "r\u00e9frig\u00e9rateur" }, { "id": 34, "name": "cong\u00e9lateur" }, { "id": 22, "name": "micro-ondes" }, { "id": 20, "name": "s\u00e8che linge" }, { "id": 19, "name": "lave linge" }, { "id": 21, "name": "lave vaisselle" }, { "id": 23, "name": "cafeti\u00e8re" }, { "id": 15, "name": "bouilloire" }, { "id": 18, "name": "grille pain" }, { "id": 14, "name": "aspirateur" }, { "id": 31, "name": "s\u00e8che cheveux" }, { "id": 16, "name": "fer \u00e0 repasser" }, { "id": 32, "name": "oreillers - couettes" }, { "id": 33, "name": "placards" }, { "id": 28, "name": "chemin\u00e9e" }, { "id": 26, "name": "ascenceur" }, { "id": 29, "name": "interphone" }, { "id": 27, "name": "climatisation" }] }, { "id": 4, "name": "Loisirs", "equipements": [{ "id": 41, "name": "salon de jardin" }, { "id": 42, "name": "terrasse" }, { "id": 36, "name": "piscine" }, { "id": 40, "name": "barbecue" }, { "id": 39, "name": "v\u00e9los" }, { "id": 37, "name": "jardin" }, { "id": 43, "name": "bois de chauffage" }] }]
                commit('set_equipements', arr)
            }
        },
        sendDocument({ commit, getters, dispatch }, obj) {
            let imgs = getters.editEc.justificatifs
            let queue = getters.documents_upload 
            let commiter = "set_documents_upload"

            commit('set_upload_ec', true)
            commit('set_has_uploaded_once', true)
            
            getters.axios.post(obj.u, obj.f)
                .then((response) => {
                    commit('set_upload_ec', false)
                    if (response.data.error) {
                        dispatch('snackError', response.data.error)
                        // retirer le fichier de la queue 
                        dispatch('removeFirstDocumentUpload')
                    } else {
                        imgs.unshift({
                            nom: response.data.ok[1],
                        })
                        queue.splice(0,1)
                        commit('setEditEcProperty', {
                            prop: "justificatifs",
                            value: imgs,
                        })
                        commit(commiter, queue)
                    }
                })
                .catch((error) => {
                    dispatch('snackBug', error)
                    // retirer le fichier de la queue
                    commit('set_upload_ec', false) 
                    dispatch('removeFirstDocumentUpload')               
                    
                })
        },
        sendImage({ commit, getters, dispatch }, obj) {
            let imgs =  getters.editEc.images
            let queue = getters.images_upload
            let commiter = "set_images_upload"

            commit('set_upload_ec', true)
            commit('set_has_uploaded_once', true)

            getters.axios.post(obj.u, obj.f)
                .then((response) => {
                    commit('set_upload_ec', false)
                    if (response.data.error) {
                        dispatch('snackError', response.data.error)
                        // retirer le fichier de la queue 
                        dispatch('removeFirstImageUpload')
                    } else {
                        imgs.unshift({
                            nom: response.data.ok[1],
                            alt: ''
                        })
                        queue.splice(0, 1)
                        commit('setEditEcProperty', {
                            prop: "images",
                            value: imgs,
                        })
                        commit(commiter, queue)
                    }
                })
                .catch((error) => {
                    dispatch('snackBug', error)
                    // retirer le fichier de la queue
                    commit('set_upload_ec', false)
                    dispatch('removeFirstImageUpload')

                })
        },
        addDocument({ commit, getters }, obj) {
            let a = getters.documents_upload
            a.push(obj)
            commit('set_documents_upload', a)
        },
        addImage({ commit, getters }, obj) {
            let a = getters.images_upload
            a.push(obj)
            commit('set_images_upload', a)
        },
        removeFirstDocumentUpload({ commit, getters }) {
            let a = getters.documents_upload
            a.splice(0,1)
            commit('set_documents_upload', a)
        },
        removeFirstImageUpload({ commit, getters }) {
            let a = getters.images_upload
            a.splice(0, 1)
            commit('set_images_upload', a)
        },
        majImagesListe({ getters, dispatch }) {
            let imgs = getters.editEc.images
            let adr = getters.url_base +"/etmoi/" +
                window.user.uid +
                "/bien/" +
                getters.editEc.bid +
                "/majImage";
            getters.axios.post(adr, {liste: imgs})
                .then((response) => {
                    if (response.data.error) {
                        dispatch('snackError', response.data.error)
                    }
                })
                .catch((error) => {
                    dispatch('snackBug', error)
                })
        },
        majBien({ commit, getters, dispatch }) {
            /** prod online */
            if (getters.environnement == "prod") {
                commit('set_btn_traiter', { is_sending: true })
                // definition de l'url de maj
                let adr = getters.url_base +"/etmoi/" +
                    window.user.uid +
                    "/bienv2/" +
                    getters.editEc.bid;
                // definition du user pour ce bien
                if (getters.type_user == "U") {
                    commit('set_editEc_property', { prop: "proprio_type", value: 'App\\User' })
                    commit('set_editEc_property', { prop: "proprio_id", value: getters.selected_id })
                    //console.log('je mets à jour le proprio en U')
                } else if (getters.type_user == "E") {
                    commit('set_editEc_property', { prop: "proprio_type", value: 'App\\Entreprise' })
                    commit('set_editEc_property', { prop:"proprio_id", value: getters.selected_eid })
                }
                // ajax
                setTimeout(() => {
                    getters.axios.post(adr, getters.editEc)
                        .then((response) => {
                            commit('set_btn_traiter', { is_sending: false })
                            if (response.data.error) {
                                dispatch('snackError', response.data.error)
                            } else if (response.data.ok) {
                                dispatch('snackOk', response.data.ok)
                                // mettre à jour le bien
                                if (getters.indexEditEc !== undefined) {
                                    commit('set_Bien', { index: getters.indexEditEc, data: response.data.vignette })
                                    /**
                                          * mettre à jour les synchros du editEc
                                          */
                                    commit('set_editEc_property', {
                                        prop: "synchro",
                                        value: response.data.synchro
                                    })
                                } else {
                                    dispatch('snackAlert', "Je n'ai pas mis à jour le teaser du bien")
                                }
                            }
                        })
                        .catch((error) => {
                            commit('set_btn_traiter', { is_sending: false })
                            dispatch('snackBug', error)
                        })
                }, 200);
                
            } else {
                let response = { "ok": "Mise \u00e0 jour OK", "vignette": { "vignette": "https:\/\/www.innov-home.fr\/images-meubles\/vignettes\/862-img-0243.jpg", "contrat": "S\u00c9R\u00c9NIT\u00c9", "valide": "0", "ref": 862, "adresse": "8200 Albufeira", "gestionnaire": "Chlo\u00e9 BENETTI", "proprio": "SCI SCI CEYRAT Investissment", "titre": "DEV maison de test de joannes" } }
                // mettre à jour le bien
                if (getters.indexEditEc !== undefined) {
                    commit('set_Bien', { index: getters.indexEditEc, data: response.vignette })
                } else {
                    dispatch('snackAlert', "Je n'ai pas mis à jour le teaser du bien")
                }
            }
        },
        setIndexPanelEditBien({ commit }, val) {
            commit('set_indexPanelEditBien', val)
        },
        actionRemoveSejourFromPeriode({ commit }, index) {
            commit('remove_sejour_periode', index)
        },
        AddSejourPeriode({ commit }, obj) {
            commit('add_sejour_periode', obj)
        },
        getCalendrier({ commit, getters, dispatch }, bid) {
            //commit('set_periode_property', { cle: 'mois', value: obj.start.month })
            //commit('set_periode_property', { cle: 'annee', value: obj.start.year })
            let infos_cal = undefined
            if (getters.environnement == "prod") {
                commit('set_main_loading', true)
                let adr = './api/get-calendrier/' + bid 
                getters.axios.get(adr)
                    .then((response) => {
                        commit('set_main_loading', false)
                        if (response.data.error) {
                            dispatch('snackError', response.data.error)
                        } else {
                            infos_cal = response.data
                            dispatch('setCalendrier', infos_cal)
                        }
                    })
                    .catch((error) => {
                        commit('set_main_loading', false)
                        dispatch('snackBug', error)
                    })
            } else {
                infos_cal = JSON.parse(document.getElementById('biensSejours').innerHTML)
                dispatch('setCalendrier', infos_cal)
            }
        },
        alertDate({ dispatch }, message) {
            dispatch('snackAlert', message)
        },
        alertBug({ dispatch }, message) {
            dispatch('snackBug', message)
        },
        setCalendrier({ commit, getters }, infos_cal) {
            // effacer les sjours
            commit('remove_all_sejour_periode')
            // effacer les semainesLouees
            commit('remove_all_semainesLouees')
            // mise en conformité des datas
            // et stockage dans periode.sejours
            // et stockage des semaines louées
            let couleur = undefined
            const reg = /\.[a-z]{2,3}$/; 
            for (let O of infos_cal) {
                for (let S of O.sejours) {
                    /**
                     * determination de la couleurs si Résa annulée ou devis en cours
                     */
                    if (O.type == "DemandeResas") {
                        if (O.reponse == 1) {
                            couleur= getters.couleurs.primaire
                        } else if (O.reponse == 2) {
                            couleur = getters.couleurs.rembourse
                        } else {
                            couleur = getters.couleurs.complementaireM1
                        }
                    } else if (O.type == "Devis") {
                        if (O.reponse == 1) {
                            couleur = getters.couleurs.primaire
                        } else {
                            couleur = getters.couleurs.complementaireM1
                        }
                    } else if (reg.test(O.type)) {
                        couleur = getters.couleurs_cal["tiers"]
                    } else {
                        couleur = getters.couleurs_cal[O.type]
                    }
                    let a = {
                        name: reg.test(O.type) ? (O.ref+" @ "+O.type) : O.ref,
                        color: couleur,
                        timed: 0,
                        start: S.duUS,
                        end: S.auUS,
                        detail: S.detail,
                        type: O.type,
                        server: true,
                        isEditing: true,
                        reponse: O.reponse
                    }
                    commit('add_sejour_periode', a)
                    // basculé en getter
                    //for (let D of S.detail) {
                    //    commit('add_semaineLouee', D.semaine)
                    //}
                }
            }
        },
        sendPeriode({ commit, getters, dispatch }) {
            commit('set_main_loading', true)
            let adr = './api/set-calendrier/' + getters.main_dialog.data.bid
            getters.axios.post(adr, getters.periode)
                .then((response) => {
                    commit('set_main_loading', false)
                    if (response.data.error) {
                        dispatch('snackError', response.data.error)
                    } else {
                        dispatch('snackOk', 'calendrier mis à jour.')
                        dispatch('setCalendrier', response.data)
                    }
                })
                .catch((error) => {
                    commit('set_main_loading', false)
                    dispatch('snackBug', error)
                })
        },
        deleteBien({ commit, getters, dispatch }) {
            if (getters.environnement == "prod") {
                commit('set_btn_traiter', { is_sending: true })
                let adr = './etmoi/' + window.user.uid + '/bien/del'
                getters.axios.post(adr, { bid: getters.main_dialog.data.bid })
                    .then((response) => {
                        commit('set_btn_traiter', { is_sending: false })
                        if (response.data.error) {
                            dispatch('snackError', response.data.error)
                        } else {
                            dispatch('snackOk', "le bien a été supprimé.")
                            // supprimer le bien de la liste
                            commit('remove_bien_from_bid', getters.main_dialog.data.bid)
                            // fermer la fenetre d'action
                            dispatch('setMainDialog', { cle: 'open', value: false })
                            
                        }

                    })
                    .catch((error) => {
                        commit('set_btn_traiter', { is_sending: false })
                        dispatch('snackBug', error)
                    })
            } else {
                commit('remove_bien_from_bid', getters.main_dialog.data.bid)
                dispatch('setMainDialog', { cle: 'open', value: false })
            }
        },
        addBien({ commit, getters, dispatch }) {
            if (getters.environnement == "prod") {
                if (!getters.main_loading) {
                    commit('set_main_loading', true)
                    let adr = './api/bien/add'
                    getters.axios.post(adr)
                        .then((response) => {
                            commit('set_main_loading', false)
                            dispatch('snackOk', "le bien a été créé. Pensez à ajouter son propriétaire !")
                            // ajouter le bien de la liste
                            commit('add_bien', response.data)
                            // lancer la recherche de datas pour ce bien
                            dispatch('getBienDatas', response.data.ref)
                            // ouvrir la fenetre d'édition
                            dispatch('setMainDialog', {cle: 'component', value: 'EditBien'})
                            dispatch('setMainDialog', {
                                cle: 'data', value: {
                                    bid: response.data.ref,
                                    hero: response.data.vignette.replace('vignettes', 'heroes')
                                }
                            })
                            dispatch('setMainDialog', { cle: 'open', value: true })
                            setTimeout(() => {
                                dispatch('setHauteurIframe')
                            }, 1000)
                            
                        })
                        .catch((error) => {
                            commit('set_main_loading', false)
                            dispatch('snackBug', error)
                        })
                }
            }
        },
        addUrlSynchro({ commit }) {
            commit('add_url__synchro')
        }
    },

}

