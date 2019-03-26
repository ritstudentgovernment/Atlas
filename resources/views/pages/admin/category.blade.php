@extends('pages.admin.layout')

@section('nav-active-index', "1")
@section('page-title')
    <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item><a href="/admin">Admin</a></el-breadcrumb-item>
        <el-breadcrumb-item><a href="/admin/spots/categories">Spot Categories</a></el-breadcrumb-item>
        <el-breadcrumb-item>{{ $category["name"] }}</el-breadcrumb-item>
    </el-breadcrumb>
@endsection

@section('page-content')

    <admin-category-editor></admin-category-editor>

@endsection