export default {
    namespaced: true,
    state: {
        categories: [],
        selectedCategory: null,
        articles: [],
        selectedArticle: null,
        search: '',
        searchWhere: [1, 2, 3],
        activeTags: [],
        clients: {
            current_page: 1,
            data: [],
            first_page_url: null,
            from: 1,
            last_page: 1,
            last_page_url: null,
            next_page_url: null,
            path: null,
            per_page: 100,
            prev_page_url: null,
            to: 0,
            total: 0,
        },
        importance: [],
    },
    actions: {
        callGetCategories({state, commit}, categoryId = undefined) {
            return axios.get(`/api/kb/categories?type=risk_repository`, {
                params: {
                    search: state.searchWhere.includes(1) ? state.search : '',
                    category_id: categoryId ? categoryId : state.selectedCategory?.id ?? null
                }
            }).then(({status, data: {data, success}}) => {
                if (status === 200 && success) {
                    commit('setCategories', data)
                    return Promise.resolve(data)
                }
                commit('setCategories', [])
                return Promise.reject([])
            });
        },
        callGetArticles({state, commit}, categoryId = undefined) {
            return axios.get(`/api/kb/articles?type=risk_repository`, {
                params: {
                    search: state.searchWhere.includes(2) || state.searchWhere.includes(3) ? state.search : '',
                    search_in_text: state.searchWhere.includes(3),
                    category_id: categoryId ? categoryId : state.selectedCategory?.id ?? null,
                    tags: state.activeTags
                }
            }).then(({data: {data, success}, status}) => {
                if (status === 200 && success) {
                    commit('setArticles', data)
                    return Promise.resolve(data)
                }
                commit('setArticles', [])
                return Promise.reject([])
            });
        },
        callGetClients({state, commit}) {
            return axios.get(`/api/client`)
                .then(({data: {data, success}, status}) => {
                    if (status === 200 && success) {
                        commit('setClients', data)
                        return Promise.resolve(data)
                    }
                    commit('setClients', {
                        current_page: 1,
                        data: [],
                        first_page_url: null,
                        from: 1,
                        last_page: 1,
                        last_page_url: null,
                        next_page_url: null,
                        path: null,
                        per_page: 100,
                        prev_page_url: null,
                        to: 0,
                        total: 0,
                    })
                    return Promise.reject([])
                });
        },
        callGetImportance({state, commit}) {
            return axios.get(`/api/kb/importance`)
                .then(({data: {data, success}, status}) => {
                    if (status === 200 && success) {
                        commit('setImportance', data)
                        return Promise.resolve(data)
                    }
                    commit('setImportance', [])
                    return Promise.reject([])
                });
        },
    },
    mutations: {
        setCategories: (state, data) => state.categories = data,
        selectCategoryById: (state, data) => state.selectedCategory = state.categories.find(i => i.id === parseInt(data)),
        unselectCategory: state => state.selectedCategory = null,
        setArticles: (state, data) => state.articles = data,
        selectArticleById: (state, data) => state.selectedArticle = state.articles.find(i => i.id === parseInt(data)),
        unselectArticle: state => state.selectedArticle = null,
        setSearch: (state, data) => state.search = data,
        setSearchWhere: (state, data) => state.searchWhere = data,
        setActiveTags: (state, data) => state.activeTags = data,
        setClients: (state, data) => state.clients = data,
        setImportance: (state, data) => state.importance = Object.values(data),
    },
    getters: {
        getCategories: state => state.categories,
        getSelectedCategory: state => state.selectedCategory,
        getArticles: state => state.articles,
        getSelectedArticle: state => state.selectedArticle,
        getSearch: state => state.search,
        getSearchWhere: state => state.searchWhere,
        getActiveTags: state => state.activeTags,
        getClients: state => state.clients.data,
        getImportance: state => state.importance,
    }
}

