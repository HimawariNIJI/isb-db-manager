<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nim',
        'nama',
        'email',
        'kelas',
        'mysql_database',
        'mysql_username',
        'mysql_password',
    ];
    public function groupDatabases()
    {
        return $this->belongsToMany(GroupDatabase::class, 'group_database_student');
    }
}
