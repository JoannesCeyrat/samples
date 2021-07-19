import Vue from 'vue';
import Vuetify from 'vuetify/lib';
import colors from 'vuetify/lib/util/colors'

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
            light: {
                primary: "#e2007a", // #E53935
                secondary: colors.pink.lighten1, // #FFCDD2
                accent: colors.pink, // #3F51B5
            },
        },
    },
});
