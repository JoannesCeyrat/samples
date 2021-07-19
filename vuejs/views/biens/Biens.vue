<template>
  <v-container fluid text-left>
    <!-- stats -->
    <v-row class="margeUp">
      <v-col xs="12" md="10" offset-md="1" xl="8" offset-xl="2">
        <v-row>
          <v-col cols="12">
            <v-card>
              <v-card-title>Statistiques</v-card-title>
              <v-card-text style="font-weight: bold">
                <v-sheet>Publiés : <span :style="{color: couleurs.ok}">{{ totPublies }}</span></v-sheet>
                <v-sheet>A valider - en cours : <span :style="{color: couleurs.alert}">{{ totNPublies }}</span></v-sheet>
                <v-sheet>Sérénité : <span :style="{color: couleurs.primaire}">{{ totSerenite }}</span></v-sheet>
                <v-sheet>Régisseur Plus : <span :style="{color: couleurs.bleu}">{{ totRp }}</span></v-sheet>
                <v-sheet>Annonceur : {{ totAnnon }}</v-sheet>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <!-- information -->
     <v-row>
       <v-col xs="12" md="10" offset-md="1" xl="8" offset-xl="2">
            <v-card width="100%">
                <v-card-text :style="{'background-color': couleurs.bleu}">
                    <v-sheet  color="transparent" :style="{color: couleurs.blanc}" class="mb-2">
                        <b>ATTENTION : si vous rentrez un bien pour un nouveau client, il faut d'abord AJOUTER le propriétaire dans la rubrique "Clients".</b>
                    </v-sheet>
                </v-card-text>
            </v-card>  
       </v-col>             
        </v-row>
    <!-- biens -->
    <v-row>
      <v-col xs="12" md="10" offset-md="1" xl="8" offset-xl="2">
        <v-row class="margeUp">
          <v-col xs="12" md="6" lg="4" xl="3" v-for="B in biensDpl" :key="'bien'+B.ref">
            <BienVignette :B="B"></BienVignette>
          </v-col>
        </v-row>
        <v-row class="margeUp"><v-col style="min-height: 12rem"></v-col></v-row>
      </v-col>
    </v-row>

    
    <!-- ajout de bien -->
    <v-footer
      fixed
      tile
      color="rgba(255, 255, 255, 0)"
      style="left: 1rem;"
    >
      <v-row>
        <v-col
          cols="12"
          align="center"
          style="padding-left: 3rem"
        >
          <!-- bouton + -->
          <v-btn
            class="mx-2"
            fab
            dark
            large
            color="#e2007a"
            title="Ajouter un bien"
            @click="tooglePlus()"
            v-show="!menu_footer_open"
          >
            <v-icon dark>mdi-plus</v-icon>
          </v-btn>
          <!-- sous menu -->
          <v-card
            class="mx-auto"
            max-width="400"
            tile
            v-show="menu_footer_open"
            v-click-outside="outside"
            :color="couleurs.bug"
            dark
          >
            <v-list-item  link>
              <v-list-item-content  @click="addBien()">
                <v-list-item-title>Ajouter un bien</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-card>
          
        </v-col>
      </v-row>
    </v-footer>

    
  </v-container>
</template>


<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
import BienVignette from "../../components/BienVignette";

export default {
  name: "Biens",
  mixins: [],
  components: {
    BienVignette
  },
  data: () => ({
    //dialog: false
    menu_footer_open: false,
  }),
  computed: {
    ...mapGetters([
      "allBiens",
      "stepBienEc",
      "stepsBiens",
      "biensDpl",
      "couleurs",
    ]),
    dplFooter() {
      return this.stepsBiens > 1 ? true : false;
    },
    step: {
            get() {
                return this.stepBienEc
            },
            set(value) {
                this.$store.commit('set_stepBienEc', value);
            }

        },
    totPublies() {
        if (this.biensDpl.length == 0) {
            return 0
        }
        const a= this.biensDpl.filter(B => parseInt(B.valide) === 1)
        return a.length
    },
    totNPublies() {
        if (this.biensDpl.length == 0) {
            return 0
        }
        const a= this.biensDpl.filter(B => parseInt(B.valide) === 0)
        return a.length
    },
    totSerenite() {
        if (this.biensDpl.length == 0) {
            return 0
        }
        const a= this.biensDpl.filter(B => B.contrat.toLowerCase() == "sérénité")
        return a.length
    },
    totRp() {
        if (this.biensDpl.length == 0) {
            return 0
        }
        const a= this.biensDpl.filter(B => B.contrat.toLowerCase() == "régisseur plus")
        return a.length
    },
    totAnnon() {
        if (this.biensDpl.length == 0) {
            return 0
        }
        const a= this.biensDpl.filter(B => B.contrat.toLowerCase() == "annonceur")
        return a.length
    },
  },
   methods: {
    ...mapActions([
      "setBannerTitle",
      "setMainDialog",
      "getBienDatas",
      "setHauteurIframe",
      "getCalendrier",
      "addBien"
    ]),
    tooglePlus() {
      setTimeout(() => {
        this.menu_footer_open= this.menu_footer_open ? false: true
      }, 100);   
    },
    outside() {
      if (this.menu_footer_open) {
        this.tooglePlus()
      }
    },
  },
  created() {
    //console.log(this.laratoken)
    this.$on("openDialog", obj => {
        /** set the datas of the store */
        if (obj.component == "EditBien") {
          this.getBienDatas(obj.B.ref);
        } else if (obj.component == "CalenBien") {
          this.getCalendrier(obj.B.ref)
        }
        this.setMainDialog({cle: 'component', value: obj.component})
        this.setMainDialog({cle: 'data', value: {
                bid: obj.B.ref,
                hero: obj.B.vignette.replace('vignettes', 'heroes')
            }
        })
        this.setMainDialog({cle: 'open', value: true})
        setTimeout(() => {
          this.setHauteurIframe();
        }, 1000);
    });
  },
  mounted() {
    this.setBannerTitle("Gestion des Biens");
  },
};
/** to="nouveau-mandat" */
</script>