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
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header"></div>

          <div class="card-body">
            <v-data-table
              :footer-props="footerProps"
              :headers="headers"
              :items="customers"
              :loading="loading"
              :loading-text="langMap.main.loading"
              :options.sync="options"
              :server-items-length="totalCustomers"
              class="elevation-1"
              fixed-header
              hide-default-footer
            >
              <template v-slot:top>
                <v-row>
                  <v-col md="10" sm="12">
                    <!--                    <v-text-field-->
                    <!--                      v-model="customersSearch"-->
                    <!--                      :color="themeBgColor"-->
                    <!--                      :label="langMap.main.search"-->
                    <!--                      class="mx-4"-->
                    <!--                      @input="debounceGetCustomers"-->
                    <!--                    ></v-text-field>-->
                  </v-col>
                  <v-col md="2" sm="12">
                    <v-select
                      v-model="options.itemsPerPage"
                      :color="themeBgColor"
                      :item-color="themeBgColor"
                      :items="footerProps.itemsPerPageOptions"
                      :label="langMap.main.items_per_page"
                      class="mx-4"
                      @change="updateItemsCount"
                    ></v-select>
                  </v-col>
                </v-row>
              </template>
              <template v-slot:footer>
                <v-pagination
                  v-model="options.page"
                  :color="themeBgColor"
                  :length="lastPage"
                  :page="options.page"
                  :total-visible="5"
                  circle
                >
                </v-pagination>
                  <button @click="createExternalSource">123</button>
              </template>
            </v-data-table>
          </div>
        </div>
      </div>
    </div>
  </v-container>
</template>
<script>

export default {
    props: {
        type: {
            type: Number,
            required: true,
            default: 1
        }
    },
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            totalCustomers: 0,
            lastPage: 0,
            loading: this.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            options: {
                page: 1,
                sortDesc: [false],
                sortBy: ['name'],
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [
                    {
                        'text': 10,
                        'value': 10,
                    },
                    {
                        'text': 20,
                        'value': 20,
                    },
                    {
                        'text': 50,
                        'value': 50,
                    },
                    {
                        'text': 100,
                        'value': 100,
                    },
                    {
                        'text': this.$store.state.lang.lang_map.sidebar.all,
                        'value': 10000,
                    }
                ]
            },
            headers: [
                {text: 'ID', align: 'end', value: 'id', sortable: false},
                {text: this.$store.state.lang.lang_map.main.type, value: 'type', sortable: false},
                {text: this.$store.state.lang.lang_map.main.name, value: 'name'},
                {text: this.$store.state.lang.lang_map.main.email, value: 'email'},
                {text: this.$store.state.lang.lang_map.main.phone, value: 'phone'},
                {text: this.$store.state.lang.lang_map.main.city, value: 'city'},
                {text: this.$store.state.lang.lang_map.main.country, value: 'country'},
                {text: this.$store.state.lang.lang_map.main.status, value: 'is_active'}
            ],
            customersSearch: '',
            externalSources: []
        }
    },
    mounted() {
        this.getExternalSourcesByType(this.type)
    },
    methods: {
        getExternalSourcesByType(type_id){
            this.loading = this.themeBgColor;
            axios.get(`/api/external-sources`,
            //     {
            //     params: {
            //         type_id: type_id
            //     }
            // }
            ).then(response => {
                if(response.success) {
                    this.externalSources = response.data;
                }
            })
        },
        createExternalSource(){
            axios.post(`/api/external-sources`, {
                domain: 'com',
                domainPrefix: 'https',
                uri: 'google',
                name: 'Google name',
                authName: 'auth_name',
                auth_method: 'post method',
                auth_headers: 'some headers',
                password: 'qwer1234',
                billingPrice: 1000,
                billingCurrency: 1,
                billingType: 1,
                isBillingAutoRenewal: false,
                // last_billet_at: '20.09.24',
                externalSourceTypeId: 1,
                entityId: 1,
                entityType: '1'

                // 'domain' => 'required|string',
                // 'domainPrefix' => 'required|string',
                // 'uri' => 'nullable|string',
                // 'name' => 'required|string',
                // 'authName' => 'required|string',
                // 'authHeaders' => 'nullable|string',
                // 'password' => 'nullable|string',
                // 'billingPrice' => 'required|int',
                // 'billingCurrency' => 'required|int',
                // 'billingType' => 'required|int',
                // 'isBillingAutoRenewal' => 'bool|string',
                // 'entityId' => ['nullable', 'int', new EntityExistsRule($this->input('entityType'))],
                // 'entityType' => 'nullable|string',
                // 'externalSourceTypeId' => 'id|exists:external_source_types,id',
            })
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value;
            localStorage.itemsPerPage = value;
            this.options.page = 1;
        }
    }
}
</script>
