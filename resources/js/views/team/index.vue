<template>
    <v-container
        class="fill-height"
        fluid
    >
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">List fof Customers</div>

                    <div class="card-body">
                        <template>
                            <v-data-table
                                :headers="headers"
                                :items="customers"
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
        :items="teams"
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
                    {text: 'Description', value: 'description'},
                ],
                teams: [],
            }
        },
        mounted() {
            this.getCompanies()
        },
        methods: {
            getCompanies() {
                axios.get('api/team').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.teams = response.data.data
                        // console.log(this.customers);
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
