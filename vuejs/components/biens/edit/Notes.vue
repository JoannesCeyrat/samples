<template>
  <v-row no-gutter>
    <v-col cols="12">Notes pour INNOV-HOME</v-col>
    <v-col cols="12" xs="12" sm="6" md="2" lg="2">
      <v-select
        :items="simples"
        label="Note calme"
        item-value="value"
        item-text="libelle"
        v-model="calme"
      ></v-select>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="2" lg="2">
      <v-select
        :items="simples"
        label="Note confort"
        item-value="value"
        item-text="libelle"
        v-model="confort"
      ></v-select>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="2" lg="2">
      <v-select
        :items="simples"
        label="Note sorties"
        item-value="value"
        item-text="libelle"
        v-model="sorties"
      ></v-select>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="6" lg="6">
      <v-select
        :items="parkings"
        label="Note parking"
        item-value="value"
        item-text="libelle"
        v-model="park"
      ></v-select>
    </v-col>


    <v-col cols="12">Classement touristique</v-col>
     <v-col cols="6" xs="6" sm="6" md="4" lg="3">
        <v-switch v-model="classMeuble" label="Meublé classé"></v-switch>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="6" lg="6">
      <v-select
        :items="simples"
        label="Etoiles / épis / clés / etc..."
        item-value="value"
        item-text="libelle"
        v-model="classNote"
      ></v-select>
    </v-col>
  </v-row>
 
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "notes",
  mixins: [],
  components: {},

  data: () => ({
      simples: [
            {libelle: "1/5", value: 1},
            {libelle: "2/5", value: 2},
            {libelle: "3/5", value: 3},
            {libelle: "4/5", value: 4},
            {libelle: "5/5", value: 5},
      ],
      parkings: [
            {libelle: "1/5 parking public payant", value: 1},
            {libelle: "2/5 parking gratuit dans la rue", value: 2},
            {libelle: "3/5 place réservée parking gratuit", value: 3},
            {libelle: "4/5 place privée sécurisée payante", value: 4},
            {libelle: "5/5 place privée sécurisée gratuite", value: 5},
      ]
  }),

  props: [],

  computed: {
    ...mapGetters(["editEc", "typesBiens"]),
    calme: {
      get() {
        return parseInt(this.editEc.calme);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "calme",
          value: value.length == 0 ? '' : parseInt(value),
        });
      }
    },
    confort: {
      get() {
        return parseInt(this.editEc.confort);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "confort",
          value: value.length == 0 ? '' : parseInt(value),
        });
      }
    },
    sorties: {
      get() {
        return parseInt(this.editEc.sorties);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "sorties",
          value: value.length == 0 ? '' : parseInt(value),
        });
      }
    },
    park: {
      get() {
        return parseInt(this.editEc.parking);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "parking",
          value: value.length == 0 ? '' : parseInt(value),
        });
      }
    },
    classMeuble: {
      get() {
        return parseInt(this.editEc.classMeuble);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "classMeuble",
          value: value.length == 0 ? '' : parseInt(value),
        });
      }
    },
    classNote: {
      get() {
        return parseInt(this.editEc.classNote);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "classNote",
          value: value.length == 0 ? '' : parseInt(value),
        });
      }
    }
    
  },

  methods: {
    ...mapActions(["setEditEcProperty"]),
  },

  created() {},

  mounted() {},

  watch: {},
};
</script>