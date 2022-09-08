<template>
    <div>
        <v-row>
            <v-col cols="7"></v-col>
            <v-col cols="2">Deadline</v-col>
            <v-col cols="2">Assigned to</v-col>
            <v-col cols="1"></v-col>

            <v-col cols="12">
                <v-card
                    v-for="action in actions"
                    :key="action.id"
                    class="mx-auto mb-2"
                    outlined
                >
                    <v-list-item three-line>
                        <v-list-item-content>
                            <v-row>
                                <v-col cols="7">
                                    <span class="text-overline mb-4">{{ action.name }}</span>
                                    <br>
                                    <small>{{ action.type }}</small>
                                </v-col>
                                <v-col cols="2">
                                    {{ action.deadline }}
                                </v-col>
                                <v-col cols="2">
                                    {{ action.assignedTo }}
                                </v-col>
                                <v-col class="text-right" cols="1">
                                    <v-menu
                                        bottom
                                        left
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                icon
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                                <v-icon>mdi-dots-vertical</v-icon>
                                            </v-btn>
                                        </template>

                                        <v-list>
                                            <v-list-item>
                                                <v-list-item-title>item 1</v-list-item-title>
                                            </v-list-item>
                                            <v-list-item>
                                                <v-list-item-title>item 2</v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </v-col>
                            </v-row>
                        </v-list-item-content>
                    </v-list-item>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'risk-tab-action-boards',
    data() {
        return {
            actions: [
                {
                    id: 1,
                    name: 'Confirm activation of crisis management team',
                    type: 'Action',
                    deadline: '+4h',
                    assignedTo: 'Crisis Leader'
                },
                {
                    id: 2,
                    name: 'Make contact with authorities',
                    type: 'Action',
                    deadline: '+4h',
                    assignedTo: ''
                },
                {
                    id: 3,
                    name: 'Obtain latest available information and verify',
                    type: 'Action',
                    deadline: '+4h',
                    assignedTo: ''
                },
                {
                    id: 4,
                    name: 'Publish first update report',
                    type: 'Action',
                    deadline: '',
                    assignedTo: ''
                }
            ],
        }
    }
}
</script>
