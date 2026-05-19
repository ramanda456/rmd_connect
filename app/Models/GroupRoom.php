<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRoom extends Model
{
    protected $fillable = ['name', 'created_by'];

    // Relasi many-to-many: group punya banyak user sebagai member
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'group_members',  // tabel pivot
            'group_id',       // foreign key group
            'user_id'         // foreign key user
        );
    }

    // Relasi: siapa yang buat group
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}