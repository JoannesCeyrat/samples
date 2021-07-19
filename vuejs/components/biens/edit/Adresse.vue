<template>
  <v-row no-gutter>
    <v-col cols="12" md="6">
       <v-text-field v-model="adr" label="numéro & rue" required></v-text-field>
    </v-col>
    <v-col cols="12" md="6">
       <getVilleIntra cible="bien"></getVilleIntra>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

import getVilleIntra from "../../GetVilleIntra";

export default {
  name: "adresse",
  mixins: [],
  components: { getVilleIntra },

  data: () => ({}),

  props: [],

  computed: {
    ...mapGetters(["editEc", "selected_ville_id"]),
    adr: {
      get() {
        return this.editEc.adresse;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "adresse",
          value: value,
        });
      },
    },
    villeChoisie() {
      return this.selected_ville_id['bien']
    }
    
    
  },

  methods: {
    ...mapActions(["setEditEcProperty"]),
  },

  created() {},

  mounted() {},

  watch: {
      villeChoisie(val) {
         console.log('watcher selected_ville de adresse')
         console.log(val)
          this.setEditEcProperty({
          prop: "commune_id",
          value: val,
        });
      }
      
  },
};
</script>