$(function () {
    let formType;
    let selectedId;

    let table = $("#user-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/users",
        },
        order: [[1, "asc"]],
        columns: [
            {
                data: "DT_RowIndex",
                orderable: false,
                class: "text-center",
            },
            { data: "name" },
            { data: "email" },
            { data: "action", class: "text-center", orderable: false },
        ],
    });

    $("#btn-add").on("click", function (e) {
        e.preventDefault();
        formType = "post";
        $("#btn-submit").html("Submit");
        $("#modal-user").modal("show");
    });

    $("#modal-user").on("shown.bs.modal", function () {
        $("#name").focus();
    });

    $("#modal-user").on("hidden.bs.modal", function () {
        $("#form-user").trigger("reset");
        $('#level').val('').trigger('change')
    });

    $('#level').select2({
        dropdownParent: $("#modal-user"),
        theme:'bootstrap4',
        placeholder:'Select user level'
    })

    $("#form-user").on("submit", function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        if (formType == "post") {
            $.post(`/users`, data)
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        $("#modal-user").modal("hide");
                        table.ajax.reload();
                    });
                })
                .catch((err) => {
                    error_alert(err);
                });
        } else {
            $.ajax({
                url: `/users/${selectedId}`,
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
                        $("#modal-user").modal("hide");
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
        $.get(`/users/${selectedId}`).then((res) => {
            $("#name").val(res.data.name);
            $("#email").val(res.data.email);
            $("#level").val(res.data.level).trigger('change');
            $("#modal-user").modal("show");
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
                    url: `/users/${selectedId}`,
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
