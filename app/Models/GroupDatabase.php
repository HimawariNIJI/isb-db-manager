<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupDatabase extends Model
{
    use HasFactory;

    protected $fillable = ['database_name', 'password'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'group_database_student');
    }
}
