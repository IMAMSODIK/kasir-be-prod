<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class ApiMenuController extends Controller
{
    public function index(Request $r)
    {
        $categories = KategoriMenu::where('status', true)
            ->pluck('nama_kategori')
            ->toArray();

        array_unshift($categories, 'Semua');

        $menus = Menu::with([
            'kategoriMenu',
            'fotoMenus'
        ])
            ->where('status', true)
            ->where('is_ready', true);

        // Filter kategori
        if ($r->category && $r->category != 'Semua') {

            $menus->whereHas('kategoriMenu', function ($q) use ($r) {
                $q->where('nama_kategori', $r->category);
            });
        }

        // Search menu
        if ($r->search) {

            $menus->where(function ($q) use ($r) {

                $q->where('nama_menu', 'like', '%' . $r->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $r->search . '%');
            });
        }

        $menus = $menus
            ->orderBy('created_at', 'desc')
            ->get();

        $products = $menus->map(function ($menu) {

            $firstPhoto = $menu->fotoMenus->first();

            return [
                'id' => $menu->id,
                'name' => $menu->nama_menu,
                'price' => (int) $menu->harga,
                'category' => $menu->kategoriMenu?->nama_kategori,
                'image' => $firstPhoto
                    ? asset('storage/' . $firstPhoto->foto_path)
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
