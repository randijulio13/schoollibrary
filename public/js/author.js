$(function () {
    let formType;
    let selectedId;

    let table = $("#author-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/authors",
        },
        order: [[1, "asc"]],
        columns: [
            {
                data: "DT_RowIndex",
                orderable: false,
                class: "text-center",
            },
            { data: "name" },
            { data: "action", class: "text-center", orderable: false },
        ],
    });

    $("#btn-add").on("click", function (e) {
        e.preventDefault();
        formType = "post";
        $("#btn-submit").html("Submit");
        $("#modal-author").modal("show");
    });

    $("#modal-author").on("shown.bs.modal", function () {
        $("#name").focus();
    });

    $("#modal-author").on("hidden.bs.modal", function () {
        $("#form-author").trigger("reset");
    });

    $("#form-author").on("submit", function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        if (formType == "post") {
            $.post(`/authors`, data)
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        $("#modal-author").modal("hide");
                        table.ajax.reload();
                    });
                })
                .catch((err) => {
                    error_alert(err);
                });
        } else {
            $.ajax({
                url: `/authors/${selectedId}`,
                data: data,
                type: "patch",
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
                        $("#modal-author").modal("hide");
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
        $.get(`/authors/${selectedId}`).then((res) => {
            $("#name").val(res.data.name);
            $("#modal-author").modal("show");
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
                    url: `/authors/${selectedId}`,
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
