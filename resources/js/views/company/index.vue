<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <v-data-table
                            :headers="headers"
                            :items="companies"
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
                clientId: 6,
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'name', value: 'name'},
                    {text: 'Company number', value: 'company_number'},
                    {text: 'Description', value: 'description'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                companies: [],
            }
        },
        mounted() {
            this.getCompanies()
        },
        methods: {
            getCompanies() {
                let route = this.$store.state.roles.includes(this.clientId) ? 'api/client' : 'api/company';
                axios.get(route).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.companies = response.data.data
                    } else {
                        console.log('error')
                    }

                });
            },
            showItem(item) {
                let route = this.$store.state.roles.includes(this.clientId) ? '/customer' : '/company';
                this.$router.push(`${route}/${item.id}`)
            }
        }
    }
</script>
