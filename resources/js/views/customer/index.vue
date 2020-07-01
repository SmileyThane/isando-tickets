<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Add New Client
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <v-text-field
                                                    color="green"
                                                    label="Name"
                                                    name="company_name"
                                                    type="text"
                                                    v-model="clientForm.client_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    color="green"
                                                    label="Description"
                                                    name="company_description"
                                                    type="text"
                                                    v-model="clientForm.client_description"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-select
                                                    label="Supplier"
                                                    color="green"
                                                    item-color="green"
                                                    item-text="name"
                                                    item-value="item"
                                                    v-model="clientForm.supplier_object"
                                                    :items="suppliers"
                                                />
                                            </div>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                color="green"
                                                @click="addClient"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            :headers="headers"
                            :items="customers"
                            :items-per-page="25"
                            class="elevation-1"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="showItem(item)"
                                >
                                    mdi-eye
                                </v-icon>
                                <v-icon
                                    small
                                    @click="showItem(item)"
                                >
                                    mdi-delete
                                </v-icon>
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

        data() {
            return {
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'name', value: 'name'},
                    {text: 'Description', value: 'description'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                customers: [],
                clientForm: {
                    client_name: '',
                    client_description: '',
                    supplier_object: '',
                    supplier_type: '',
                    supplier_id: ''
                },
                suppliers: [],
            }
        },
        mounted() {
            this.getClients()
            this.getSuppliers()
        },
        methods: {
            getClients() {
                axios.get('api/client').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.customers = response.data.data
                        // console.log(this.customers);
                    } else {
                        console.log('error')
                    }
                });
            },
            addClient() {
                this.clientForm.supplier_type = Object.keys(this.clientForm.supplier_object.item)[0]
                this.clientForm.supplier_id = Object.values(this.clientForm.supplier_object.item)[0]
                axios.post('api/client', this.clientForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClients()
                    } else {
                        console.log('error')
                    }
                });
                // console.log(this.clientForm);
            },
            getSuppliers() {
                axios.get('api/supplier').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.suppliers = response.data
                        this.clientForm.supplier_object = this.suppliers[0]
                    } else {
                        console.log('error')
                    }
                });
            },
            showItem(item) {
                this.$router.push(`/customer/${item.id}`)
            }
        }
    }
</script>
