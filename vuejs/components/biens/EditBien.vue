<template>
  <v-sheet min-height="100%">
    <v-toolbar dark color="#C9006B" :src="main_dialog.data.hero" tile extended>
      <v-toolbar-title class="textShadowed">
        <h3>Edition bien {{ main_dialog.data.bid }}</h3>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-card 
              flat
              color="transparent"
            >
              <v-card-actions>
              <v-btn
                class="mx-2" fab  large color="#e2007a"
                @click="majBien"
                v-on="on"
                dark
                :loading="btn_traiter.is_sending"
              >
                <v-icon large>mdi-content-save</v-icon>
              </v-btn>
              </v-card-actions>
            </v-card>
          </template>
          <span>Enregister les modifications pour ce bien</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              dark
              @click="setMainDialog({cle: 'open', value: false})"
              v-on="on"
              class="textShadowed"
            >
              <v-icon size="30" style="padding-top: 1rem">mdi-close</v-icon>
            </v-btn>
          </template>
          <span>Fermer</span>
        </v-tooltip>
      </v-toolbar-items>
    </v-toolbar>

    <v-row no-gutters class="margeUp">
      <v-col xs="12" md="10" offset-md="1" xl="8" offset-xl="2">
        <div
          :style="{
                            height: hauteur_iframe,
                            overflow: 'scroll',
                            padding: '0.2rem 0.75rem'
                        }"
        >
          <v-card flat>
            <v-card-text>
              <v-row>
                <v-col cols="12" sm="6">
                  <v-switch v-model="visible" label="Valide et visible sur le site"></v-switch>
                </v-col>
                <v-col cols="12" sm="6" md="4" lg="3" xl="2" offset-md="2" offset-lg="3" offset-xl="4">
                  <v-switch v-model="cdc" label="Coup de coeur" class="text-right"></v-switch>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
          <v-expansion-panels hover focusable v-model="panel">
            
            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-account-circle</v-icon>Gestionnaire</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><gestionnaire></gestionnaire></v-expansion-panel-content>
            </v-expansion-panel>
            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-file-document</v-icon>Type du contrat</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><contrat></contrat></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-account</v-icon>Mandater, facturer à ...</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content>
                    <v-row no-gutter>
                        <v-col cols="12">
                            <choixTypeUser></choixTypeUser>
                        </v-col>
                    </v-row>
              </v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-calendar-sync</v-icon>Synchronisation</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><airbnb></airbnb></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-weather-lightning-rainy</v-icon>Assurance</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content>
                  <sinistre></sinistre>
              </v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-home-lightbulb</v-icon>Type du meublé</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><type></type></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-map-marker</v-icon>Adresse du meublé</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><adresse></adresse></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-format-title</v-icon>Titre de l'annonce</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><titre></titre></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-format-title</v-icon>Descriptif de l'annonce</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content style="min-height:350px"><descriptif></descriptif></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-star</v-icon>Notes / classement du meublé</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><notes></notes></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-currency-eur</v-icon>Tarification : loyers € / semaine</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content>
                <tarification></tarification>
              </v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-account-group</v-icon>Capacité d'accueil & nombre de ...</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content>
               <nombres></nombres>
              </v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-coffee-maker</v-icon>Liste des équipements</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><equipements></equipements></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-account-heart</v-icon>Meublé idéal pour ...</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><ideal></ideal></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-map-marker-distance</v-icon>Meublé proche de ...</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><proche></proche></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-camera</v-icon>Images du meublé</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><images></images></v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header><v-sheet><v-icon left>mdi-file-certificate</v-icon>Justificatifs de propriété</v-sheet></v-expansion-panel-header>
              <v-expansion-panel-content><justificatifs></justificatifs></v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>

          <div style="height: 1rem"></div>
        </div>
      </v-col>
    </v-row>
  </v-sheet>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

import tarification from "./edit/Tarification";
import nombres from "./edit/Nombres";
import sinistre from "./edit/Sinistre";
import type from "./edit/Type";
import contrat from "./edit/Contrat";
import adresse from "./edit/Adresse";
import titre from "./edit/Titre";
import descriptif from "./edit/Descriptif";
import notes from "./edit/Notes";
import equipements from "./edit/Equipements";
import ideal from "./edit/Ideal";
import proche from "./edit/Proche";
import choixTypeUser from '../ChoixTypeUser'
import images from './edit/Images'
import justificatifs from './edit/Justificatifs'
import gestionnaire from './edit/Gestionnaire'
import airbnb from './edit/Airbnb'

export default {
  name: "EditBien",
  components: { 
      tarification,
      nombres,
      sinistre,
      type,
      contrat,
      adresse,
      titre,
      descriptif,
      notes,
      equipements,
      ideal,
      proche,
      choixTypeUser,
      images,
      justificatifs,
      gestionnaire,
      airbnb
  },
  data: () => ({}),
  computed: {
    ...mapGetters([
        "main_dialog",
        "hauteur_iframe",
        "gestionnaires",
        "typesBiens",
        "typesContrats",
        "editEc",
        "equipements",
        "type_user",
        "btn_traiter",
        "indexPanelEditBien"
    ]),
    panel: {
      get() {
          return this.indexPanelEditBien
      },
      set(value) {
          this.setIndexPanelEditBien(value)
      }
    },
    proprio_type() {
      if (this.editEc.proprio_type == undefined) {
        return "U"
      }
      let t= String(this.editEc.proprio_type).toLowerCase()
      return (t == "app\\user") ? "U" : "E"
    },
    proprio_id() {
      return this.editEc.proprio_id
    },
    visible: {
      get() {
        return parseInt(this.editEc.valide);
      },
      set(value) {
        let s= value ? 1 : 0
        this.setEditEcProperty({
          prop: "valide",
          value: s,
        });
      }
    },
    userFor() {
      return {
        cible: 'bien',
        id: this.editEc.bid
      }
    },
    cdc: {
      get() {
        return parseInt(this.editEc.cdc);
      },
      set(value) {
        let s= value ? 1 : 0
        this.setEditEcProperty({
          prop: "cdc",
          value: s,
        });
      }
    },
  },
  props: [],
  methods: {
    ...mapActions([
        "setMainDialog",
        "getBienDatas",
        "getGestionnaires",
        "getTypesBiens",
        "getTypesContrats",
        "getEquipements",
        "initNewUser",
        "initNewEntreprise",
        "setSelectedEntreprise",
        "setSelectedUser",
        "setEditEcProperty",
        "majBien",
        "setIndexPanelEditBien"
    ]),
    lienClient() {
      if (this.type_user == "E") {
        this.setSelectedEntreprise(0)
      } else {
        this.setSelectedUser(0)
      }
      this.setMainDialog({cle: 'open', value: false})
      document.location.href= "./#/users"
    }
  },
  watch: {},
  mounted() {
    this.getBienDatas(this.main_dialog.data.bid);

    if (this.gestionnaires.length == 0 ) {
        this.getGestionnaires()
    }
    if (this.typesBiens.length == 0 ) {
        this.getTypesBiens()
    }
    if (this.typesContrats.length == 0 ) {
        this.getTypesContrats()
    }
    if (this.equipements.length == 0 ) {
        this.getEquipements()
    }
  },
  
};
</script>
