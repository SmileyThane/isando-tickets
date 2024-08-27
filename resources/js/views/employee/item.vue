<template>
  <div>
    <v-tabs :color="themeBgColor" v-model="employeeActiveTab" align-with-title>
      <v-tab v-for="item in items" :key="item">
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="employeeActiveTab">
      <v-tab-item v-for="item in items" :key="item">
        <v-card flat>
          <basic-view v-if="item === 'basic'" />
          <tickets v-else />
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </div>
</template>

<script>
import BasicView from './basic.vue';
import TicketsView from '../ticket/index.vue';

export default {
  components: {
      'basic-view': BasicView,
      'tickets': TicketsView,
  },
  data() {
    return {
      themeBgColor: this.$store.state.themeBgColor,
      employeeActiveTab: 'basic',
      items: ['basic', 'tickets'],
    };
  },
};
</script>
<script setup></script>
