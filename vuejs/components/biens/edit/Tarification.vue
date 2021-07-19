<template>
  <v-row no-gutter>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="l1" label="1 personne" required></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="l2" label="2 personnes"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="l3" label="3 personnes"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="l4" label="4 personnes"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="l5" label="5 personnes"></v-text-field>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "tarification",
  mixins: [],
  components: {},

  data: () => ({}),

  props: [],

  computed: {
      ...mapGetters(["editEc"]),
        l1: {
            get() {
                return this.editEc.loyer;
            },
            set(value) {
                this.setEditEcProperty({
                    prop: "loyer",
                    value: value,
                });
            }
        },
        l2: {
            get() {
                return this.editEc.loyer2;
            },
            set(value) {
                this.setEditEcProperty({
                    prop: "loyer2",
                    value: value,
                });
            }
        },
        l3: {
            get() {
                return this.editEc.loyer3;
            },
            set(value) {
                this.setEditEcProperty({
                    prop: "loyer3",
                    value: value,
                });
            }
        },
        l4: {
            get() {
                return this.editEc.loyer4;
            },
            set(value) {
                this.setEditEcProperty({
                    prop: "loyer4",
                    value: value,
                });
            }
        },
        l5: {
            get() {
                return this.editEc.loyer5;
            },
            set(value) {
                this.setEditEcProperty({
                    prop: "loyer5",
                    value: value,
                });
            }
        },
  },

  methods: {
      ...mapActions(["setEditEcProperty"]),
  },

  created() {},

  mounted() {},

  watch: {},
};
</script>