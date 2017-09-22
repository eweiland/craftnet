import * as types from './mutation-types'
import api from '../api'

export const getCraftIdData = ({commit}) => {
    return new Promise((resolve, reject) => {
        let userId = window.currentUserId;

        api.getCraftIdData(userId, data => {
            commit(types.RECEIVE_CRAFT_ID_DATA, {data})
            resolve(data);
        },
        response => {
            reject(response);
        })
    })
};

export const saveUser = ({commit, state}, user) => {
    return new Promise((resolve, reject) => {
        api.saveUser(user, data => {
            if (!data.errors) {
                commit(types.SAVE_USER, {user, data});
                resolve(data);
            } else {
                reject(data);
            }
        },
        response => {
            reject(response);
        })
    })
};

export const getStripeAccount = ({commit}) => {
    return new Promise((resolve, reject) => {
        api.getStripeAccount(data => {
            commit(types.RECEIVE_STRIPE_ACCOUNT, {data})
            resolve(data);
        }, response => {
            reject(response);
        })
    })
};

export const getStripeCustomer = ({commit}) => {
    return new Promise((resolve, reject) => {
        api.getStripeCustomer(data => {
            commit(types.RECEIVE_STRIPE_CUSTOMER, {data})
            commit(types.RECEIVE_STRIPE_CARD, {data})
            resolve(data);
        }, response => {
            reject(response);
        })
    })
};

export const disconnectStripeAccount = ({commit}) => {
    return new Promise((resolve, reject) => {
        api.disconnectStripeAccount(data => {
            commit(types.DISCONNECT_STRIPE_ACCOUNT, {data})
            resolve(data);
        }, response => {
            reject(response);
        })
    })
};

export const saveCard = ({commit}, token) => {
    return new Promise((resolve, reject) => {
        api.saveCard(token, data => {
            commit(types.SAVE_CARD, {data})
            resolve(data);
        }, response => {
            reject(response);
        })
    })
};

export const removeCard = ({commit}) => {
    return new Promise((resolve, reject) => {
        api.removeCard(data => {
            commit(types.REMOVE_CARD, {data})
            resolve(data);
        }, response => {
            reject(response);
        })
    })
};

export const saveLicense = ({commit, state}, license) => {
    return new Promise((resolve, reject) => {
        api.saveLicense(license, data => {
            if (!data.errors) {
                commit(types.SAVE_LICENSE, {license, data});
                resolve(data);
            } else {
                reject(data);
            }
        }, response => {
            reject(response);
        })
    })
};

export const savePlugin = ({commit, state}, plugin) => {
    return new Promise((resolve, reject) => {
        api.savePlugin(plugin, data => {
            if (data.success) {
                commit(types.SAVE_PLUGIN, {plugin, data});
                resolve(data);
            } else {
                reject(data);
            }
        }, response => {
            reject(response);
        })
    })
};
