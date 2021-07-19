<template>
  <v-row no-gutter>
    <v-col cols="12">
        <v-card class="rounded-lg"  flat color="rgb(247, 247, 247)">
            <v-card-title>Rappel du  format du titre</v-card-title>
            <v-card-text class="rose">VILLE ACTIVITÉS + type du logement + mention libre (+) ville du bien</v-card-text>
        </v-card>
    </v-col>
    <v-col cols="12">
       <v-text-field v-model="titre" label="Titre annonce au format ci-dessus svp" required></v-text-field>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";


export default {
  name: "titre",
  mixins: [],
  components: { },

  data: () => ({}),

  props: [ ],

  computed: {
    ...mapGetters(["editEc"]),
    titre: {
      get() {
        return this.editEc.titreFR;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "titreFR",
          value: value,
        });
      },
    },
    
  },

  methods: {
    ...mapActions(["setEditEcProperty"]),
  },

  created() {},

  mounted() {},

  watch: {
      
  },
};
</script>