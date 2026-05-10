
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

let table = $('#dataTable').DataTable({
    processing: true,
    ajax: {
        url: '/meja/data',
        dataSrc: function (json) {

            // DATA STATIK
            const staticRow = {
                id: 'all',
                nama_meja: 'QRCode Take Away',
                status: true,
                qrcode: '/qrcode_meja/qr-code.png',
                is_static: true
            };

            json.data.unshift(staticRow);

            return json.data;
        },
        data: function (d) {
            d.status = 1;
        }
    },

    columnDefs: [{
        targets: [0, 2, 3, 4],
        className: 'text-center'
    }],

    columns: [

        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        },

        {
            data: 'nama_meja'
        },

        {
            data: 'status',
            render: function (data) {
                return data
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            }
        },

        {
            data: 'qrcode',
            render: function (data) {

                if (data) {

                    let url = `/storage/${data}`;

                    return `
                        <div class="d-flex justify-content-center">
                            <div class="qr-wrapper">
                                <img src="${url}" class="qr-img">

                                <a href="${url}"
                                   download
                                   class="qr-download">

                                    <i class="fa fa-download"></i>

                                </a>
                            </div>
                        </div>
                    `;
                }

                return '<span class="text-muted">No QR Code</span>';
            }
        },

        {
            data: null,
            render: function (data, type, row) {

                // JIKA DATA STATIK
                if (row.is_static) {

                    return `
                        <a href="/storage/${row.qrcode}"
                           download
                           class="btn btn-sm btn-success">

                            <i class="fa fa-download"></i>

                        </a>
                    `;
                }

                // DATA NORMAL
                return `
                    <button class="btn btn-sm btn-primary edit-btn"
                        data-id="${row.id}">

                        <i class="fa fa-pencil-square-o"></i>

                    </button>

                    <button class="btn btn-sm btn-danger delete-btn"
                        data-id="${row.id}">

                        <i class="fa fa-trash"></i>

                    </button>
                `;
            }
        }

    ]
});

let tableTrash = $('#dataTableTrash').DataTable({
    processing: true,
    ajax: {
        url: '/meja/data',
        dataSrc: 'data',
        data: function (d) {
            d.status = 0;
        }
    },
    columnDefs: [{
        targets: [0, 2, 3, 4],
        className: 'text-center'
    }],
    columns: [{
        data: null,
        render: function (data, type, row, meta) {
            return meta.row + 1;
        }
    },
    {
        data: 'nama_meja'
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
        data: 'qrcode',
        render: function (data) {
            if (data) {
                return `<img src="../../storage/${data}" alt="QR Code" width="100">`;
            } else {
                return '<span class="text-muted">No QR Code</span>';
            }
        }
    },
    {
        data: 'id',
        render: function (data) {
            return `
                            <button class="btn btn-sm btn-primary restore-btn" data-id="${data}"><i class="fa fa-retweet" aria-hidden="true"></i></button>
                        `;
        }
    }
    ]
});
{/* <button class="btn btn-sm btn-danger destroy-btn" data-id="${data}"><i class="fa fa-trash" aria-hidden="true"></i></button> */}

$('#tambah-data').on('click', function () {
    $('#formCreate')[0].reset();
    $('.text-danger').text('');

    let modal = new bootstrap.Modal(document.getElementById('modalCreate'));
    modal.show();
});

$('#formCreate').submit(function (e) {
    e.preventDefault();
    $('.text-danger').text('');

    let namaMeja = $('input[name="nama_meja"]').val();
    let slug = slugify(namaMeja);
    if (!namaMeja) {
        alertResult('warning', 'Validasi', 'Nomor meja wajib diisi');
        return;
    }

    QRCode.toDataURL($('meta[name="app-url"]').attr('content') + '?table=' + slug, function (err, url) {
        if (err) {
            alertResult('error', 'Error', 'Gagal generate QR');
            return;
        }

        $('#qrcode_base64').val(url);

        let form = document.getElementById('formCreate');
        let formData = new FormData(form);
        let token = $('meta[name="csrf-token"]').attr('content');
        let slugCtr = slug;
        formData.append('_token', token);
        formData.append('slug', slugCtr);

        $.ajax({
            url: '/meja/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $('#modalCreate').modal('hide');
                    $('#formCreate')[0].reset();

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
});

$(document).on('click', '.edit-btn', function () {
    let id = $(this).data('id');

    $('.text-danger').text('');

    $.get('/meja/' + id, function (res) {
        $('#edit_id').val(res.id);
        $('#edit_nama_meja').val(res.nama_meja);

        let modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    });
});

$('#formEdit').submit(function (e) {
    e.preventDefault();

    $('.text-danger').text('');

    let namaMeja = $('#edit_nama_meja').val();
    let id = $('#edit_id').val();
    let slug = slugify(namaMeja);
    if (!namaMeja) {
        alertResult('warning', 'Validasi', 'Nomor meja wajib diisi');
        return;
    }

    QRCode.toDataURL($('meta[name="app-url"]').attr('content') + '?table=' + slug, function (err, url) {
        if (err) {
            alertResult('error', 'Error', 'Gagal generate QR');
            return;
        }

        $('#edit_qrcode_base64').val(url);

        let form = document.getElementById('formEdit');
        let formData = new FormData(form);
        let token = $('meta[name="csrf-token"]').attr('content');
        let slugCtr = slug;
        formData.append('_token', token);
        formData.append('slug', slugCtr);

        $.ajax({
            url: '/meja/update/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $('#modalEdit').modal('hide');
                    $('#formEdit')[0].reset();

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

    // $.ajax({
    //     url: '/meja/update/' + id,
    //     type: 'POST',
    //     data: formData,
    //     processData: false,
    //     contentType: false,
    //     success: function (res) {
    //         if (res.success) {
    //             $('#modalEdit').modal('hide');

    //             table.ajax.reload(null, false);

    //             alertResult('success', 'Berhasil', res.message);
    //         }
    //     },
    //     error: function (err) {
    //         if (err.status === 422) {
    //             let errors = err.responseJSON.errors;

    //             $.each(errors, function (key, value) {
    //                 $('#formEdit .error-' + key).text(value[0]);
    //             });

    //             alertResult('warning', 'Validasi Gagal', 'Periksa kembali data');
    //         } else {
    //             alertResult('error', 'Error', 'Terjadi kesalahan server');
    //         }
    //     }
    // });
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
                url: '/meja/delete/' + id,
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
                url: '/meja/destroy/' + id,
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
                url: '/meja/restore/' + id,
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

$('#download-data').click(function () {
    window.open('/meja/download-qrcode', '_blank');
});