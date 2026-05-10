@extends('layouts.template')

@section('own_style')
    <style>
        .qr-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            overflow: hidden;
            border-radius: 10px;
        }

        .qr-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        /* tombol */
        .qr-download {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);

            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 10px;
            border-radius: 55%;

            opacity: 0;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        /* 🔥 efek hover */
        .qr-wrapper:hover .qr-img {
            filter: blur(3px);
            transform: scale(1.1);
        }

        .qr-wrapper:hover .qr-download {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .qr-wrapper:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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
                        Data Meja
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail">
                        Arsip Terhapus
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- TAB 1 -->
                <div class="tab-pane fade show active" id="user">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" id="download-data" style="margin-right: 5px">
                                        <i class="fa fa-download me-2" aria-hidden="true"></i> Download QRCode
                                    </button>
                                    <button class="btn btn-primary" id="tambah-data" style="margin-right: 5px">
                                        <i class="fa fa-plus-circle me-2"></i> Tambah Data
                                    </button>
                                    <button class="btn btn-info refresh-data" data-table="data" style="margin-right: 5px">
                                        <i class="fa fa-refresh me-2" aria-hidden="true"></i> Refresh Data
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body">
                                <table id="dataTable" class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Nomor Meja</th>
                                            <th>Status</th>
                                            <th>QRCode</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2 -->
                <div class="tab-pane fade" id="detail">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-info refresh-data" data-table="trash" style="margin-right: 5px">
                                        <i class="fa fa-refresh me-2" aria-hidden="true"></i> Refresh Data
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body">
                                <table id="dataTableTrash" class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Nomor Meja</th>
                                            <th>Status</th>
                                            <th>QRCode</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCreate">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Meja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_meja">Nomor Meja (Wajib diisi)</label>
                            <input type="text" name="nama_meja" class="form-control" placeholder="Masukkan nomor meja">
                            <small class="text-danger error-nama_meja"></small>
                        </div>
                        <input type="hidden" name="qrcode_base64" id="qrcode_base64">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEdit">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Meja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="edit_nama_meja">Nomor Meja (Wajib diisi)</label>
                            <input type="text" name="edit_nama_meja" class="form-control"
                                placeholder="Masukkan nomor meja" id="edit_nama_meja">
                            <small class="text-danger error-edit_nama_meja"></small>
                        </div>
                        <input type="hidden" name="edit_qrcode_base64" id="edit_qrcode_base64">

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
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script src="{{ asset('own_assets/scripts/meja.js') }}"></script>
@endsection
