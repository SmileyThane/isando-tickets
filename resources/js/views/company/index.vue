<template>
        <v-container
            class="fill-height"
            fluid
        >
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Companies</div>

                        <div class="card-body">
                            <template>
                                <v-data-table
                                    :headers="headers"
                                    :items="companies"
                                    :items-per-page="25"
                                    class="elevation-1"
                                ></v-data-table>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </v-container>
</template>

<template>
    <v-data-table
        :headers="headers"
        :items="companies"
        :items-per-page="25"
        class="elevation-1"
    ></v-data-table>
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
