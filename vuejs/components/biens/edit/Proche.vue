<template>
  <v-row no-gutter>
    <v-col cols="12">
      <v-textarea
            label="Proche de ..."
            required
            v-model="proche"
            height="350"
            full-width
          ></v-textarea>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";


export default {
  name: "proche",
  mixins: [],
  components: { },

  data: () => ({}),

  props: [ ],

  computed: {
    ...mapGetters(["editEc"]),
    proche: {
      get() {
        return (this.editEc.proche)//.replace(regex, '<br>');
      },
      set(value) {
        this.setEditEcProperty({
          prop: "proche",
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