<template>
    <component :is="component" />
</template>

<script>
import incident_reporting from "../incident_reporting";
import knowledge_base from "./index";

export default {
    components: [
        incident_reporting,
        knowledge_base
    ],
    data: () => ({
        component: null,
    }),
    created() {
        switch (this.$route.params.alias) {
            case 'incident_reporting': this.component = incident_reporting; break;
            default: this.component = knowledge_base
        }
    },
}
</script>
