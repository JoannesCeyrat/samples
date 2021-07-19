<template>
  <v-row no-gutter>
    <!-- saisie des images -->

    <v-col cols="12">
      <v-card flat>
        <v-card-text>
          <droparea cible="justificatifs"></droparea>
        </v-card-text>
      </v-card>
    </v-col>

    <!-- thumbs -->
    <v-col cols="12">
      <!-- loaders -->
        <v-row>
        <v-col v-for="(D, index) of documents_upload" :key="'p_'+index" cols="12" xs="12" sm="6" md="4" lg="3">
            <v-card>
                <v-card-text>
                    <v-progress-linear indeterminate color="grey darken-2"></v-progress-linear>
                </v-card-text>
                <v-card-text  align="center" justify="center">
                        <v-icon large>mdi-file</v-icon>
                </v-card-text>
                <v-card-text>
                    <v-text-field v-model="D.name" label="En cours de traitement" disabled></v-text-field>
                </v-card-text>
            </v-card>
        </v-col>
      </v-row>

      <v-row>
        <!-- fichiers -->
        <v-col
          v-for="(J, index) of justificatifs"
          :key="'j_'+index"
          cols="12"
          xs="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card>
                <v-card-text align="center" justify="center">
                    <v-btn class="mx-2" fab dark large color="grey" :href="downloadLinkGenerate(index)" target="blank">
                        <v-icon>mdi-cloud-download</v-icon>
                    </v-btn>
                </v-card-text>
                <v-card-text>
                    <v-text-field v-model="J.nom" label="Fichier" disabled></v-text-field>
                </v-card-text>
            </v-card>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import droparea from "./Droparea";

export default {
  name: "justificatifs",
  mixins: [],
  components: { droparea },

  data: () => ({
  }),

  props: [],

  computed: {
    ...mapGetters(["editEc", "upload_ec", "has_uploaded_once", "documents_upload"]),
    justificatifs() {
        return this.editEc.justificatifs
    },
  },

  methods: {
    ...mapActions(["setEditEcProperty", "sendDocument", "addDocument", "downloadFile"]),
    upload_next() {
      let formData = new FormData();
      formData.append("fichier", this.documents_upload[0]);
      let adr =
        "https://www.innov-home.fr/etmoi/" +
        window.user.uid +
        "/bien/" +
        this.editEc.bid +
        "/addJustificatif";
      this.sendDocument({
        u: adr,
        f: formData,
        n: this.documents_upload[0]["name"],
      });
    },
    downloadLinkGenerate(index) {
        let nom= this.justificatifs[index]["nom"]
        let adr= "https://" + location.hostname + "/justificatif/" + nom
        return adr
    }
  },

  created() {
    this.$root.$on("ajout_documents", (obj) => {
        
        if (obj.cible == "justificatifs") {
            // emis par droparea
            //this.document_upload= []
            for (let d of obj.documents) {
                this.addDocument(d);
            }
        }
    });
  },

  mounted() {},

  watch: {
    documents_upload(val) {
      if (val.length > 0 && !this.upload_ec) {
        this.upload_next();
      }
    },
  },
};
</script>