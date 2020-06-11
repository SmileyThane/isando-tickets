<template>
    <v-data-table
        :headers="headers"
        :items="desserts"
        :single-expand="singleExpand"
        :expanded.sync="expanded"
        item-key="name"
        show-expand
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-switch v-model="singleExpand" label="Single expand" class="mt-2"></v-switch>
            </v-toolbar>
        </template>
        <template v-slot:expanded-item="{ headers, item }">
            <td :colspan="headers.length">More info about {{ item.name }}</td>
        </template>
    </v-data-table>
</template>
<template>
    <v-data-table
        :headers="headers"
        :items="tickets"
        :single-expand="singleExpand"
        :expanded.sync="expanded"
        item-key="id"
        show-expand
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-switch v-model="singleExpand" label="Single expand" color="green" class="mt-2"></v-switch>
            </v-toolbar>
        </template>
        <template v-slot:expanded-item="{ headers, item }">
            <td :colspan="headers.length">
                <p></p>
                <p><strong>From:</strong> {{ item.from.name }}</p>
                <p><strong>To:</strong> {{ item.to.name }}</p>
                <p><strong>Description:</strong> {{ item.description }}</p>
                <p><strong>Contact name:</strong> {{ item.contact.user_data.name }}</p>
                <p><strong>Contact email:</strong> {{ item.contact.user_data.email }}</p>
                <p><strong>Due date:</strong> {{ item.due_date }}</p>
                <p><strong>Access details:</strong> {{ item.access_details }}</p>
            </td>
        </template>
    </v-data-table>
</template>


<script>
    export default {
        data() {
            return {
                expanded: [],
                singleExpand: false,
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'Status', value: 'status.name'},
                    {text: 'Priority', value: 'priority.name'},
                    {text: 'Product', value: 'product.name'},
                    {text: 'Title', value: 'name'},
                    {text: 'Last update', value: 'last_update'},
                    {text: '', value: 'data-table-expand'},
                ],
                tickets: [
                    // {
                    //     name: 'Frozen Yogurt',
                    //     calories: 159,
                    //     fat: 6.0,
                    //     carbs: 24,
                    //     protein: 4.0,
                    //     iron: '1%',
                    // },
                    // {
                    //     name: 'Ice cream sandwich',
                    //     calories: 237,
                    //     fat: 9.0,
                    //     carbs: 37,
                    //     protein: 4.3,
                    //     iron: '1%',
                    // },
                    // {
                    //     name: 'Eclair',
                    //     calories: 262,
                    //     fat: 16.0,
                    //     carbs: 23,
                    //     protein: 6.0,
                    //     iron: '7%',
                    // },
                    // {
                    //     name: 'Cupcake',
                    //     calories: 305,
                    //     fat: 3.7,
                    //     carbs: 67,
                    //     protein: 4.3,
                    //     iron: '8%',
                    // },
                    // {
                    //     name: 'Gingerbread',
                    //     calories: 356,
                    //     fat: 16.0,
                    //     carbs: 49,
                    //     protein: 3.9,
                    //     iron: '16%',
                    // },
                    // {
                    //     name: 'Jelly bean',
                    //     calories: 375,
                    //     fat: 0.0,
                    //     carbs: 94,
                    //     protein: 0.0,
                    //     iron: '0%',
                    // },
                    // {
                    //     name: 'Lollipop',
                    //     calories: 392,
                    //     fat: 0.2,
                    //     carbs: 98,
                    //     protein: 0,
                    //     iron: '2%',
                    // },
                    // {
                    //     name: 'Honeycomb',
                    //     calories: 408,
                    //     fat: 3.2,
                    //     carbs: 87,
                    //     protein: 6.5,
                    //     iron: '45%',
                    // },
                    // {
                    //     name: 'Donut',
                    //     calories: 452,
                    //     fat: 25.0,
                    //     carbs: 51,
                    //     protein: 4.9,
                    //     iron: '22%',
                    // },
                    // {
                    //     name: 'KitKat',
                    //     calories: 518,
                    //     fat: 26.0,
                    //     carbs: 65,
                    //     protein: 7,
                    //     iron: '6%',
                    // },
                ],
            }
        },
        mounted() {
            this.getTickets()
        },
        methods: {
            getTickets() {
                axios.get('api/ticket').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.tickets = response.data.data
                        // console.log(this.customers);
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
