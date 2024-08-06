<template>
  <v-container fluid>
    <v-snackbar
      v-model="snackbar"
      :bottom="true"
      :color="actionColor"
      :right="true"
    >
      {{ snackbarMessage }}
    </v-snackbar>
    <v-card class="pa-5">
      <v-card-title>Servers</v-card-title>
      <v-card-subtitle>
        This is where you configure all your servers
      </v-card-subtitle>
      <v-card-text class="d-flex justify-space-between align-items-center mb-6">
        <div>
          <v-btn
            :color="themeBgColor"
            class="mb-1"
            :style="`color: ${themeFgColor}`"
            @click="addServer"
          >
            Add new server
          </v-btn>
          <v-btn
            :color="themeBgColor"
            class="mb-1"
            :style="`color: ${themeFgColor}`"
            @click="createGroup"
          >
            Create new group
          </v-btn>
        </div>
        <v-btn
          :color="themeBgColor"
          :style="`color: ${themeBgColor}`"
          class="mb-1"
          icon
          small
        >
          <v-icon :color="themeBgColor"> mdi-reload</v-icon>
        </v-btn>
      </v-card-text>
      <v-card-actions>
        <template>
          <v-card style="width: 100%">
            <v-card-title>
              <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                :color="themeBgColor"
                hide-details
              ></v-text-field>
            </v-card-title>
            <v-data-table
              :headers="headers1"
              :items="desserts"
              :search="search"
            ></v-data-table>
          </v-card>
        </template>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
export default {
  props() {},
  data() {
    return {
      langMap: this.$store.state.lang.lang_map,
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
      snackbar: false,
      actionColor: '',
      snackbarMessage: '',
      search: '',
      headers1: [
        {
          text: 'Dessert (100g serving)',
          align: 'start',
          filterable: false,
          value: 'name',
        },
        { text: 'Calories', value: 'calories' },
        { text: 'Fat (g)', value: 'fat' },
        { text: 'Carbs (g)', value: 'carbs' },
        { text: 'Protein (g)', value: 'protein' },
        { text: 'Iron (%)', value: 'iron' },
      ],
      desserts: [
        {
          name: 'Frozen Yogurt',
          calories: 159,
          fat: 6.0,
          carbs: 24,
          protein: 4.0,
          iron: 1,
        },
        {
          name: 'Ice cream sandwich',
          calories: 237,
          fat: 9.0,
          carbs: 37,
          protein: 4.3,
          iron: 1,
        },
        {
          name: 'Eclair',
          calories: 262,
          fat: 16.0,
          carbs: 23,
          protein: 6.0,
          iron: 7,
        },
        {
          name: 'Cupcake',
          calories: 305,
          fat: 3.7,
          carbs: 67,
          protein: 4.3,
          iron: 8,
        },
        {
          name: 'Gingerbread',
          calories: 356,
          fat: 16.0,
          carbs: 49,
          protein: 3.9,
          iron: 16,
        },
        {
          name: 'Jelly bean',
          calories: 375,
          fat: 0.0,
          carbs: 94,
          protein: 0.0,
          iron: 0,
        },
        {
          name: 'Lollipop',
          calories: 392,
          fat: 0.2,
          carbs: 98,
          protein: 0,
          iron: 2,
        },
        {
          name: 'Honeycomb',
          calories: 408,
          fat: 3.2,
          carbs: 87,
          protein: 6.5,
          iron: 45,
        },
        {
          name: 'Donut',
          calories: 452,
          fat: 25.0,
          carbs: 51,
          protein: 4.9,
          iron: 22,
        },
        {
          name: 'KitKat',
          calories: 518,
          fat: 26.0,
          carbs: 65,
          protein: 7,
          iron: 6,
        },
      ],
    };
  },
  mounted() {},
  methods: {
    addServer() {
      console.log('Add new server');
    },
    createGroup() {
      console.log('Create new group');
    },
  },
  watch: {},
};
</script>
