@extends('pages.admin.layout')

@section('nav-open-index', "2")
@section('nav-active-index', "2-1")
@section('page-title')
    <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item><a href="/admin">Admin</a></el-breadcrumb-item>
        <el-breadcrumb-item><a href="/admin/users/all">Users</a></el-breadcrumb-item>
        <el-breadcrumb-item>Staff</el-breadcrumb-item>
    </el-breadcrumb>
@endsection

@section('page-content')

    <admin-staff-manager :raw-users="{{ $users }}"></admin-staff-manager>

@endsection