@extends('pages.admin.layout')

@section('nav-active-index', '3')

@section('page-title')
    <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item><a href="/admin">Admin</a></el-breadcrumb-item>
        <el-breadcrumb-item>Settings</el-breadcrumb-item>
    </el-breadcrumb>
@endsection

@section('page-content')

    <admin-settings-bulk-spots></admin-settings-bulk-spots>

@endsection
