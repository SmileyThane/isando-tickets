<template>
  <v-container fluid>
    <v-row>
      <v-snackbar
        v-model="snackbar"
        :bottom="true"
        :color="actionColor"
        :right="true"
      >
        {{ snackbarMessage }}
      </v-snackbar>

      <v-col cols="6">
        <CustomerInternalBillingCard :onSetContactName="handleSetContactName" />
      </v-col>
      <v-col cols="6">
        <CustomerProductInfoCard :onSetContactName="handleSetContactName" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import CustomerInternalBillingCard from './cards/CustomerInternalBillingCard.vue';
import CustomerProductInfoCard from './cards/CustomerProductInfoCard.vue';

export default {
  name: 'CustomerAdditionalTab',
  components: { CustomerProductInfoCard, CustomerInternalBillingCard },
  props: {
    onSetCompanyName: {
      type: Function,
      default: null,
    },
  },
  data() {
    return {
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
      langMap: this.$store.state.lang.lang_map,
      snackbar: false,
      actionColor: '',
      snackbarMessage: '',
    };
  },
  methods: {
    handleSetContactName(email) {
      this.$emit('onSetContactName', email);
    },
  },
};
</script>
