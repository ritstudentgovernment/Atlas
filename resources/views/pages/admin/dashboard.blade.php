@extends('pages.admin.layout')

@section('nav-active-index', "0")
@section('page-title')
    <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item>Admin</el-breadcrumb-item>
    </el-breadcrumb>
@endsection

@section('page-content')

    <admin-dashboard></admin-dashboard>

@endsection