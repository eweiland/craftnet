<template>
    <div>
        <div class="flex">
            <div class="flex-1">
                <h4>Invoice details</h4>

                <dl v-if="!showForm">
                    <dt>VAT ID</dt>
                    <dd>
                        <template v-if="billingAddress && billingAddress.businessTaxId">
                            {{ billingAddress.businessTaxId }}
                        </template>
                        <template v-else>
                            <span class="text-secondary">VAT ID not defined.</span>
                        </template>
                    </dd>
                </dl>
            </div>

            <div v-if="!showForm">
                <btn small icon="pencil" @click="editInvoiceDetails()">Edit</btn>
            </div>
        </div>

        <form v-if="showForm" @submit.prevent="save()">
            <textbox id="businessTaxId" label="Tax ID" v-model="invoiceDetailsDraft.businessTaxId" :errors="errors.businessTaxId" />
            <btn kind="primary" type="submit" :loading="loading" :disabled="loading">Save</btn>
            <btn @click="cancel()" :disabled="loading">Cancel</btn>
        </form>
    </div>
</template>

<script>
    import {mapState} from 'vuex'

    export default {
        data() {
            return {
                loading: false,
                errors: {},
                showForm: false,
                invoiceDetailsDraft: {},
            }
        },

        computed: {
            ...mapState({
                user: state => state.account.user,
                billingAddress: state => state.account.billingAddress,
            }),
        },

        methods: {
            editInvoiceDetails() {
                this.showForm = true

                if (this.billingAddress) {
                    this.invoiceDetailsDraft = JSON.parse(JSON.stringify(this.billingAddress))
                }
            },

            save() {
                this.loading = true

                let data = {
                    businessTaxId: this.invoiceDetailsDraft.businessTaxId,
                }

                if (this.billingAddress) {
                    data = Object.assign({}, data, {
                        id: this.billingAddress.id,
                        firstName: this.billingAddress.firstName,
                        lastName: this.billingAddress.lastName,
                        businessName: this.billingAddress.businessName,
                        address1: this.billingAddress.address1,
                        address2: this.billingAddress.address2,
                        city: this.billingAddress.city,
                        state: this.billingAddress.state,
                        zipCode: this.billingAddress.zipCode,
                        country: this.billingAddress.country,
                    })
                }

                this.$store.dispatch('account/saveBillingInfo', data)
                    .then(() => {
                        this.loading = false
                        this.$store.dispatch('app/displayNotice', 'Invoice details saved.')
                        this.showForm = false
                        this.errors = {}
                    })
                    .catch(response => {
                        this.loading = false
                        const errorMessage = response.data && response.data.error ? response.data.error : 'Couldn’t save invoice details.'
                        this.$store.dispatch('app/displayError', errorMessage)
                        this.errors = response.data && response.data.errors ? response.data.errors : {}
                    })
            },

            cancel() {
                this.showForm = false
                this.errors = {}
            }
        }
    }
</script>
