<template>
  <v-row no-gutter>
    <v-col cols="12">
        <v-card class="rounded-lg"  flat color="rgb(247, 247, 247)">
            <v-card-text class="rose pb-0">Le calendrier peut être synchronisé</v-card-text>
            <v-card-text class="ma-0"><v-icon left right :color="couleurs.ok">mdi-check</v-icon>AirBnB
                  <v-icon left right :color="couleurs.ok">mdi-check</v-icon>Booking
                  <v-icon left right :color="couleurs.ok">mdi-check</v-icon>Abritel
                  <v-icon left right :color="couleurs.ok">mdi-check</v-icon>Channels-managers
            </v-card-text>
            
            <v-card-text class="ma-0 pt-0">Dans ce cas, ajoutez une ou des urls de synchronisation.</v-card-text>

        </v-card>
    </v-col>

    <v-col cols="12">
        <v-card flat>
            <v-card-title>Url(s) synchronisation tierce</v-card-title>
            <v-card-subtitle class="pt-2">
                <v-sheet class="mb-3"><span :style="{color: couleurs.gris}">Format accepté : <b>iCal</b>. Nous <b>ignorons les réservations non confirmées</b>, car les plateformes, par leurs pratiques de gestion, bloquent
                parfois des biens libres.</span>
                </v-sheet>
                 

                <synchro
                  v-for="(sync, index) of synchros"
                  :index="index"
                  :key="'synchro'+index"
                ></synchro>
                 
                <v-btn 
                  depressed
                  @click="addUrlSynchro"
                >
                  <v-icon left>mdi-link-plus</v-icon> ajouter une url
                </v-btn>
                
              </v-card-subtitle>
          
            <v-card-title>Url synchronisation INNOV-HOME </v-card-title>
            <v-card-subtitle class="pt-2">A indiquer à la plateforme ou au channel-manager.</v-card-subtitle>
            <v-card-text class="pt-0"><h4 :style="{color: couleurs.primaire}">https://www.innov-home.fr/icals/{{ editEc.bid }}</h4></v-card-text>

        </v-card>
    </v-col>
   
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

import synchro from "./Synchro"

export default {
  name: "airbnb",
  mixins: [],
  components: {
    synchro
   },

  data: () => ({}),

  props: [ ],

  computed: {
    ...mapGetters(["editEc", "couleurs"]),
    synchros() {
        return this.editEc.synchro;
    },
     
    
  },
  methods: {
    ...mapActions(["addUrlSynchro"]),
  },

  created() {},

  mounted() {},

  watch: {
      
  },
};
</script>