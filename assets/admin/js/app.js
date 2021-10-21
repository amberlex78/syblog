$(function () {

    // Approve Delete Modal (for list records)
    let approveDeleteModal = $('#approveDeleteModal');
    $('.btn-trash').on('click', function (e) {
        e.preventDefault();
    });
    approveDeleteModal.on('show.bs.modal', function(e) {
        let data = $(e.relatedTarget).data();
        $('.delete-record-title', this).text(data.recordTitle);
        $('.btn-delete', this).data('recordId', data.recordId);
    });
    approveDeleteModal.on('click', '.btn-delete', function(e) {
        $('#form' + $(this).data('recordId')).submit();
        $(e.delegateTarget).modal('hide');
    });

    // Delete image on the edit form
    $('.delete-img').on('click', function (e) {
        e.preventDefault();
        $.post({
            type: 'PATCH',
            url: $(this).attr('href')
        }).done(function (data) {
            if (data.status === true) {
                location.reload();
            } else {
            }
            console.log(data.message);
        });
    });
});
