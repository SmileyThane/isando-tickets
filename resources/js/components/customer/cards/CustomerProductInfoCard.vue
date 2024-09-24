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
        :items="client.products"
        :loading="loadingProducts"
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
                    v-model="supplierForm.product_id"
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
                  @click="addProductClient"
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
  name: 'CustomerProductInfoCard',
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
      loadingProducts: this.themeBgColor,
      loadingEmployees: this.themeBgColor,
      loadingActivities: this.themeBgColor,
      loadingBilling: this.themeBgColor,
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
      client: {
        client_name: '',
        short_name: '',
        client_description: '',
        supplier_object: {},
        products: [
          {
            product_data: {},
          },
        ],
        phones: [],
        addresses: [],
        socials: [],
        employees: [
          {
            employee: {
              user_id: '',
              company_id: '',
              roles: [],
              user_data: {
                contact_phone: {},
                phones: [],
                addresses: [],
              },
            },
          },
        ],
        logo_url: '',
        is_active: false,
        notes: '',
        filter_groups: [],
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
      supplierEmployees: [],
      supplierForm: {
        client_id: null,
        product_id: null,
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
      isCompanyContactsLoading: false,
    };
  },
  mounted() {
    this.getClient();
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
    getClient() {
      this.isCompanyContactsLoading = true;
      this.loadingEmployees = this.themeBgColor;
      this.loadingActivities = this.themeBgColor;
      axios.get(`/api/client/${this.$route.params.id}`).then((response) => {
        response = response.data;
        if (response.success === true) {
          this.$emit('onSetCompanyName', response.data.supplier_name);
          this.client = response.data;
          this.client.client_name = response.data.name;
          this.client.client_description = response.data.description;
          this.client.supplier_object = {};
          this.client.supplier_object[this.client.supplier_type] =
            this.client.supplier_id;
          this.$store.state.pageName = this.client.client_name;
          this.activityForm.model_id = this.client.id;
          this.supplierEmployees =
            this.client.supplier_type === 'App\\Client'
              ? this.client.supplier.employees_without_pivot
              : this.client.supplier.employees;
          this.isCompanyContactsLoading = false;
          this.loadingEmployees = false;
          this.loadingActivities = false;
          this.client.filter_groups = response.data.client_filter_groups;
          this.client_groups = response.data.client_filter_groups.map(
            (group) => group.data
          );
        } else {
          this.snackbarMessage = this.langMap.main.generic_error;
          this.actionColor = 'error';
          this.snackbar = true;
          this.isCompanyContactsLoading = false;
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
    addProductClient() {
      this.supplierForm.client_id = this.client.id;
      axios
        .post(
          `/api/product/${this.supplierForm.product_id}/client`,
          this.supplierForm
        )
        .then((response) => {
          response = response.data;
          if (response.success === true) {
            this.getClient();
            this.resetProduct();
          } else {
            console.log('error');
          }
        });
    },
  },
};
</script>
