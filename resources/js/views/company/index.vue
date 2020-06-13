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
                        ></v-data-table>
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
                    {text: 'Company number', value: 'company_number'},
                    {text: 'Description', value: 'description'},
                    {text: 'Actions', value: ''},
                ],
                companies: [],
            }
        },
        mounted() {
            this.getCompanies()
        },
        methods: {
            getCompanies() {
                axios.get('api/company').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.companies = [response.data]
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
