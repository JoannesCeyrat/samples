<template>
  <v-row no-gutter>
    <!-- saisie des images -->

    <v-col cols="12">
      <v-card flat>
        <v-card-text>
          <droparea cible="images"></droparea>
        </v-card-text>
      </v-card>
    </v-col>

    <!-- thumbs -->
    <v-col cols="12">
      <!-- loaders -->
        <v-row>
        <v-col v-for="(D, index) of images_upload" :key="'l_'+index" cols="12" xs="12" sm="6" md="4" lg="3">
            <v-card>
                <v-card-text>
                    <v-progress-linear indeterminate color="grey darken-2"></v-progress-linear>
                </v-card-text>
                <v-card-text>
                    <v-card flat>
                    <v-img
                        src="https://www.innov-home.fr/images-meubles/vignettes/0_loading-vignette.jpg"
                        aspect-ratio="1.5"
                    >
                    </v-img>
                    </v-card>
                </v-card-text>
                <v-card-text>
                    <v-text-field v-model="D.name" label="En cours de traitement" disabled></v-text-field>
                </v-card-text>
            </v-card>
        </v-col>
      </v-row>

      <draggable class="row" v-model="images" :sort="true">
        <!-- images -->
        <v-col
          v-for="(I, index) of images"
          :key="'v_'+index"
          cols="12"
          xs="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card>
            <v-card-text>
              <v-card flat>
                <v-img
                  :src="'https://www.innov-home.fr/images-meubles/vignettes/'+I.nom"
                  aspect-ratio="1.5"
                >
                  <v-row align="start" style="float:right; margin-right:0rem">
                    <v-btn class="mx-2" fab dark small color="red" @click="deleteImgByIndex(index)">
                      <v-icon dark>mdi-delete</v-icon>
                    </v-btn>
                  </v-row>
                  <template v-slot:placeholder>
                    <v-row class="fill-height ma-0" align="center" justify="center">
                      <v-progress-circular indeterminate color="grey darken-2"></v-progress-circular>
                    </v-row>
                  </template>
                </v-img>
              </v-card>
            </v-card-text>
            <v-card-text>
              <v-text-field v-model="I.alt" label="légende" @blur="sendLegende"></v-text-field>
            </v-card-text>
          </v-card>
        </v-col>
      </draggable>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import droparea from "./Droparea";
import draggable from "vuedraggable";

export default {
  name: "images",
  mixins: [],
  components: { droparea, draggable },

  data: () => ({
  }),

  props: [],

  computed: {
    ...mapGetters(["editEc", "upload_ec", "has_uploaded_once", "images_upload"]),
    images: {
      //return this.editEc.images

      get() {
        return this.editEc.images;
        /** 
            let a= []
            for (let I of this.editEc.images) {
                a.push(I)
            }
            return a
            */
      },
      set(value) {
        this.setEditEcProperty({
          prop: "images",
          value: value,
        });
      },
    },
  },

  methods: {
    ...mapActions(["setEditEcProperty", "sendImage", "addImage", "majImagesListe"]),
    sendLegende() {
      //console.log(this.images);
      this.majImagesListe()
    },
    deleteImgByIndex(index) {
      this.editEc.images.splice(index, 1);
      this.setEditEcProperty({
        prop: "images",
        value: this.editEc.images,
      });
      this.majImagesListe()
    },
    upload_next() {
      let formData = new FormData();
      formData.append("fichier", this.images_upload[0]);
      let adr =
        "https://www.innov-home.fr/etmoi/" +
        window.user.uid +
        "/bien/" +
        this.editEc.bid +
        "/addImage";
      this.sendImage({
        u: adr,
        f: formData,
        n: this.images_upload[0]["name"],
      });
    },
  },

  created() {
    this.$root.$on("ajout_documents", (obj) => {
        if (obj.cible == "images") {
            // emis par droparea
            //this.document_upload= []
            for (let d of obj.documents) {
                this.addImage(d);
            }
        }
    });
  },

  mounted() {},

  watch: {
    images_upload(val) {
      if (val.length > 0 && !this.upload_ec) {
        this.upload_next();
      }
    },
  },
};
</script>