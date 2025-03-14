<template>
  <v-app-bar :color="themeBgColor" app dark>
    <v-app-bar-nav-icon
      v-show="!localDrawer"
      @click.stop="localDrawer = !localDrawer"
      :color="themeFgColor"
    ></v-app-bar-nav-icon>

    <v-icon v-if="isBackButtonVisible" :color="themeFgColor" @click="back()">
      mdi-arrow-left
    </v-icon>
    <v-spacer></v-spacer>
    <v-toolbar-title :style="`color: ${themeFgColor};`">{{
      this.$store.state.pageName
    }}</v-toolbar-title>
    <v-spacer></v-spacer>
    <v-menu bottom left>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon :color="themeFgColor">mdi-dots-vertical</v-icon>
        </v-btn>
      </template>

      <v-list>
        <v-list-item link @click="showUser">
          <v-list-item-title>{{
            this.$store.state.lang.lang_map.sidebar.profile
          }}</v-list-item-title>
          <v-list-item-action>
            <v-icon>mdi-account-settings</v-icon>
          </v-list-item-action>
        </v-list-item>
        <v-list-item link @click="logout">
          <v-list-item-title>{{
            this.$store.state.lang.lang_map.sidebar.logout
          }}</v-list-item-title>
          <v-list-item-action>
            <v-icon>mdi-logout</v-icon>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-menu>
    <v-chip class="ma-3" color="white" text-color="black">
      <v-avatar left>
        <v-img :src="avatar" />
      </v-avatar>
      <v-label class="d-sm-none d-md-flex">{{ username }}</v-label>
    </v-chip>
  </v-app-bar>
</template>

<script>
import EventBus from './EventBus';

//back button logic
const backButtonRoutes = {
  'ticket/': '/tickets',
  'customer/': '/customer',
  'employee/': '/employee',
  'product/': '/product',
  'projects/': '/tracking/projects',
  'notify_history/': '/notify_history',
  'company/': '/company',
  'team/': '/team',
};

export default {
  name: 'Header',
  props: { value: { type: Boolean } },
  data() {
    return {
      username:
        localStorage.getItem('name') + ' ' + localStorage.getItem('surname'),
      avatar: localStorage.getItem('avatar'),
      localDrawer: null,
      searchCategories: [
        {
          id: 1,
          name: 'ID',
        },
        {
          id: 2,
          name: 'Subject',
        },
        {
          id: 3,
          name: 'Contact',
        },
      ],
      searchLabel: this.$store.state.lang.lang_map.main.search,
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
      isBackButtonVisible: false,
      backButtonPath: '',
    };
  },
  watch: {
    value: function () {
      this.localDrawer = this.value;
    },
    localDrawer: function () {
      this.$emit('input', this.localDrawer);
    },
    $route(to) {
      if (this.$route.path.includes('tickets')) {
        localStorage.setItem('ticketsPath', JSON.stringify(to.query));
      }

      if (this.$route.path.includes('customer')) {
        localStorage.setItem('customerTicketsPath', JSON.stringify(to.query));
      }

      if (
        !this.$route.path.includes('tickets') ||
        !this.$route.path.includes('ticket')
      ) {
        localStorage.removeItem('ticketsPath');
      }

      if (!this.$route.path.includes('customer')) {
        localStorage.removeItem('customerTicketsPath');
      }

      for (const [path, backPath] of Object.entries(backButtonRoutes)) {
        if (this.$route.path.includes(path)) {
          this.isBackButtonVisible = true;
          this.backButtonPath = backPath;
          return;
        }
      }

      this.isBackButtonVisible = false;
      this.backButtonPath = '';
    },
  },
  mounted() {
    if (this.$route.path.includes('notify/')) {
      this.isBackButtonVisible = true;
      this.backButtonPath = '/notify';
    }
    for (const [path, backPath] of Object.entries(backButtonRoutes)) {
      if (this.$route.path.includes(path)) {
        this.isBackButtonVisible = true;
        return;
      }
    }
    this.getUsername();
    let that = this;
    EventBus.$on('update-theme-fg-color', function (color) {
      that.themeFgColor = color;
    });
    EventBus.$on('update-theme-bg-color', function (color) {
      that.themeBgColor = color;
    });
  },
  methods: {
    showUser() {
      location.href = '/user';
    },
    getUsername() {
      axios.get('/api/user').then((response) => {
        response = response.data;
        if (response.success === true) {
          this.username = response.data.name + ' ' + response.data.surname;
          this.avatar = response.data.avatar_url;
        }
      });
    },
    back() {
      if (this.backButtonPath) {
        this.$router.push(localStorage.getItem('backPath') ? JSON.parse(localStorage.getItem('backPath')) : this.backButtonPath );
      } else window.history.back();

      localStorage.removeItem('backPath');
    },
    logout(e) {
      e.preventDefault();
      localStorage.removeItem('auth_token');
      localStorage.removeItem('themeBgColor');
      window.open('/login', '_self');
    },
    selectSearchCategory(item) {
      this.searchLabel = item.name;
    },
  },
};
</script>
