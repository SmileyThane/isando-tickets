export default {
    namespaced: true,
    state: {
        products: []
    },
    actions: {
        getProductList({commit}, { search }) {
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/tracking/products?${queryParams.toString()}`)
                .then(({ data: { success, data: products } }) => {
                    if (success) {
                        commit('GET_PRODUCTS', products)
                    }
                })
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

