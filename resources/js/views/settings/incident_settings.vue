<template>
    <v-card style="height: 80vh">
        <v-tabs
            v-model="tab"
            align-with-title
        >
            <v-tab
                v-for="(component, index) in components"
                :key="index"
            >
                {{ component.name }}
            </v-tab>
        </v-tabs>
        <v-tabs-items v-model="tab">
            <v-tab-item
                v-for="(component, index) in components"
                :key="index"
            >
                <component :is="component.component" :entity="component.entity" />
            </v-tab-item>
        </v-tabs-items>
    </v-card>
</template>

<script>
import tabIncidentSettings from './components/tab_incident_settings'

export default {
    name: 'incident_settings',
    components: {
        tabIncidentSettings,
    },
    data: function () {
        return {
            tab: 0,
            components: [
                {
                    name: 'Impact potential',
                    component: tabIncidentSettings,
                    entity: 'ImpactPotentials',
                },
                {
                    name: 'Process states',
                    component: tabIncidentSettings,
                    entity: 'ProcessStates',
                },
                {
                    name: 'Event types',
                    component: tabIncidentSettings,
                    entity: 'EventTypes',
                },
                {
                    name: 'Focus priorities',
                    component: tabIncidentSettings,
                    entity: 'FocusPriorities',
                },
                {
                    name: 'Resource types',
                    component: tabIncidentSettings,
                    entity: 'ResourceTypes',
                },
                {
                    name: 'Action types',
                    component: tabIncidentSettings,
                    entity: 'ActionTypes',
                },
                {
                    name: 'Stakeholder types',
                    component: tabIncidentSettings,
                    entity: 'StakeholderTypes',
                },
                {
                    name: 'Action Board Statuses',
                    component: tabIncidentSettings,
                    entity: 'ActionBoardStatuses',
                },
            ]
        }
    },

}
</script>

<style scoped>
>>> .v-tabs-bar .v-tab {
    font-size: 12px !important;
}
</style>
