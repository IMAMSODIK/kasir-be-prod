let doneLimit = 10;

$(document).ready(function(){

    loadPendingOrders();
    loadDoneOrders();

});





/*
|--------------------------------------------------------------------------
| ORDER AKTIF
|--------------------------------------------------------------------------
*/
function loadPendingOrders(keyword = '')
{
    $.ajax({

        url: '/orders/pending',
        type: 'GET',

        data: {
            keyword: keyword
        },

        success: function(response){

            let html = '';
            let modalHtml = '';

            if(response.length > 0){

                response.forEach(order => {

                    let totalItem = 0;
                    let itemRows = '';

                    order.items.forEach(item => {

                        totalItem += item.qty;

                        itemRows += `
                        
                        <tr>

                            <td>

                                <div class="fw-semibold">
                                    ${item.nama_menu}
                                </div>

                                ${
                                    item.note
                                    ?
                                    `
                                    <small class="text-muted">
                                        Catatan : ${item.note}
                                    </small>
                                    `
                                    :
                                    ''
                                }

                            </td>

                            <td>
                                Rp ${formatRupiah(item.harga)}
                            </td>

                            <td>
                                ${item.qty}
                            </td>

                            <td>
                                Rp ${formatRupiah(item.harga * item.qty)}
                            </td>

                        </tr>

                        `;

                    });





                    /*
                    |--------------------------------------------------------------------------
                    | ITEM
                    |--------------------------------------------------------------------------
                    */
                    html += `
                    
                    <div class="order-item"
                         data-bs-toggle="modal"
                         data-bs-target="#modalPending${order.id}">

                        <div class="order-item-left">

                            <div class="order-icon bg-warning">
                                <i class="fa fa-cutlery"></i>
                            </div>

                            <div>

                                <div class="d-flex align-items-center gap-2 mb-1">

                                    <h6 class="mb-0 fw-bold">
                                        ${order.order_id}
                                    </h6>

                                    <span class="badge bg-warning text-dark">
                                        ${order.status}
                                    </span>

                                </div>

                                <div class="small text-muted mb-1">

                                    ${order.customer_name ?? 'Customer Umum'}
                                    •
                                    Meja ${order.meja ? order.meja.nama_meja : '-'}

                                </div>

                                <div class="small text-muted">

                                    ${totalItem} item

                                </div>

                            </div>

                        </div>

                        <div class="order-item-right">

                            <div class="fw-bold text-primary">
                                Rp ${formatRupiah(order.total_amount)}
                            </div>

                            <small class="text-muted">
                                klik detail
                            </small>

                        </div>

                    </div>

                    `;





                    /*
                    |--------------------------------------------------------------------------
                    | MODAL
                    |--------------------------------------------------------------------------
                    */
                    modalHtml += `
                    
                    <div class="modal fade"
                         id="modalPending${order.id}"
                         tabindex="-1">

                        <div class="modal-dialog modal-lg modal-dialog-scrollable">

                            <div class="modal-content border-0">

                                <div class="modal-header bg-warning">

                                    <div>

                                        <h5 class="modal-title fw-bold">
                                            Detail Order
                                        </h5>

                                        <small>
                                            ${order.order_id}
                                        </small>

                                    </div>

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"></button>

                                </div>





                                <div class="modal-body">

                                    <div class="table-responsive">

                                        <table class="table align-middle">

                                            <thead class="table-light">

                                                <tr>
                                                    <th>Menu</th>
                                                    <th>Harga</th>
                                                    <th>Qty</th>
                                                    <th>Subtotal</th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                                ${itemRows}

                                            </tbody>

                                        </table>

                                    </div>





                                    <div class="text-end mt-4">

                                        <h4 class="fw-bold text-primary">
                                            Rp ${formatRupiah(order.total_amount)}
                                        </h4>

                                    </div>

                                </div>





                                <div class="modal-footer">

                                    <button class="btn btn-success btnSelesai"
                                            data-id="${order.id}">

                                        <i class="fa fa-check me-2"></i>
                                        Selesaikan

                                    </button>

                                    <button class="btn btn-danger btnBatalkan"
                                            data-id="${order.id}">

                                        <i class="fa fa-times me-2"></i>
                                        Batalkan

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    `;

                });

            }else{

                html = `
                    <div class="alert alert-warning text-center">
                        Belum ada order aktif
                    </div>
                `;

            }

            $('#pendingContainer').html(html);

            $('#modalContainer').html(modalHtml);

        }

    });
}





