<template ref="calenBien">
  <v-sheet min-height="100%">
    <v-toolbar dark color="#C9006B" :src="main_dialog.data.hero" tile extended>
      <v-toolbar-title class="textShadowed">
        <h3>Calendrier bien {{ main_dialog.data.bid }}</h3>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-card flat color="transparent">
              <v-card-actions>
                <v-btn
                  class="mx-2"
                  fab
                  large
                  color="#e2007a"
                  @click="sendPeriode"
                  v-on="on"
                  dark
                  :loading="btn_traiter.is_sending"
                >
                  <v-icon large>mdi-content-save</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>
          </template>
          <span>Bloquer la période</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              dark
              @click="setMainDialog({ cle: 'open', value: false })"
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
            padding: '0.2rem 0.75rem',
          }"
        >
          <v-card flat>
            <!-- calendrier récap -->
            <v-card-actions
              class="hand"
              @click="showLegende = showLegende ? false : true"
              >{{ showLegende ? "Masquer" : "Afficher" }} la
              légende</v-card-actions
            >
            <v-card-text v-show="showLegende">
              <v-row>
                <v-col cols="12" md="6">
                  <v-row>
                    <v-col
                      cols="3"
                      :style="{ 'background-color': couleurs.primaire }"
                    ></v-col>
                    <v-col cols="9">Réservation depuis INNOV-HOME</v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" md="6">
                  <v-row>
                    <v-col
                      cols="3"
                      :style="{ 'background-color': couleurs.bloqueTiers }"
                    ></v-col>
                    <v-col cols="9">Réservation depuis site tiers</v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" md="6">
                  <v-row>
                    <v-col
                      cols="3"
                      :style="{ 'background-color': couleurs.bloqueProprio }"
                    ></v-col>
                    <v-col cols="9">Blocage propriétaire</v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" md="6">
                  <v-row>
                    <v-col
                      cols="3"
                      :style="{ 'background-color': couleurs.bloqueCons }"
                    ></v-col>
                    <v-col cols="9">Blocage conseiller</v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" md="6">
                  <v-row>
                    <v-col
                      cols="3"
                      :style="{ 'background-color': couleurs.rembourse }"
                    ></v-col>
                    <v-col cols="9">Demande remboursée bien non libre</v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" md="6">
                  <v-row>
                    <v-col
                      cols="3"
                      :style="{ 'background-color': couleurs.complementaireM1 }"
                    ></v-col>
                    <v-col cols="9">Devis en cours</v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
            <!-- navigation calendrier -->
            <v-card-text>
              <v-row>
                <v-col cols="2" sm="1" style="height: 54px">
                  <v-btn class="mx-2" fab small @click="calendrierPrev()">
                    <v-icon large>mdi-chevron-left</v-icon>
                  </v-btn>
                </v-col>
                <v-col cols="8" sm="10" style="height: 54px"
                  ><h3 class="text-center pt-3">
                    {{ calendrier.title }}
                  </h3></v-col
                >
                <v-col cols="2" sm="1" style="height: 54px">
                  <v-btn class="mx-2" fab small @click="calendrierNext()">
                    <v-icon large>mdi-chevron-right</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-text>
              <v-row>
                <v-col>
                  <v-sheet>
                    <v-calendar
                      ref="calendar"
                      v-model="calendarValue"
                      type="month"
                      :now="today"
                      :value="today"
                      :weekdays="weekday"
                      :show-week="sm"
                      first-day-of-week="1"
                      locale="FR"
                      locale-first-day-of-year="4"
                      :color="couleurs.calendarNow"
                      :style="objStyle"
                      :min="today"
                      @click:date="btnDateClick"
                      @change="changeListener"
                    >
                      <template v-slot:day="{ date }">
                        <v-row class="fill-height">
                          <template
                            v-if="
                              tracked[date] &&
                              tracked[date]['sheets'] != undefined
                            "
                          >
                            <v-sheet width="50%" height="100%">
                              <div
                                :title="
                                  tracked[date]['sheets']['sheet1']['name']
                                "
                                :style="{
                                  'background-color':
                                    tracked[date]['sheets']['sheet1']['color'],
                                  width:
                                    tracked[date]['sheets']['sheet1']['width'],
                                  height: '100%',
                                }"
                                tile
                                dark
                                :class="
                                  tracked[date]['sheets']['sheet1']['class']
                                "
                                @click="clickEditSejour(date, 'sheet1')"
                              ></div>
                            </v-sheet>
                            <v-sheet width="50%" height="100%">
                              <div
                                :title="
                                  tracked[date]['sheets']['sheet2']['name']
                                "
                                :style="{
                                  'background-color':
                                    tracked[date]['sheets']['sheet2']['color'],
                                  width:
                                    tracked[date]['sheets']['sheet2']['width'],
                                  height: '100%',
                                }"
                                tile
                                dark
                                :class="
                                  tracked[date]['sheets']['sheet2']['class']
                                "
                                @click="clickEditSejour(date, 'sheet2')"
                              ></div>
                            </v-sheet>
                          </template>
                        </v-row>
                      </template>
                    </v-calendar>
                    <!-- 
                        -- menu card ajout de sejour 
                        -->
                    <v-dialog
                      v-model="menuOver.open"
                      width="500"
                      @click:outside="dismissNewSejour"
                    >
                      <v-card color="grey lighten-4" min-width="330px" flat>
                        <v-toolbar :color="menuOver.color" dark>
                          <v-btn icon>
                            <v-icon>mdi-calendar-plus</v-icon>
                          </v-btn>
                          <v-toolbar-title
                            v-html="menuOver.title"
                          ></v-toolbar-title>
                          <v-spacer></v-spacer>
                        </v-toolbar>
                        <!-- inputs -->
                        <v-card-text>
                          <editSejourCalendrier
                            :index="indexSejourEc"
                          ></editSejourCalendrier>
                        </v-card-text>
                      </v-card>
                    </v-dialog>
                  </v-sheet>
                </v-col>
              </v-row>
            </v-card-text>

            <!-- bloquer -->
          </v-card>

          <div style="height: 1rem"></div>
        </div>
      </v-col>
    </v-row>
  </v-sheet>
