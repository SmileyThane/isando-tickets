<template>
  <v-card>
    <v-toolbar :color="themeBgColor" dark dense flat>
      <v-toolbar-title :style="`color: ${themeFgColor};`"
        >{{ langMap.product.info }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-card-text>
      <v-data-table
        :footer-props="footerProps"
        :headers="productHeaders"
        :items="userData.employee.assigned_to_products"
        :options.sync="options"
        class="elevation-1"
        dense
        item-key="id"
        @update:options="updateItemsPerPage"
      >
        <template v-slot:item.actions="{ item }">
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                v-bind="attrs"
                @click="showProduct(item.product_data)"
                v-on="on"
              >
                <v-icon small>mdi-eye</v-icon>
              </v-btn>
            </template>
            <span>{{ langMap.customer.show_product }}</span>
          </v-tooltip>
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                v-bind="attrs"
                @click="showDeleteProductDlg(item)"
                v-on="on"
              >
                <v-icon small>mdi-link-off</v-icon>
              </v-btn>
            </template>
            <span>{{ langMap.product.unlink_product }}</span>
          </v-tooltip>
        </template>
      </v-data-table>

      <v-spacer>&nbsp;</v-spacer>

      <v-expansion-panels>
        <v-expansion-panel @click="resetProduct">
          <v-expansion-panel-header>
            {{ langMap.product.add_new }}
            <template v-slot:actions>
              <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`"
                >mdi-plus
              </v-icon>
            </template>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-form>
              <div class="row">
                <v-col cols="md-12">
                  <v-autocomplete
                    v-model="employeeProductForm.product_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="products"
                    :label="langMap.main.products"
                    item-text="name"
                    item-value="id"
                  />
                </v-col>
                <v-btn
                  :color="themeBgColor"
                  bottom
                  dark
                  fab
                  right
                  @click="addProductEmployee"
                >
                  <v-icon
                    :color="themeBgColor"
                    :style="`color: ${themeFgColor};`"
                  >
                    mdi-plus
                  </v-icon>
                </v-btn>
              </div>
            </v-form>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
  </v-card>
</template>

<script>
import moment from 'moment-timezone';
import EventBus from '../../EventBus.vue';

export default {
  name: 'ProductInfoCard',
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
      footerProps: {
        showFirstLastPage: true,
        itemsPerPageOptions: [
          {
            text: 10,
            value: 10,
          },
          {
            text: 20,
            value: 20,
          },
          {
            text: 50,
            value: 50,
          },
          {
            text: 100,
            value: 100,
          },
          {
            text: this.$store.state.lang.lang_map.sidebar.all,
            value: 10000,
          },
        ],
      },
      productHeaders: [
        {
          text: 'ID',
          align: 'start',
          sortable: false,
          value: 'product_data.id',
        },
        {
          text: `${this.$store.state.lang.lang_map.main.name}`,
          value: 'product_data.name',
        },
        {
          text: `${this.$store.state.lang.lang_map.main.description}`,
          value: 'product_data.description',
        },
        {
          text: `${this.$store.state.lang.lang_map.main.actions}`,
          value: 'actions',
          sortable: false,
        },
      ],
      userData: {
        id: '',
        title: '',
        title_before_name: '',
        surname: '',
        country: '',
        anredeform: '',
        lang: '',
        name: '',
        email: '',
        phones: [],
        addresses: [],
        emails: [],
        socials: [],
        status: '',
        notification_statuses: [],
        number: '',
        avatar_url: '',
        deleted_at: null,
        filter_groups: [],
        employee: {
          notes: '',
        },
        billing: null,
      },
      options: {
        itemsPerPage: localStorage.itemsPerPage
          ? parseInt(localStorage.itemsPerPage)
          : 10,
      },
      deleteProductDlg: false,
      selectedProductId: null,
      products: [],
      productsSearch: '',
      employeeProductForm: {
        company_user_id: null,
        product_id: null,
      },
      primaryEmailId: null,
      companies: [],
      employeeForm: {
        client_id: null,
        company_user_id: null,
        description: '',
      },
      activityForm: {
        model_id: null,
        model_type: 'App\\CompanyUser',
        date: moment().format('YYYY-MM-DD'),
        time: moment().format('H:m'),
        files: null,
      },
      notificationStatuses: [],
      avatar: '',
    };
  },
  mounted() {
    this.getUser();
    this.getProducts();
    let that = this;
    EventBus.$on('update-theme-fg-color', function (color) {
      that.themeFgColor = color;
    });
    EventBus.$on('update-theme-bg-color', function (color) {
      that.themeBgColor = color;
    });
  },
  methods: {
    getUser() {
      axios.get(`/api/user/find/${this.$route.params.id}`).then((response) => {
        response = response.data;
        if (response.success === true) {
          this.$emit('onSetContactName', response.data.email);
          this.userData = response.data;
          this.primaryEmailId = this.userData.email_id;
          const employeeCompanies =
            this.userData.employee.assigned_to_clients.filter(
              (item) => item.deleted_at === null
            );
          this.companies =
            employeeCompanies.length > 0
              ? employeeCompanies
              : this.userData.companies;
          this.employeeForm.company_user_id = this.userData.employee.id;
          this.activityForm.model_id = this.userData.employee.id;
          if (this.userData.notification_statuses.length) {
            let that = this;
            this.userData.notification_statuses.forEach(function (item) {
              if (item.status !== 0) {
                that.notificationStatuses.push(item.status);
              }
            });
          } else {
            this.notificationStatuses = [101, 102, 103, 201, 202, 301, 302];
          }

          if (this.userData.avatar_url) {
            this.avatar = this.userData.avatar_url;
          } else {
            this.avatar =
              'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgDTD2qgAAAAASUVORK5CYII=';
          }
          this.userData.filter_groups = this.userData.client_filter_groups.map(
            (group) => {
              return group.data.name;
            }
          );
        } else {
          this.snackbarMessage = this.langMap.main.generic_error;
          this.actionColor = 'error';
          this.snackbar = true;
        }
      });
    },
    getProducts() {
      this.loadingProducts = this.themeBgColor;
      axios
        .get(
          `/api/product?
                    search=${this.productsSearch}&
                    sort_by=name&
                    sort_val=false&
                    `
        )
        .then((response) => {
          response = response.data;
          if (response.success === true) {
            this.products = response.data.data;
            this.totalProducts = response.data.total;
            this.lastPage = response.data.last_page;
            this.loadingProducts = false;
          } else {
            console.log('error');
          }
        });
    },
    updateItemsPerPage(options) {
      localStorage.itemsPerPage = options.itemsPerPage;
    },
    showProduct(item) {
      this.$router.push(`/product/${item.id}`);
    },
    showDeleteProductDlg(item) {
      this.selectedProductId = item.id;
      this.deleteProductDlg = true;
    },
    resetProduct() {
      this.employeeProductForm.product_id = null;
      this.employeeProductForm.company_user_id = this.userData.employee.id;
    },
    addProductEmployee() {
      axios
        .post(
          `/api/product/${this.employeeProductForm.product_id}/employee`,
          this.employeeProductForm
        )
        .then((response) => {
          response = response.data;
          if (response.success === true) {
            this.getUser();
            this.resetProduct();
          } else {
            console.log('error');
          }
        });
    },
  },
};
</script>
