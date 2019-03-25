@extends('pages.admin.layout')

@section('nav-active-index', "0")
@section('page-title')
    <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item>Admin</el-breadcrumb-item>
    </el-breadcrumb>
@endsection

@section('page-content')

    <el-row :gutter="20">
        <el-col :span="8"><div class="grid-content bg-purple"></div></el-col>
        <el-col :span="16"><div class="grid-content bg-purple"></div></el-col>
    </el-row>

@endsection