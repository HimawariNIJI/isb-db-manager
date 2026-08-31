<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\GroupDatabase;

class DashboardController extends Controller
{
    public function index()
    {
        $students = Student::latest()
            ->take(5)
            ->get();

        $totalStudents = Student::count();

        // Database pribadi mahasiswa
        $personalDatabases = Student::whereNotNull('mysql_database')
            ->count();

        // Database kelompok
        $groupDatabases = GroupDatabase::count();

        // Total database pribadi + kelompok
        $totalDatabases = $personalDatabases + $groupDatabases;

        $todayStudents = Student::whereDate(
            'created_at',
            today()
        )->count();

        return view('dashboard', compact(
            'students',
            'totalStudents',
            'totalDatabases',
            'todayStudents'
        ));
    }
}