/*
|--------------------------------------------------------------------------
| RIWAYAT
|--------------------------------------------------------------------------
*/
function loadDoneOrders(keyword = '')
{
    $.ajax({

        url: '/orders/done',
        type: 'GET',

        data: {
            keyword: keyword,
            limit: doneLimit
        },

        success: function(response){

            let html = '';
            let modalHtml = '';

            if(response.data.length > 0){

                response.data.forEach(order => {

                    let totalItem = 0;
                    let itemRows = '';

                    order.items.forEach(item => {

                        totalItem += item.qty;

                        itemRows += `
                        
                        <tr>

                            <td>${item.nama_menu}</td>

                            <td>
                                Rp ${formatRupiah(item.harga)}
                            </td>

                            <td>
                                ${item.qty}
                            </td>

                            <td>
                                Rp ${formatRupiah(item.harga * item.qty)}
                            </td>

                        </tr>

                        `;

                    });





                    html += `
                    
                    <div class="order-item"
                         data-bs-toggle="modal"
                         data-bs-target="#modalDone${order.id}">

                        <div class="order-item-left">

                            <div class="order-icon bg-success">
                                <i class="fa fa-check"></i>
                            </div>

                            <div>

                                <div class="d-flex align-items-center gap-2 mb-1">

                                    <h6 class="mb-0 fw-bold">
                                        ${order.order_id}
                                    </h6>

                                    <span class="badge bg-success">
                                        ${order.status}
                                    </span>

                                </div>

                                <div class="small text-muted mb-1">

                                    ${order.customer_name ?? 'Customer Umum'}
                                    •
                                    Meja ${order.meja ? order.meja.nama_meja : '-'}

                                </div>

                                <div class="small text-muted">

                                    ${totalItem} item

                                </div>

                            </div>

                        </div>

                        <div class="order-item-right">

                            <div class="fw-bold text-success">
                                Rp ${formatRupiah(order.total_amount)}
                            </div>

                            <small class="text-muted">
                                transaksi selesai
                            </small>

                        </div>

                    </div>

                    `;





                    modalHtml += `
                    
                    <div class="modal fade"
                         id="modalDone${order.id}"
                         tabindex="-1">

                        <div class="modal-dialog modal-lg modal-dialog-scrollable">

                            <div class="modal-content border-0">

                                <div class="modal-header bg-success text-white">

                                    <div>

                                        <h5 class="modal-title fw-bold">
                                            Detail Transaksi
                                        </h5>

                                        <small>
                                            ${order.order_id}
                                        </small>

                                    </div>

                                    <button type="button"
                                            class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>

                                </div>





                                <div class="modal-body">

                                    <div class="table-responsive">

                                        <table class="table">

                                            <thead class="table-light">

                                                <tr>
                                                    <th>Menu</th>
                                                    <th>Harga</th>
                                                    <th>Qty</th>
                                                    <th>Subtotal</th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                                ${itemRows}

                                            </tbody>

                                        </table>

                                    </div>





                                    <div class="text-end mt-4">

                                        <h4 class="fw-bold text-success">
                                            Rp ${formatRupiah(order.total_amount)}
                                        </h4>

                                    </div>

                                </div>





                                <div class="modal-footer">

                                    <button class="btn btn-danger btnDelete"
                                            data-id="${order.id}">

                                        <i class="fa fa-trash me-2"></i>
                                        Hapus

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    `;

                });

            }

            $('#doneContainer').html(html);

            $('#modalContainer').append(modalHtml);

        }

    });
}





/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/
$('#searchPending').keyup(function(){

    loadPendingOrders($(this).val());

});

$('#searchDone').keyup(function(){

    loadDoneOrders($(this).val());

});





/*
|--------------------------------------------------------------------------
| REFRESH
|--------------------------------------------------------------------------
*/
$('#refreshPending').click(function(){

    $('#searchPending').val('');

    loadPendingOrders();

});

$('#refreshDone').click(function(){

    $('#searchDone').val('');

    doneLimit = 10;

    loadDoneOrders();

});





/*
|--------------------------------------------------------------------------
| LOAD MORE
|--------------------------------------------------------------------------
*/
$('#loadMoreBtn').click(function(){

    doneLimit += 10;

    loadDoneOrders($('#searchDone').val());

});





/*
|--------------------------------------------------------------------------
| SELESAIKAN
|--------------------------------------------------------------------------
*/
$(document).on('click', '.btnSelesai', function(){

    let id = $(this).data('id');

    $.ajax({

        url: '/orders/' + id + '/selesai',
        type: 'POST',

        data: {
            _token: '{{ csrf_token() }}'
        },

        success: function(){

            $('.modal').modal('hide');

            loadPendingOrders();

            loadDoneOrders();

        }

    });

});





/*
|--------------------------------------------------------------------------
| BATALKAN
|--------------------------------------------------------------------------
*/
$(document).on('click', '.btnBatalkan', function(){

    let id = $(this).data('id');

    $.ajax({

        url: '/orders/' + id + '/batalkan',
        type: 'POST',

        data: {
            _token: '{{ csrf_token() }}'
        },

        success: function(){

            $('.modal').modal('hide');

            loadPendingOrders();

            loadDoneOrders();

        }

    });

});





/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/
$(document).on('click', '.btnDelete', function(){

    if(confirm('Hapus order ini ?')){

        let id = $(this).data('id');

        $.ajax({

            url: '/orders/' + id,
            type: 'DELETE',

            data: {
                _token: '{{ csrf_token() }}'
            },

            success: function(){

                $('.modal').modal('hide');

                loadDoneOrders();

            }

        });

    }

});





/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/
function formatRupiah(angka)
{
    return new Intl.NumberFormat('id-ID').format(angka);
}
