$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
});

function error_alert(err) {
    if (err.status == 422) {
        return $.each(err.responseJSON.errors, function (index, value) {
            $("#" + index)
                .addClass("is-invalid")
                .siblings("div.invalid-feedback")
                .text(value[0]);
        });
    } else {
        return Swal.fire({
            icon: "error",
            title: "Error",
            text: err.responseJSON.message,
            timer: 1500,
            showConfirmButton: false,
        });
    }
}
