<template>
  <v-row>
    <!-- dates Pickers -->
    <v-col cols="12" sm="6">
      <v-menu
        ref="menuF"
        v-model="menus.from"
        :close-on-content-click="false"
        :return-value.sync="from"
        transition="scale-transition"
        offset-y
        min-width="260px"
      >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="from"
            label="Du"
            prepend-icon="mdi-calendar"
            readonly
            v-on="on"
            :color="couleurs.primaire"
          ></v-text-field>
        </template>
        <v-date-picker
          v-model="from"
          no-title
          scrollable
          show-week
          first-day-of-week="1"
          locale="FR"
          locale-first-day-of-year="4"
          @change="$refs.menuF.save(from)"
          :allowed-dates="allowedDate"
        ></v-date-picker>
      </v-menu>
    </v-col>
    <v-col cols="12" sm="6">
      <v-menu
        ref="menuT"
        v-model="menus.to"
        :close-on-content-click="false"
        :return-value.sync="to"
        transition="scale-transition"
        offset-y
        min-width="260px"
        content-class="rose"
      >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="to"
            label="Au"
            prepend-icon="mdi-calendar"
            readonly
            v-on="on"
          ></v-text-field>
        </template>
        <v-date-picker
          v-model="to"
          no-title
          scrollable
          show-week
          first-day-of-week="1"
          locale="FR"
          locale-first-day-of-year="4"
          @change="$refs.menuT.save(to)"
          :color="couleurs.primaire"
          :allowed-dates="allowedDate"
        ></v-date-picker>
      </v-menu>
    </v-col>
    <v-row v-if="S != undefined">
        <v-col cols="6"  class="text-left">
            <v-btn class="mx-2" fab small dark @click="efface" :color="S.color"><v-icon>mdi-delete</v-icon></v-btn>
        </v-col>
        <v-col cols="6"  class="text-right">
            <v-btn class="mx-2" fab small dark @click="validate" :color="S.color"><v-icon>mdi-check-bold</v-icon></v-btn>
        </v-col>
    </v-row>
  </v-row>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
import moment from "moment";

export default {
  name: "editSejourCalendrier",
  mixins: [],
  components: {},

  data: () => ({
    menus: {
      from: false,
      to: false,
    },
  }),

  props: ["index"],

  computed: {
    ...mapGetters([
        "periode",
        "couleurs"
    ]),
    S() {
      return this.periode.sejours[this.index];
    },
    // 2 way binding vueEx => footer, address
    from: {
      get() {
        return this.S == undefined ? undefined : this.S.start;
      },
      set(value) {
        this.$store.commit("set_sejour_periode", {
          index: this.index,
          propriete: "start",
          valeur: value,
        });
      },
    },
    to: {
      get() {
        return this.S == undefined ? undefined : this.S.end;
      },
      set(value) {
        this.$store.commit("set_sejour_periode", {
          index: this.index,
          propriete: "end",
          valeur: value,
        });
      },
    },
    arr_sem: {
      get() {
        let a = [];
        let From = moment(this.from);
        let To = moment(this.to);
        if (From == undefined || To == undefined) {
            return a
        }
        /** si le debut est un dimanche et que je séjour dure plus que la nuit de dimanche
         * on ajoute un jour pour aviter la prise en compte de la semaien du dimanche
         */
        if (From.isoWeekday() == 7 && To.diff(From, "days") > 1) {
          From.add(1, "d");
        }
        for (let w = From.isoWeek(); w <= To.isoWeek(); w++) {
          a.push({
            annee: From.year(),
            semaine: w,
            nb_pers: 1,
            loyer: 0,
          });
        }
        return a;
      },
      set(value) {},
    },
    tokenDays() {
      //return this.periode.sejours
      let a = [];
      for (let S of this.periode.sejours) {
        let Ms = moment(S.start);
        let Me = moment(S.end);
        let duration = Me.diff(Ms, "days");
        for (let i = 0; i <= duration; i++) {
          let Mt = moment(S.start).add(i, "days");
          a.push(Mt.format('YYYY-MM-DD'))
        }
      }
      return a
    },
  },

  methods: {
    update_detail() {
      this.$store.commit("set_sejour_periode", {
        index: this.index,
        propriete: "detail",
        valeur: this.arr_sem,
      });
      if (this.periode.sejours[this.index]["server"]) {
        this.$store.commit("set_sejour_periode", {
            index: this.index,
            propriete: "action",
            valeur: "edit",
        })
      }
    },
    validate() {
        this.$root.$emit('setMenuOverOpen', false)
    },
    efface() { 
        if (this.S == undefined) {
          return
        }
        if (this.S.server) { // taguer delete au pour ce séjour
            this.$store.commit("set_sejour_periode", {
                index: this.index,
                propriete: "action",
                valeur: "delete",
            })
        } else { // supprimer de periode.sejours l'index
            this.$store.commit("remove_sejour_periode", this.index)
        }
        // fermer la dialog box
        this.$root.$emit('setMenuOverOpen', false)
    },
    allowedDate(val) {
      if (this.tokenDays.indexOf(val) >= 0) {
        return 0
      }
      let Now= moment()
      let D= moment(val)
      return D.isBefore(Now) ? 0 : 1
    }
  },

  created() {},

  mounted() {},

  watch: {
    from(value) {
        if (value !== undefined) {
            setTimeout(() => {
                this.update_detail();
            }, 200)
        }
    },
    to(value) {
        if (value !== undefined) {
            setTimeout(() => {
                this.update_detail();
            }, 200)
        }
    }
  },
};
</script>