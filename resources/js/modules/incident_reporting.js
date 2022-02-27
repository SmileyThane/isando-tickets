export default {
    namespaced: true,
    state: {
        search: '',
        searchWhere: [1,2,3],
        activeTags: [],
    },
    actions: {

    },
    mutations: {
        setSearch: (state, data) => state.search = data,
        setSearchWhere: (state, data) => state.searchWhere = data,
        setActiveTags: (state, data) => state.activeTags = data,
    },
    getters: {
        getSearch: state => state.search,
        getSearchWhere: state => state.searchWhere,
        getActiveTags: state => state.activeTags,
    }
}

