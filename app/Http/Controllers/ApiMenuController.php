<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class ApiMenuController extends Controller
{
    public function index()
    {
        $categories = KategoriMenu::where('status', true)
            ->pluck('nama_kategori')
            ->toArray();

        array_unshift($categories, 'Semua');

        $menus = Menu::with(['kategoriMenu', 'fotoMenus'])
            ->where('status', true)
            ->where('is_ready', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $products = $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->nama_menu,
                'price' => (int) $menu->harga,
                'category' => $menu->kategoriMenu?->nama_kategori,
                'image' => $menu->fotoMenus->first()
                    ? asset('storage/' . $menu->fotoMenus->first()->foto_path)
                    : null,
                'description' => $menu->deskripsi,
            ];
        });

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
