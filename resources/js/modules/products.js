export default {
    namespaced: true,
    state: {
        products: []
    },
    actions: {
        getProductList({commit, state}, {search, force = false}) {
            if (state.products.length && !search && !force) {
                return state.products;
            } else {
                const queryParams = new URLSearchParams({
                    search: search ?? ''
                });
                axios.get(`/api/product?${queryParams.toString()}`, {retry: 5, retryDelay: 1000})
                    .then(({data: {success, data: products}}) => {
                        if (success) {
                            commit('GET_PRODUCTS', products.data)
                        }
                    })
            }
        }
    },
    mutations: {
        GET_PRODUCTS(state, products) {
            state.products = products
        }
    },
    getters: {
        getProducts(state) {
            return state.products
        }
    }
}

