$(function () {
    let formType;
    let selectedId;

    let table = $("#book-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/books",
        },
        order: [[2, "asc"]],
        columns: [
            {
                data: "DT_RowIndex",
                class: "text-center",
                width: "1%",
                orderable: false,
            },
            { data: "cover", class: "text-center", orderable: false },
            { data: "title" },
            { data: "author" },
            { data: "description" },
            { data: "action", class: "text-center", orderable: false },
        ],
    });

    $("#btn-add").on("click", function (e) {
        e.preventDefault();
        formType = "post";
        $("#btn-submit").html("Submit");
        $("#modal-book").modal("show");
    });

    $("#modal-book").on("shown.bs.modal", function () {
        $("#title").focus();
    });

    $("#modal-book").on("hidden.bs.modal", function () {
        $("#form-book").trigger("reset");
        $('#book_author_id').val('').trigger('change')
    });

    $("#book_author_id").select2({
        theme: "bootstrap4",
        dropdownParent: $("#modal-book"),
        placeholder: "Select author",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "/authors/select2",
            dataType: "json",
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (author) {
                        return {
                            text: author.name,
                            id: author.id,
                        };
                    }),
                };
            },
        },
    });

    $("#form-book").on("submit", function (e) {
        e.preventDefault();
        let data = new FormData(this);
        if (formType == "post") {
            $.ajax({
                type: "post",
                url: "/books",
                data: data,
                enctype: "multipart/form-data",
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
            })
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        $("#modal-book").modal("hide");
                        table.ajax.reload();
                    });
                })
                .catch((err) => {
                    error_alert(err);
                });
        } else {
            $.ajax({
                type: "post",
                url: `/books/${selectedId}`,
                data: data,
                enctype: "multipart/form-data",
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
            })
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        $("#modal-book").modal("hide");
                        table.ajax.reload();
                    });
                })
                .catch((err) => {
                    error_alert(err);
                });
        }
    });

    $(document).on("click", ".btn-edit", function (e) {
        e.preventDefault();
        $("#btn-submit").html("Update");
        formType = "patch";
        selectedId = $(this).parents("tr").attr("id");
        $.get(`/books/${selectedId}`).then((res) => {
            $("#title").val(res.data.title);
            $("#description").html(res.data.description);
            $("#modal-book").modal("show");
            $('#book_author_id').append(`<option value="${res.data.book_author_id}" selected>${res.data.author.name}</option>`)
            $('#book_author_id').trigger('change')
        });
    });

    $(document).on("click", ".btn-delete", function (e) {
        e.preventDefault();
        selectedId = $(this).parents("tr").attr("id");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/books/${selectedId}`,
                    type: "delete",
                    dataType: "json",
                }).then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        table.ajax.reload();
                    });
                });
            }
        });
    });
});
