<template>
</template>

<script>
    export default {
        props: ['moduleRoute'],
        methods: {
            showModal: function (id) {
                swal({
                        title: "Are you sure?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    }, () => {
                        let config = {
                            headers: {'X-Requested-With': 'XMLHttpRequest'} // Laravel recognition
                        };
                                             
                        axios.delete(this.moduleRoute + '/' + id, {}, config).then((response) => {
                            swal("Deleted", "Item was deleted.", "success");
                            Event.$emit('refreshTable');
                        }).catch((error) => {
                            console.log(error);
                        });
                    });
            }
        },
        created() {
            Event.$on('showDeleteModal', this.showModal);
        }
    }
</script>