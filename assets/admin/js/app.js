$(function () {

    // Approve Delete Modal (for list records)
    let approveDeleteModal = $('#approveDeleteModal');
    $('.btn-trash').on('click', function (e) {
        e.preventDefault();
    });
    approveDeleteModal.on('show.bs.modal', function (e) {
        let data = $(e.relatedTarget).data();
        $('.delete-record-title', this).text(data.recordTitle);
        $('.btn-delete', this).data('recordId', data.recordId);
    });
    approveDeleteModal.on('click', '.btn-delete', function (e) {
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

    // Add tags
    let tagsSelect2 = $('.select-tags').select2({
        width: '100%',
        theme: 'bootstrap-5',
        minimumInputLength: 2,
        tags: true,
        createTag: function (params) {
            if (params.term.indexOf('@') === -1) {
                return null;
            }
        }
    });
    let formAddTag = $('form[name="tag"]');
    $('#btnAddTag').on('click', function () {
        formAddTag[0].reset();
    });
    $('.btn-add-tag').on('click', function (e) {
        e.preventDefault();
        $.post({
            type: 'POST',
            url: formAddTag.attr('action'),
            data: new FormData(formAddTag[0]),
            processData: false,
            contentType: false,
            cache: false,
        }).done(function (data) {
            if (data.status === true) {
                let newOption = new Option(data.tag.text, data.tag.id, false, false);
                let selectedIds = tagsSelect2.val();
                selectedIds.push(data.tag.id);
                tagsSelect2.append(newOption).val(selectedIds).trigger('change');
                $('#addTagModal').modal('hide');
            } else {
                console.log('ERROR: ', data);
            }
        });
    });

});
