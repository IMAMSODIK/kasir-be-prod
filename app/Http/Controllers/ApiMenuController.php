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

        $menus = Menu::with('kategoriMenu')
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
                'image' => $menu->image
                    ? asset('storage/' . $menu->image)
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
