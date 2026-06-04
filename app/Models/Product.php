<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Override;

class Product extends Model
{
    use HasFactory;

    protected $attributes = [
        'stock' => 0,
        'image' => null,
    ];

    protected $fillable = [
        'name',
        'image',
        'descriptions',
        'price',
        'stock'
    ];

    protected $casts = [
        'price' => 'integer', //Cast ke integer (untuk menghilangkan .00)
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Asesor untuk format price
    public function getFormatPriceAttribute() {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    //Mutator untuk price (otomatis format sebelum disimpan)
    public function getImageUrlAttribute() {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    //Scope untuk stock product yang habis
    public function scopeStock($query) {
        return $query->where('stock', '=', 0);
    }

    //Cek product punya gambar
    public function hasImage() {
        return !is_null($this->image) && (Storage::disk('public')->exists($this->image));
    }

    //Hapus gambar
    public function deleteImage() {
        if($this->hasImage()) {
            Storage::disk('public')->delete($this->image);
            $this->image = null;
            $this->save();
            return true;
        }

        return false;
    }
}