</template>

<style>
.v-calendar-weekly__weeknumber,
.v-calendar-weekly__head-weeknumber,
.v-calendar-weekly__head-weekday,
.v-calendar-weekly__head-weekday {
  background-color: "#fff"; /**var(--primary) !important ; */
  color: var(--primary) !important ;
  /** font-size: 1rem; **/
  border-bottom: 1px solid #e0e0e0;
  padding: 1rem 0;
  /** min-width: 3rem; **/
}
.pl-1 {
  padding: 0.6rem;
  font-size: 0.95rem;
}
.fill-height {
  min-height: "40px";
}
.v-calendar-weekly__day {
  min-height: 85px;
}

.v-event {
  margin: 0.3rem 2px !important;
}
.calenCell {
  height: 100%;
}
</style>

<script>
import { mapGetters, mapActions } from "vuex";
import editSejourCalendrier from "./EditSejourCalendrier";
import moment from "moment";

moment.locale("fr");

export default {
  name: "CalenBien",
  components: {
    editSejourCalendrier,
  },
  data: () => ({
    panel_ec: undefined,
    weekday: [1, 2, 3, 4, 5, 6, 0],
    sm: true,
    heightEvent: 40,
    calendarValue: "",
    objCalendar: undefined, // watched
    indexSejourEc: null,
    menuOver: {
      open: false,
      from: undefined,
      color: undefined,
      title: "",
      to: undefined,
    },
    joursAffiches: undefined,
    showLegende: false
  }),
  computed: {
    ...mapGetters([
      "main_dialog",
      "hauteur_iframe",
      "btn_traiter",
      "couleurs",
      "periode",
      "logged_user_is_admin",
      "semainesLouees",
      "calendrierDroits",
    ]),
    today() {
      let D = new moment();
      return D.format("YYYY-MM-DD");
    },
    objStyle() {
      return {
        "--primary": this.couleurs.primaire,
      };
    },
    calendrier() {
      return {
        title: !!this.objCalendar ? this.objCalendar.title : "",
      };
    },
    couleur_blocage() {
      return this.logged_user_is_admin > 0
        ? this.couleurs.bloqueCons
        : this.couleurs.bloqueProprio;
    },
    userDroits() {
      return this.logged_user_is_admin == 1 ? "cons" : "prop";
    },
    tracked() {
      //return this.periode.sejours
      if (this.joursAffiches == undefined) {
        return {};
      }
      let a = {};
      for (let d of this.joursAffiches) {
        a[d] = { sheets: undefined };
      }

      for (let S of this.periode.sejours) {
        let Ms = moment(S.start);
        let Me = moment(S.end);
        let duration = Me.diff(Ms, "days");
        for (let i = 0; i <= duration; i++) {
          let Mt = moment(S.start).add(i, "days");
          let indexJour = Mt.format("YYYY-MM-DD");
          if (this.joursAffiches.indexOf(indexJour) < 0) {
            continue;
          }
          if (
            !!S["action"] &&
            String(S["action"]).localeCompare("delete") == 0
          ) {
            a[indexJour]["sheets"] = undefined;
          } else {
            if (a[indexJour]["sheets"] == undefined) {
              a[indexJour]["sheets"] = {
                sheet1: {
                  width: i < duration ? "100%" : "75%",
                  color: i == 0 ? "transparent" : S.color,
                  type: i == 0 ? undefined : S.type,
                  name: i == 0 ? "" : S.name,
                  reponse:  S.reponse,
                  class:
                    i == 0
                      ? "float-left calenCell"
                      : "hand float-left calenCell",
                },
                sheet2: {
                  width: i == 0 ? "75%" : "100%",
                  color: i < duration ? S.color : "transparent",
                  type: i < duration ? S.type : undefined,
                  name: i < duration ? S.name : "",
                  reponse: S.reponse,
                  class:
                    i == 0
                      ? "hand float-right calenCell"
                      : "float-left calenCell",
                },
              };
            } else {
              if (a[indexJour]["sheets"]["sheet1"]["type"] == undefined) {
                a[indexJour]["sheets"]["sheet1"] = {
                  width: i < duration ? "100%" : "75%",
                  color: i == 0 ? "transparent" : S.color,
                  type: i == 0 ? undefined : S.type,
                  name: i == 0 ? "" : S.name,
                  reponse:  S.reponse,
                  class:
                    i == 0
                      ? "float-left calenCell"
                      : "hand float-left calenCell",
                };
              }
              if (a[indexJour]["sheets"]["sheet2"]["type"] == undefined) {
                a[indexJour]["sheets"]["sheet2"] = {
                  width: i == 0 ? "75%" : "100%",
                  color: i < duration ? S.color : "transparent",
                  type: i < duration ? S.type : undefined,
                  name: i < duration ? S.name : "",
                  reponse: S.reponse,
                  class:
                    i == 0
                      ? "hand float-right calenCell"
                      : "float-left calenCell",
                };
              }
            }
          }
        }
      }

      return a;
    },
  },
  props: [],
  methods: {
    ...mapActions([
      "setMainDialog",
      "AddSejourPeriode",
      "setContainerSejour",
      "setCallbackSejour",
      "actionRemoveSejourFromPeriode",
      "alertDate",
      "alertBug",
      "sendPeriode",
      "displayBailFromCalendier"
    ]),
    getIndexByName(name) {
      for (let i = 0; i < this.periode.sejours.length; i++) {
        if (this.periode.sejours[i]["name"] == name) {
          return i;
        }
      }
      return -1;
    },
    calendrierNext() {
      if (this.objCalendar) {
        this.$refs.calendar.next();
      } else {
        return;
      }
    },
    calendrierPrev() {
      if (this.objCalendar) {
        this.$refs.calendar.prev();
      } else {
        return;
      }
    },
    btnDateClick(obj) {
      if (obj.past) {
        this.alertDate("Cette date est passée, vous ne pouvez l'éditer.");
        return;
      }

      let M = moment(obj.date);
      let reg = /^blocage/i;
      // cette date a des sheets on ouvre l'édit
      let Sheets = this.tracked[M.format("YYYY-MM-DD")]["sheets"];
      if (Sheets != undefined) {
        if (
          Sheets["sheet1"]["type"] != undefined &&
          Sheets["sheet2"]["type"] != undefined
        ) {
          // les 2 sheets sont non éditables
          if (
            !reg.test(Sheets["sheet1"]["type"]) &&
            !reg.test(Sheets["sheet2"]["type"])
          ) {
            this.alertDate("Vous ne pouvez editer cette période.");
            return;
          }
          // edit by name
          else if (reg.test(Sheets["sheet1"]["type"])) {
            this.editPeriodeByName(Sheets["sheet1"]["name"]);
            return;
          } else if (reg.test(Sheets["sheet2"]["type"])) {
            this.editPeriodeByName(Sheets["sheet2"]["name"]);
            return;
          }
        } else if (Sheets["sheet2"]["type"] != undefined) {
          // la  sheet2  non éditable
          if (!reg.test(Sheets["sheet2"]["type"])) {
            this.alertDate("Vous ne pouvez editer cette période.");
            return;
          }
          // edit by name
          else if (reg.test(Sheets["sheet2"]["type"])) {
            this.editPeriodeByName(Sheets["sheet2"]["name"]);
            return;
          }
        }
      }
      /** 
      if (this.semainesLouees.indexOf(M.week()) >= 0) {
         this.alertDate('Vous ne pouvez bloquer cette date car elle appartient à une semaine déjà louée.')
         return;
      }
      */
      // pas de sheets, on ajoute une période
      let index = this.periode.sejours.length;
      this.AddSejourPeriode({
        name: "Nouveau " + index,
        start: obj.date,
        end: obj.date,
        color: this.couleur_blocage,
        timed: 0,
        action: "add", // edit || delete || add => mappé à un getter de l'action blocage / déblocage
        detail: [],
        type: this.logged_user_is_admin == 1 ? "BlocageCons" : "BlocageProp",
        server: false,
        isEditing: false,
      });
      this.menuOver = {
        open: true,
        from: obj.date,
        color: this.couleur_blocage,
        title: "Bloquer une période",
        to: obj.date,
      };
      this.indexSejourEc = index;
    },
    /** 
    eventColor(event) {
      return event.color;
    },
    */
    editPeriodeByName(name) {
      //edition
      // determiner l'index de l'event
      this.indexSejourEc = this.getIndexByName(name);
      if (this.indexSejourEc < 0) {
        this.alertBug("Erreur identification numéro de séjour.");
        return;
      }
      // mettre à jour isEditing => on le met sur true pour eviter effacement de l'event lors de dismiss
      this.$store.commit("set_sejour_periode", {
        index: this.indexSejourEc,
        propriete: "isEditing",
        valeur: true,
      });
      // ouvrir la dialog box sur le sejour courant
      let S = this.periode.sejours[this.indexSejourEc];
      this.menuOver = {
        open: true,
        from: S.start,
        color: S.color,
        title: "Editer la période " + S.name,
        to: S.end,
      };
    },
    changeListener(event) {
      this.setJoursAffiches(event.start.date, event.end.date);
      //this.getCalendrier(event);
    },
    dismissNewSejour() {
      if (!this.periode.sejours[this.indexSejourEc]["isEditing"]) {
        this.actionRemoveSejourFromPeriode(this.indexSejourEc);
      }
    },
    clickEditSejour(laDate, laSheet) {
        
        let voir_bail= ['DemandeResas', 'Devis']
        let M = moment(laDate);
        // date passée
        let Now = moment();
        //
        let Sheets = this.tracked[M.format("YYYY-MM-DD")]["sheets"];
        //console.log(Sheets)
        // sejour non editable
        let regBloc = /^Blocage/i;
        for (let k of Object.keys(Sheets)) {
            //console.log(Sheets[k])
            if (Sheets[k]["type"] === undefined) {
                continue
            }
            if (!regBloc.test(Sheets[k]["type"]) && voir_bail.indexOf(Sheets[k]["type"]) <0) {
                this.alertDate("Vous ne pouvez éditer ce séjour.");
                return;
            } else if (voir_bail.indexOf(Sheets[k]["type"]) >= 0) {
                if (parseInt(Sheets[k]["reponse"]) != 1) {
                    this.alertDate("Seul votre conseiller peut modifier cette réservation.");
                    return;
                }
                // si résa ou devis on montre le bail
                this.displayBailFromCalendier(Sheets[laSheet]["name"])
                return
            }
        }
        // blocage des dates passées
        if (M.isBefore(Now)) {
            this.alertDate("Le jour sur lequel vous venez de cliquer est passé. Merci de choisir un jour à venir");
            return;
        }
        // sinon on peut editer
        this.editPeriodeByName(Sheets[laSheet]["name"]);
    },
    setJoursAffiches(start, end) {
      let Ms = moment(start).startOf("week");
      let Me = moment(end).endOf("week");
      this.joursAffiches = [];
      do {
        this.joursAffiches.push(Ms.format("YYYY-MM-DD"));
        Ms.add(1, "days");
      } while (Ms.isBefore(Me));
    },
  },
  watch: {
    $refs() {
      if (this.$refs.calendar) {
        this.objCalendar = $this.$refs.calendar;
      }
    },
  },
  mounted() {
    //this.getBienDatas(this.main_dialog.data.bid);
    this.setCallbackSejour("set_sejour_periode");
    this.setContainerSejour("periode");

    this.$watch(() => {
      if (this.$refs.calendar) {
        this.objCalendar = this.$refs.calendar;
      }
    });
  },

  created() {
    this.$root.$on("setMenuOverOpen", (val) => {
      this.menuOver.open = val;
    });
  },
};
</script>
