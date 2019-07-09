<template>
    <el-dropdown-item @click.native="markApprovalAsRejected">Reject Approval</el-dropdown-item>
</template>

<script>
    export default {
        props: {
            rows: { default: null },
            scope: { default: null },
        },
        data() {
            return {
                title: '',
                message: '',
            }
        },
        methods: {
            markApprovalAsRejected(event) {
                this.$confirm(this.message, this.title, {
                    confirmButtonText: this.trans('core.button.reject-approval'),
                    cancelButtonText: this.trans('core.button.cancel'),
                    type: 'warning',
                    confirmButtonClass: 'el-button--danger',
                }).then(() => {
                    const vm = this
                    axios.get(this.scope.row.urls.reject_approval_url)
                        .then((response) => {
                            vm.$message({
                                type: 'success',
                                message: response.data.message,
                            })

                            this.scope.row.pending_approval = response.data.pending_approval;
                            this.scope.row.status           = response.data.status;
                        })
                        .catch((error) => {
                            vm.$message({
                                type: 'error',
                                message: error.response.data.error.message,
                            })
                        })
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: this.trans('core.reject-approval cancelled'),
                    })
                })
            },
        },
        mounted() {
            this.title = this.trans('core.modal.title')
            this.message = this.trans('core.modal.reject-approval-confirmation-message')
        },
    }
</script>
