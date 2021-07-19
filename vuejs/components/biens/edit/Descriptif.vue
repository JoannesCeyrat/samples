<template>
  <v-row no-gutter>
    <v-col cols="12">
      <v-textarea
            label="Descriptif de l'annonce"
            required
            v-model="desc"
            height="350"
            full-width
          ></v-textarea>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";


export default {
  name: "descriptif",
  mixins: [],
  components: { },

  data: () => ({}),

  props: [ ],

  computed: {
    ...mapGetters(["editEc"]),
    desc: {
      get() {
        const regex = /\n/g
        return (this.editEc.descFR)//.replace(regex, '<br>');
      },
      set(value) {
        this.setEditEcProperty({
          prop: "descFR",
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