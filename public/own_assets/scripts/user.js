
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

let table = $('#dataTable').DataTable({
    processing: true,
    ajax: {
        url: '/users/data',
        dataSrc: 'data',
        data: function (d) {
            d.status = 1;
        }
    },
    columnDefs: [{
        targets: [0, 3, 4, 5],
        className: 'text-center'
    }],
    columns: [{
        data: null,
        render: function (data, type, row, meta) {
            return meta.row + 1;
        }
    },
    {
        data: 'name'
    },
    {
        data: 'email'
    },
    {
        data: 'status',
        render: function (data) {
            return data ?
                '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-secondary">Inactive</span>';
        }
    },
    {
        data: 'role',
        render: function (data) {
            let roleClass = '';
            switch (data) {
                case 'admin':
                    roleClass = 'bg-primary';
                    break;
                case 'kasir':
                    roleClass = 'bg-info';
                    break;
                case 'kitchen':
                    roleClass = 'bg-warning';
                    break;
                case 'customer':
                    roleClass = 'bg-secondary';
                    break;
                case 'waiter':
                    roleClass = 'bg-dark';
                    break;
            }
            return `<span class="badge ${roleClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
        }
    },
    {
        data: 'id',
        render: function (data) {
            return `
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${data}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            <button class="btn btn-sm btn-danger reset-btn" data-id="${data}">Reset Password</button>
                        `;
        }
    }
    ]
});

let tableTrash = $('#dataTableTrash').DataTable({
    processing: true,
    ajax: {
        url: '/users/data',
        dataSrc: 'data',
        data: function (d) {
            d.status = 0;
        }
    },
    columnDefs: [{
        targets: [0, 3, 4, 5],
        className: 'text-center'
    }],
    columns: [{
        data: null,
        render: function (data, type, row, meta) {
            return meta.row + 1;
        }
    },
    {
        data: 'name'
    },
    {
        data: 'email'
    },
    {
        data: 'status',
        render: function (data) {
            return data ?
                '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-secondary">Inactive</span>';
        }
    },
    {
        data: 'role',
        render: function (data) {
            let roleClass = '';
            switch (data) {
                case 'admin':
                    roleClass = 'bg-primary';
                    break;
                case 'kasir':
                    roleClass = 'bg-info';
                    break;
                case 'kitchen':
                    roleClass = 'bg-warning';
                    break;
                case 'customer':
                    roleClass = 'bg-secondary';
                    break;
                case 'waiter':
                    roleClass = 'bg-dark';
                    break;
            }
            return `<span class="badge ${roleClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
        }
    },
    {
        data: 'id',
        render: function (data) {
            return `
                            <button class="btn btn-sm btn-primary restore-btn" data-id="${data}"><i class="fa fa-retweet" aria-hidden="true"></i></button>
                            <button class="btn btn-sm btn-danger destroy-btn" data-id="${data}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        `;
        }
    }
    ]
});

$('#tambah-data').on('click', function () {
    $('#formCreate')[0].reset();
    $('.text-danger').text('');

    let modal = new bootstrap.Modal(document.getElementById('modalCreate'));
    modal.show();
});

$('#formCreate').submit(function (e) {
    e.preventDefault();

    $('.text-danger').text('');

    let form = document.getElementById('formCreate');
    let token = $("meta[name='csrf-token']").attr("content");
    let formData = new FormData(form);
    
    formData.append('_token', token);

    $.ajax({
        url: '/users/store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $('#modalCreate').modal('hide');

                $('#formCreate')[0].reset();
                $('#preview-foto').hide();

                table.ajax.reload(null, false);

                alertResult('success', 'Berhasil', res.message);
            }
        },
        error: function (err) {
            if (err.status === 422) {
                let errors = err.responseJSON.errors;

                $.each(errors, function (key, value) {
                    $('.error-' + key).text(value[0]);
                });

                alertResult('warning', 'Validasi Gagal', 'Periksa kembali data kamu');

            } else {
                alertResult('error', 'Error', 'Terjadi kesalahan server');
            }
        }
    });
});

$(document).on('click', '.edit-btn', function () {
    let id = $(this).data('id');

    $('.text-danger').text('');

    $.get('/users/' + id, function (res) {
        $('#edit_id').val(res.id);
        $('#edit_name').val(res.name);
        $('#edit_email').val(res.email);
        $('#edit_role').val(res.role);

        if (res.foto) {
            $('#preview-edit_foto')
                .attr('src', '../../storage/' + res.foto)
                .show();
        } else {
            $('#preview-edit_foto').hide();
        }

        let modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    });
});

$('#formEdit').submit(function (e) {
    e.preventDefault();

    $('.text-danger').text('');

    let id = $('#edit_id').val();
    let formData = new FormData(this);
    let token = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', token);

    $.ajax({
        url: '/users/update/' + id,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $('#modalEdit').modal('hide');

                table.ajax.reload(null, false);

                alertResult('success', 'Berhasil', res.message);
            }
        },
        error: function (err) {
            if (err.status === 422) {
                let errors = err.responseJSON.errors;

                $.each(errors, function (key, value) {
                    $('#formEdit .error-' + key).text(value[0]);
                });

                alertResult('warning', 'Validasi Gagal', 'Periksa kembali data');
            } else {
                alertResult('error', 'Error', 'Terjadi kesalahan server');
            }
        }
    });
});

$(document).on('click', '.delete-btn', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Yakin?',
        text: "Apakah anda yakin ingin menghapus data?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: '/users/delete/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        table.ajax.reload(null, false);
                        alertResult('success', 'Berhasil', res.message);
                    } else {
                        alertResult('warning', 'Hapus Data Gagal', 'Gagal menghapus data');
                    }
                },
                error: function () {
                    alertResult('error', 'Error', 'Terjadi kesalahan server');
                }
            });

        }
    });
});

$(document).on('click', '.reset-btn', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Yakin?',
        text: "Apakah anda yakin ingin reset password user ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: '/users/reset/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        table.ajax.reload(null, false);
                        alertResult('success', 'Berhasil', res.message);
                    } else {
                        alertResult('warning', 'Reset Data Gagal', 'Gagal mereset password');
                    }
                },
                error: function () {
                    alertResult('error', 'Error', 'Terjadi kesalahan server');
                }
            });

        }
    });
});

$(document).on('click', '.destroy-btn', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Yakin?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: '/users/destroy/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        tableTrash.ajax.reload(null, false);
                        alertResult('success', 'Berhasil', res.message);
                    } else {
                        alertResult('warning', 'Hapus Data Gagal', 'Gagal menghapus data');
                    }
                },
                error: function () {
                    alertResult('error', 'Error', 'Terjadi kesalahan server');
                }
            });

        }
    });
});

$(document).on('click', '.restore-btn', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Yakin?',
        text: "Apakah anda ingin mengembalikan data ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, kembalikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: '/users/restore/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        tableTrash.ajax.reload(null, false);
                        alertResult('success', 'Berhasil', res.message);
                    } else {
                        alertResult('warning', 'Pengembalian Data Gagal', 'Gagal mengembalikan data');
                    }
                },
                error: function () {
                    alertResult('error', 'Error', 'Terjadi kesalahan server');
                }
            });

        }
    });
});

$(".refresh-data").on("click", function () {
    let tableType = $(this).data("table");
    if (tableType === "data") {
        table.ajax.reload(null, false);
    } else if (tableType === "trash") {
        tableTrash.ajax.reload(null, false);
    }
})