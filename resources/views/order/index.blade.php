@extends('layouts.template')

@section('own_style')
    <style>
        .order-list{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.order-item{
    background:#fff;
    border-radius:18px;
    padding:18px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 15px rgba(0,0,0,.06);
    transition:.2s;
    cursor:pointer;
}

.order-item:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(0,0,0,.10);
}

.order-item-left{
    display:flex;
    align-items:center;
    gap:15px;
}

.order-icon{
    width:55px;
    height:55px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:20px;
}

.order-item-right{
    text-align:right;
}

.search-order{
    height:50px;
    border-radius:14px;
    padding-left:45px;
    border:none;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
}

.search-order:focus{
    box-shadow:0 4px 20px rgba(0,0,0,.10);
    border:none;
}

.search-icon{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#999;
    z-index:10;
}

.modal-content{
    border-radius:20px;
}

@media(max-width:768px){

    .order-item{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .order-item-right{
        width:100%;
        text-align:left;
    }

}
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>{{ $pageTitle }}</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#stroke-home') }}">
                                    </use>
                                </svg></a></li>
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid product-wrapper sidebaron">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @else
            <ul class="nav nav-tabs" id="myTab">
                <li class="nav-item">
                    <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user">
                        Data Order
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail">
                        Arsip Terhapus
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="user">

                    <div class="container-fluid">

                        <!-- HEADER -->
                        <div class="row mb-3">

                            <div class="col-md-6">

                                <div class="position-relative">

                                    <i class="fa fa-search search-icon"></i>

                                    <input type="text" class="form-control search-order" id="searchPending"
                                        placeholder="Cari order aktif...">

                                </div>

                            </div>

                            <div class="col-md-6 text-end">

                                <button class="btn btn-info" id="refreshPending">

                                    <i class="fa fa-refresh me-2"></i>
                                    Refresh Data

                                </button>

                            </div>

                        </div>

                        <!-- LIST -->
                        <div id="pendingContainer" class="order-list">

                        </div>

                    </div>

                </div>

                <div class="tab-pane fade" id="detail">

                    <div class="container-fluid">

                        <!-- HEADER -->
                        <div class="row mb-3">

                            <div class="col-md-6">

                                <div class="position-relative">

                                    <i class="fa fa-search search-icon"></i>

                                    <input type="text" class="form-control search-order" id="searchDone"
                                        placeholder="Cari riwayat transaksi...">

                                </div>

                            </div>

                            <div class="col-md-6 text-end">

                                <button class="btn btn-info" id="refreshDone">

                                    <i class="fa fa-refresh me-2"></i>
                                    Refresh Data

                                </button>

                            </div>

                        </div>

                        <!-- LIST -->
                        <div id="doneContainer" class="order-list">

                        </div>

                        <!-- LOAD MORE -->
                        <div class="text-center mt-4">

                            <button class="btn btn-outline-primary" id="loadMoreBtn">

                                <i class="fa fa-plus me-2"></i>
                                Load More

                            </button>

                        </div>

                    </div>

                </div>

                <div id="modalContainer"></div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEdit">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                            <small class="text-danger error-name"></small>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                            <small class="text-danger error-email"></small>
                        </div>

                        <div class="mb-3">
                            <label>Foto</label>
                            <input type="file" name="foto" class="form-control" id="edit_foto">

                            <div class="mt-2">
                                <img id="preview-edit_foto" style="max-width:150px; border-radius:8px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" id="edit_role" class="form-control">
                                <option value="kasir">Kasir</option>
                                <option value="waiter">Waiter</option>
                                <option value="kitchen">Kitchen</option>
                            </select>
                            <small class="text-danger error-role"></small>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script src="{{ asset('own_assets/scripts/order.js') }}"></script>
@endsection
