<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\vendor\AlatController;
use App\Http\Controllers\vendor\BahanController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\vendor\ProdukController;
use App\Http\Controllers\vendor\LaporanController;
use App\Http\Controllers\vendor\pos\PosController;
use App\Http\Controllers\vendor\PenggunaController;
use App\Http\Controllers\vendor\PelangganController;
use App\Http\Controllers\vendor\TransaksiController;
use App\Http\Controllers\vendor\pos\InvoiceController;
use App\Http\Controllers\vendor\SpesifikasiController;
use App\Http\Controllers\vendor\pos\CheckoutController;
use App\Http\Controllers\vendor\KategoriProdukController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'dev'])->group(function () {
    Route::get('/administrator', [UserDashboardController::class, 'devDashboard'])
        ->name('dev.dashboard');

    // users routes resource
    Route::resource('/administrator/users', UserController::class);

    // vendors routes resource
    Route::resource('/administrator/vendors', VendorController::class);
});

Route::middleware(['auth', 'verified', 'vendor', 'tenants'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'vendorDashboard'])
        ->name('dashboard');

    // vendor users routes resource
    Route::resource('/dashboard/pengguna', PenggunaController::class);

    // Alat routes resource
    Route::resource('/dashboard/alat', AlatController::class);

    // Bahan routes resource
    Route::resource('/dashboard/bahan', BahanController::class);
    Route::delete('/dashboard/bahan/wholesale-price/{id}', [BahanController::class, 'deleteWholesalePrice'])
        ->name('bahan.wholesale-price.delete');

    // Pelanggan routes resource
    Route::resource('/dashboard/pelanggan', PelangganController::class);

    // Spesifikasi input type bahan produk routes resource
    Route::resource('/dashboard/spesifikasi', SpesifikasiController::class);

    // Produk routes resource
    Route::resource('/dashboard/produk', ProdukController::class);

    // Kategori Produk routes resource
    Route::resource('/dashboard/kategori-produk', KategoriProdukController::class);

    // Transaksi routes resource
    Route::resource('/dashboard/transaksi', TransaksiController::class);
    Route::get('/dashboard/transaksi/{id}/invoice', [TransaksiController::class, 'generateInvoice'])
        ->name('transaksi.generateInvoice');

    // routes Laporan
    Route::get('/dashboard/penjualan-harian', [LaporanController::class, 'penjualanHarian'])
        ->name('laporan.penjualan-harian');
    Route::get('/dashboard/penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])
        ->name('laporan.penjualan-bulanan');
    Route::get('/dashboard/penjualan-tahunan', [LaporanController::class, 'penjualanTahunan'])
        ->name('laporan.penjualan-tahunan');
    Route::get('/dashboard/export/penjualan', [LaporanController::class, 'exportPenjualan'])
        ->name('laporan.export-penjualan');

    // pos routes
    Route::prefix('/dashboard/pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [PosController::class, 'category'])->name('category');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::get('/cart', [PosController::class, 'cart'])->name('cart');
        Route::post('/add-to-cart', [PosController::class, 'addToCart'])->name('addToCart');
        Route::get('/cart/remove/{index}', [PosController::class, 'removeItem'])->name('removeItem');
        Route::get('/cart/clear', [PosController::class, 'clearCart'])->name('clearCart');
        Route::post('/check-price', [PosController::class, 'checkPrice'])->name('checkPrice');
        Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');

        Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
        
        Route::post('/customer/create', [CheckoutController::class, 'createCustomer'])->name('customer.create');
        Route::get('/invoice/{transaksi}', [InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/invoice/{transaksi}/download', [InvoiceController::class, 'download'])->name('invoice.download');
    });

    // profile route
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/dashboard/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
