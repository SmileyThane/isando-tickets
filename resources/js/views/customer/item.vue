<template>
  <div>
    <v-tabs v-model="customerActiveTab" :color="themeBgColor" align-with-title>
      <v-tab
        v-for="(item, index) in items"
        @click="handleChangeCustomerTab(index)"
        :key="item"
      >
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="customerActiveTab">
      <v-tab-item v-for="item in items" :key="item">
        <v-card flat>
          <basic-view
            @onSetCompanyName="onSetCompanyName"
            v-if="item === 'basic'"
          />

          <tickets
            :searchValue="companyName"
            searchLabel="Company"
            pageName="customer"
            :searchDisabled="true"
            v-if="item === 'tickets'"
          />

          <CustomerAdditionalTab
            v-if="item === 'additional'"
            @onSetCompanyName="onSetCompanyName"
          />
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </div>
</template>

<script>
import BasicView from './basic.vue';
import TicketsView from '../../components/tickets/tickets.vue';
import CustomerAdditionalTab from '../../components/customer/CustomerAdditionalTab.vue';

export default {
  props: {
    tab: 0,
  },
  components: {
    'basic-view': BasicView,
    tickets: TicketsView,
    CustomerAdditionalTab,
  },
  data() {
    return {
      items: ['basic', 'tickets', 'additional'],
      companyName: '',
      customerActiveTab: this.$route.query.customerActiveTab
        ? parseInt(this.$route.query.customerActiveTab)
        : 0,
      themeBgColor: this.$store.state.themeBgColor,
    };
  },
    mounted() {
        this.getClient(this.$route.params.id);
    },
  methods: {
      getClient() {
          axios.get(`/api/client/${this.$route.params.id}`).then((response) => {
              response = response.data;
              if (response.success === true) {
                  this.onSetCompanyName(response.data.supplier_name);
              }
          });
      },
    onSetCompanyName(name) {
      this.companyName = name;
    },
    handleChangeCustomerTab(key) {
      if (key !== this.$route.query.customerActiveTab) {
        this.$router.push({
          name: 'customer',
          query: {
            ...this.$route.query,
            customerActiveTab: key,
          },
        });
      }
    },
  },
  watch: {
    $route() {
      if (this.$route.query.customerActiveTab) {
        this.customerActiveTab = parseInt(this.$route.query.customerActiveTab);
      }

      if (typeof this.$route.query.customerActiveTab === 'undefined') {
        this.customerActiveTab = 0;
      }
    },
  },
};
</script>
<script setup></script>
