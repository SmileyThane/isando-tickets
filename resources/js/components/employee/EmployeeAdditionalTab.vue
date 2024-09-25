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
        <InternalBillingCard :onSetContactName="handleSetContactName" />
      </v-col>
      <v-col cols="6">
        <ProductInfoCard :onSetContactName="handleSetContactName" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import InternalBillingCard from './cards/EmplyeeInternalBillingCard.vue';
import ProductInfoCard from './cards/EmployeeProductInfoCard.vue';

export default {
  name: 'EmployeeAdditionalTab',
  components: { InternalBillingCard, ProductInfoCard },
  props: {
    onSetContactName: {
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
