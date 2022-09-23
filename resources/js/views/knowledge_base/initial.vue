<template>
    <component :is="component"/>
</template>

<script>
import risk_repository from "../risk_repository";
import knowledge_base from "./index";

export default {
    components: {
        risk_repository,
        knowledge_base
    },
    data: () => ({
        component: null,
    }),
    created() {
        switch (this.$route.params.alias) {
            case 'risk_repository':
                this.component = risk_repository;
                break;
            default:
                this.component = knowledge_base
        }
    },
}
</script>
