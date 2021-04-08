export default {
    namespaced: true,
    state: {
        currencies: [],
        search: null
    },
    actions: {
        getCurrencyList({commit}, { search }) {
            commit('SET_SEARCH', search)
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            return axios.get(`/api/currencies?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('GET_CURRENCIES', data)
                    }
                    return success;
                })
        },
        createCurrency({commit, dispatch, state}, { name, slug, symbol }) {
            return axios.post(`/api/currencies`, { name, slug, symbol }, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getCurrencyList', { search: state.search });
                    }
                    return success;
                })
        },
        updateCurrency({commit, dispatch, state}, { id, name, slug, symbol }) {
            return axios.patch(`/api/currencies/${id}`, { name, slug, symbol }, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getCurrencyList', { search: state.search });
                    }
                    return success;
                })
        },
        removeCurrency({commit, dispatch, state}, id) {
            return axios.delete(`/api/currencies/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getCurrencyList', { search: state.search });
                    }
                    return success;
                })
        }
    },
    mutations: {
        GET_CURRENCIES(state, currencies) {
            state.currencies = currencies
        },
        SET_SEARCH(state, search) {
            state.search = search
        }
    },
    getters: {
        getCurrencies(state) {
            return state.currencies;
        }
    }
}

