<template>
  <v-container fluid>
    <v-tabs v-model="employeeActiveTab" :color="themeBgColor" align-with-title>
      <v-tab
        v-for="(item, index) in items"
        @click="handleChangeCustomerTab(index)"
        :key="item"
      >
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="employeeActiveTab">
      <v-tab-item v-for="item in items" :key="item">
        <v-card flat>
          <basic-employee
            @onSetContactName="onSetContactName"
            v-if="item === 'basic'"
          />
          <tickets :contactName="contactName" v-else />
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>

<script>
import BasicEmployee from './basic';
import tickets from './tickets.vue';

export default {
  components: {
    tickets: tickets,
    'basic-employee': BasicEmployee,
  },
  data() {
    return {
      items: ['basic', 'tickets'],
      contactName: '',
      employeeActiveTab: this.tab,
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
    };
  },
  methods: {
    onSetContactName(name) {
      this.contactName = name;
    },
    handleChangeCustomerTab(key) {
      this.employeeActiveTab = key;
      this.$router.push({
        name: 'individual',
        query: {
          ...this.$route.query,
          customerTab: key,
        },
      });
    },
  },
};
</script>
